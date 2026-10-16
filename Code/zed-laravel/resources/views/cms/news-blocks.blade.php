@extends('cms.layouts.app', ['pageTitle' => 'News blocks'])

@section('breadcrumb')<li class="breadcrumb-item active" aria-current="page">News blocks</li>@endsection

@section('content')
<h1 class="h3 mb-2">News blocks</h1>
<p class="text-secondary mb-4">The home page news blocks group news items by type: block 1 articles, block 2 external links, block 3 files.</p>
<form method="post" action="{{ route('cms.news-blocks') }}" class="card card-body">
    @csrf
    @foreach ($blocks as $block)
        <div class="row g-3 align-items-end {{ $loop->last ? '' : 'mb-3' }}">
            <div class="col-md-8">
                <label class="form-label" for="b-{{ $block->news_block_id }}">Block {{ $block->news_block_id }} heading</label>
                <input class="form-control" id="b-{{ $block->news_block_id }}" name="blocks[{{ $block->news_block_id }}][heading]" value="{{ old('blocks.'.$block->news_block_id.'.heading', $block->news_block_heading) }}" required maxlength="200">
            </div>
            <div class="col-md-4">
                <label class="form-label" for="bs-{{ $block->news_block_id }}">Show on home page</label>
                <select class="form-select" id="bs-{{ $block->news_block_id }}" name="blocks[{{ $block->news_block_id }}][status]">
                    <option value="active" @selected($block->news_block_status === 'active')>Yes</option>
                    <option value="inactive" @selected($block->news_block_status !== 'active')>No</option>
                </select>
            </div>
        </div>
    @endforeach
    <div class="mt-3"><button type="submit" class="btn btn-primary">Save</button></div>
</form>
@endsection
