@extends('cms.layouts.app', ['pageTitle' => 'Dashboard'])

@section('content')
<h1 class="h3 mb-4">Welcome, {{ auth()->user()->first_name ?: auth()->user()->displayName() }}</h1>
<div class="row g-3 mb-4">
    @foreach ($counts as $label => [$count, $url])
        <div class="col-sm-6 col-xl-3">
            <a class="card card-body text-decoration-none stat-card" href="{{ $url }}">
                <span class="stat-number">{{ number_format($count) }}</span>
                <span class="text-secondary">{{ $label }}</span>
            </a>
        </div>
    @endforeach
</div>
<div class="card card-body">
    <h2 class="h5">Quick links</h2>
    <div class="d-flex flex-wrap gap-2">
        <a class="btn btn-outline-primary" href="{{ route('cms.resource.create', 'news') }}"><i class="bi bi-plus-lg"></i> Add news</a>
        <a class="btn btn-outline-primary" href="{{ route('cms.resource.create', 'pages') }}"><i class="bi bi-plus-lg"></i> Add page</a>
        @if (\App\Cms\Resources\SliderResource::allows(auth()->user()))
            <a class="btn btn-outline-primary" href="{{ route('cms.resource.index', 'sliders') }}"><i class="bi bi-images"></i> Home sliders</a>
        @endif
        <a class="btn btn-outline-secondary" href="{{ url('/') }}" target="_blank" rel="noopener"><i class="bi bi-box-arrow-up-right"></i> Open the website</a>
    </div>
</div>
@endsection
