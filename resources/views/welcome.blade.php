<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Aplikasi Manajemen Tugas Modern - Kelola tugas Anda dengan mudah dan efisien">
    <title>{{ config('app.name', 'TodoApp') }} - Kelola Tugas dengan Mudah</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="antialiased bg-gradient-to-br from-blue-50 via-white to-purple-50 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900">
    <div class="relative min-h-screen flex flex-col">
        <!-- Navigation -->
        <nav class="fixed top-0 left-0 right-0 z-50 bg-white/80 dark:bg-gray-900/80 backdrop-blur-lg border-b border-gray-200 dark:border-gray-700">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center h-16">
                    <div class="flex items-center space-x-2">
                        <div class="w-10 h-10 bg-gradient-to-br from-blue-600 to-purple-600 rounded-xl flex items-center justify-center">
                            <i class="fas fa-tasks text-white text-xl"></i>
                        </div>
                        <span class="text-2xl font-bold bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">
                            TodoApp
                        </span>
                    </div>
                    
                    <div class="flex items-center space-x-4">
                        @auth
                            <a href="{{ route('dashboard') }}" class="px-6 py-2.5 bg-gradient-to-r from-blue-600 to-purple-600 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-200">
                                <i class="fas fa-tachometer-alt mr-2"></i>
                                Dashboard
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="px-4 py-2 text-gray-700 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 font-medium transition-colors duration-200">
                                Masuk
                            </a>
                            <a href="{{ route('register') }}" class="px-6 py-2.5 bg-gradient-to-r from-blue-600 to-purple-600 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-200">
                                Daftar Gratis
                            </a>
                        @endauth
                    </div>
                </div>
            </div>
        </nav>

        <!-- Hero Section -->
        <div class="flex-1 flex items-center justify-center pt-16">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                    <!-- Left Content -->
                    <div class="space-y-8">
                        <div class="space-y-4">
                            <h1 class="text-5xl lg:text-6xl font-bold text-gray-900 dark:text-white leading-tight">
                                Kelola Tugas dengan
                                <span class="bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent block mt-2">
                                    Lebih Mudah & Efisien
                                </span>
                            </h1>
                            <p class="text-xl text-gray-600 dark:text-gray-400">
                                Aplikasi manajemen tugas modern yang membantu Anda tetap produktif dan terorganisir. Gratis selamanya!
                            </p>
                        </div>

                        <div class="flex flex-col sm:flex-row gap-4">
                            @guest
                                <a href="{{ route('register') }}" class="inline-flex items-center justify-center px-8 py-4 bg-gradient-to-r from-blue-600 to-purple-600 text-white font-bold rounded-xl shadow-xl hover:shadow-2xl transform hover:scale-105 transition-all duration-200">
                                    <i class="fas fa-rocket mr-2"></i>
                                    Mulai Sekarang
                                </a>
                                <a href="#features" class="inline-flex items-center justify-center px-8 py-4 bg-white dark:bg-gray-800 text-gray-900 dark:text-white font-semibold rounded-xl shadow-lg hover:shadow-xl border border-gray-300 dark:border-gray-600 transform hover:scale-105 transition-all duration-200">
                                    <i class="fas fa-info-circle mr-2"></i>
                                    Pelajari Lebih Lanjut
                                </a>
                            @else
                                <a href="{{ route('dashboard') }}" class="inline-flex items-center justify-center px-8 py-4 bg-gradient-to-r from-blue-600 to-purple-600 text-white font-bold rounded-xl shadow-xl hover:shadow-2xl transform hover:scale-105 transition-all duration-200">
                                    <i class="fas fa-tachometer-alt mr-2"></i>
                                    Buka Dashboard
                                </a>
                            @endguest
                        </div>

                        <!-- Stats -->
                        <div class="grid grid-cols-3 gap-6 pt-8">
                            <div class="text-center">
                                <div class="text-3xl font-bold text-blue-600 dark:text-blue-400">100%</div>
                                <div class="text-sm text-gray-600 dark:text-gray-400">Gratis</div>
                            </div>
                            <div class="text-center">
                                <div class="text-3xl font-bold text-purple-600 dark:text-purple-400">∞</div>
                                <div class="text-sm text-gray-600 dark:text-gray-400">Tugas</div>
                            </div>
                            <div class="text-center">
                                <div class="text-3xl font-bold text-green-600 dark:text-green-400">⚡</div>
                                <div class="text-sm text-gray-600 dark:text-gray-400">Cepat</div>
                            </div>
                        </div>
                    </div>

                    <!-- Right Content - Visual -->
                    <div class="relative">
                        <div class="relative">
                            <!-- Decorative Gradient Blob -->
                            <div class="absolute top-0 -left-4 w-72 h-72 bg-purple-300 rounded-full mix-blend-multiply filter blur-xl opacity-70 animate-pulse"></div>
                            <div class="absolute top-0 -right-4 w-72 h-72 bg-blue-300 rounded-full mix-blend-multiply filter blur-xl opacity-70 animate-pulse animation-delay-2000"></div>
                            
                            <!-- Mock App Preview -->
                            <div class="relative bg-white dark:bg-gray-800 rounded-2xl shadow-2xl p-6 space-y-4">
                                <div class="flex items-center justify-between pb-4 border-b border-gray-200 dark:border-gray-700">
                                    <h3 class="font-semibold text-gray-900 dark:text-white">Tugas Hari Ini</h3>
                                    <span class="px-3 py-1 bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 text-sm font-medium rounded-full">3 tugas</span>
                                </div>
                                
                                <div class="space-y-3">
                                    <div class="flex items-center gap-3 p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                                        <div class="w-5 h-5 rounded border-2 border-green-500"></div>
                                        <span class="flex-1 text-gray-700 dark:text-gray-300">Selesaikan laporan PKL</span>
                                        <span class="px-2 py-1 bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300 text-xs rounded">Selesai</span>
                                    </div>
                                    
                                    <div class="flex items-center gap-3 p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                                        <div class="w-5 h-5 rounded border-2 border-blue-500"></div>
                                        <span class="flex-1 text-gray-700 dark:text-gray-300">Review kode project</span>
                                        <span class="px-2 py-1 bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300 text-xs rounded">Progress</span>
                                    </div>
                                    
                                    <div class="flex items-center gap-3 p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                                        <div class="w-5 h-5 rounded border-2 border-gray-300 dark:border-gray-600"></div>
                                        <span class="flex-1 text-gray-700 dark:text-gray-300">Meeting tim pukul 14:00</span>
                                        <span class="px-2 py-1 bg-gray-100 dark:bg-gray-600 text-gray-800 dark:text-gray-300 text-xs rounded">Pending</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Features Section -->
        <div id="features" class="py-20 bg-white dark:bg-gray-800">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-16">
                    <h2 class="text-4xl font-bold text-gray-900 dark:text-white mb-4">
                        Fitur Unggulan
                    </h2>
                    <p class="text-xl text-gray-600 dark:text-gray-400">
                        Semua yang Anda butuhkan untuk produktivitas maksimal
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    <!-- Feature 1 -->
                    <div class="p-6 bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900/20 dark:to-blue-800/20 rounded-2xl border border-blue-200 dark:border-blue-800 hover:shadow-lg transition-shadow">
                        <div class="w-12 h-12 bg-blue-600 rounded-xl flex items-center justify-center mb-4">
                            <i class="fas fa-list-check text-white text-xl"></i>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">Manajemen Tugas</h3>
                        <p class="text-gray-600 dark:text-gray-400">Buat, edit, dan kelola tugas dengan mudah. Atur prioritas dan status sesuai kebutuhan Anda.</p>
                    </div>

                    <!-- Feature 2 -->
                    <div class="p-6 bg-gradient-to-br from-purple-50 to-purple-100 dark:from-purple-900/20 dark:to-purple-800/20 rounded-2xl border border-purple-200 dark:border-purple-800 hover:shadow-lg transition-shadow">
                        <div class="w-12 h-12 bg-purple-600 rounded-xl flex items-center justify-center mb-4">
                            <i class="fas fa-tags text-white text-xl"></i>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">Kategori Tugas</h3>
                        <p class="text-gray-600 dark:text-gray-400">Organisir tugas dengan kategori warna-warni. Work, Personal, Study - semuanya teratur!</p>
                    </div>

                    <!-- Feature 3 -->
                    <div class="p-6 bg-gradient-to-br from-green-50 to-green-100 dark:from-green-900/20 dark:to-green-800/20 rounded-2xl border border-green-200 dark:border-green-800 hover:shadow-lg transition-shadow">
                        <div class="w-12 h-12 bg-green-600 rounded-xl flex items-center justify-center mb-4">
                            <i class="fas fa-chart-line text-white text-xl"></i>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">Statistik Progress</h3>
                        <p class="text-gray-600 dark:text-gray-400">Pantau produktivitas Anda dengan visualisasi data yang menarik dan informatif.</p>
                    </div>

                    <!-- Feature 4 -->
                    <div class="p-6 bg-gradient-to-br from-orange-50 to-orange-100 dark:from-orange-900/20 dark:to-orange-800/20 rounded-2xl border border-orange-200 dark:border-orange-800 hover:shadow-lg transition-shadow">
                        <div class="w-12 h-12 bg-orange-600 rounded-xl flex items-center justify-center mb-4">
                            <i class="fas fa-moon text-white text-xl"></i>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">Dark Mode</h3>
                        <p class="text-gray-600 dark:text-gray-400">Nyaman untuk mata di kondisi cahaya apapun. Toggle otomatis atau manual.</p>
                    </div>

                    <!-- Feature 5 -->
                    <div class="p-6 bg-gradient-to-br from-pink-50 to-pink-100 dark:from-pink-900/20 dark:to-pink-800/20 rounded-2xl border border-pink-200 dark:border-pink-800 hover:shadow-lg transition-shadow">
                        <div class="w-12 h-12 bg-pink-600 rounded-xl flex items-center justify-center mb-4">
                            <i class="fas fa-mobile-alt text-white text-xl"></i>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">Responsive Design</h3>
                        <p class="text-gray-600 dark:text-gray-400">Akses dari desktop, tablet, atau smartphone. Interface yang sempurna di semua device.</p>
                    </div>

                    <!-- Feature 6 -->
                    <div class="p-6 bg-gradient-to-br from-indigo-50 to-indigo-100 dark:from-indigo-900/20 dark:to-indigo-800/20 rounded-2xl border border-indigo-200 dark:border-indigo-800 hover:shadow-lg transition-shadow">
                        <div class="w-12 h-12 bg-indigo-600 rounded-xl flex items-center justify-center mb-4">
                            <i class="fas fa-shield-alt text-white text-xl"></i>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">Aman & Private</h3>
                        <p class="text-gray-600 dark:text-gray-400">Data Anda terenkripsi dan aman. Hanya Anda yang bisa mengakses tugas pribadi Anda.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <footer class="py-8 bg-gray-50 dark:bg-gray-900 border-t border-gray-200 dark:border-gray-700">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center text-gray-600 dark:text-gray-400">
                    <p>&copy; {{ date('Y') }} TodoApp. Dibuat dengan ❤️ oleh Syhdn selama PKL di PT Pratama Solusi Teknologi.</p>
                    <p class="mt-2 text-sm">Laravel 11 • Tailwind CSS • Alpine.js</p>
                </div>
            </div>
        </footer>
    </div>
</body>
</html>
