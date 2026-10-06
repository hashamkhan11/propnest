import defaultTheme from 'tailwindcss/defaultTheme';
import colors from 'tailwindcss/colors';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    // The UI ships a single light theme; keep framework dark: variants off.
    darkMode: 'class',
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Geist', ...defaultTheme.fontFamily.sans],
                heading: ['Geist', ...defaultTheme.fontFamily.sans],
                // Editorial serif, used only for large display headlines.
                display: ['"Instrument Serif"', ...defaultTheme.fontFamily.serif],
                mono: ['"Geist Mono"', ...defaultTheme.fontFamily.mono],
            },
            fontWeight: {
                // Numeric aliases (font-500..font-800) used throughout the views
                // alongside Tailwind's named weights (font-semibold, etc.).
                500: '500',
                600: '600',
                700: '650',
                800: '700',
            },
            colors: {
                // Warm neutrals everywhere instead of Tailwind's cool grays.
                gray: colors.stone,

                // Ink: the brand's near-black, used for text, primary actions and dark surfaces.
                primary: {
                    50: '#F4F3EF',
                    100: '#E7E5DF',
                    200: '#D0CDC4',
                    300: '#AAA59A',
                    400: '#7E796E',
                    500: '#58544B',
                    600: '#3D3A34',
                    700: '#2A2824',
                    800: '#1D1C19',
                    900: '#141311',
                },
                // Terracotta: the single accent, for calls to action and highlights.
                accent: {
                    50: '#FCF4EF',
                    100: '#F8E3D6',
                    200: '#F0C2A8',
                    300: '#E69B76',
                    400: '#DB7849',
                    500: '#C95B2C',
                    600: '#AC4720',
                    700: '#8A381C',
                    800: '#6C2E1A',
                    900: '#512416',
                },
                // Paper: the page background.
                cream: {
                    DEFAULT: '#F6F4EF',
                    100: '#EFEBE3',
                    200: '#E4DED2',
                },
            },
            borderRadius: {
                xl: '0.625rem',
                '2xl': '0.75rem',
                '3xl': '1rem',
            },
            boxShadow: {
                lg: '0 10px 30px -14px rgb(29 28 25 / 0.22)',
                xl: '0 18px 40px -18px rgb(29 28 25 / 0.28)',
                '2xl': '0 28px 60px -24px rgb(29 28 25 / 0.35)',
            },
            letterSpacing: {
                tightest: '-0.035em',
            },
        },
    },

    plugins: [forms],
};
