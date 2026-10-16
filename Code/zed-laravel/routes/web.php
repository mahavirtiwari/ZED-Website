<?php

use App\Http\Controllers\CaptchaController;
use Illuminate\Support\Facades\Route;

// captcha images (CMS sign-in and website Contact Us form)
Route::get('captcha/{form}.png', CaptchaController::class)->where('form', '[a-z_]+')->name('captcha');

// CMS (/cms/...) before the public site, whose last route is the /{slug} catch-all
Route::middleware(['harden:cms', 'pwt'])->prefix('cms')->name('cms.')->group(base_path('routes/cms.php'));

// public site
require __DIR__.'/site.php';
