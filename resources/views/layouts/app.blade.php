<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="description" content="Aplikasi Manajemen Tugas - Kelola tugas Anda dengan mudah dan efisien">

        <title>{{ isset($title) ? $title . ' - ' . config('app.name', 'Laravel') : config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <!-- Additional Styles for Icons -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

        <!-- Custom Theme Variables -->
        <style>
            :root {
                --theme-transition: background-color 0.3s ease, color 0.3s ease, border-color 0.3s ease;
            }

            * {
                transition: var(--theme-transition);
            }

            /* Smooth transitions for theme changes */
            .theme-transition {
                transition: background-color 0.3s ease, color 0.3s ease, border-color 0.3s ease, box-shadow 0.3s ease;
            }

            /* Custom scrollbar for dark mode */
            .dark ::-webkit-scrollbar {
                width: 8px;
            }

            .dark ::-webkit-scrollbar-track {
                background: #374151;
            }

            .dark ::-webkit-scrollbar-thumb {
                background: #6b7280;
                border-radius: 4px;
            }

            .dark ::-webkit-scrollbar-thumb:hover {
                background: #9ca3af;
            }
        </style>
    </head>
    <body class="font-sans antialiased bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100 theme-transition">
        <!-- Navigation -->
        @include('layouts.navigation')

        <!-- Page Content -->
        <main class="min-h-screen">
            {{ $slot }}
        </main>

        <!-- Footer -->
        @include('layouts.footer')

        <!-- Global Scripts -->
        <script>
            // CSRF Token setup for AJAX requests
            window.Laravel = {
                csrfToken: '{{ csrf_token() }}'
            };

            // Advanced Dark Mode System
            class ThemeManager {
                constructor() {
                    this.darkMode = this.getInitialTheme();
                    this.init();
                }

                init() {
                    this.applyTheme();
                    this.setupSystemThemeListener();
                    this.setupAlpineIntegration();
                    this.setupKeyboardShortcut();
                }

                getInitialTheme() {
                    const saved = localStorage.getItem('theme');
                    if (saved) {
                        return saved === 'dark';
                    }
                    return window.matchMedia('(prefers-color-scheme: dark)').matches;
                }

                applyTheme() {
                    const root = document.documentElement;
                    if (this.darkMode) {
                        root.classList.add('dark');
                        root.classList.remove('light');
                    } else {
                        root.classList.add('light');
                        root.classList.remove('dark');
                    }
                    localStorage.setItem('theme', this.darkMode ? 'dark' : 'light');

                    // Update meta theme-color for mobile browsers
                    this.updateMetaThemeColor();
                }

                updateMetaThemeColor() {
                    const metaThemeColor = document.querySelector('meta[name="theme-color"]');
                    if (metaThemeColor) {
                        metaThemeColor.setAttribute('content', this.darkMode ? '#1f2937' : '#ffffff');
                    }
                }

                toggle() {
                    this.darkMode = !this.darkMode;
                    this.applyTheme();
                    this.dispatchThemeChange();
                    this.showToggleFeedback();
                }

                setTheme(theme) {
                    this.darkMode = theme === 'dark';
                    this.applyTheme();
                    this.dispatchThemeChange();
                }

                setupSystemThemeListener() {
                    window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', (e) => {
                        if (!localStorage.getItem('theme')) {
                            this.darkMode = e.matches;
                            this.applyTheme();
                            this.dispatchThemeChange();
                        }
                    });
                }

                setupAlpineIntegration() {
                    window.themeManager = this;
                }

                setupKeyboardShortcut() {
                    document.addEventListener('keydown', (e) => {
                        // Ctrl/Cmd + Shift + D for dark mode toggle
                        if ((e.ctrlKey || e.metaKey) && e.shiftKey && e.key === 'D') {
                            e.preventDefault();
                            this.toggle();
                        }
                    });
                }

                dispatchThemeChange() {
                    window.dispatchEvent(new CustomEvent('theme-change', {
                        detail: { darkMode: this.darkMode, theme: this.darkMode ? 'dark' : 'light' }
                    }));
                }

                showToggleFeedback() {
                    // Create a subtle feedback animation
                    const feedback = document.createElement('div');
                    feedback.className = 'fixed top-4 right-4 z-50 pointer-events-none animate-bounce-in';
                    feedback.innerHTML = `
                        <div class="bg-gray-800 dark:bg-gray-200 text-white dark:text-gray-800 px-4 py-2 rounded-lg shadow-lg transform transition-all duration-300">
                            ${this.darkMode ? '🌙 Dark mode' : '☀️ Light mode'}
                        </div>
                    `;
                    document.body.appendChild(feedback);

                    setTimeout(() => {
                        feedback.querySelector('div').classList.add('translate-x-full');
                        setTimeout(() => feedback.remove(), 300);
                    }, 2000);
                }

                isDark() {
                    return this.darkMode;
                }

                getCurrentTheme() {
                    return this.darkMode ? 'dark' : 'light';
                }
            }

            // Initialize theme manager
            const themeManager = new ThemeManager();

            // Legacy support for old dark mode code
            window.toggleDarkMode = () => themeManager.toggle();
        </script>
    </body>
</html>
