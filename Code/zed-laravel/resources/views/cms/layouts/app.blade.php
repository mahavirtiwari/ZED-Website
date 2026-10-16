@php
    $user = auth()->user();
    $current = request()->route('resource');
    $nav = collect(config('cms.resources'))->filter(fn ($r) => $r::allows($user));
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    @include('cms.partials.head')
</head>
<body class="cms">
<a class="visually-hidden-focusable" href="#main">Skip to content</a>
<div class="cms-shell">
    <nav class="cms-sidebar" id="cms-sidebar" aria-label="CMS">
        <a class="cms-brand" href="{{ route('cms.dashboard') }}">
            <img src="{{ asset('assets/images/zed-logo.png') }}" alt="ZED CMS">
        </a>
        <ul class="nav flex-column">
            <li class="nav-item"><a class="nav-link {{ request()->routeIs('cms.dashboard') ? 'active' : '' }}" href="{{ route('cms.dashboard') }}"><i class="bi bi-speedometer2"></i> Dashboard</a></li>
            @foreach ($nav as $resource)
                <li class="nav-item"><a class="nav-link {{ $current === $resource::$slug ? 'active' : '' }}" href="{{ route('cms.resource.index', $resource::$slug) }}"><i class="bi {{ $resource::$icon }}"></i> {{ $resource::$title }}</a></li>
            @endforeach
            <li class="nav-item"><a class="nav-link {{ request()->routeIs('cms.news-blocks') ? 'active' : '' }}" href="{{ route('cms.news-blocks') }}"><i class="bi bi-layout-three-columns"></i> News blocks</a></li>
            @if ($user->isAdmin())
                <li class="nav-item"><a class="nav-link {{ request()->routeIs('cms.contacts.*') ? 'active' : '' }}" href="{{ route('cms.contacts.index') }}"><i class="bi bi-envelope"></i> Contact queries</a></li>
            @endif
            <li class="nav-heading">Settings</li>
            @foreach (config('cms.settings') as $group => [$title, $roles])
                @if ($user->inGroup($roles))
                    <li class="nav-item"><a class="nav-link {{ request()->route('group') === $group ? 'active' : '' }}" href="{{ route('cms.settings', $group) }}"><i class="bi {{ $group === 'security' ? 'bi-shield-lock' : 'bi-gear' }}"></i> {{ $title }}</a></li>
                @endif
            @endforeach
            @if ($user->isSuperAdmin())
                <li class="nav-item"><a class="nav-link {{ request()->routeIs('cms.users.*') ? 'active' : '' }}" href="{{ route('cms.users.index') }}"><i class="bi bi-person-gear"></i> Users</a></li>
            @endif
        </ul>
    </nav>
    <div class="cms-main">
        <header class="cms-topbar">
            <button class="btn btn-outline-secondary d-lg-none" type="button" data-toggle-sidebar aria-controls="cms-sidebar" aria-label="Menu"><i class="bi bi-list"></i></button>
            <nav aria-label="breadcrumb" class="me-auto">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('cms.dashboard') }}">Dashboard</a></li>
                    @yield('breadcrumb')
                </ol>
            </nav>
            <a class="btn btn-sm btn-outline-secondary" href="{{ url('/') }}" target="_blank" rel="noopener"><i class="bi bi-box-arrow-up-right"></i> View site</a>
            <div class="dropdown">
                <button class="btn btn-sm btn-light dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false"><i class="bi bi-person-circle"></i> {{ $user->displayName() }}</button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item" href="{{ route('cms.profile') }}">Profile and password</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <form method="post" action="{{ route('cms.logout') }}">@csrf<button class="dropdown-item" type="submit">Sign out</button></form>
                    </li>
                </ul>
            </div>
        </header>
        <main id="main" class="cms-content">
            @include('cms.partials.flash')
            @yield('content')
        </main>
    </div>
</div>
<script src="{{ asset('vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('vendor/jquery/jquery-3.7.1.min.js') }}"></script>
<script src="{{ asset('vendor/trumbowyg/trumbowyg.min.js') }}"></script>
<script src="{{ asset('vendor/trumbowyg/plugins/upload/trumbowyg.upload.min.js') }}"></script>
<script src="{{ asset('vendor/trumbowyg/plugins/table/trumbowyg.table.min.js') }}"></script>
<script src="{{ asset('vendor/trumbowyg/plugins/colors/trumbowyg.colors.min.js') }}"></script>
<script src="{{ asset('vendor/trumbowyg/plugins/fontsize/trumbowyg.fontsize.min.js') }}"></script>
<script src="{{ asset('vendor/trumbowyg/plugins/cleanpaste/trumbowyg.cleanpaste.min.js') }}"></script>
<script src="{{ asset('js/zed-pwt.js') }}"></script>
<script src="{{ asset('js/zed-editor-styles.js') }}"></script>
<script src="{{ asset('js/cms.js') }}"></script>
</body>
</html>
