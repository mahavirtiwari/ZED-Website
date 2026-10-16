<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once dirname(FCPATH).'/application/third_party/zed/Zed_captcha.php';

/**
 * Captcha image for the CMS login and forgot-password forms (no login needed).
 * Each request makes a new code for that form: /cms/captcha/image/login
 */
class Captcha extends MY_Controller
{
	public function image($form = 'login')
	{
		if ( ! in_array($form, array('login', 'forgot_password'), TRUE))
		{
			show_404();
		}
		$this->output
			->set_content_type('png')
			->set_header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0')
			->set_output(Zed_captcha::png(Zed_captcha::generate($form)));
	}
}
