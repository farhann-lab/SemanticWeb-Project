@extends('layouts.app')

@section('content')

{{-- 1. Fullwidth GSAP Parallax Hero Section with Live SPARQL Stats --}}
<x-HeroSection :stats="$stats" />

{{-- 2. Additional Knowledge Graph Info Section --}}
<section class="relative z-30 bg-[#0A0A0A] py-20 border-t border-gray-900">
    <div class="max-w-6xl mx-auto px-6 md:px-12">
        <div class="mb-12">
            <span class="text-xs font-bold uppercase tracking-widest text-gray-500">Semantic Web Architecture</span>
            <h2 class="text-2xl md:text-4xl font-bold text-white tracking-tight mt-1">
                Explore Our Knowledge Graph
            </h2>
        </div>

        <div class="grid md:grid-cols-3 gap-6">
            <div class="p-8 rounded-[24px] bg-[#171717] border border-gray-800/80 hover:border-gray-700 transition-all group">
                <div class="w-10 h-10 rounded-full bg-white/10 text-white flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="3" y1="21" x2="21" y2="21"/>
                        <line x1="6" y1="21" x2="6" y2="10"/>
                        <line x1="10" y1="21" x2="10" y2="10"/>
                        <line x1="14" y1="21" x2="14" y2="10"/>
                        <line x1="18" y1="21" x2="18" y2="10"/>
                        <polygon points="12 3 2 10 22 10 12 3"/>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-white mb-2">
                    Heritage Sites
                </h3>
                <p class="text-sm text-gray-400 leading-relaxed">
                    Browse detailed information about World Heritage Sites queried directly from RDF dataset.
                </p>
            </div>

            <div class="p-8 rounded-[24px] bg-[#171717] border border-gray-800/80 hover:border-gray-700 transition-all group">
                <div class="w-10 h-10 rounded-full bg-white/10 text-white flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="11" cy="11" r="8"/>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"/>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-white mb-2">
                    Search & Filter
                </h3>
                <p class="text-sm text-gray-400 leading-relaxed">
                    Find heritage sites based on name, country, region, category, and UNESCO criteria.
                </p>
            </div>

            <div class="p-8 rounded-[24px] bg-[#171717] border border-gray-800/80 hover:border-gray-700 transition-all group">
                <div class="w-10 h-10 rounded-full bg-white/10 text-white flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-white mb-2">
                    Related Heritage
                </h3>
                <p class="text-sm text-gray-400 leading-relaxed">
                    Discover connected semantic nodes and recommended heritage properties.
                </p>
            </div>
        </div>
    </div>
</section>

@endsection
