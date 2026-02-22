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
                    50: '#fff5f5',
                    100: '#ffeaea',
                    200: '#ffcfcf',
                    300: '#ffa3a3',
                    400: '#ff6b6b',
                    500: '#ff0033',  // ERP System Red
                    600: '#e6002e',  // Hover Red
                    700: '#cc0029',
                    800: '#99001f',
                    900: '#660015',
                },
                ink: {
                    main: '#1e1e1e',   // Modern Text Dark (No black)
                    dark: '#2d2d2d',
                    light: '#525252',
                    muted: '#a3a3a3',
                },
                surface: {
                    50: '#ffffff',     // Pure white background
                    100: '#fcfcfc',
                    200: '#f4f4f5',
                },
                border: '#f2f2f2',     // Red Brand Soft Border
            },
            fontFamily: {
                sans: ['Inter', 'Roboto', 'Cairo', 'ui-sans-serif', 'system-ui', 'sans-serif'],
                inter: ['Inter', 'sans-serif'],
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

    plugins: [
        require('@tailwindcss/forms'),
        require('@tailwindcss/typography')
    ],
};
