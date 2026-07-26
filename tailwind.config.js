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
                sans: ['Manrope', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                ink: 'oklch(0.22 0.01 260)',
                secondary: 'oklch(0.5 0.02 260)',
                border: 'oklch(0.9 0.006 260)',
                divider: 'oklch(0.93 0.005 260)',
                app: 'oklch(0.995 0.002 260)',
                sidebar: 'oklch(0.97 0.006 260)',
                accent: {
                    DEFAULT: 'oklch(0.55 0.18 290)',
                    dark: 'oklch(0.46 0.18 290)',
                    tint: 'oklch(0.94 0.03 290)',
                },
                success: {
                    DEFAULT: 'oklch(0.55 0.14 150)',
                    tint: 'oklch(0.95 0.04 150)',
                },
                info: {
                    DEFAULT: 'oklch(0.5 0.15 250)',
                    tint: 'oklch(0.95 0.03 250)',
                },
                warning: {
                    DEFAULT: 'oklch(0.6 0.15 80)',
                    tint: 'oklch(0.95 0.05 80)',
                },
                danger: {
                    DEFAULT: 'oklch(0.58 0.19 25)',
                    tint: 'oklch(0.95 0.04 25)',
                },
                neutral: {
                    DEFAULT: 'oklch(0.5 0.01 260)',
                    tint: 'oklch(0.94 0.005 260)',
                },
            },
            borderRadius: {
                card: '14px',
                'card-sm': '12px',
                control: '9px',
                chip: '8px',
            },
        },
    },

    plugins: [forms],
};
