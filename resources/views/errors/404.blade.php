<x-app-layout title="Halaman Tidak Ditemukan - {{ config('app.name', 'TodoApp') }}">
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900 flex items-center justify-center px-4">
        <div class="max-w-md w-full text-center">
            <!-- Error Illustration -->
            <div class="mb-8">
                <div class="relative">
                    <div class="w-32 h-32 mx-auto bg-gradient-to-br from-blue-100 to-purple-100 dark:from-blue-900/30 dark:to-purple-900/30 rounded-full flex items-center justify-center">
                        <svg class="w-16 h-16 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 12h6m-6-4h6m2 5.291A7.962 7.962 0 0112 15c-2.34 0-4.29-.882-5.657-2.343A7.975 7.975 0 014 12c0-2.34.882-4.29 2.343-5.657A7.975 7.975 0 0112 4c2.34 0 4.29.882 5.657 2.343A7.975 7.975 0 0120 12c0 1.994-.764 3.81-2.019 5.166"/>
                        </svg>
                    </div>
                    <div class="absolute -top-2 -right-2 w-8 h-8 bg-red-500 rounded-full flex items-center justify-center animate-bounce">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Error Message -->
            <div class="mb-8">
                <h1 class="text-6xl font-bold text-gray-900 dark:text-white mb-4">404</h1>
                <h2 class="text-2xl font-semibold text-gray-700 dark:text-gray-300 mb-4">Halaman Tidak Ditemukan</h2>
                <p class="text-gray-600 dark:text-gray-400 leading-relaxed">
                    Maaf, halaman yang Anda cari tidak dapat ditemukan. Mungkin halaman tersebut telah dipindahkan atau dihapus.
                </p>
            </div>

            <!-- Action Buttons -->
            <div class="space-y-4">
                <a href="{{ route('dashboard') }}"
                   class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-200">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                    Kembali ke Dashboard
                </a>

                <button onclick="history.back()"
                        class="inline-flex items-center px-6 py-3 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 font-semibold rounded-xl transition-all duration-200">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                    Kembali
                </button>
            </div>

            <!-- Additional Help -->
            <div class="mt-12 pt-8 border-t border-gray-200 dark:border-gray-700">
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    Jika Anda merasa ini adalah kesalahan, silakan
                    <a href="mailto:support@todolist.com" class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 font-medium">
                        hubungi dukungan
                    </a>
                </p>
            </div>
        </div>
    </div>
</x-app-layout>
