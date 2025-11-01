<x-app-layout title="Terjadi Kesalahan Server - {{ config('app.name', 'TodoApp') }}">
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900 flex items-center justify-center px-4">
        <div class="max-w-md w-full text-center">
            <!-- Error Illustration -->
            <div class="mb-8">
                <div class="relative">
                    <div class="w-32 h-32 mx-auto bg-gradient-to-br from-red-100 to-orange-100 dark:from-red-900/30 dark:to-orange-900/30 rounded-full flex items-center justify-center">
                        <svg class="w-16 h-16 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                        </svg>
                    </div>
                    <div class="absolute -top-2 -right-2 w-8 h-8 bg-yellow-500 rounded-full flex items-center justify-center animate-pulse">
                        <span class="text-xs font-bold text-white">!</span>
                    </div>
                </div>
            </div>

            <!-- Error Message -->
            <div class="mb-8">
                <h1 class="text-6xl font-bold text-gray-900 dark:text-white mb-4">500</h1>
                <h2 class="text-2xl font-semibold text-gray-700 dark:text-gray-300 mb-4">Terjadi Kesalahan Server</h2>
                <p class="text-gray-600 dark:text-gray-400 leading-relaxed">
                    Maaf, terjadi kesalahan pada server kami. Tim kami telah diberitahu dan sedang memperbaikinya.
                </p>
            </div>

            <!-- Action Buttons -->
            <div class="space-y-4">
                <button onclick="window.location.reload()"
                        class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-200">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    Coba Lagi
                </button>

                <a href="{{ route('dashboard') }}"
                   class="inline-flex items-center px-6 py-3 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 font-semibold rounded-xl transition-all duration-200">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                    Kembali ke Dashboard
                </a>
            </div>

            <!-- Error Details (Development Only) -->
            @if(config('app.debug'))
            <div class="mt-8 p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg text-left">
                <h3 class="text-sm font-medium text-red-800 dark:text-red-200 mb-2">Debug Information</h3>
                <div class="text-xs text-red-700 dark:text-red-300 font-mono">
                    <div><strong>File:</strong> {{ $exception->getFile() }}:{{ $exception->getLine() }}</div>
                    <div><strong>Message:</strong> {{ $exception->getMessage() }}</div>
                </div>
            </div>
            @endif

            <!-- Additional Help -->
            <div class="mt-12 pt-8 border-t border-gray-200 dark:border-gray-700">
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    Jika masalah berlanjut, silakan
                    <a href="mailto:support@todolist.com" class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 font-medium">
                        hubungi dukungan
                    </a>
                </p>
            </div>
        </div>
    </div>
</x-app-layout>
