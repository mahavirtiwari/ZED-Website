@extends('cms.layouts.app', ['pageTitle' => 'Profile'])

@section('breadcrumb')<li class="breadcrumb-item active" aria-current="page">Profile and password</li>@endsection

@section('content')
<h1 class="h3 mb-4">Profile and password</h1>
<div class="row g-4">
    <div class="col-lg-6">
        <form method="post" action="{{ route('cms.profile.update') }}" class="card card-body">
            @csrf
            <h2 class="h5">Your details</h2>
            <div class="row g-3">
                <div class="col-sm-6">
                    <label class="form-label" for="first_name">First name</label>
                    <input class="form-control" id="first_name" name="first_name" value="{{ old('first_name', $user->first_name) }}" required maxlength="50">
                </div>
                <div class="col-sm-6">
                    <label class="form-label" for="last_name">Last name</label>
                    <input class="form-control" id="last_name" name="last_name" value="{{ old('last_name', $user->last_name) }}" maxlength="50">
                </div>
                <div class="col-sm-6">
                    <label class="form-label" for="phone">Phone</label>
                    <input class="form-control" id="phone" name="phone" value="{{ old('phone', $user->phone) }}" maxlength="20">
                </div>
                <div class="col-sm-6">
                    <label class="form-label" for="company">Organisation</label>
                    <input class="form-control" id="company" name="company" value="{{ old('company', $user->company) }}" maxlength="100">
                </div>
                <div class="col-12">
                    <p class="form-text mb-0">Signed in as {{ $user->email }} ({{ \App\Http\Controllers\Cms\UserController::ROLE_LABELS[$user->role()] ?? '' }}).</p>
                </div>
            </div>
            <div class="mt-3"><button class="btn btn-primary" type="submit">Save details</button></div>
        </form>
    </div>
    <div class="col-lg-6">
        <form method="post" action="{{ route('cms.profile.password') }}" class="card card-body" autocomplete="off">
            @csrf
            <h2 class="h5">Change password</h2>
            <div class="mb-3">
                <label class="form-label" for="current_password">Current password</label>
                <input class="form-control" type="password" id="current_password" name="current_password" required autocomplete="off">
            </div>
            <div class="mb-3">
                <label class="form-label" for="password">New password</label>
                <input class="form-control" type="password" id="password" name="password" required autocomplete="off" aria-describedby="new-password-help">
                <div id="new-password-help" class="form-text">At least 10 characters, with upper and lower case letters, a number and a symbol.</div>
            </div>
            <div class="mb-3">
                <label class="form-label" for="password_confirmation">Repeat the new password</label>
                <input class="form-control" type="password" id="password_confirmation" name="password_confirmation" required autocomplete="off">
            </div>
            <div><button class="btn btn-primary" type="submit">Change password</button></div>
        </form>
    </div>
</div>
@endsection
