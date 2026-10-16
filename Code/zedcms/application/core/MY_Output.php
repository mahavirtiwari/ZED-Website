<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once APPPATH.'third_party/zed/Zed_security.php';

/**
 * Public site output: security headers on every response, and HTML pages
 * rewritten for the nonce-based Content-Security-Policy with e-mail addresses
 * obfuscated (see third_party/zed/Zed_security.php).
 */
class MY_Output extends CI_Output
{
	public function __construct()
	{
		parent::__construct();
		Zed_security::send_headers(array(
			// Zoho Desk contact form, YouTube / dashboard / map embeds
			'script-src'  => array('https://js.zohostatic.in'),
			'style-src'   => array('https://fonts.googleapis.com', 'https://js.zohostatic.in'),
			'font-src'    => array('https://fonts.gstatic.com'),
			'img-src'     => array('https:'),
			'connect-src' => array('https://desk.zoho.in'),
			'frame-src'   => array('https://www.youtube.com', 'https://www.youtube-nocookie.com', 'https://msmekawach.org.in', 'https://www.google.com', 'https://desk.zoho.in'),
			'form-action' => array('https://desk.zoho.in'),
		));
	}

	public function _display($output = '')
	{
		if ($output === '')
		{
			$output = $this->final_output;
		}
		if (stripos((string) $this->get_content_type(), 'html') !== FALSE)
		{
			$output = Zed_security::harden_html($output, array('obfuscate_emails' => TRUE));
		}
		parent::_display($output);
	}
}
