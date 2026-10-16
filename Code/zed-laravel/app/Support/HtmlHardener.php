<?php

namespace App\Support;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Vite;

/**
 * Rewrites HTML responses so they work under the strict, nonce-based
 * Content-Security-Policy sent by App\Http\Middleware\SecurityHeaders
 * (no 'unsafe-inline'; security audit item 4). Used by the HardenHtml middleware.
 *
 * - <script> and <style> elements get the request nonce;
 * - style="..." attributes become a class (so the first paint is right) and are
 *   then re-applied as real inline styles through the CSSOM, which CSP allows,
 *   so scripts that read or change el.style keep working;
 * - on*="..." handlers and javascript: links become addEventListener calls.
 *   Both are done by one nonced script injected before </body>.
 * - Optional: e-mail addresses in text become abc[at]nic[dot]in and mailto:
 *   links are dropped (security audit item 5, CWE-200).
 * - Optional: autocomplete="off" on forms and password fields (audit item 9).
 *
 * Needed mainly for content saved by the CMS editor (style attributes) and for
 * markup ported from the old site. HTML that scripts build at runtime is not
 * rewritten: write it without style/on* attributes (classes and listeners).
 */
class HtmlHardener
{
    /** Events that a <body on...> attribute registers on window. */
    private const WINDOW_EVENTS = ['load', 'unload', 'beforeunload', 'resize', 'scroll', 'hashchange', 'popstate', 'pageshow', 'pagehide', 'message', 'online', 'offline'];

    private string $nonce;

    /** @var array<string, string> class => CSS declarations */
    private array $css = [];

    /** @var array<int, array<int, array{0: string, 1: string, 2: int}>> element id => handlers */
    private array $events = [];

    private int $seq = 0;

    public function __construct(
        private bool $obfuscateEmails = false,
        private bool $autocompleteOff = false,
    ) {
        $this->nonce = (string) Vite::cspNonce();
    }

    public function harden(string $html): string
    {
        if (! str_contains($html, '<')) {
            return $html;
        }

        $attrs = '((?:[^>"\']++|"[^"]*+"|\'[^\']*+\')*+)';
        $pattern = '~(<!--.*?-->)'                                          // 1 comment
            .'|<(script|style|textarea|title)\b'.$attrs.'>(.*?)</\2\s*>'    // 2 tag, 3 attrs, 4 raw content
            .'|(</[^>]*>)'                                                    // 5 end tag
            .'|<([a-zA-Z][a-zA-Z0-9:-]*)'.$attrs.'>'                          // 6 tag, 7 attrs
            .'|([^<]+)'                                                       // 8 text
            .'|(<)~is';                                                       // 9 stray "<"

        $out = preg_replace_callback($pattern, function (array $m): string {
            if (($m[1] ?? '') !== '') {
                // harvesters read comments too
                return $this->obfuscateEmails ? self::obfuscateEmails($m[1]) : $m[1];
            }
            if (($m[5] ?? '') !== '' || ($m[9] ?? '') !== '') {
                return $m[0];
            }
            if (($m[2] ?? '') !== '') {
                $tag = strtolower($m[2]);
                $attributes = $this->rewriteAttributes($tag, $m[3]);
                if (($tag === 'script' || $tag === 'style') && ! preg_match('/\snonce\s*=/i', $attributes)) {
                    $attributes .= ' nonce="'.$this->nonce.'"';
                }

                return '<'.$m[2].$attributes.'>'.$m[4].'</'.$m[2].'>';
            }
            if (($m[6] ?? '') !== '') {
                return '<'.$m[6].$this->rewriteAttributes(strtolower($m[6]), $m[7]).'>';
            }
            if (isset($m[8]) && $m[8] !== '' && $this->obfuscateEmails) {
                return self::obfuscateEmails($m[8]);
            }

            return $m[0];
        }, $html);

        if ($out === null) {
            // PCRE limit hit: better the page as it was than no page
            Log::error('HtmlHardener: '.preg_last_error_msg());

            return $html;
        }

        if ($this->css !== []) {
            $rules = '';
            foreach ($this->css as $class => $css) {
                // :not(#x) adds ID-level specificity, so the class wins like the inline style did
                $rules .= '.'.$class.':not(#zs-x):not(#zs-x):not(#zs-x){'.str_replace('<', '\3C ', $css)."}\n";
            }
            $out = self::insertBefore($out, '</head>', '<style nonce="'.$this->nonce.'">'."\n".$rules.'</style>', true);
        }
        if ($this->css !== [] || $this->events !== []) {
            $out = self::insertBefore($out, '</body>', $this->binderScript(), false);
        }

        return $out;
    }

    private function rewriteAttributes(string $tag, string $raw): string
    {
        $needsAutocomplete = $this->autocompleteOff && ($tag === 'form' || $tag === 'input');
        if (! $needsAutocomplete && ! preg_match('/\b(style|on[a-z]+|href)\s*=/i', $raw)) {
            return $raw;
        }

        preg_match_all('~([^\s"\'>/=]+)(?:\s*=\s*("[^"]*"|\'[^\']*\'|[^\s"\'>]+))?~', $raw, $found, PREG_SET_ORDER);
        $selfClosing = (bool) preg_match('~/\s*$~', $raw);

        $list = [];          // [name, raw value or null]
        $styles = '';
        $handlers = [];
        $hrefHandler = null;
        foreach ($found as $a) {
            $name = $a[1];
            $value = $a[2] ?? null;
            $lname = strtolower($name);
            $decoded = $value === null ? '' : html_entity_decode(trim($value, '"\''), ENT_QUOTES | ENT_HTML5, 'UTF-8');

            if ($lname === 'style') {
                $styles .= trim($decoded, " \t\r\n;").';';

                continue;
            }
            if (str_starts_with($lname, 'on') && strlen($lname) > 2 && ctype_alpha(substr($lname, 2))) {
                if (trim($decoded) !== '') {
                    $handlers[] = [substr($lname, 2), $decoded, 0];
                }

                continue;
            }
            if ($lname === 'href' && preg_match('/^\s*javascript\s*:(.*)$/is', $decoded, $js)) {
                $code = trim($js[1]);
                // runs after any onclick, and only if that did not cancel (like following the link)
                $hrefHandler = preg_match('/^(void\s*\(?\s*0\s*\)?\s*;?|;)?$/i', $code) ? ['click', '', 1] : ['click', $code, 2];
                $list[] = ['href', '"#"'];

                continue;
            }
            if ($lname === 'href' && $this->obfuscateEmails && preg_match('/^\s*mailto:/i', $decoded)) {
                continue; // harvesters collect mailto: links first
            }
            $list[] = [$name, $value];
        }
        if ($hrefHandler !== null) {
            $handlers[] = $hrefHandler;
        }

        $styles = trim($styles, '; ');
        if ($styles !== '') {
            $class = 'zs'.substr(md5($styles), 0, 10);
            $this->css[$class] = $styles;
            $merged = false;
            foreach ($list as $i => $item) {
                if (strtolower($item[0]) === 'class' && $item[1] !== null) {
                    $current = html_entity_decode(trim($item[1], '"\''), ENT_QUOTES | ENT_HTML5, 'UTF-8');
                    $list[$i][1] = '"'.htmlspecialchars(trim($current.' '.$class), ENT_QUOTES, 'UTF-8').'"';
                    $merged = true;
                }
            }
            if (! $merged) {
                $list[] = ['class', '"'.$class.'"'];
            }
            $list[] = ['data-zs', '"'.$class.'"'];
        }
        if ($handlers !== []) {
            $id = ++$this->seq;
            $this->events[$id] = $handlers;
            $list[] = ['data-ze', '"'.$id.'"'];
        }
        if ($needsAutocomplete) {
            $has = false;
            $isPassword = false;
            foreach ($list as $item) {
                $n = strtolower($item[0]);
                $has = $has || $n === 'autocomplete';
                if ($n === 'type' && $item[1] !== null && strtolower(trim($item[1], '"\'')) === 'password') {
                    $isPassword = true;
                }
            }
            if (! $has && ($tag === 'form' || $isPassword)) {
                $list[] = ['autocomplete', '"off"'];
            }
        }

        $rebuilt = '';
        foreach ($list as $item) {
            $rebuilt .= ' '.$item[0].($item[1] === null ? '' : '='.$item[1]);
        }

        return $rebuilt.($selfClosing ? ' /' : '');
    }

    /** abc.def@nic.in -> abc[dot]def[at]nic[dot]in */
    public static function obfuscateEmails(string $text): string
    {
        if (! str_contains($text, '@')) {
            return $text;
        }

        return preg_replace_callback('/[A-Za-z0-9._%+-]+@[A-Za-z0-9-]+(?:\.[A-Za-z0-9-]+)+/', fn ($m) => str_replace(['@', '.'], ['[at]', '[dot]'], $m[0]), $text);
    }

    private static function insertBefore(string $html, string $marker, string $insert, bool $first): string
    {
        $pos = $first ? stripos($html, $marker) : strripos($html, $marker);
        if ($pos === false) {
            return $first ? $insert.$html : $html.$insert;
        }

        return substr($html, 0, $pos).$insert.substr($html, $pos);
    }

    private function binderScript(): string
    {
        $css = json_encode((object) $this->css, JSON_HEX_TAG | JSON_HEX_AMP | JSON_UNESCAPED_SLASHES);
        $events = [];
        foreach ($this->events as $id => $handlers) {
            $items = [];
            foreach ($handlers as [$event, $code, $mode]) {
                $code = str_ireplace('</script', '<\/script', $code);
                $items[] = '['.json_encode($event).',function(event){'."\n".$code."\n".'},'.$mode.']';
            }
            $events[] = json_encode((string) $id).':['.implode(',', $items).']';
        }
        $windowEvents = json_encode(self::WINDOW_EVENTS);

        return '<script nonce="'.$this->nonce.'">'."\n"
            .'(function(){var S='.$css.',E={'.implode(",\n", $events).'},W='.$windowEvents.';'."\n"
            .'function each(s,f){var l=document.querySelectorAll(s);for(var i=0;i<l.length;i++)f(l[i]);}'
            // style: re-apply as a real inline style (CSSOM is allowed by CSP), keep what scripts already set
            .'each("[data-zs]",function(el){var c=el.getAttribute("data-zs");el.removeAttribute("data-zs");if(!S.hasOwnProperty(c))return;'
            .'var t=document.createElement("div").style;t.cssText=S[c];'
            .'for(var i=0;i<t.length;i++){var p=t[i];if(!el.style.getPropertyValue(p))el.style.setProperty(p,t.getPropertyValue(p),t.getPropertyPriority(p));}'
            .'el.classList.remove(c);});'
            // handlers: same semantics as on...="": "this" is the element, "return false" cancels the default action
            .'each("[data-ze]",function(el){var h=E[el.getAttribute("data-ze")];el.removeAttribute("data-ze");if(!h)return;'
            .'h.forEach(function(x){var t=(el===document.body&&W.indexOf(x[0])>=0)?window:el;'
            .'t.addEventListener(x[0],function(e){if(x[2]===2){if(!e.defaultPrevented){x[1].call(el,e);e.preventDefault();}return;}'
            .'var r=x[1].call(el,e);if(r===false||x[2])e.preventDefault();});});});'
            .'})();'."\n".'</script>';
    }
}
