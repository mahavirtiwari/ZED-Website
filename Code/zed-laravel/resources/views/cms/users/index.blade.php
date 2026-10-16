@extends('cms.layouts.app', ['pageTitle' => 'Users'])

@section('breadcrumb')<li class="breadcrumb-item active" aria-current="page">Users</li>@endsection

@section('content')
<div class="d-flex align-items-center gap-2 mb-3">
    <h1 class="h3 mb-0 me-auto">Users</h1>
    <a class="btn btn-primary btn-sm" href="{{ route('cms.users.create') }}"><i class="bi bi-plus-lg"></i> Add user</a>
</div>
<div class="card">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light"><tr><th>Name</th><th>E-mail</th><th>Role</th><th>Last sign-in</th><th>Status</th><th></th></tr></thead>
            <tbody>
            @foreach ($users as $u)
                <tr>
                    <td>{{ $u->displayName() }}</td>
                    <td>{{ $u->email }}</td>
                    <td>{{ \App\Http\Controllers\Cms\UserController::ROLE_LABELS[$u->role()] ?? 'No CMS access' }}</td>
                    <td class="text-nowrap">{{ $u->last_login ? date('d M Y, H:i', $u->last_login) : 'Never' }}</td>
                    <td>@if ($u->active)<span class="badge text-bg-success">Active</span>@else<span class="badge text-bg-secondary">Inactive</span>@endif</td>
                    <td class="text-end"><a class="btn btn-sm btn-outline-primary" href="{{ route('cms.users.edit', $u) }}">Edit</a></td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
