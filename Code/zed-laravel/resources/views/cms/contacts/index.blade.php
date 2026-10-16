@extends('cms.layouts.app', ['pageTitle' => 'Contact queries'])

@section('breadcrumb')<li class="breadcrumb-item active" aria-current="page">Contact queries</li>@endsection

@section('content')
<div class="d-flex flex-wrap align-items-center gap-2 mb-3">
    <h1 class="h3 mb-0 me-auto">Contact queries</h1>
    <form method="get" class="d-flex" role="search">
        <input class="form-control form-control-sm" type="search" name="q" value="{{ $search }}" placeholder="Search name or e-mail" aria-label="Search contact queries">
    </form>
</div>
<p class="text-secondary">Queries sent with the website Contact Us form (Settings &rsaquo; Security &rsaquo; Contact Us form). Queries sent with the Zoho Desk form are in Zoho Desk.</p>
<form method="post" action="{{ route('cms.contacts.delete') }}" data-confirm="Delete the selected queries?">
    @csrf
    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="col-check"><input class="form-check-input" type="checkbox" data-check-all aria-label="Select all"></th>
                        <th>Name</th><th>E-mail</th><th>Category</th><th>Received</th>
                    </tr>
                </thead>
                <tbody>
                @forelse ($contacts as $contact)
                    <tr>
                        <td><input class="form-check-input" type="checkbox" name="ids[]" value="{{ $contact->id }}" aria-label="Select"></td>
                        <td><a href="{{ route('cms.contacts.show', $contact->id) }}">{{ $contact->contact_name }}</a></td>
                        <td>{{ $contact->contact_email }}</td>
                        <td>{{ $contact->query_category }}</td>
                        <td class="text-nowrap">{{ $contact->created_on?->format('d M Y, H:i') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center text-secondary py-4">No queries yet.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="d-flex flex-wrap align-items-center gap-2 mt-3">
        @if ($contacts->count())<button type="submit" class="btn btn-outline-danger btn-sm"><i class="bi bi-trash"></i> Delete selected</button>@endif
        <div class="ms-auto">{{ $contacts->links() }}</div>
    </div>
</form>
@endsection
