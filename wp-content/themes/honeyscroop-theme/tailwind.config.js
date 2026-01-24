/** @type {import('tailwindcss').Config} */
module.exports = {
    content: [
        "./*.php",
        "./inc/**/*.php",
        "./patterns/**/*.php",
        "./src/**/*.{js,jsx,ts,tsx}"
    ],
    theme: {
        extend: {
            colors: {
                honey: {
                    50: '#FFFBEB',
                    100: '#FEF3C7',
                    200: '#FDE68A',
                    300: '#FCD34D',
                    400: '#FBBF24',
                    500: '#F59E0B',
                    600: '#D97706',
                    700: '#B45309',
                    800: '#92400E',
                    900: '#78350F',
                },
                cream: '#FFFEF7',
                forest: '#14532D',
            },
            fontFamily: {
                heading: ['"Cormorant Garamond"', 'Georgia', 'serif'],
                body: ['"Inter"', 'sans-serif'],
            },
        },
    },
    plugins: [],
}
