<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Image captcha shared by the public site (Contact Us) and the CMS (login,
 * forgot password).
 *
 * - 6 characters, upper-case letters and digits without look-alikes (0/O, 1/I);
 *   the answer is not case-sensitive.
 * - The code lives only in the session, per form, and is checked on the server.
 * - Single use: every check() discards the code, right or wrong, so it cannot
 *   be replayed or guessed repeatedly; it also expires after TTL seconds.
 * - Drawn with GD's built-in font (no TTF file needed), each character rotated
 *   and scaled, over noise lines and dots.
 */
class Zed_captcha
{
	const CHARS  = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
	const LENGTH = 6;
	const TTL    = 600;

	/** Form names that may have a captcha (the image URL takes one of these). */
	public static $forms = array('login', 'forgot_password', 'contact');

	/**
	 * Make a new code for $form and keep it in the session.
	 * @return string|FALSE  the code, or FALSE for an unknown form
	 */
	public static function generate($form)
	{
		if ( ! in_array($form, self::$forms, TRUE))
		{
			return FALSE;
		}
		$code = '';
		$max = strlen(self::CHARS) - 1;
		for ($i = 0; $i < self::LENGTH; $i++)
		{
			$code .= self::CHARS[random_int(0, $max)];
		}
		$_SESSION['zed_captcha'][$form] = array('code' => $code, 'time' => time());
		return $code;
	}

	/**
	 * Check the user's answer for $form. The stored code is discarded either way.
	 * @return bool
	 */
	public static function check($form, $answer)
	{
		$entry = isset($_SESSION['zed_captcha'][$form]) ? $_SESSION['zed_captcha'][$form] : NULL;
		unset($_SESSION['zed_captcha'][$form]);
		if (empty($entry['code']) OR time() - $entry['time'] > self::TTL)
		{
			return FALSE;
		}
		$answer = strtoupper(preg_replace('/\s+/', '', (string) $answer));
		return strlen($answer) === self::LENGTH && hash_equals($entry['code'], $answer);
	}

	/**
	 * PNG image of $code.
	 * @return string PNG bytes
	 */
	public static function png($code)
	{
		$width = 190;
		$height = 60;
		$img = imagecreatetruecolor($width, $height);
		$bg = imagecolorallocate($img, 244, 247, 250);
		imagefilledrectangle($img, 0, 0, $width, $height, $bg);

		// background noise
		for ($i = 0; $i < 8; $i++)
		{
			$c = imagecolorallocate($img, random_int(150, 210), random_int(150, 210), random_int(150, 210));
			imagesetthickness($img, random_int(1, 2));
			imageline($img, random_int(0, $width), random_int(0, $height), random_int(0, $width), random_int(0, $height), $c);
		}

		// characters: draw each with the built-in font on a small tile, rotate, scale up
		$x = 10;
		$length = strlen($code);
		for ($i = 0; $i < $length; $i++)
		{
			$tile = imagecreatetruecolor(14, 20);
			$tile_bg = imagecolorallocate($tile, 244, 247, 250);
			imagefilledrectangle($tile, 0, 0, 14, 20, $tile_bg);
			$fg = imagecolorallocate($tile, random_int(10, 90), random_int(30, 100), random_int(60, 130));
			imagestring($tile, 5, 3, 2, $code[$i], $fg);
			$rotated = imagerotate($tile, random_int(-22, 22), $tile_bg);
			$w = imagesx($rotated);
			$h = imagesy($rotated);
			$scale = random_int(185, 215) / 100;
			$dw = (int) round($w * $scale);
			$dh = (int) round($h * $scale);
			$y = random_int(0, max(0, $height - $dh));
			imagecopyresampled($img, $rotated, $x, $y, 0, 0, $dw, $dh, $w, $h);
			imagedestroy($tile);
			imagedestroy($rotated);
			$x += random_int(25, 29);
		}

		// foreground noise
		for ($i = 0; $i < 3; $i++)
		{
			$c = imagecolorallocate($img, random_int(60, 140), random_int(60, 140), random_int(60, 140));
			imagesetthickness($img, 1);
			imageline($img, 0, random_int(0, $height), $width, random_int(0, $height), $c);
		}
		for ($i = 0; $i < 350; $i++)
		{
			$c = imagecolorallocate($img, random_int(100, 220), random_int(100, 220), random_int(100, 220));
			imagesetpixel($img, random_int(0, $width - 1), random_int(0, $height - 1), $c);
		}

		ob_start();
		imagepng($img);
		imagedestroy($img);
		return ob_get_clean();
	}
}
