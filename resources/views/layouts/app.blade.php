<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ $title ?? config('app.name', 'Pokemon') }}</title>

    <!-- Styles / Scripts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Hanken+Grotesk:wght@100..900&family=Lato:wght@300;400;700;900&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/charts.css/dist/charts.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
</head>

<body class="font-lato bg-black">
    <nav class="flex justify-between items-center py-4 px-12 border-b border-white/10" style="background: #E22536">
        <div class="max-w-[1200px] mx-auto">
            <img src="{{ Vite::asset('resources/images/pokemon.png') }}" alt="Pokemon" width="130">
        </div>
    </nav>

    <main class="max-w-[1200px] mx-auto">
        {{ $slot }}
    </main>
</body>

</html>