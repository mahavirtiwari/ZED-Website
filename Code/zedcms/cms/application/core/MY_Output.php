<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// shared with the public site, which sits one level above cms/
require_once dirname(FCPATH).'/application/third_party/zed/Zed_security.php';

/**
 * CMS output: security headers (never cached) on every response, and HTML
 * pages rewritten for the nonce-based Content-Security-Policy, with
 * autocomplete turned off on forms and password fields.
 */
class MY_Output extends CI_Output
{
	public function __construct()
	{
		parent::__construct();
		Zed_security::send_headers(array(
			'style-src' => array('https://fonts.googleapis.com'),
			'font-src'  => array('https://fonts.gstatic.com'),
			// cropper previews and editor images
			'img-src'   => array('blob:', 'https:'),
			'frame-src' => array('https://www.youtube.com', 'https://www.youtube-nocookie.com'),
		), TRUE);
	}

	public function _display($output = '')
	{
		if ($output === '')
		{
			$output = $this->final_output;
		}
		if (stripos((string) $this->get_content_type(), 'html') !== FALSE)
		{
			$output = Zed_security::harden_html($output, array('autocomplete_off' => TRUE));
		}
		parent::_display($output);
	}
}
