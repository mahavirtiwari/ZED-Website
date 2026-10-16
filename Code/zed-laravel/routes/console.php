<?php

use App\Http\Controllers\Cms\AuthController;
use App\Models\Group;
use App\Models\User;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/*
 * php artisan zed:cms-admin someone@example.com
 * Creates a CMS superadmin, or resets that user's password and makes them an
 * active superadmin. The password is asked for (not shown, not stored in the
 * shell history). Used on a new server (deploy\windows\Deploy-ZedSite.ps1).
 */
Artisan::command('zed:cms-admin {email} {--name=Administrator}', function (string $email) {
    if (Validator::make(['email' => $email], ['email' => ['required', 'email', 'max:100']])->fails()) {
        $this->error('Not a valid e-mail address.');

        return 1;
    }
    $password = (string) $this->secret('New password (at least 10 characters: upper and lower case letters, a digit and a symbol)');
    if ($password !== (string) $this->secret('Repeat the password')) {
        $this->error('The passwords do not match.');

        return 1;
    }
    $check = Validator::make(['password' => $password], ['password' => ['required', AuthController::passwordRule()]]);
    if ($check->fails()) {
        $this->error(implode(' ', $check->errors()->all()));

        return 1;
    }

    $user = User::query()->where('email', $email)->first() ?? new User;
    $isNew = ! $user->exists;
    $user->forceFill([
        'email' => $email,
        'password' => Hash::make($password),
        'active' => true,
    ]);
    if ($isNew) {
        $user->forceFill([
            'username' => strstr($email, '@', true),
            'first_name' => $this->option('name'),
            'last_name' => '',
            'company' => '',
            'phone' => '',
            'ip_address' => '127.0.0.1',
            'created_on' => time(),
        ]);
    }
    $user->save();
    $group = Group::query()->firstOrCreate(['name' => 'superadmin'], ['description' => 'Super Admin']);
    $user->groups()->syncWithoutDetaching([$group->id]);

    $this->info(($isNew ? 'Created' : 'Updated').' CMS superadmin '.$email.'. Sign in at '.url('/cms').'.');

    return 0;
})->purpose('Create a CMS superadmin or reset its password');
