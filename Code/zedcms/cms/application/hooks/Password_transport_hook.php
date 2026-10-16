<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * post_controller_constructor hook: decrypt password fields posted by
 * assets/js/zed-pwt.js before the controller method reads them.
 */
function zed_decrypt_posted_passwords()
{
	if (empty($_POST))
	{
		return;
	}
	$CI =& get_instance();
	$CI->load->library('password_transport');
	$CI->password_transport->decrypt_post();
}
