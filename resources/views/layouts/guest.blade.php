@props(['title' => null, 'head' => null])
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $title. ($title !== null ? ' | ' : ''). config('app.name') }}</title>

        <link rel="icon" type="image/png" href="{{ asset('/favicon-96x96.png') }}" sizes="96x96" />
        <link rel="icon" type="image/svg+xml" href=" {{ asset('/favicon.svg') }}" />
        <link rel="shortcut icon" href=" {{ asset('/favicon.ico') }}" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <!-- Styles -->
        @livewireStyles
        @filamentStyles

        {{ $head }}
    </head>
    <body class="bg-gray-50">
        <div class="font-sans text-gray-900 antialiased">
            {{ $slot }}
        </div>

        @livewireScripts
    </body>
</html>
