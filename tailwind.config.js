import defaultTheme from 'tailwindcss/defaultTheme';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/**/*.blade.php',
        './resources/**/*.js',
        './resources/**/*.vue',
    ],
    theme: {
        extend: {
            colors: {
                "black": "#060606",
                "red": "#E22536"
            },
            fontFamily: {
                "lato": ["Lato", "sans-serif"]
            },
            fontSize: {
                '2xs': '0.7rem'
            }
        },
    },
    plugins: [],
};
