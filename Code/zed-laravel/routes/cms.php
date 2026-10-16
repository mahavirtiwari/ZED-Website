<?php

use App\Http\Controllers\Cms\AdminController;
use App\Http\Controllers\Cms\AuthController;
use App\Http\Controllers\Cms\ResourceController;
use App\Http\Controllers\Cms\UserController;
use Illuminate\Support\Facades\Route;

/*
 * CMS: /cms/... (web + harden:cms + pwt middleware, see bootstrap/app.php)
 */

// old CodeIgniter address of the login page
Route::redirect('auth/login', '/cms/login', 301);

Route::middleware('guest')->group(function () {
    Route::get('login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('login', [AuthController::class, 'login'])->middleware('throttle:20,1');
    Route::get('forgot-password', [AuthController::class, 'showForgot'])->name('password.request');
    Route::post('forgot-password', [AuthController::class, 'sendResetLink'])->middleware('throttle:5,1')->name('password.email');
    Route::get('reset-password/{token}', [AuthController::class, 'showReset'])->name('password.reset');
    Route::post('reset-password', [AuthController::class, 'reset'])->middleware('throttle:10,1')->name('password.update');
});

Route::middleware(['auth', 'cms.user'])->group(function () {
    Route::post('logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');

    Route::get('profile', [AuthController::class, 'profile'])->name('profile');
    Route::post('profile', [AuthController::class, 'updateProfile'])->name('profile.update');
    Route::post('profile/password', [AuthController::class, 'updatePassword'])->name('profile.password');

    Route::get('settings/{group}', [AdminController::class, 'settings'])->name('settings');
    Route::post('settings/{group}', [AdminController::class, 'saveSettings']);

    Route::get('news-blocks', [AdminController::class, 'newsBlocks'])->name('news-blocks');
    Route::post('news-blocks', [AdminController::class, 'saveNewsBlocks']);

    Route::middleware('cms.user:superadmin,admin')->group(function () {
        Route::get('contacts', [AdminController::class, 'contacts'])->name('contacts.index');
        Route::get('contacts/{id}', [AdminController::class, 'contact'])->whereNumber('id')->name('contacts.show');
        Route::post('contacts/delete', [AdminController::class, 'deleteContacts'])->name('contacts.delete');
    });

    Route::middleware('cms.user:superadmin')->group(function () {
        Route::get('users', [UserController::class, 'index'])->name('users.index');
        Route::get('users/create', [UserController::class, 'create'])->name('users.create');
        Route::post('users', [UserController::class, 'store'])->name('users.store');
        Route::get('users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
        Route::put('users/{user}', [UserController::class, 'update'])->name('users.update');
    });

    Route::post('editor/upload', [AdminController::class, 'editorUpload'])->middleware('throttle:60,1')->name('editor.upload');

    // generic modules from config/cms.php
    Route::get('{resource}', [ResourceController::class, 'index'])->name('resource.index');
    Route::get('{resource}/create', [ResourceController::class, 'create'])->name('resource.create');
    Route::post('{resource}', [ResourceController::class, 'store'])->name('resource.store');
    Route::post('{resource}/delete', [ResourceController::class, 'destroyMany'])->name('resource.delete');
    Route::post('{resource}/upload-many', [ResourceController::class, 'uploadMany'])->name('resource.upload-many');
    Route::get('{resource}/{id}/edit', [ResourceController::class, 'edit'])->whereNumber('id')->name('resource.edit');
    Route::put('{resource}/{id}', [ResourceController::class, 'update'])->whereNumber('id')->name('resource.update');
});
