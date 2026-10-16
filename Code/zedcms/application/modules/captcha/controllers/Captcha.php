<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once APPPATH.'third_party/zed/Zed_captcha.php';

/**
 * Captcha image for the site's own Contact Us form.
 * Each request makes a new code: /captcha/image/contact
 */
class Captcha extends MY_Controller
{
	public function image($form = 'contact')
	{
		if ($form !== 'contact')
		{
			show_404();
		}
		$this->output
			->set_content_type('png')
			->set_header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0')
			->set_output(Zed_captcha::png(Zed_captcha::generate($form)));
	}
}
