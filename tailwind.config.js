/** @type {import('tailwindcss').Config} */
export default {
    content: [
        "./resources/**/*.blade.php",
        "./resources/**/*.js",
        "./resources/**/*.vue",
    ],
    theme: {
        extend: {
            colors: {
                primary: {
                    DEFAULT: '#5E5CE8',
                    light: '#7674EC',
                    dark: '#4542B8',
                },
                secondary: {
                    DEFAULT: '#78E181',
                    dark: '#60C866',
                },
                accent: {
                    DEFAULT: '#FCB032',
                    dark: '#E39A1E',
                },
                info: {
                    DEFAULT: '#B2DFEB',
                    dark: '#8FCFDE',
                },
                gray: {
                    DEFAULT: '#AEBBC3',
                    dark: '#8A9BA6',
                    light: '#E8EDEF',
                }
            },
            fontFamily: {
                sans: ['Open Sans', 'sans-serif'],
                heading: ['Montserrat', 'sans-serif'],
            }
        },
    },
    plugins: [],
}
