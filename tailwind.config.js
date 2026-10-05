/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './resources/**/*.blade.php',
        './resources/**/*.js',
    ],
    theme: {
        // Enforce strict monochrome editorial palette by overriding default colors
        colors: {
            transparent: 'transparent',
            current: 'currentColor',
            white: '#FFFFFF',
            black: '#0A0A0A',
            gray: {
                0: '#FFFFFF',
                50: '#FAFAFA',
                100: '#F5F5F5',
                200: '#E5E5E5',
                300: '#D4D4D4',
                400: '#A3A3A3',
                500: '#737373',
                600: '#525252',
                800: '#262626',
                900: '#171717',
                950: '#0A0A0A',
            },
            // Semantic tokens mapped to CSS variables
            bg: {
                DEFAULT: 'var(--color-bg)',
                alt: 'var(--color-bg-alt)',
                inverse: 'var(--color-bg-inverse)',
            },
            surface: 'var(--color-surface)',
            border: {
                DEFAULT: 'var(--color-border)',
                strong: 'var(--color-border-strong)',
            },
            text: {
                DEFAULT: 'var(--color-text)',
                muted: 'var(--color-text-muted)',
                subtle: 'var(--color-text-subtle)',
                inverse: 'var(--color-text-inverse)',
            },
            primary: {
                DEFAULT: 'var(--color-primary)',
                hover: 'var(--color-primary-hover)',
            },
            focus: 'var(--color-focus)',
        },
        extend: {
            fontFamily: {
                sans: [
                    'Plus Jakarta Sans',
                    'Inter Tight',
                    'system-ui',
                    '-apple-system',
                    'BlinkMacSystemFont',
                    '"Segoe UI"',
                    'Roboto',
                    'sans-serif',
                ],
            },
            borderRadius: {
                sm: 'var(--radius-sm)',
                md: 'var(--radius-md)',
                lg: 'var(--radius-lg)',
                xl: 'var(--radius-xl)',
                '2xl': 'var(--radius-2xl)',
                full: 'var(--radius-full)',
            },
            boxShadow: {
                sm: 'var(--shadow-sm)',
                md: 'var(--shadow-md)',
                lg: 'var(--shadow-lg)',
            },
            transitionTimingFunction: {
                'ease-out': 'var(--ease-out)',
                'ease-in-out': 'var(--ease-in-out)',
                'ease-spring': 'var(--ease-spring)',
            },
            transitionDuration: {
                fast: 'var(--dur-fast)',
                base: 'var(--dur-base)',
                slow: 'var(--dur-slow)',
                hero: 'var(--dur-hero)',
            },
        },
    },
    plugins: [],
};

