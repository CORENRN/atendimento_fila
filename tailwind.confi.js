import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.tsx',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                primary: "#56cbec",
                blackPrimary: "#141e22",
                blackSecondary: "#1a262d",
                blackThirdy: "#202e36",
                red: "#fd3b75",
                yellow: "#ffeb39",
                green: "#39db7d",
                lightW: "#eceef0"
            },
        },
    },

    plugins: [forms],
};
