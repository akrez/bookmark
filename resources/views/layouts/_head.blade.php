<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<meta name="csrf-token" content="{{ csrf_token() }}">

<link rel="shortcut icon" href="{{ asset('favicon.ico') }}">

@hasSection('title')
    <title>@yield('title')</title>
@else
    @hasSection('header')
        <title>@yield('header')@hasSection('subheader'){{ ' | ' }}@yield('subheader')@endif</title>
    @else
        <title>{{ config('app.name') }}</title>
    @endif
@endif

@if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
    @vite(['resources/css/app.css', 'resources/js/app.js'])
@endif
