<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Parallax GSAP - Laravel Blade</title>
    
    {{-- Import Asset via Vite --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-900 text-white antialiased overflow-x-hidden">

    {{-- Parallax Section --}}
    <div class="relative w-full overflow-hidden">
        <section class="relative h-screen w-full">
            <div data-parallax-layers class="relative h-full w-full">
                
                {{-- Layer 1: Background Terjauh --}}
                <img 
                    src="{{ asset('img/1.png') }}" 
                    data-parallax-layer="1" 
                    alt="Background Layer" 
                    class="absolute inset-0 w-full h-full object-cover"
                />

                {{-- Layer 2: Midground Hutan --}}
                <img 
                    src="{{ asset('img/3.png') }}"  
                    data-parallax-layer="2" 
                    alt="Midground Layer" 
                    class="absolute inset-0 w-full h-full object-cover opacity-80"
                />

                {{-- Layer 3: Teks Judul Parallax --}}
                <div 
                    data-parallax-layer="3" 
                    class="absolute inset-0 flex items-center justify-center pointer-events-none"
                >
                    <h1 class="text-6xl md:text-9xl font-black uppercase tracking-widest text-white drop-shadow-2xl">
                        Heritage
                    </h1>
                </div>

                {{-- Layer 4: Foreground Terdekat --}}
                <img 
                    src="{{ asset('img/last layer.png') }}" 
                    data-parallax-layer="4" 
                    alt="Foreground Layer" 
                    class="absolute inset-0 w-full h-full object-cover opacity-100"
                />
            </div>

            {{-- Fade Gradient di bagian bawah section --}}
            <div class="absolute bottom-0 left-0 right-0 h-32 bg-gradient-to-t from-slate-900 to-transparent z-10"></div>
        </section>

        {{-- Section Konten Setelah Parallax --}}
        <section class="relative z-20 min-h-screen bg-slate-900 p-8 md:p-16 flex flex-col items-center justify-center">
            <div class="max-w-2xl text-center space-y-6">
                <h2 class="text-3xl md:text-5xl font-bold text-white">
                    Konten Setelah Scroll
                </h2>
                <p class="text-slate-400 leading-relaxed">
                    Efek parallax di atas bergerak secara dinamis berdasarkan posisi *scroll* layar menggunakan GSAP ScrollTrigger dan disempurnakan dengan Lenis Smooth Scroll tanpa membutuhkan React.
                </p>
            </div>
        </section>
    </div>

</body>
</html>