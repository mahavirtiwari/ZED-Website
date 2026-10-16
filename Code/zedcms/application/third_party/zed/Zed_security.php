<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Security layer shared by the public site and the CMS (used from MY_Output in
 * both applications).
 *
 * 1. Security headers, including a Content-Security-Policy built on a
 *    per-request nonce, without 'unsafe-inline'.
 * 2. harden_html() rewrites a page so it still works under that policy:
 *    - <script> and <style> elements get the nonce;
 *    - style="..." attributes become a class (so the first paint is right) and
 *      are then re-applied as real inline styles through the CSSOM, which CSP
 *      allows, so script that reads or changes el.style keeps working;
 *    - on*="..." handlers and javascript: links become addEventListener calls.
 *    Both are done by one nonced script injected before </body>.
 * 3. Optional e-mail address obfuscation (abc@nic.in -> abc[at]nic[dot]in)
 *    for public pages, against e-mail harvesters (CWE-200).
 *
 * HTML that scripts build at runtime is not rewritten: write it without
 * style/on* attributes (use classes, .css() and .on() instead).
 */
class Zed_security
{
	private static $nonce;

	// events that a <body on...> attribute registers on window
	private static $window_events = array('load', 'unload', 'beforeunload', 'resize', 'scroll', 'hashchange', 'popstate', 'pageshow', 'pagehide', 'message', 'online', 'offline');

	public static function nonce()
	{
		if (self::$nonce === NULL)
		{
			self::$nonce = rtrim(strtr(base64_encode(random_bytes(18)), '+/', '-_'), '=');
		}
		return self::$nonce;
	}

	public static function is_https()
	{
		return ( ! empty($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) !== 'off')
			OR (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && strtolower($_SERVER['HTTP_X_FORWARDED_PROTO']) === 'https')
			OR (isset($_SERVER['SERVER_PORT']) && (int) $_SERVER['SERVER_PORT'] === 443);
	}

	/**
	 * Send the security headers.
	 * @param array $csp     extra CSP sources per directive, e.g. array('frame-src' => array('https://www.youtube.com'))
	 * @param bool  $private TRUE for pages that must never be cached (CMS)
	 */
	public static function send_headers(array $csp = array(), $private = FALSE)
	{
		if (PHP_SAPI === 'cli' OR headers_sent())
		{
			return;
		}
		header_remove('X-Powered-By');

		$nonce = "'nonce-".self::nonce()."'";
		$directives = array(
			'default-src'     => array("'self'"),
			'script-src'      => array("'self'", $nonce),
			'style-src'       => array("'self'", $nonce),
			'img-src'         => array("'self'", 'data:'),
			'font-src'        => array("'self'", 'data:'),
			'connect-src'     => array("'self'"),
			'frame-src'       => array("'self'"),
			'form-action'     => array("'self'"),
			'object-src'      => array("'none'"),
			'base-uri'        => array("'self'"),
			'frame-ancestors' => array("'self'"),
		);
		foreach ($csp as $directive => $sources)
		{
			$current = isset($directives[$directive]) ? $directives[$directive] : array();
			$directives[$directive] = array_merge($current, (array) $sources);
		}
		$policy = array();
		foreach ($directives as $directive => $sources)
		{
			$policy[] = $directive.' '.implode(' ', array_unique($sources));
		}
		if (self::is_https())
		{
			$policy[] = 'upgrade-insecure-requests';
		}

		header('Content-Security-Policy: '.implode('; ', $policy));
		header('X-Frame-Options: SAMEORIGIN');
		header('X-Content-Type-Options: nosniff');
		header('X-XSS-Protection: 1; mode=block');
		header('Referrer-Policy: strict-origin-when-cross-origin');
		header('Permissions-Policy: camera=(), microphone=(), geolocation=(), payment=(), usb=()');
		if (self::is_https())
		{
			header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
		}
		if ($private)
		{
			header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
			header('Pragma: no-cache');
		}
	}

	/**
	 * Rewrite an HTML page for the nonce-based CSP (see the class comment).
	 * @param string $html
	 * @param array  $options obfuscate_emails (bool), autocomplete_off (bool: forms and password fields)
	 * @return string
	 */
	public static function harden_html($html, array $options = array())
	{
		if ( ! is_string($html) OR strpos($html, '<') === FALSE)
		{
			return $html;
		}
		$state = (object) array(
			'nonce'    => self::nonce(),
			'css'      => array(),   // class => declarations
			'events'   => array(),   // element id => list of array(event, code, prevent_default)
			'seq'      => 0,
			'emails'   => ! empty($options['obfuscate_emails']),
			'autocomp' => ! empty($options['autocomplete_off']),
		);

		$attrs = '((?:[^>"\']++|"[^"]*+"|\'[^\']*+\')*+)';
		$pattern = '~(<!--.*?-->)'                                        // 1 comment
			.'|<(script|style|textarea|title)\b'.$attrs.'>(.*?)</\2\s*>'  // 2 tag, 3 attrs, 4 raw content
			.'|(</[^>]*>)'                                                  // 5 end tag
			.'|<([a-zA-Z][a-zA-Z0-9:-]*)'.$attrs.'>'                        // 6 tag, 7 attrs
			.'|([^<]+)'                                                     // 8 text
			.'|(<)~is';                                                     // 9 stray "<"

		$out = preg_replace_callback($pattern, function ($m) use ($state) {
			if ( ! empty($m[1]))
			{
				// harvesters read comments too
				return $state->emails ? Zed_security::obfuscate_emails($m[1]) : $m[1];
			}
			if ( ! empty($m[5]) OR ! empty($m[9]))
			{
				return $m[0];
			}
			if ( ! empty($m[2]))
			{
				$tag = strtolower($m[2]);
				$attributes = Zed_security::rewrite_attributes($tag, $m[3], $state);
				if (($tag === 'script' OR $tag === 'style') && ! preg_match('/\snonce\s*=/i', $attributes))
				{
					$attributes .= ' nonce="'.$state->nonce.'"';
				}
				return '<'.$m[2].$attributes.'>'.$m[4].'</'.$m[2].'>';
			}
			if ( ! empty($m[6]))
			{
				return '<'.$m[6].Zed_security::rewrite_attributes(strtolower($m[6]), $m[7], $state).'>';
			}
			if (isset($m[8]) && $m[8] !== '' && $state->emails)
			{
				return Zed_security::obfuscate_emails($m[8]);
			}
			return $m[0];
		}, $html);

		if ($out === NULL)
		{
			// PCRE limit hit: send the page as it was rather than nothing
			log_message('error', 'Zed_security::harden_html: '.preg_last_error_msg());
			return $html;
		}

		if ( ! empty($state->css))
		{
			$rules = '';
			foreach ($state->css as $class => $css)
			{
				// :not(#x) adds ID-level specificity, so the class wins like the inline style did
				$rules .= '.'.$class.':not(#zs-x):not(#zs-x):not(#zs-x){'.str_replace('<', '\3C ', $css)."}\n";
			}
			$out = self::insert_before($out, '</head>', '<style nonce="'.$state->nonce.'">'."\n".$rules.'</style>', TRUE);
		}
		if ( ! empty($state->css) OR ! empty($state->events))
		{
			$out = self::insert_before($out, '</body>', self::binder_script($state), FALSE);
		}
		return $out;
	}

	/**
	 * Rewrite one start tag's attribute string.
	 * Public only so the preg callback can reach it.
	 */
	public static function rewrite_attributes($tag, $raw, $state)
	{
		$needs_autocomplete = $state->autocomp && ($tag === 'form' OR $tag === 'input');
		if ( ! $needs_autocomplete && ! preg_match('/\b(style|on[a-z]+|href)\s*=/i', $raw))
		{
			return $raw;
		}
		preg_match_all('~([^\s"\'>/=]+)(?:\s*=\s*("[^"]*"|\'[^\']*\'|[^\s"\'>]+))?~', $raw, $found, PREG_SET_ORDER);
		$self_closing = (bool) preg_match('~/\s*$~', $raw);

		$list = array();      // array(name, raw value or NULL)
		$styles = '';
		$handlers = array();
		$href_handler = NULL;
		foreach ($found as $a)
		{
			$name = $a[1];
			$value = isset($a[2]) ? $a[2] : NULL;
			$lname = strtolower($name);
			$decoded = $value === NULL ? '' : html_entity_decode(trim($value, '"\''), ENT_QUOTES | ENT_HTML5, 'UTF-8');

			if ($lname === 'style')
			{
				$styles .= trim($decoded, " \t\r\n;").';';
				continue;
			}
			if (strncmp($lname, 'on', 2) === 0 && strlen($lname) > 2 && ctype_alpha(substr($lname, 2)))
			{
				if (trim($decoded) !== '')
				{
					$handlers[] = array(substr($lname, 2), $decoded, FALSE);
				}
				continue;
			}
			if ($lname === 'href' && preg_match('/^\s*javascript\s*:(.*)$/is', $decoded, $js))
			{
				$code = trim($js[1]);
				// run after any onclick, and only if it did not cancel (like the browser following the link)
				$href_handler = preg_match('/^(void\s*\(?\s*0\s*\)?\s*;?|;)?$/i', $code)
					? array('click', '', 1)
					: array('click', $code, 2);
				$list[] = array('href', '"#"');
				continue;
			}
			if ($lname === 'href' && $state->emails && preg_match('/^\s*mailto:/i', $decoded))
			{
				// a mailto: link is what harvesters collect first
				continue;
			}
			$list[] = array($name, $value);
		}

		if ($href_handler !== NULL)
		{
			$handlers[] = $href_handler;
		}
		$styles = trim($styles, '; ');
		if ($styles !== '')
		{
			$class = 'zs'.substr(md5($styles), 0, 10);
			$state->css[$class] = $styles;
			$merged = FALSE;
			foreach ($list as $i => $item)
			{
				if (strtolower($item[0]) === 'class' && $item[1] !== NULL)
				{
					$current = html_entity_decode(trim($item[1], '"\''), ENT_QUOTES | ENT_HTML5, 'UTF-8');
					$list[$i][1] = '"'.htmlspecialchars(trim($current.' '.$class), ENT_QUOTES, 'UTF-8').'"';
					$merged = TRUE;
				}
			}
			if ( ! $merged)
			{
				$list[] = array('class', '"'.$class.'"');
			}
			$list[] = array('data-zs', '"'.$class.'"');
		}
		if ( ! empty($handlers))
		{
			$id = ++$state->seq;
			$state->events[$id] = $handlers;
			$list[] = array('data-ze', '"'.$id.'"');
		}
		if ($state->autocomp)
		{
			$has = FALSE;
			$is_password = FALSE;
			foreach ($list as $item)
			{
				$n = strtolower($item[0]);
				$has = ($has || $n === 'autocomplete');
				if ($n === 'type' && $item[1] !== NULL && strtolower(trim($item[1], '"\'')) === 'password')
				{
					$is_password = TRUE;
				}
			}
			if ( ! $has && ($tag === 'form' OR ($tag === 'input' && $is_password)))
			{
				$list[] = array('autocomplete', '"off"');
			}
		}

		$rebuilt = '';
		foreach ($list as $item)
		{
			$rebuilt .= ' '.$item[0].($item[1] === NULL ? '' : '='.$item[1]);
		}
		return $rebuilt.($self_closing ? ' /' : '');
	}

	/** abc.def@nic.in -> abc[dot]def[at]nic[dot]in in a text node */
	public static function obfuscate_emails($text)
	{
		if (strpos($text, '@') === FALSE)
		{
			return $text;
		}
		return preg_replace_callback('/[A-Za-z0-9._%+-]+@[A-Za-z0-9-]+(?:\.[A-Za-z0-9-]+)+/', function ($m) {
			return str_replace(array('@', '.'), array('[at]', '[dot]'), $m[0]);
		}, $text);
	}

	private static function insert_before($html, $marker, $insert, $first)
	{
		$pos = $first ? stripos($html, $marker) : strripos($html, $marker);
		if ($pos === FALSE)
		{
			return $first ? $insert.$html : $html.$insert;
		}
		return substr($html, 0, $pos).$insert.substr($html, $pos);
	}

	private static function binder_script($state)
	{
		$css = json_encode((object) $state->css, JSON_HEX_TAG | JSON_HEX_AMP | JSON_UNESCAPED_SLASHES);
		$events = array();
		foreach ($state->events as $id => $handlers)
		{
			$items = array();
			foreach ($handlers as $h)
			{
				$code = str_ireplace('</script', '<\/script', $h[1]);
				$items[] = '['.json_encode($h[0]).',function(event){'."\n".$code."\n".'},'.(int) $h[2].']';
			}
			$events[] = json_encode((string) $id).':['.implode(',', $items).']';
		}
		$window_events = json_encode(self::$window_events);

		return '<script nonce="'.$state->nonce.'">'."\n"
			.'(function(){var S='.$css.',E={'.implode(",\n", $events).'},W='.$window_events.';'."\n"
			.'function each(s,f){var l=document.querySelectorAll(s);for(var i=0;i<l.length;i++)f(l[i]);}'
			// style: re-apply as a real inline style (CSSOM is allowed by CSP); keep what scripts already set
			.'each("[data-zs]",function(el){var c=el.getAttribute("data-zs");el.removeAttribute("data-zs");if(!S.hasOwnProperty(c))return;'
			.'var t=document.createElement("div").style;t.cssText=S[c];'
			.'for(var i=0;i<t.length;i++){var p=t[i];if(!el.style.getPropertyValue(p))el.style.setProperty(p,t.getPropertyValue(p),t.getPropertyPriority(p));}'
			.'el.classList.remove(c);});'
			// handlers: same semantics as on...="": "this" is the element, "return false" cancels the default action
			.'each("[data-ze]",function(el){var h=E[el.getAttribute("data-ze")];el.removeAttribute("data-ze");if(!h)return;'
			.'h.forEach(function(x){var t=(el===document.body&&W.indexOf(x[0])>=0)?window:el;'
			.'t.addEventListener(x[0],function(e){if(x[2]===2){if(!e.defaultPrevented){x[1].call(el,e);e.preventDefault();}return;}var r=x[1].call(el,e);if(r===false||x[2])e.preventDefault();});});});'
			.'})();'."\n".'</script>';
	}
}
