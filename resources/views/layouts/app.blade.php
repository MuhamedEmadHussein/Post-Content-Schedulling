<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Content Scheduler') }} - @yield('title', 'Dashboard')</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700" rel="stylesheet" />

    <!-- Scripts & Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 font-sans antialiased" x-data="{
    user: null,
    loading: true,
    async init() {
        // Check if we're on the login page
        if (window.location.pathname === '{{ route('login') }}') {
            return;
        }

        const token = localStorage.getItem('auth_token');
        if (!token) {
            window.location.href = '{{ route('login') }}';
            return;
        }

        try {
            const response = await fetch('{{ url('api/user') }}', {
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json'
                }
            });

            if (response.ok) {
                const data = await response.json();
                this.user = data;
            } else if (response.status === 401) {
                // Only redirect on unauthorized response
                localStorage.removeItem('auth_token');
                window.location.href = '{{ route('login') }}';
            }
        } catch (error) {
            console.error('Auth error:', error);
            // Only redirect on network errors
            if (error.name === 'TypeError') {
                localStorage.removeItem('auth_token');
                window.location.href = '{{ route('login') }}';
            }
        } finally {
            this.loading = false;
        }
    },
    async logout() {
        try {
            const token = localStorage.getItem('auth_token');
            if (token) {
                await fetch('{{ url('api/logout') }}', {
                    method: 'POST',
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
                    }
                });
            }
        } catch (error) {
            console.error('Logout error:', error);
        } finally {
            localStorage.removeItem('auth_token');
            window.location.href = '{{ route('login') }}';
        }
    }
}">
    <div class="min-h-screen">
        <!-- Navigation -->
        <nav class="bg-white shadow-sm border-b border-gray-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center h-16">
                    <!-- Logo & Brand -->
                    <div class="flex items-center">
                        <a href="{{ route('dashboard') }}" class="flex items-center space-x-2">
                            <div class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <span class="text-xl font-semibold text-gray-900">ContentScheduler</span>
                        </a>
                    </div>

                    <!-- Navigation Links -->
                    <div class="hidden md:flex items-center space-x-8">
                        <a href="{{ route('dashboard') }}"
                           class="text-gray-600 hover:text-gray-900 {{ request()->routeIs('dashboard') ? 'text-blue-600 font-medium' : '' }}">
                            Dashboard
                        </a>
                        <a href="{{ route('posts.create') }}"
                           class="text-gray-600 hover:text-gray-900 {{ request()->routeIs('posts.create') ? 'text-blue-600 font-medium' : '' }}">
                            Create Post
                        </a>
                        <a href="{{ route('analytics') }}"
                           class="text-gray-600 hover:text-gray-900 {{ request()->routeIs('analytics') ? 'text-blue-600 font-medium' : '' }}">
                            Analytics
                        </a>
                        <a href="{{ route('settings') }}"
                           class="text-gray-600 hover:text-gray-900 {{ request()->routeIs('settings') ? 'text-blue-600 font-medium' : '' }}">
                            Settings
                        </a>
                    </div>

                    <!-- User Menu -->
                    <div class="flex items-center space-x-4">
                        <!-- User Info -->
                        <template x-if="user">
                            <div class="flex items-center space-x-3">
                                <span class="text-sm text-gray-700" x-text="user.name"></span>
                                <div class="relative" x-data="{ open: false }">
                                    <button @click="open = !open"
                                            class="flex items-center text-sm rounded-full focus:outline-none focus:ring-2 focus:ring-blue-500">
                                        <div class="w-8 h-8 bg-gray-300 rounded-full flex items-center justify-center">
                                            <span class="text-sm font-medium text-gray-700"
                                                  x-text="user.name ? user.name.charAt(0).toUpperCase() : '?'"></span>
                                        </div>
                                    </button>

                                    <!-- Dropdown Menu -->
                                    <div x-show="open"
                                         @click.away="open = false"
                                         x-transition:enter="transition ease-out duration-100"
                                         x-transition:enter-start="transform opacity-0 scale-95"
                                         x-transition:enter-end="transform opacity-100 scale-100"
                                         x-transition:leave="transition ease-in duration-75"
                                         x-transition:leave-start="transform opacity-100 scale-100"
                                         x-transition:leave-end="transform opacity-0 scale-95"
                                         class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50">
                                        <a href="{{ route('profile') }}"
                                           class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                            Profile
                                        </a>
                                        <a href="{{ route('activity-logs') }}"
                                           class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                            Activity Log
                                        </a>
                                        <button @click="logout()"
                                                class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                            Sign out
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>

                    <!-- Mobile menu button -->
                    <div class="md:hidden">
                        <button @click="mobileMenuOpen = !mobileMenuOpen"
                                class="text-gray-500 hover:text-gray-700"
                                x-data="{ mobileMenuOpen: false }">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M4 6h16M4 12h16M4 18h16"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Mobile Navigation Menu -->
                <div x-show="mobileMenuOpen"
                     x-data="{ mobileMenuOpen: false }"
                     class="md:hidden border-t border-gray-200 py-4">
                    <div class="space-y-1">
                        <a href="{{ route('dashboard') }}"
                           class="block px-4 py-2 text-gray-600 hover:text-gray-900 hover:bg-gray-50">
                            Dashboard
                        </a>
                        <a href="{{ route('posts.create') }}"
                           class="block px-4 py-2 text-gray-600 hover:text-gray-900 hover:bg-gray-50">
                            Create Post
                        </a>
                        <a href="{{ route('analytics') }}"
                           class="block px-4 py-2 text-gray-600 hover:text-gray-900 hover:bg-gray-50">
                            Analytics
                        </a>
                        <a href="{{ route('settings') }}"
                           class="block px-4 py-2 text-gray-600 hover:text-gray-900 hover:bg-gray-50">
                            Settings
                        </a>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Page Content -->
        <main class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            @yield('content')
        </main>

        <!-- Footer -->
        <footer class="bg-white border-t border-gray-200 mt-auto">
            <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center">
                    <p class="text-sm text-gray-500">
                        © {{ date('Y') }} ContentScheduler. Built with Laravel & Alpine.js
                    </p>
                    <div class="flex space-x-4">
                        <a href="#" class="text-sm text-gray-500 hover:text-gray-700">Privacy</a>
                        <a href="#" class="text-sm text-gray-500 hover:text-gray-700">Terms</a>
                        <a href="#" class="text-sm text-gray-500 hover:text-gray-700">Support</a>
                    </div>
                </div>
            </div>
        </footer>
    </div>

    <!-- Toast Notifications -->
    <div x-data="{
        notifications: [],
        addNotification(message, type = 'success') {
            const id = Date.now();
            this.notifications.push({ id, message, type });
            setTimeout(() => this.removeNotification(id), 5000);
        },
        removeNotification(id) {
            this.notifications = this.notifications.filter(n => n.id !== id);
        }
    }"
    x-on:notify.window="addNotification($event.detail.message, $event.detail.type)"
    class="fixed top-4 right-4 z-50 space-y-2">
        <template x-for="notification in notifications" :key="notification.id">
            <div x-show="true"
                 x-transition:enter="transform ease-out duration-300 transition"
                 x-transition:enter-start="translate-x-full opacity-0"
                 x-transition:enter-end="translate-x-0 opacity-100"
                 x-transition:leave="transition ease-in duration-100"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 :class="{
                     'bg-green-50 border-green-200 text-green-800': notification.type === 'success',
                     'bg-red-50 border-red-200 text-red-800': notification.type === 'error',
                     'bg-yellow-50 border-yellow-200 text-yellow-800': notification.type === 'warning',
                     'bg-blue-50 border-blue-200 text-blue-800': notification.type === 'info'
                 }"
                 class="max-w-sm w-full border rounded-lg p-4 shadow-lg">
                <div class="flex items-center justify-between">
                    <p class="text-sm font-medium" x-text="notification.message"></p>
                    <button @click="removeNotification(notification.id)"
                            class="ml-4 text-gray-400 hover:text-gray-600">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>
        </template>
    </div>
</body>
</html>
