<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| Hooks
| -------------------------------------------------------------------------
| This file lets you define "hooks" to extend CI without hacking the core
| files.  Please see the user guide for info:
|
|	https://codeigniter.com/user_guide/general/hooks.html
|
*/

// Password fields arrive RSA-encrypted (assets/js/zed-pwt.js); put the clear
// value back into $_POST before the controller method runs.
$hook['post_controller_constructor'][] = array(
	'function' => 'zed_decrypt_posted_passwords',
	'filename' => 'Password_transport_hook.php',
	'filepath' => 'hooks',
);
