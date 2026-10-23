import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['"Plus Jakarta Sans"', 'Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                brand: {
                    forest: '#064e3b',
                    forestDark: '#042f2e',
                    emerald: '#059669',
                    lightMint: '#ecfdf5',
                    softBg: '#f0f5f2',
                    dark: '#064e3b',
                    DEFAULT: '#059669',
                    light: '#d1fae5',
                },
            },
            boxShadow: {
                'bento': '0 10px 30px -5px rgba(15, 23, 42, 0.05), 0 2px 6px -1px rgba(15, 23, 42, 0.02)',
                'bento-hover': '0 20px 35px -8px rgba(6, 78, 59, 0.12), 0 6px 12px -2px rgba(15, 23, 42, 0.04)',
                'pill': '0 8px 30px -4px rgba(0, 0, 0, 0.12)',
                'float-bridge': '0 16px 36px -8px rgba(6, 78, 59, 0.16), 0 4px 12px -2px rgba(0, 0, 0, 0.06)',
            },
            borderRadius: {
                '4xl': '2rem',
                '5xl': '2.5rem',
            },
        },
    },

    plugins: [forms],
};
