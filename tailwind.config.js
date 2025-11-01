import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    darkMode: 'class',

    theme: {
        extend: {
            // Modern Typography System
            fontFamily: {
                sans: ['Inter', 'ui-sans-serif', 'system-ui', '-apple-system', 'BlinkMacSystemFont', 'Segoe UI', 'Roboto', 'Helvetica Neue', 'Arial', 'Noto Sans', 'sans-serif', 'Apple Color Emoji', 'Segoe UI Emoji', 'Segoe UI Symbol', 'Noto Color Emoji'],
                mono: ['JetBrains Mono', 'ui-monospace', 'SFMono-Regular', 'Monaco', 'Consolas', 'Liberation Mono', 'Courier New', 'monospace'],
                display: ['Poppins', 'ui-sans-serif', 'system-ui'],
                body: ['Inter', 'ui-sans-serif', 'system-ui'],
            },

            // Enhanced Color Palette
            colors: {
                // Primary Colors
                primary: {
                    50: '#eff6ff',
                    100: '#dbeafe',
                    200: '#bfdbfe',
                    300: '#93c5fd',
                    400: '#60a5fa',
                    500: '#3b82f6',
                    600: '#2563eb',
                    700: '#1d4ed8',
                    800: '#1e40af',
                    900: '#1e3a8a',
                    950: '#172554',
                },

                // Secondary Colors
                secondary: {
                    50: '#f8fafc',
                    100: '#f1f5f9',
                    200: '#e2e8f0',
                    300: '#cbd5e1',
                    400: '#94a3b8',
                    500: '#64748b',
                    600: '#475569',
                    700: '#334155',
                    800: '#1e293b',
                    900: '#0f172a',
                    950: '#020617',
                },

                // Success Colors
                success: {
                    50: '#f0fdf4',
                    100: '#dcfce7',
                    200: '#bbf7d0',
                    300: '#86efac',
                    400: '#4ade80',
                    500: '#22c55e',
                    600: '#16a34a',
                    700: '#15803d',
                    800: '#166534',
                    900: '#14532d',
                    950: '#052e16',
                },

                // Warning Colors
                warning: {
                    50: '#fffbeb',
                    100: '#fef3c7',
                    200: '#fde68a',
                    300: '#fcd34d',
                    400: '#fbbf24',
                    500: '#f59e0b',
                    600: '#d97706',
                    700: '#b45309',
                    800: '#92400e',
                    900: '#78350f',
                    950: '#451a03',
                },

                // Danger/Error Colors
                danger: {
                    50: '#fef2f2',
                    100: '#fee2e2',
                    200: '#fecaca',
                    300: '#fca5a5',
                    400: '#f87171',
                    500: '#ef4444',
                    600: '#dc2626',
                    700: '#b91c1c',
                    800: '#991b1b',
                    900: '#7f1d1d',
                    950: '#450a0a',
                },

                // Info Colors
                info: {
                    50: '#eff6ff',
                    100: '#dbeafe',
                    200: '#bfdbfe',
                    300: '#93c5fd',
                    400: '#60a5fa',
                    500: '#3b82f6',
                    600: '#2563eb',
                    700: '#1d4ed8',
                    800: '#1e40af',
                    900: '#1e3a8a',
                    950: '#172554',
                },

                // Lavender Theme Colors
                lavender: {
                    50: '#faf5ff',
                    100: '#f3e8ff',
                    200: '#e9d5ff',
                    300: '#d8b4fe',
                    400: '#c084fc',
                    500: '#a855f7',
                    600: '#9333ea',
                    700: '#7c3aed',
                    800: '#6b21a8',
                    900: '#581c87',
                    950: '#3b0764',
                },

                // Sage Theme Colors
                sage: {
                    50: '#f7f9f7',
                    100: '#eff2ef',
                    200: '#dfe6df',
                    300: '#c8d7c8',
                    400: '#9fb99f',
                    500: '#7d9b7d',
                    600: '#647d64',
                    700: '#526652',
                    800: '#455445',
                    900: '#3c473c',
                    950: '#1a1f1a',
                },

                // Neutral Gray Scale
                gray: {
                    50: '#f9fafb',
                    100: '#f3f4f6',
                    200: '#e5e7eb',
                    300: '#d1d5db',
                    400: '#9ca3af',
                    500: '#6b7280',
                    600: '#4b5563',
                    700: '#374151',
                    800: '#1f2937',
                    900: '#111827',
                    950: '#030712',
                },
            },

            // Enhanced Spacing Scale
            spacing: {
                '18': '4.5rem',
                '88': '22rem',
                '112': '28rem',
                '128': '32rem',
            },

            // Modern Border Radius
            borderRadius: {
                '4xl': '2rem',
                '5xl': '2.5rem',
                '6xl': '3rem',
            },

            // Enhanced Shadows
            boxShadow: {
                'soft': '0 2px 15px -3px rgba(0, 0, 0, 0.07), 0 10px 20px -2px rgba(0, 0, 0, 0.04)',
                'medium': '0 4px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04)',
                'large': '0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04)',
                'xl': '0 25px 50px -12px rgba(0, 0, 0, 0.25)',
                'inner-soft': 'inset 0 2px 4px 0 rgba(0, 0, 0, 0.05)',
                'inner-medium': 'inset 0 4px 8px 0 rgba(0, 0, 0, 0.1)',
                'glow': '0 0 20px rgba(59, 130, 246, 0.15)',
                'glow-success': '0 0 20px rgba(34, 197, 94, 0.15)',
                'glow-warning': '0 0 20px rgba(245, 158, 11, 0.15)',
                'glow-danger': '0 0 20px rgba(239, 68, 68, 0.15)',
            },

            // Advanced Animations
            animation: {
                'fade-in': 'fadeIn 0.3s ease-in',
                'fade-out': 'fadeOut 0.3s ease-out',
                'slide-up': 'slideUp 0.3s ease-out',
                'slide-down': 'slideDown 0.3s ease-out',
                'slide-left': 'slideLeft 0.3s ease-out',
                'slide-right': 'slideRight 0.3s ease-out',
                'bounce-in': 'bounceIn 0.5s ease-out',
                'bounce-out': 'bounceOut 0.3s ease-in',
                'scale-in': 'scaleIn 0.2s ease-out',
                'scale-out': 'scaleOut 0.2s ease-in',
                'spin-slow': 'spin 3s linear infinite',
                'pulse-slow': 'pulse 3s cubic-bezier(0.4, 0, 0.6, 1) infinite',
                'shimmer': 'shimmer 2s linear infinite',
                'float': 'float 3s ease-in-out infinite',
                'heartbeat': 'heartbeat 1.5s ease-in-out infinite',
                'wiggle': 'wiggle 1s ease-in-out infinite',
            },

            keyframes: {
                fadeIn: {
                    '0%': { opacity: '0' },
                    '100%': { opacity: '1' },
                },
                fadeOut: {
                    '0%': { opacity: '1' },
                    '100%': { opacity: '0' },
                },
                slideUp: {
                    '0%': { transform: 'translateY(10px)', opacity: '0' },
                    '100%': { transform: 'translateY(0)', opacity: '1' },
                },
                slideDown: {
                    '0%': { transform: 'translateY(-10px)', opacity: '0' },
                    '100%': { transform: 'translateY(0)', opacity: '1' },
                },
                slideLeft: {
                    '0%': { transform: 'translateX(10px)', opacity: '0' },
                    '100%': { transform: 'translateX(0)', opacity: '1' },
                },
                slideRight: {
                    '0%': { transform: 'translateX(-10px)', opacity: '0' },
                    '100%': { transform: 'translateX(0)', opacity: '1' },
                },
                bounceIn: {
                    '0%': { transform: 'scale(0.3)', opacity: '0' },
                    '50%': { transform: 'scale(1.05)' },
                    '70%': { transform: 'scale(0.9)' },
                    '100%': { transform: 'scale(1)', opacity: '1' },
                },
                bounceOut: {
                    '0%': { transform: 'scale(1)', opacity: '1' },
                    '100%': { transform: 'scale(0.3)', opacity: '0' },
                },
                scaleIn: {
                    '0%': { transform: 'scale(0.95)', opacity: '0' },
                    '100%': { transform: 'scale(1)', opacity: '1' },
                },
                scaleOut: {
                    '0%': { transform: 'scale(1)', opacity: '1' },
                    '100%': { transform: 'scale(0.95)', opacity: '0' },
                },
                shimmer: {
                    '0%': { backgroundPosition: '-200% 0' },
                    '100%': { backgroundPosition: '200% 0' },
                },
                float: {
                    '0%, 100%': { transform: 'translateY(0px)' },
                    '50%': { transform: 'translateY(-6px)' },
                },
                heartbeat: {
                    '0%, 100%': { transform: 'scale(1)' },
                    '50%': { transform: 'scale(1.1)' },
                },
                wiggle: {
                    '0%, 100%': { transform: 'rotate(-3deg)' },
                    '50%': { transform: 'rotate(3deg)' },
                },
            },

            // Z-Index Utilities
            zIndex: {
                '60': '60',
                '70': '70',
                '80': '80',
                '90': '90',
                '100': '100',
            },

            // Enhanced Transitions
            transitionProperty: {
                'height': 'height',
                'spacing': 'margin, padding',
            },

            transitionDuration: {
                '0': '0ms',
                '400': '400ms',
                '600': '600ms',
            },

                transitionTimingFunction: {
                    'bounce-in': 'cubic-bezier(0.68, -0.55, 0.265, 1.55)',
                    'bounce-out': 'cubic-bezier(0.34, 1.56, 0.64, 1)',
                },

                // Scrollbar utilities
                scrollbar: {
                    'thin': 'thin',
                    'none': 'none',
            },
        },
    },

    plugins: [
        forms,
        // Scrollbar plugin for custom scrollbar styling
        function({ addUtilities }) {
            addUtilities({
                '.scrollbar-thin': {
                    'scrollbar-width': 'thin',
                },
                '.scrollbar-none': {
                    'scrollbar-width': 'none',
                },
                '.scrollbar-thumb-gray-300': {
                    'scrollbar-color': 'rgb(209 213 219) transparent',
                },
                '.scrollbar-thumb-gray-400': {
                    'scrollbar-color': 'rgb(156 163 175) transparent',
                },
                '.scrollbar-thumb-gray-500': {
                    'scrollbar-color': 'rgb(107 114 128) transparent',
                },
                '.scrollbar-thumb-gray-600': {
                    'scrollbar-color': 'rgb(75 85 99) transparent',
                },
                '.scrollbar-track-transparent': {
                    'scrollbar-color': 'transparent transparent',
                },
            });
        },
        // Custom plugins can be added here
    ],
};
