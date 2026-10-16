<!DOCTYPE html>
<html lang="en">
<head>
    @include('cms.partials.head')
</head>
<body class="cms-auth">
    <main class="auth-card card shadow-sm">
        <div class="row g-0">
            <div class="col-md-5 auth-brand d-none d-md-flex">
                <img src="{{ asset('assets/images/zed-logo.png') }}" alt="ZED" class="img-fluid">
                <p class="mt-3 mb-0">Zero Defect. Zero Effect.</p>
            </div>
            <div class="col-md-7 p-4 p-lg-5">
                <h1 class="h4 mb-4">@yield('heading')</h1>
                @include('cms.partials.flash')
                @yield('content')
            </div>
        </div>
    </main>
    <script src="{{ asset('js/zed-pwt.js') }}"></script>
    <script src="{{ asset('js/password-toggle.js') }}"></script>
    <script src="{{ asset('js/zed-captcha.js') }}"></script>
</body>
</html>
