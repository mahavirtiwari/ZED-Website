@extends('cms.layouts.app', ['pageTitle' => $resource::$title])

@section('breadcrumb')<li class="breadcrumb-item active" aria-current="page">{{ $resource::$title }}</li>@endsection

@section('content')
<div class="d-flex flex-wrap align-items-center gap-2 mb-3">
    <h1 class="h3 mb-0 me-auto">{{ $resource::$title }}</h1>
    @if ($resource::searchable() !== [])
        <form method="get" class="d-flex" role="search">
            <input class="form-control form-control-sm" type="search" name="q" value="{{ $search }}" placeholder="Search" aria-label="Search {{ strtolower($resource::$title) }}">
        </form>
    @endif
    @if ($resource::$canCreate)
        <a class="btn btn-primary btn-sm" href="{{ route('cms.resource.create', $resource::$slug) }}"><i class="bi bi-plus-lg"></i> Add {{ strtolower($resource::$singular) }}</a>
    @endif
</div>

@if ($resource::$multiUpload)
    <form method="post" action="{{ route('cms.resource.upload-many', $resource::$slug) }}" enctype="multipart/form-data" class="card card-body mb-3">
        @csrf
        <label class="form-label fw-semibold" for="files">Upload several photos at once</label>
        <div class="d-flex gap-2">
            <input class="form-control" type="file" name="files[]" id="files" accept="image/*" multiple required>
            <button class="btn btn-outline-primary text-nowrap" type="submit"><i class="bi bi-upload"></i> Upload</button>
        </div>
        <div class="form-text">JPG, PNG, GIF or WebP, up to 50 files. Titles and captions can be added afterwards.</div>
    </form>
@endif

<form method="post" action="{{ route('cms.resource.delete', $resource::$slug) }}" data-confirm="Delete the selected {{ strtolower($resource::$title) }}?">
    @csrf
    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        @if ($resource::$canDelete)
                            <th scope="col" class="col-check"><input class="form-check-input" type="checkbox" data-check-all aria-label="Select all"></th>
                        @endif
                        @foreach ($resource::columns() as $column => $label)
                            <th scope="col">{{ \Illuminate\Support\Str::after($label, ':') }}</th>
                        @endforeach
                        <th scope="col" class="text-end"><span class="visually-hidden">Actions</span></th>
                    </tr>
                </thead>
                <tbody>
                @forelse ($records as $record)
                    <tr>
                        @if ($resource::$canDelete)
                            <td><input class="form-check-input" type="checkbox" name="ids[]" value="{{ $record->getKey() }}" aria-label="Select"></td>
                        @endif
                        @foreach ($resource::columns() as $column => $label)
                            @php($value = $record->getAttribute($column))
                            <td>
                                @if (str_starts_with($label, 'badge:'))
                                    @if ((int) $value === 1)<span class="badge text-bg-success">Active</span>@else<span class="badge text-bg-secondary">Inactive</span>@endif
                                @elseif (str_starts_with($label, 'image:'))
                                    @if ($value)<img src="{{ media_url($value) }}" alt="" class="list-thumb" loading="lazy">@endif
                                @elseif ($loop->first)
                                    <a href="{{ route('cms.resource.edit', [$resource::$slug, $record->getKey()]) }}">{{ \Illuminate\Support\Str::limit((string) $value, 90) }}</a>
                                @else
                                    {{ \Illuminate\Support\Str::limit((string) $value, 60) }}
                                @endif
                            </td>
                        @endforeach
                        <td class="text-end text-nowrap">
                            @if ($url = $resource::publicUrl($record))
                                <a class="btn btn-sm btn-outline-secondary" href="{{ $url }}" target="_blank" rel="noopener" title="View on site"><i class="bi bi-box-arrow-up-right"></i></a>
                            @endif
                            <a class="btn btn-sm btn-outline-primary" href="{{ route('cms.resource.edit', [$resource::$slug, $record->getKey()]) }}">Edit</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="99" class="text-center text-secondary py-4">Nothing here yet.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="d-flex flex-wrap align-items-center gap-2 mt-3">
        @if ($resource::$canDelete && $records->count())
            <button type="submit" class="btn btn-outline-danger btn-sm"><i class="bi bi-trash"></i> Delete selected</button>
        @endif
        <div class="ms-auto">{{ $records->links() }}</div>
    </div>
</form>
@endsection
