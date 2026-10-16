<?php

namespace App\Support;

use Illuminate\Contracts\Session\Session;

/**
 * Image captcha (security audit item 7, CWE-307): CMS login, forgot password
 * and the website Contact Us form.
 *
 * - 6 characters, upper-case letters and digits without look-alikes (0/O, 1/I);
 *   answers are not case-sensitive.
 * - The code lives only in the session, per form, and is checked on the server.
 * - Single use: every check discards the code, right or wrong; it also expires.
 * - Drawn with GD's built-in font (no font file needed), each character rotated
 *   and scaled, over noise lines and dots.
 */
class Captcha
{
    public const CHARS = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';

    public const LENGTH = 6;

    public const TTL = 600;

    /** Forms that may show a captcha (the image URL takes one of these). */
    public const FORMS = ['login', 'forgot_password', 'contact'];

    public function __construct(private Session $session) {}

    public function generate(string $form): string
    {
        $code = '';
        $max = strlen(self::CHARS) - 1;
        for ($i = 0; $i < self::LENGTH; $i++) {
            $code .= self::CHARS[random_int(0, $max)];
        }
        $this->session->put('captcha.'.$form, ['code' => $code, 'time' => time()]);

        return $code;
    }

    public function check(string $form, ?string $answer): bool
    {
        $entry = $this->session->pull('captcha.'.$form);
        if (empty($entry['code']) || time() - $entry['time'] > self::TTL) {
            return false;
        }
        $answer = strtoupper(preg_replace('/\s+/', '', (string) $answer));

        return strlen($answer) === self::LENGTH && hash_equals($entry['code'], $answer);
    }

    /** PNG image of $code. */
    public static function png(string $code): string
    {
        $width = 190;
        $height = 60;
        $img = imagecreatetruecolor($width, $height);
        $bg = imagecolorallocate($img, 244, 247, 250);
        imagefilledrectangle($img, 0, 0, $width, $height, $bg);

        for ($i = 0; $i < 8; $i++) {
            $c = imagecolorallocate($img, random_int(150, 210), random_int(150, 210), random_int(150, 210));
            imagesetthickness($img, random_int(1, 2));
            imageline($img, random_int(0, $width), random_int(0, $height), random_int(0, $width), random_int(0, $height), $c);
        }

        $x = 10;
        for ($i = 0, $n = strlen($code); $i < $n; $i++) {
            $tile = imagecreatetruecolor(14, 20);
            $tileBg = imagecolorallocate($tile, 244, 247, 250);
            imagefilledrectangle($tile, 0, 0, 14, 20, $tileBg);
            $fg = imagecolorallocate($tile, random_int(10, 90), random_int(30, 100), random_int(60, 130));
            imagestring($tile, 5, 3, 2, $code[$i], $fg);
            $rotated = imagerotate($tile, random_int(-22, 22), $tileBg);
            $w = imagesx($rotated);
            $h = imagesy($rotated);
            $scale = random_int(185, 215) / 100;
            $dw = (int) round($w * $scale);
            $dh = (int) round($h * $scale);
            imagecopyresampled($img, $rotated, $x, random_int(0, max(0, $height - $dh)), 0, 0, $dw, $dh, $w, $h);
            imagedestroy($tile);
            imagedestroy($rotated);
            $x += random_int(25, 29);
        }

        for ($i = 0; $i < 3; $i++) {
            $c = imagecolorallocate($img, random_int(60, 140), random_int(60, 140), random_int(60, 140));
            imagesetthickness($img, 1);
            imageline($img, 0, random_int(0, $height), $width, random_int(0, $height), $c);
        }
        for ($i = 0; $i < 350; $i++) {
            $c = imagecolorallocate($img, random_int(100, 220), random_int(100, 220), random_int(100, 220));
            imagesetpixel($img, random_int(0, $width - 1), random_int(0, $height - 1), $c);
        }

        ob_start();
        imagepng($img);
        imagedestroy($img);

        return (string) ob_get_clean();
    }
}
