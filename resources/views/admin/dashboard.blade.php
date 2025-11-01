<x-app-layout title="Admin Dashboard - {{ config('app.name', 'TodoApp') }}">
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900">
        <!-- Header -->
        <div class="bg-white dark:bg-gray-800 shadow-sm border-b border-gray-200 dark:border-gray-700">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
                            <i class="fas fa-cog mr-3 text-blue-600"></i>
                            Admin Dashboard
                        </h1>
                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                            Kelola sistem ToDo List dan monitor aktivitas pengguna
                        </p>
                    </div>
                    <div class="mt-4 lg:mt-0 flex items-center space-x-4">
                        <div class="text-right">
                            <p class="text-sm text-gray-600 dark:text-gray-400">Terakhir diperbarui:</p>
                            <p id="lastUpdated" class="text-sm font-medium text-gray-900 dark:text-white">Memuat...</p>
                            <div class="flex items-center mt-2">
                                <div id="statusIndicator" class="w-2 h-2 bg-green-400 rounded-full mr-2 animate-pulse"></div>
                                <span class="text-xs text-gray-500 dark:text-gray-400">Auto-refresh: 30s</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Content -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Welcome Admin -->
            <div class="bg-gradient-to-r from-blue-600 to-purple-600 rounded-2xl shadow-lg mb-8">
                <div class="p-8 text-white">
                    <div class="flex items-center space-x-4">
                        <div class="w-16 h-16 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
                            <i class="fas fa-user-shield text-2xl"></i>
                        </div>
                        <div>
                            <h2 class="text-2xl font-bold">Selamat datang, {{ Auth::user()->name }}!</h2>
                            <p class="text-blue-100 mt-1">Kelola sistem ToDo List dan monitor aktivitas pengguna</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8" id="statsContainer">
                <!-- Stats will be loaded via AJAX -->
            </div>

            <!-- Detailed Stats -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
                <!-- User Statistics -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-700 p-6">
                    <div class="flex items-center mb-6">
                        <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/30 rounded-xl flex items-center justify-center mr-3">
                            <i class="fas fa-users text-blue-600 dark:text-blue-400"></i>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">User Statistics</h3>
                    </div>
                    <div class="space-y-4" id="userStats">
                        <!-- User stats will be loaded via AJAX -->
                    </div>
                </div>

                <!-- Task Statistics -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-700 p-6">
                    <div class="flex items-center mb-6">
                        <div class="w-10 h-10 bg-green-100 dark:bg-green-900/30 rounded-xl flex items-center justify-center mr-3">
                            <i class="fas fa-chart-bar text-green-600 dark:text-green-400"></i>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Task Statistics</h3>
                    </div>
                    <div class="space-y-4" id="taskStats">
                        <!-- Task stats will be loaded via AJAX -->
                    </div>
                </div>
            </div>

            <!-- Most Used Categories -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-700 mb-8">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-purple-100 dark:bg-purple-900/30 rounded-xl flex items-center justify-center mr-3">
                            <i class="fas fa-trophy text-purple-600 dark:text-purple-400"></i>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Kategori Terpopuler</h3>
                    </div>
                </div>
                <div class="p-6">
                    <div id="mostUsedCategories" class="space-y-4">
                        <!-- Categories will be loaded via AJAX -->
                    </div>
                </div>
            </div>

            <!-- Recent Activities -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-700 mb-8">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-orange-100 dark:bg-orange-900/30 rounded-xl flex items-center justify-center mr-3">
                            <i class="fas fa-history text-orange-600 dark:text-orange-400"></i>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Aktivitas Terbaru</h3>
                    </div>
                </div>
                <div class="p-6">
                    <div id="recentActivities" class="space-y-4">
                        <!-- Activities will be loaded via AJAX -->
                    </div>
                </div>
            </div>

            <!-- User Management -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-700 mb-8">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
                        <div class="flex items-center mb-4 lg:mb-0">
                            <div class="w-10 h-10 bg-red-100 dark:bg-red-900/30 rounded-xl flex items-center justify-center mr-3">
                                <i class="fas fa-user-cog text-red-600 dark:text-red-400"></i>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Manajemen User</h3>
                        </div>
                        <div class="flex space-x-3">
                            <button id="exportUsers" class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-medium rounded-xl shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-200">
                                <i class="fas fa-file-csv mr-2"></i>
                                Export CSV
                            </button>
                            <button id="refreshUsers" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-xl shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-200">
                                <i class="fas fa-sync-alt mr-2"></i>
                                Refresh
                            </button>
                        </div>
                    </div>

                    <!-- User Search -->
                    <div class="mt-4">
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-search text-gray-400"></i>
                            </div>
                            <input type="text"
                                   id="userSearch"
                                   placeholder="Cari user berdasarkan nama atau email..."
                                   class="w-full pl-10 pr-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 transition-colors">
                        </div>
                    </div>
                </div>

                <!-- Users Table -->
                <div class="p-6">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">User</th>
                                    <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Role</th>
                                    <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Tasks</th>
                                    <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Bergabung</th>
                                    <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="usersTableBody" class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                <!-- Users will be loaded via AJAX -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Loading Indicator -->
            <div id="adminLoading" class="bg-white overflow-hidden shadow-sm sm:rounded-lg hidden">
                <div class="p-12 text-center">
                    <div class="inline-block animate-spin rounded-full h-12 w-12 border-b-2 border-blue-500"></div>
                    <p class="mt-4 text-gray-600">Memuat data admin...</p>
                </div>
            </div>

            <!-- Alert untuk notifikasi -->
            <div id="adminAlert" class="fixed top-20 right-4 z-50 hidden">
                <div class="bg-green-500 text-white px-6 py-3 rounded-md shadow-lg">
                    <span id="adminAlertMessage"></span>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Konfirmasi -->
    <div id="confirmModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden">
        <div class="flex items-center justify-center h-full p-4">
            <div class="bg-white rounded-lg shadow-xl max-w-md w-full">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4" id="confirmTitle">Konfirmasi</h3>
                    <p class="text-gray-600 mb-6" id="confirmMessage">Apakah Anda yakin?</p>
                    <div class="flex justify-end space-x-3">
                        <button id="confirmCancel" class="px-4 py-2 text-gray-600 bg-gray-200 rounded-md hover:bg-gray-300 transition">
                            Batal
                        </button>
                        <button id="confirmAction" class="px-4 py-2 bg-red-500 text-white rounded-md hover:bg-red-600 transition">
                            Ya, Lanjutkan
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- CSS untuk styling tambahan -->
    <style>
        .fade-in {
            animation: fadeIn 0.3s ease-in;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .category-bar {
            height: 20px;
            border-radius: 10px;
            transition: width 0.3s ease;
        }
    </style>

    <script>
        // Set CSRF token dan user ID globally
        window.csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        window.authUserId = {{ Auth::id() }};
    </script>
    <script src="{{ asset('js/admin-dashboard.js') }}"></script>
</x-app-layout>
