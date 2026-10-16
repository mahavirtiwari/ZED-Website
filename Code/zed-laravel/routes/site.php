<?php

use App\Http\Controllers\SiteController;
use Illuminate\Support\Facades\Route;

/*
 * Public website (harden:public = CSP rewrite + e-mail obfuscation). Same URLs as
 * the old site. The /{slug} catch-all (pages and news) must stay last.
 */
Route::get('sitemap', [SiteController::class, 'sitemap']);
Route::get('sitemap.xml', [SiteController::class, 'sitemap']);

Route::middleware('harden:public')->group(function () {
    Route::get('/', [SiteController::class, 'home'])->name('home');
    Route::match(['get', 'post'], 'contact-us', [SiteController::class, 'contact'])->middleware('throttle:30,1')->name('contact');
    Route::get('video', [SiteController::class, 'video']);
    Route::get('gallery', [SiteController::class, 'gallery'])->name('gallery');
    Route::get('gallery/{year}/{month}', [SiteController::class, 'galleryMonth'])->whereNumber('year')->where('month', '[A-Za-z]+');
    Route::match(['get', 'post'], 'search', [SiteController::class, 'search'])->middleware('throttle:60,1');
    Route::get('news', [SiteController::class, 'news']);

    // AJAX used by the parameters, maturity and guidance pages (old URLs)
    Route::post('home/disciplineparameter', [SiteController::class, 'disciplines']);
    Route::post('home/zedmatrixparameter', [SiteController::class, 'maturity']);
    Route::get('home/zedguidance', [SiteController::class, 'guidance']);

    // old CodeIgniter addresses
    Route::redirect('home', '/', 301);
    Route::get('home/page/{slug}', fn (string $slug) => redirect('/'.$slug, 301))->where('slug', '[A-Za-z0-9-]+');
    Route::get('home/posts/{slug}', fn (string $slug) => redirect('/'.$slug, 301))->where('slug', '[A-Za-z0-9-]+');

    // pages and news; POST for the verify-zed-certificate form
    Route::match(['get', 'post'], '{slug}', [SiteController::class, 'slug'])->where('slug', '[A-Za-z0-9-]+')->name('slug');
});
