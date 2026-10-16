@php($editing = $user->exists)
@extends('cms.layouts.app', ['pageTitle' => $editing ? 'Edit user' : 'Add user'])

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('cms.users.index') }}">Users</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{ $editing ? 'Edit' : 'Add' }}</li>
@endsection

@section('content')
<h1 class="h3 mb-4">{{ $editing ? 'Edit user' : 'Add user' }}</h1>
<form method="post" action="{{ $editing ? route('cms.users.update', $user) : route('cms.users.store') }}" class="card card-body" autocomplete="off">
    @csrf
    @if ($editing) @method('PUT') @endif
    <div class="row g-3">
        <div class="col-md-6">
            <label class="form-label" for="first_name">First name <span class="text-danger" aria-hidden="true">*</span></label>
            <input class="form-control" id="first_name" name="first_name" value="{{ old('first_name', $user->first_name) }}" required maxlength="50">
        </div>
        <div class="col-md-6">
            <label class="form-label" for="last_name">Last name</label>
            <input class="form-control" id="last_name" name="last_name" value="{{ old('last_name', $user->last_name) }}" maxlength="50">
        </div>
        <div class="col-md-6">
            <label class="form-label" for="email">E-mail address (used to sign in) <span class="text-danger" aria-hidden="true">*</span></label>
            <input class="form-control" type="email" id="email" name="email" value="{{ old('email', $user->email) }}" required maxlength="100">
        </div>
        <div class="col-md-6">
            <label class="form-label" for="role">Role</label>
            <select class="form-select" id="role" name="role">
                @foreach (\App\Http\Controllers\Cms\UserController::ROLE_LABELS as $value => $label)
                    <option value="{{ $value }}" @selected(old('role', $role) === $value)>{{ $label }}</option>
                @endforeach
            </select>
            <div class="form-text">Content managers can edit news, pages, categories and settings; admins everything except users.</div>
        </div>
        <div class="col-md-6">
            <label class="form-label" for="phone">Phone</label>
            <input class="form-control" id="phone" name="phone" value="{{ old('phone', $user->phone) }}" maxlength="20">
        </div>
        <div class="col-md-6">
            <label class="form-label" for="company">Organisation</label>
            <input class="form-control" id="company" name="company" value="{{ old('company', $user->company) }}" maxlength="100">
        </div>
        <div class="col-md-6">
            <label class="form-label" for="password">{{ $editing ? 'New password (leave empty to keep)' : 'Password' }}</label>
            <input class="form-control" type="password" id="password" name="password" @unless ($editing) required @endunless autocomplete="off">
            <div class="form-text">At least 10 characters, with upper and lower case letters, a number and a symbol.</div>
        </div>
        <div class="col-md-6">
            <label class="form-label" for="password_confirmation">Repeat the password</label>
            <input class="form-control" type="password" id="password_confirmation" name="password_confirmation" autocomplete="off">
        </div>
        <div class="col-12">
            <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" role="switch" id="active" name="active" value="1" @checked(old('active', $user->active))>
                <label class="form-check-label" for="active">Active (can sign in)</label>
            </div>
        </div>
    </div>
    <div class="mt-3 d-flex gap-2">
        <button type="submit" class="btn btn-primary">{{ $editing ? 'Save changes' : 'Add user' }}</button>
        <a class="btn btn-link" href="{{ route('cms.users.index') }}">Cancel</a>
    </div>
</form>
@endsection
