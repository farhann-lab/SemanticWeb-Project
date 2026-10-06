<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'HeritageFinder — Semantic Explorer for World Heritage Sites')</title>

    {{-- Fonts & Design Tokens --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    {{-- Vite Assets: Tailwind CSS & Alpine.js --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-white text-gray-950 min-h-screen antialiased flex flex-col justify-between">

    {{-- Modular Airbnb-inspired Navbar --}}
    <x-Navbar />

    {{-- Main Page Content --}}
    <main class="pt-[88px] w-full min-h-screen flex-1">
        @yield('content')
    </main>

    {{-- Footer diletakkan di luar <main> agar bisa full-width --}}
    <x-explore.Footer />

</body>
</html>