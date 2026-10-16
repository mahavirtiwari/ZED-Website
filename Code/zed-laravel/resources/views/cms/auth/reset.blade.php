@extends('cms.layouts.auth')

@section('heading', 'Choose a new password')

@section('content')
<form method="post" action="{{ route('cms.password.update') }}" autocomplete="off" novalidate>
    @csrf
    <input type="hidden" name="token" value="{{ $token }}">
    <div class="mb-3">
        <label for="email" class="form-label">E-mail address</label>
        <input type="email" name="email" id="email" class="form-control" value="{{ old('email', $email) }}" required>
    </div>
    <div class="mb-3">
        <label for="password" class="form-label">New password</label>
        <input type="password" name="password" id="password" class="form-control" required autocomplete="off" aria-describedby="password-help">
        <div id="password-help" class="form-text">At least 10 characters, with upper and lower case letters, a number and a symbol.</div>
    </div>
    <div class="mb-3">
        <label for="password_confirmation" class="form-label">Repeat the new password</label>
        <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" required autocomplete="off">
    </div>
    <button type="submit" class="btn btn-primary w-100">Save password</button>
</form>
@endsection
