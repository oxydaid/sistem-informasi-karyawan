<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-50">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <title>{{ $title ?? ($appSettings->app_name ?? config('app.name')) }}</title>

        <!-- Google Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

        <!-- Dynamic Theme Colors -->
        <style>
            :root {
                --color-primary: {{ $appSettings->primary_color ?? '#0ea5e9' }};
                --color-secondary: {{ $appSettings->secondary_color ?? '#334155' }};
                font-family: 'Outfit', sans-serif;
            }
        </style>

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @livewireStyles
    </head>
    <body class="h-full text-slate-800 antialiased">
        
        <div class="flex min-h-full flex-col justify-center py-12 sm:px-6 lg:px-8">
            {{ $slot }}
        </div>

        <x-toast />

        @livewireScripts
    </body>
</html>
