<?php

namespace App\Http\Controllers\Cms;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Support\Captcha;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password as PasswordRule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

/**
 * CMS sign-in, sign-out, forgot / reset password, own profile and password.
 * Passwords arrive RSA-encrypted (DecryptPasswords middleware); the captcha
 * (CMS > Security) is checked before the password is tried; failed sign-ins
 * are throttled per e-mail address and IP.
 */
class AuthController extends Controller
{
    private const MAX_ATTEMPTS = 5;

    private const LOCKOUT_SECONDS = 900;

    public function showLogin(): View
    {
        return view('cms.auth.login', ['captcha' => captcha_enabled('captcha_cms_login')]);
    }

    public function login(Request $request, Captcha $captcha): RedirectResponse
    {
        try {
            return $this->attemptLogin($request, $captcha);
        } catch (ValidationException $e) {
            // explicit target: "back" would be the captcha image, the last GET of the session
            throw $e->redirectTo(route('cms.login'));
        }
    }

    private function attemptLogin(Request $request, Captcha $captcha): RedirectResponse
    {
        $useCaptcha = captcha_enabled('captcha_cms_login');
        $request->validate([
            'email' => ['required', 'email', 'max:100'],
            'password' => ['required', 'string', 'max:200'],
            'captcha' => $useCaptcha ? ['required', 'string'] : ['nullable'],
        ], ['password.required' => 'Please enter your password.']);

        $key = 'cms-login:'.Str::lower($request->input('email')).'|'.$request->ip();
        if (RateLimiter::tooManyAttempts($key, self::MAX_ATTEMPTS)) {
            $minutes = (int) ceil(RateLimiter::availableIn($key) / 60);
            throw ValidationException::withMessages(['email' => "Too many failed sign-ins. Please try again in {$minutes} minutes."]);
        }
        if ($useCaptcha && ! $captcha->check('login', $request->input('captcha'))) {
            throw ValidationException::withMessages(['captcha' => 'The security code was not correct. Please type the new code.']);
        }

        $credentials = ['email' => $request->input('email'), 'password' => $request->input('password')];
        if (! Auth::attempt($credentials, $request->boolean('remember')) || ! Auth::user()->canUseCms()) {
            Auth::logout();
            RateLimiter::hit($key, self::LOCKOUT_SECONDS);
            throw ValidationException::withMessages(['email' => 'The e-mail address or password is not correct.']);
        }

        RateLimiter::clear($key);
        $request->session()->regenerate();
        Auth::user()->forceFill(['last_login' => time(), 'ip_address' => (string) $request->ip()])->save();

        return redirect()->intended(route('cms.dashboard'));
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('cms.login')->with('status', 'You have signed out.');
    }

    public function showForgot(): View
    {
        return view('cms.auth.forgot', ['captcha' => captcha_enabled('captcha_cms_login')]);
    }

    public function sendResetLink(Request $request, Captcha $captcha): RedirectResponse
    {
        try {
            return $this->requestResetLink($request, $captcha);
        } catch (ValidationException $e) {
            throw $e->redirectTo(route('cms.password.request'));
        }
    }

    private function requestResetLink(Request $request, Captcha $captcha): RedirectResponse
    {
        $useCaptcha = captcha_enabled('captcha_cms_login');
        $request->validate([
            'email' => ['required', 'email'],
            'captcha' => $useCaptcha ? ['required', 'string'] : ['nullable'],
        ]);
        if ($useCaptcha && ! $captcha->check('forgot_password', $request->input('captcha'))) {
            throw ValidationException::withMessages(['captcha' => 'The security code was not correct. Please type the new code.']);
        }
        Password::sendResetLink($request->only('email'));

        // the same answer whether or not the address exists (no account discovery)
        return redirect()->route('cms.login')->with('status', 'If that e-mail address belongs to a CMS user, a password reset link has been sent to it.');
    }

    public function showReset(Request $request, string $token): View
    {
        return view('cms.auth.reset', ['token' => $token, 'email' => (string) $request->query('email')]);
    }

    public function reset(Request $request): RedirectResponse
    {
        $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', self::passwordRule()],
        ]);
        $status = Password::reset($request->only('email', 'password', 'password_confirmation', 'token'), function (User $user, string $password) {
            $user->forceFill(['password' => Hash::make($password), 'remember_token' => Str::random(60)])->save();
            event(new PasswordReset($user));
        });

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('cms.login')->with('status', 'Your password has been changed. Please sign in.')
            : back()->withErrors(['email' => __($status)]);
    }

    public function profile(): View
    {
        return view('cms.auth.profile', ['user' => Auth::user()]);
    }

    public function updateProfile(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'first_name' => ['required', 'string', 'max:50'],
            'last_name' => ['nullable', 'string', 'max:50'],
            'phone' => ['nullable', 'string', 'max:20'],
            'company' => ['nullable', 'string', 'max:100'],
        ]);
        Auth::user()->forceFill($data)->save();

        return back()->with('status', 'Profile updated.');
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', self::passwordRule()],
        ]);
        Auth::user()->forceFill(['password' => Hash::make($request->input('password'))])->save();
        $request->session()->regenerate();

        return back()->with('status', 'Password changed.');
    }

    public static function passwordRule(): PasswordRule
    {
        return PasswordRule::min(10)->letters()->mixedCase()->numbers()->symbols();
    }
}
