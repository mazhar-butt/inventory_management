import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';
import typography from '@tailwindcss/typography';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './vendor/laravel/jetstream/**/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                primary: {
                    DEFAULT: '#E96012',
                    50: '#FEF3E6',
                    100: '#FDE6CC',
                    200: '#FBCD99',
                    300: '#F9B366',
                    400: '#F79A33',
                    500: '#E96012',
                    600: '#CC4D0F',
                    700: '#A33E0C',
                    800: '#7A2F09',
                    900: '#522006',
                },
                dark: {
                    DEFAULT: '#11192B',
                    50: '#3D4A63',
                    100: '#344159',
                    200: '#2B364C',
                    300: '#222B3F',
                    400: '#1A2332',
                    500: '#11192B',
                    600: '#0D121D',
                    700: '#080C10',
                    800: '#040608',
                    900: '#000000',
                },
            },
        },
    },

    plugins: [forms, typography],
};
