@extends('cms.layouts.auth')

@section('heading', 'Sign in to the ZED CMS')

@section('content')
<form method="post" action="{{ route('cms.login') }}" autocomplete="off" novalidate>
    @csrf
    <div class="mb-3">
        <label for="email" class="form-label">E-mail address</label>
        <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" required autofocus autocomplete="off" maxlength="100">
    </div>
    <div class="mb-3">
        <label for="password" class="form-label">Password</label>
        <input type="password" name="password" id="password" class="form-control" required autocomplete="off">
    </div>
    @if ($captcha)
        @include('cms.partials.captcha', ['form' => 'login'])
    @endif
    <div class="form-check mb-3">
        <input class="form-check-input" type="checkbox" name="remember" value="1" id="remember">
        <label class="form-check-label" for="remember">Keep me signed in on this computer</label>
    </div>
    <button type="submit" class="btn btn-primary w-100">Sign in</button>
    <p class="mt-3 mb-0 text-center"><a href="{{ route('cms.password.request') }}">Forgot your password?</a></p>
</form>
@endsection
