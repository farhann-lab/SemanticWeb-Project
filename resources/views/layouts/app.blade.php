<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>HeritageFinder — Semantic Explorer for World Heritage Sites</title>

    {{-- Fonts & Design Tokens --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    {{-- Vite Assets: Tailwind CSS & Alpine.js --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-white text-gray-950 min-h-screen antialiased">

    {{-- Modular Airbnb-inspired Navbar --}}
    <x-Navbar />

    {{-- Main Page Content (Offset 88px for expanded fixed navbar) --}}
    <main class="pt-[88px] max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        @yield('content')
    </main>

</body>
</html>
