<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Authentication') - {{ config('app.name', 'Content Scheduler') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700" rel="stylesheet" />

    <!-- Scripts & Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gradient-to-br from-blue-50 to-indigo-100 font-sans antialiased">
    <div class="min-h-screen flex flex-col justify-center py-12 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="sm:mx-auto sm:w-full sm:max-w-md">
            <div class="flex justify-center">
                <div class="w-12 h-12 bg-blue-600 rounded-lg flex items-center justify-center">
                    <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
            <h1 class="mt-6 text-center text-3xl font-bold text-gray-900">
                ContentScheduler
            </h1>
            <p class="mt-2 text-center text-sm text-gray-600">
                @yield('subtitle', 'Schedule your content across multiple platforms')
            </p>
        </div>

        <!-- Main Content -->
        <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
            <div class="bg-white py-8 px-4 shadow-xl rounded-lg sm:px-10">
                @yield('content')
            </div>

            <!-- Additional Links -->
            <div class="mt-6">
                @yield('links')
            </div>
        </div>

        <!-- Footer -->
        <div class="mt-8 text-center">
            <p class="text-sm text-gray-500">
                © {{ date('Y') }} ContentScheduler. All rights reserved.
            </p>
        </div>
    </div>

    <!-- Toast Notifications for Auth -->
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
