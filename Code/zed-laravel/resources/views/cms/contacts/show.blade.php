@extends('cms.layouts.app', ['pageTitle' => 'Contact query'])

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('cms.contacts.index') }}">Contact queries</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{ $contact->contact_name }}</li>
@endsection

@section('content')
<h1 class="h3 mb-4">Query from {{ $contact->contact_name }}</h1>
<div class="card card-body">
    <dl class="row mb-0">
        <dt class="col-sm-3">E-mail</dt><dd class="col-sm-9"><a href="mailto:{{ $contact->contact_email }}">{{ $contact->contact_email }}</a></dd>
        <dt class="col-sm-3">Phone</dt><dd class="col-sm-9">{{ $contact->contact_phone ?: '—' }}</dd>
        <dt class="col-sm-3">Enterprise</dt><dd class="col-sm-9">{{ $contact->enterprise_name ?: '—' }}</dd>
        <dt class="col-sm-3">Category</dt><dd class="col-sm-9">{{ $contact->query_category ?: '—' }}</dd>
        <dt class="col-sm-3">Received</dt><dd class="col-sm-9">{{ $contact->created_on?->format('d M Y, H:i') }}</dd>
        <dt class="col-sm-3">Message</dt><dd class="col-sm-9 text-pre-line">{{ $contact->contact_query }}</dd>
    </dl>
</div>
<a class="btn btn-link mt-3 px-0" href="{{ route('cms.contacts.index') }}">&larr; Back to all queries</a>
@endsection
