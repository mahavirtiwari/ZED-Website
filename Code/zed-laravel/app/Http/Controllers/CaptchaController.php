<?php

namespace App\Http\Controllers;

use App\Support\Captcha;
use Illuminate\Http\Response;

/** Captcha image: /captcha/{form}.png — each request makes a new code. */
class CaptchaController extends Controller
{
    public function __invoke(Captcha $captcha, string $form): Response
    {
        abort_unless(in_array($form, Captcha::FORMS, true), 404);

        return response(Captcha::png($captcha->generate($form)), 200, [
            'Content-Type' => 'image/png',
            'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
        ]);
    }
}
