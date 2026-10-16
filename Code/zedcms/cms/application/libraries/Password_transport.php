<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Keeps passwords out of the request body in clear text (audit finding
 * "Cleartext Submission of Password", CWE-319).
 *
 * Each session gets its own RSA-2048 key pair. The public key and a nonce are
 * printed as <meta> tags (pwt_meta() in global_helper); assets/js/zed-pwt.js
 * encrypts every password field as RSA-OAEP(SHA-1) of "nonce:password" and
 * posts "pwt1:<base64>" instead. The post_controller_constructor hook calls
 * decrypt_post(), which puts the clear password back into $_POST before any
 * controller code runs, so Ion Auth and form validation need no change.
 *
 * The nonce is single use (rotated after every request that carried an
 * encrypted field), so a captured value cannot be replayed. Passwords are
 * still stored as bcrypt hashes by Ion Auth. HTTPS remains required.
 */
class Password_transport
{
	const PREFIX = 'pwt1:';

	protected $CI;

	public function __construct()
	{
		$this->CI =& get_instance();
	}

	/** OpenSSL options; the bundled config file makes key generation work on Windows too */
	protected function openssl_options()
	{
		return array(
			'config'           => APPPATH.'config/openssl.cnf',
			'private_key_bits' => 2048,
			'private_key_type' => OPENSSL_KEYTYPE_RSA,
		);
	}

	/**
	 * Base64 SPKI (DER) public key for this session, created on first use.
	 * @return string '' when OpenSSL cannot make a key (the form then posts as before)
	 */
	public function public_key()
	{
		$public = $this->CI->session->userdata('pwt_public');
		if ( ! $public OR ! $this->CI->session->userdata('pwt_private'))
		{
			$key = openssl_pkey_new($this->openssl_options());
			if ($key === FALSE OR ! openssl_pkey_export($key, $private, NULL, $this->openssl_options()))
			{
				log_message('error', 'Password_transport: RSA key generation failed: '.openssl_error_string());
				return '';
			}
			$details = openssl_pkey_get_details($key);
			$public = $details['key'];
			$this->CI->session->set_userdata(array('pwt_private' => $private, 'pwt_public' => $public));
		}
		return preg_replace('/-----[^-]+-----|\s+/', '', $public);
	}

	/** Nonce the browser must put in front of each password */
	public function nonce()
	{
		$nonce = $this->CI->session->userdata('pwt_nonce');
		if ( ! $nonce)
		{
			$nonce = bin2hex(random_bytes(16));
			$this->CI->session->set_userdata('pwt_nonce', $nonce);
		}
		return $nonce;
	}

	/**
	 * Replace every "pwt1:..." value in $_POST with the decrypted password.
	 * A value that does not decrypt, or carries the wrong nonce, becomes ''
	 * so it can never match a real password.
	 */
	public function decrypt_post()
	{
		if (empty($_POST))
		{
			return;
		}
		$private = $this->CI->session->userdata('pwt_private');
		$nonce = $this->CI->session->userdata('pwt_nonce');
		$seen = FALSE;

		foreach ($_POST as $field => $value)
		{
			if ( ! is_string($value) OR strncmp($value, self::PREFIX, strlen(self::PREFIX)) !== 0)
			{
				continue;
			}
			$seen = TRUE;
			$_POST[$field] = '';
			$cipher = base64_decode(substr($value, strlen(self::PREFIX)), TRUE);
			if ( ! $private OR ! $nonce OR $cipher === FALSE
				OR ! openssl_private_decrypt($cipher, $plain, $private, OPENSSL_PKCS1_OAEP_PADDING))
			{
				continue;
			}
			$sep = strpos($plain, ':');
			if ($sep !== FALSE && hash_equals($nonce, substr($plain, 0, $sep)))
			{
				$_POST[$field] = substr($plain, $sep + 1);
			}
		}

		if ($seen)
		{
			$this->CI->session->set_userdata('pwt_nonce', bin2hex(random_bytes(16)));
		}
	}
}
