import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

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
            fontFamily: {
                sans: ['"Public Sans"', ...defaultTheme.fontFamily.sans],
                display: ['Fraunces', ...defaultTheme.fontFamily.serif],
            },
            colors: {
                ink: 'oklch(0.24 0.02 50)',
                secondary: 'oklch(0.48 0.03 55)',
                border: 'oklch(0.87 0.015 55)',
                divider: 'oklch(0.91 0.012 55)',
                app: 'oklch(0.98 0.01 70)',
                sidebar: 'oklch(0.955 0.014 65)',
                accent: {
                    DEFAULT: 'oklch(0.58 0.16 42)',
                    dark: 'oklch(0.46 0.15 40)',
                    tint: 'oklch(0.94 0.045 50)',
                },
                success: {
                    DEFAULT: 'oklch(0.53 0.12 145)',
                    tint: 'oklch(0.95 0.035 145)',
                },
                info: {
                    DEFAULT: 'oklch(0.5 0.11 235)',
                    tint: 'oklch(0.95 0.025 235)',
                },
                warning: {
                    DEFAULT: 'oklch(0.63 0.14 75)',
                    tint: 'oklch(0.95 0.045 80)',
                },
                danger: {
                    DEFAULT: 'oklch(0.55 0.18 27)',
                    tint: 'oklch(0.95 0.04 27)',
                },
                neutral: {
                    DEFAULT: 'oklch(0.5 0.015 55)',
                    tint: 'oklch(0.94 0.01 60)',
                },
            },
            borderRadius: {
                card: '10px',
                'card-sm': '8px',
                control: '7px',
                chip: '9999px',
            },
            boxShadow: {
                card: '0 1px 2px oklch(0.24 0.02 50 / 6%), 0 1px 0 oklch(0.24 0.02 50 / 4%)',
                popover: '0 12px 24px -8px oklch(0.24 0.02 50 / 18%), 0 0 0 1px oklch(0.24 0.02 50 / 6%)',
                toast: '0 10px 30px -6px oklch(0.24 0.02 50 / 25%)',
            },
            transitionTimingFunction: {
                out: 'cubic-bezier(0.23, 1, 0.32, 1)',
                'in-out': 'cubic-bezier(0.77, 0, 0.175, 1)',
            },
        },
    },

    plugins: [forms],
};
