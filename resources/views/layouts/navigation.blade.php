<nav x-data="{ open: false, darkMode: localStorage.getItem('dark-mode') === 'true' }" class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 shadow-sm">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ auth()->check() ? route('dashboard') : route('home') }}" class="flex items-center space-x-2">
                        <div class="bg-blue-600 text-white p-2 rounded-lg">
                            <i class="fas fa-tasks text-lg"></i>
                        </div>
                        <span class="font-bold text-xl text-gray-900 dark:text-white">{{ config('app.name', 'TodoApp') }}</span>
                    </a>
                </div>

                <!-- Navigation Links -->
                @auth
                <div class="hidden space-x-1 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        <i class="fas fa-tachometer-alt mr-2"></i>
                        Dashboard
                    </x-nav-link>

                    @if(auth()->user()->isAdmin())
                    <x-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.*')">
                        <i class="fas fa-cog mr-2"></i>
                        Admin
                    </x-nav-link>
                    @endif
                </div>
                @endauth
            </div>

            <!-- Right Side Menu -->
            <div class="flex items-center space-x-4">
                <!-- Dark Mode Toggle -->
                <div class="relative">
                    <button @click="window.themeManager.toggle()"
                            class="group relative inline-flex h-10 w-16 items-center rounded-full bg-gray-200 dark:bg-gray-700 transition-colors focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 focus:ring-offset-white dark:focus:ring-offset-gray-800"
                            role="switch"
                            :aria-checked="window.themeManager.isDark()"
                            aria-label="Toggle dark mode">
                        <span class="sr-only">Toggle dark mode</span>
                        <span :class="window.themeManager.isDark() ? 'translate-x-6' : 'translate-x-1'"
                              class="inline-block h-8 w-8 transform rounded-full bg-white dark:bg-gray-200 shadow transition-transform duration-200 ease-in-out flex items-center justify-center">
                            <i class="fas text-sm" :class="window.themeManager.isDark() ? 'fa-moon text-gray-700' : 'fa-sun text-yellow-500'"></i>
                        </span>
                    </button>
                    <!-- Tooltip -->
                    <div class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-2 py-1 text-xs text-white bg-gray-900 dark:bg-gray-700 rounded opacity-0 group-hover:opacity-100 transition-opacity duration-200 pointer-events-none whitespace-nowrap z-10">
                        Toggle Theme (Ctrl+Shift+D)
                    </div>
                </div>

                @auth
                <!-- Settings Dropdown -->
                <div class="hidden sm:flex sm:items-center">
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button class="flex items-center space-x-2 px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors">
                                <div class="w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center text-white text-sm font-semibold">
                                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                </div>
                                <div class="hidden md:block text-left">
                                    <div class="text-sm font-medium">{{ Auth::user()->name }}</div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">{{ Auth::user()->email }}</div>
                                </div>
                                <svg class="w-4 h-4 ml-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            <div class="px-4 py-2 border-b border-gray-200 dark:border-gray-600">
                                <div class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ Auth::user()->name }}</div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">{{ Auth::user()->email }}</div>
                                <div class="text-xs text-gray-400 dark:text-gray-500 mt-1">
                                    Role: <span class="capitalize {{ auth()->user()->isAdmin() ? 'text-purple-600 dark:text-purple-400' : 'text-blue-600 dark:text-blue-400' }}">
                                        {{ auth()->user()->role }}
                                    </span>
                                </div>
                            </div>

                            <x-dropdown-link :href="route('profile.edit')">
                                <i class="fas fa-user mr-2"></i>
                                Profile
                            </x-dropdown-link>

                            @if(auth()->user()->isAdmin())
                            <x-dropdown-link :href="route('admin.dashboard')">
                                <i class="fas fa-cog mr-2"></i>
                                Admin Panel
                            </x-dropdown-link>
                            @endif

                            <!-- Authentication -->
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <x-dropdown-link :href="route('logout')"
                                        onclick="event.preventDefault();
                                                    this.closest('form').submit();">
                                    <i class="fas fa-sign-out-alt mr-2"></i>
                                    {{ __('Log Out') }}
                                </x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                </div>
                @else
                <!-- Guest Navigation -->
                <div class="hidden sm:flex sm:items-center sm:space-x-4">
                    <a href="{{ route('login') }}" class="text-gray-700 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 px-3 py-2 rounded-md text-sm font-medium transition-colors">
                        Login
                    </a>
                    <a href="{{ route('register') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                        Daftar
                    </a>
                </div>
                @endauth

                <!-- Hamburger Menu -->
                <div class="-me-2 flex items-center sm:hidden">
                    <button @click="open = !open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 dark:text-gray-500 hover:text-gray-500 dark:hover:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-900 focus:outline-none focus:bg-gray-100 dark:focus:bg-gray-900 focus:text-gray-500 dark:focus:text-gray-400 transition duration-150 ease-in-out">
                        <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                            <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700">
        @auth
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                <i class="fas fa-tachometer-alt mr-2"></i>
                Dashboard
            </x-responsive-nav-link>

            @if(auth()->user()->isAdmin())
            <x-responsive-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.*')">
                <i class="fas fa-cog mr-2"></i>
                Admin Panel
            </x-responsive-nav-link>
            @endif
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200 dark:border-gray-600">
            <div class="px-4 py-2">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-blue-600 rounded-full flex items-center justify-center text-white text-sm font-semibold">
                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                    </div>
                    <div>
                        <div class="font-medium text-base text-gray-800 dark:text-gray-200">{{ Auth::user()->name }}</div>
                        <div class="font-medium text-sm text-gray-500 dark:text-gray-400">{{ Auth::user()->email }}</div>
                    </div>
                </div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    <i class="fas fa-user mr-2"></i>
                    Profile
                </x-responsive-nav-link>

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        <i class="fas fa-sign-out-alt mr-2"></i>
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
        @else
        <div class="pt-2 pb-3 space-y-1">
            <a href="{{ route('home') }}" class="block px-4 py-2 text-base font-medium text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700">
                <i class="fas fa-home mr-2"></i>
                Beranda
            </a>
            <a href="{{ route('login') }}" class="block px-4 py-2 text-base font-medium text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700">
                <i class="fas fa-sign-in-alt mr-2"></i>
                Login
            </a>
            <a href="{{ route('register') }}" class="block px-4 py-2 text-base font-medium bg-blue-600 text-white hover:bg-blue-700">
                <i class="fas fa-user-plus mr-2"></i>
                Daftar
            </a>
        </div>
        @endauth
    </div>
</nav>
