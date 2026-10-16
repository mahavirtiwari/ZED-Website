@extends('cms.layouts.auth')

@section('heading', 'Forgot your password?')

@section('content')
<p class="text-secondary">Enter your e-mail address and we will send you a link to choose a new password.</p>
<form method="post" action="{{ route('cms.password.email') }}" autocomplete="off" novalidate>
    @csrf
    <div class="mb-3">
        <label for="email" class="form-label">E-mail address</label>
        <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" required autofocus>
    </div>
    @if ($captcha)
        @include('cms.partials.captcha', ['form' => 'forgot_password'])
    @endif
    <button type="submit" class="btn btn-primary w-100">Send reset link</button>
    <p class="mt-3 mb-0 text-center"><a href="{{ route('cms.login') }}">Back to sign in</a></p>
</form>
@endsection
