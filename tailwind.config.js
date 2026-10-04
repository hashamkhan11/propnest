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
                sans: ['Inter', ...defaultTheme.fontFamily.sans],
                heading: ['Poppins', ...defaultTheme.fontFamily.sans],
            },
            fontWeight: {
                // Numeric aliases (font-500..font-800) used throughout the design system
                // alongside Tailwind's named weights (font-semibold, etc.).
                500: '500',
                600: '600',
                700: '700',
                800: '800',
            },
            colors: {
                // PropNest brand palette (Emerald & Gold).
                primary: {
                    50: '#edf5f4',
                    100: '#d1e6e3',
                    200: '#a3cdc7',
                    300: '#74b3ab',
                    400: '#4c948c',
                    500: '#2f726b',
                    600: '#1e5850',
                    700: '#0F3D3E',
                    800: '#0c3233',
                    900: '#092627',
                },
                accent: {
                    50: '#fbf6e7',
                    100: '#f5e9c2',
                    200: '#ebd489',
                    300: '#dfbe58',
                    400: '#d3ac38',
                    500: '#C9A227',
                    600: '#a6841e',
                    700: '#7d6417',
                    800: '#5a480f',
                    900: '#3c300a',
                },
                cream: {
                    DEFAULT: '#FBF8F1',
                    100: '#F7F1E4',
                    200: '#F0E6D0',
                },
            },
        },
    },

    plugins: [forms],
};
