@php($editing = $record->exists)
@extends('cms.layouts.app', ['pageTitle' => ($editing ? 'Edit ' : 'Add ').strtolower($resource::$singular)])

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('cms.resource.index', $resource::$slug) }}">{{ $resource::$title }}</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{ $editing ? 'Edit' : 'Add' }}</li>
@endsection

@section('content')
<div class="d-flex align-items-center gap-2 mb-3">
    <h1 class="h3 mb-0 me-auto">{{ $editing ? 'Edit '.strtolower($resource::$singular) : 'Add '.strtolower($resource::$singular) }}</h1>
    @if ($editing && ($url = $resource::publicUrl($record)))
        <a class="btn btn-sm btn-outline-secondary" href="{{ $url }}" target="_blank" rel="noopener"><i class="bi bi-box-arrow-up-right"></i> View on site</a>
    @endif
</div>

<form method="post" enctype="multipart/form-data" novalidate
      action="{{ $editing ? route('cms.resource.update', [$resource::$slug, $record->getKey()]) : route('cms.resource.store', $resource::$slug) }}">
    @csrf
    @if ($editing) @method('PUT') @endif
    <div class="card card-body">
        <div class="row g-3">
            @foreach ($resource::fields() as $field)
                @include('cms.resource.field', ['field' => $field, 'record' => $record])
            @endforeach
        </div>
    </div>
    <div class="d-flex gap-2 mt-3">
        <button type="submit" class="btn btn-primary">{{ $editing ? 'Save changes' : 'Add '.strtolower($resource::$singular) }}</button>
        <button type="submit" class="btn btn-outline-primary" name="stay" value="1">Save and keep editing</button>
        <a class="btn btn-link" href="{{ route('cms.resource.index', $resource::$slug) }}">Cancel</a>
    </div>
</form>
@endsection
