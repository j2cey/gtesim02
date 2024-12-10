/** @type {import('tailwindcss').Config} */
export default {
    prefix: 'tw-',
    content: [
        "./resources/**/*.blade.php",
        "./resources/**/*.js",
    ],
    theme: {
        extend: {},
        fontSize: {
            xs: ['10px', '16px'],
            sm: ['11px', '20px'],
            base: ['16px', '24px'],
            lg: ['20px', '28px'],
            xl: ['24px', '32px'],
        }
    },
    plugins: [],
}

