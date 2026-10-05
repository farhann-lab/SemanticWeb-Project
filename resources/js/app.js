import './bootstrap';

import Alpine from 'alpinejs';
window.Alpine = Alpine;
Alpine.start();

import gsap from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';
import Lenis from '@studio-freight/lenis';

// Registrasi plugin ScrollTrigger
gsap.registerPlugin(ScrollTrigger);

document.addEventListener('DOMContentLoaded', () => {
    const triggerElement = document.querySelector('[data-parallax-layers]');

    if (triggerElement) {
        // 1. Buat Timeline GSAP untuk Parallax
        const tl = gsap.timeline({
            scrollTrigger: {
                trigger: triggerElement,
                start: "top top",
                end: "bottom top",
                scrub: true
            }
        });

        // 2. Definisi pergerakan setiap layer (yPercent menentukan kecepatan parallax)
        const layers = [
            { layer: "1", yPercent: 70 },
            { layer: "2", yPercent: 55 },
            { layer: "3", yPercent: 40 },
            { layer: "4", yPercent: 10 }
        ];

        layers.forEach((layerObj, idx) => {
            const targets = triggerElement.querySelectorAll(`[data-parallax-layer="${layerObj.layer}"]`);
            if (targets.length > 0) {
                tl.to(
                    targets,
                    {
                        yPercent: layerObj.yPercent,
                        ease: "none"
                    },
                    idx === 0 ? undefined : "<" // Mulai bersamaan dengan tween sebelumnya
                );
            }
        });
    }

    // 3. Inisialisasi Smooth Scrolling dengan Lenis
    const lenis = new Lenis({
        duration: 1.2,
        easing: (t) => Math.min(1, 1.001 - Math.pow(2, -10 * t)),
    });

    // Hubungkan Lenis dengan ScrollTrigger GSAP
    lenis.on('scroll', ScrollTrigger.update);

    gsap.ticker.add((time) => {
        lenis.raf(time * 1000);
    });

    gsap.ticker.lagSmoothing(0);
});