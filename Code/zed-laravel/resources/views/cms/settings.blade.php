@extends('cms.layouts.app', ['pageTitle' => $title])

@section('breadcrumb')
    <li class="breadcrumb-item">Settings</li>
    <li class="breadcrumb-item active" aria-current="page">{{ $title }}</li>
@endsection

@section('content')
<h1 class="h3 mb-4">{{ $title }}</h1>
@if ($group === 'security')
    <p class="text-secondary">The Zoho Desk contact form uses Zoho's own captcha; the Contact Us captcha switch applies to the website form.</p>
@endif
<form method="post" action="{{ route('cms.settings', $group) }}" class="card card-body">
    @csrf
    <div class="row g-3">
        @foreach ($fields as $name => [$label, $type])
            @php($value = old($name, $values[$name] ?? ''))
            <div class="{{ $type === 'textarea' ? 'col-12' : 'col-md-6' }}">
                <label class="form-label" for="s-{{ $name }}">{{ $label }}</label>
                @if ($type === 'textarea')
                    <textarea class="form-control" id="s-{{ $name }}" name="{{ $name }}" rows="4">{{ $value }}</textarea>
                @elseif (str_starts_with($type, 'select:'))
                    @php($default = ['captcha_cms_login' => '1', 'captcha_contact_us' => '1', 'contact_form_type' => 'zoho', 'dashboard_status' => '1'][$name] ?? '')
                    <select class="form-select" id="s-{{ $name }}" name="{{ $name }}">
                        @foreach (\App\Http\Controllers\Cms\AdminController::selectOptions($type) as $optionValue => $optionLabel)
                            <option value="{{ $optionValue }}" @selected((string) ($value === '' ? $default : $value) === (string) $optionValue)>{{ $optionLabel }}</option>
                        @endforeach
                    </select>
                @else
                    <input class="form-control" id="s-{{ $name }}" name="{{ $name }}" value="{{ $value }}" type="{{ $type === 'number' ? 'number' : 'text' }}">
                @endif
            </div>
        @endforeach
    </div>
    <div class="mt-3"><button type="submit" class="btn btn-primary">Save</button></div>
</form>
@endsection
