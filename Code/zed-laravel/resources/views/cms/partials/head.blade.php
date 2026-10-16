@php($pwt = app(\App\Support\PasswordTransport::class))
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">
@if ($pwtKey = $pwt->publicKey())
<meta name="zed-pwt-key" content="{{ $pwtKey }}">
<meta name="zed-pwt-nonce" content="{{ $pwt->nonce() }}">
@endif
<meta name="robots" content="noindex, nofollow">
<title>{{ isset($pageTitle) ? $pageTitle.' · ' : '' }}ZED CMS</title>
<link rel="icon" href="{{ asset('assets/images/fav.png') }}">
<link rel="stylesheet" href="{{ asset('vendor/bootstrap/css/bootstrap.min.css') }}">
<link rel="stylesheet" href="{{ asset('vendor/bootstrap-icons/bootstrap-icons.min.css') }}">
<link rel="stylesheet" href="{{ asset('vendor/trumbowyg/ui/trumbowyg.min.css') }}">
<link rel="stylesheet" href="{{ asset('vendor/trumbowyg/plugins/colors/ui/trumbowyg.colors.min.css') }}">
<link rel="stylesheet" href="{{ asset('vendor/trumbowyg/plugins/table/ui/trumbowyg.table.min.css') }}">
<link rel="stylesheet" href="{{ asset('css/cms.css') }}">
