/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',
    ],

    theme: {
        extend: {
            colors: {
                brand: {
                    50: '#fff1f2',
                    100: '#ffe4e6',
                    200: '#fecdd3',
                    300: '#fda4af',
                    400: '#fb7185',
                    500: '#ff2d55', // Primary Modern Red (Apple/Nike style)
                    600: '#e11d48',
                    700: '#be123c',
                    800: '#9f1239',
                    900: '#881337',
                    950: '#4c0519',
                },
                ink: {
                    main: '#0a0a0a',   // Carbon Black
                    dark: '#171717',   // Deep Charcoal
                    light: '#525252',  // Medium Gray
                    muted: '#a3a3a3',  // Light Gray
                },
                surface: {
                    50: '#f8f9fa',     // Very Light Gray
                    100: '#f4f4f5',
                    200: '#e4e4e7',
                },
                border: '#e5e5e5',     // Neutral Border
            },
            fontFamily: {
                sans: ['Roboto', 'Cairo', 'ui-sans-serif', 'system-ui', 'sans-serif'],
                roboto: ['Roboto', 'sans-serif'],
                cairo: ['Cairo', 'sans-serif'],
            },
            boxShadow: {
                'premium': '0 4px 6px -1px rgb(0 0 0 / 0.05), 0 2px 4px -2px rgb(0 0 0 / 0.05)',
                'soft-red': '0 10px 15px -3px rgba(255, 45, 85, 0.2), 0 4px 6px -4px rgba(255, 45, 85, 0.1)',
                'glow-red': '0 0 15px rgba(255, 45, 85, 0.3)',
                'flat': '0 0 0 1px rgba(0, 0, 0, 0.05)',
            },
            borderRadius: {
                'xl': '0.75rem',
                '2xl': '1rem',
                '3xl': '1.5rem',
            },
            animation: {
                'fade-in': 'fadeIn 0.5s ease-out',
                'slide-up': 'slideUp 0.6s ease-out',
                'bounce-sm': 'bounce-sm 0.5s cubic-bezier(0.36, 0, 0.66, -0.56)',
            },
            keyframes: {
                fadeIn: {
                    '0%': { opacity: '0' },
                    '100%': { opacity: '1' },
                },
                slideUp: {
                    '0%': { transform: 'translateY(20px)', opacity: '0' },
                    '100%': { transform: 'translateY(0)', opacity: '1' },
                },
                'bounce-sm': {
                    '0%, 100%': { transform: 'scale(1)' },
                    '50%': { transform: 'scale(1.2)' },
                }
            },
        },
    },

    plugins: [require('@tailwindcss/forms')],
};
