@extends('cms.layouts.app', ['pageTitle' => $title])

@section('breadcrumb')
    <li class="breadcrumb-item">Settings</li>
    <li class="breadcrumb-item active" aria-current="page">{{ $title }}</li>
@endsection

@section('content')
<h1 class="h3 mb-2">{{ $title }}</h1>
@if ($group === 'security')
    <p class="text-secondary">The Zoho Desk contact form uses Zoho's own captcha; the Contact Us captcha switch applies to the website form.</p>
@else
    <p class="text-secondary mb-4">Changes show on the website within a minute. A field shows the text currently on the site; empty text fields hide that item.</p>
@endif
<form method="post" action="{{ route('cms.settings', $group) }}" enctype="multipart/form-data" class="card card-body">
    @csrf
    <div class="row g-3">
        @foreach ($fields as $name => $field)
            @php
                [$label, $type] = $field;
                $default = $field[2] ?? '';
                $help = $field[3] ?? null;
                $value = old($name, array_key_exists($name, $values) ? $values[$name] : $default);
                $id = 's-'.$name;
            @endphp
            @if ($type === 'heading')
                <div class="col-12 {{ $loop->first ? '' : 'pt-3' }}"><h2 class="h5 border-bottom pb-2 mb-0">{{ $label }}</h2></div>
                @continue
            @endif
            <div class="{{ $type === 'textarea' ? 'col-12' : 'col-md-6' }}">
                <label class="form-label" for="{{ $id }}">{{ $label }}</label>
                @if ($type === 'textarea')
                    <textarea class="form-control" id="{{ $id }}" name="{{ $name }}" rows="4">{{ $value }}</textarea>
                @elseif ($type === 'toggle')
                    <select class="form-select" id="{{ $id }}" name="{{ $name }}">
                        <option value="1" @selected((string) ($value === '' ? '1' : $value) === '1')>Show</option>
                        <option value="0" @selected((string) $value === '0')>Hide</option>
                    </select>
                @elseif (str_starts_with($type, 'select:'))
                    <select class="form-select" id="{{ $id }}" name="{{ $name }}">
                        @foreach (\App\Http\Controllers\Cms\AdminController::selectOptions($type) as $optionValue => $optionLabel)
                            <option value="{{ $optionValue }}" @selected((string) ($value === '' ? $default : $value) === (string) $optionValue)>{{ $optionLabel }}</option>
                        @endforeach
                    </select>
                @elseif ($type === 'image')
                    @php($current = ($values[$name] ?? '') !== '' ? media_url($values[$name]) : asset($default))
                    <div class="d-flex align-items-center gap-3 mb-2">
                        <img src="{{ $current }}" alt="Current image" class="img-thumbnail settings-image">
                        @if (($values[$name] ?? '') !== '')
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="reset_{{ $name }}" value="1" id="reset-{{ $name }}">
                                <label class="form-check-label" for="reset-{{ $name }}">Use the original image again</label>
                            </div>
                        @endif
                    </div>
                    <input class="form-control" type="file" id="{{ $id }}" name="{{ $name }}" accept="image/*">
                @else
                    <input class="form-control" id="{{ $id }}" name="{{ $name }}" value="{{ $value }}" type="{{ $type === 'number' ? 'number' : 'text' }}" @if ($type === 'url') placeholder="https://… or /page-address" @endif>
                @endif
                @if ($help)<div class="form-text">{{ $help }}</div>@endif
            </div>
        @endforeach
    </div>
    <div class="mt-3"><button type="submit" class="btn btn-primary">Save</button></div>
</form>
@endsection
