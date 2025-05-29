@extends('layouts.app')

@section('title', 'Settings')

@section('content')
<div x-data="settings" x-init="init()">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900">Settings</h1>
        <p class="mt-1 text-sm text-gray-600">
            Manage your platforms and account preferences
        </p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Platform Management -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow-sm border p-6">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900">Connected Platforms</h2>
                        <p class="text-sm text-gray-600">
                            Select the platforms where you want to publish your content
                        </p>
                    </div>
                </div>

                <!-- Loading State -->
                <div x-show="loading" class="flex justify-center py-8">
                    <div class="spinner w-8 h-8"></div>
                </div>

                <!-- Platform List -->
                <div x-show="!loading" class="space-y-4">
                    <template x-for="platform in availablePlatforms" :key="platform.id">
                        <div class="flex items-center justify-between p-4 border rounded-lg hover:bg-gray-50 transition-colors">
                            <div class="flex items-center space-x-4">
                                <!-- Platform Icon -->
                                <div class="w-10 h-10">
                                    <template x-if="platform.type === 'twitter'">
                                        <div class="w-10 h-10 bg-blue-400 rounded-full flex items-center justify-center">
                                            <span class="text-white text-lg font-bold">T</span>
                                        </div>
                                    </template>
                                    <template x-if="platform.type === 'instagram'">
                                        <div class="w-10 h-10 bg-gradient-to-r from-purple-400 to-pink-400 rounded-full flex items-center justify-center">
                                            <span class="text-white text-lg font-bold">I</span>
                                        </div>
                                    </template>
                                    <template x-if="platform.type === 'linkedin'">
                                        <div class="w-10 h-10 bg-blue-600 rounded-full flex items-center justify-center">
                                            <span class="text-white text-lg font-bold">L</span>
                                        </div>
                                    </template>
                                    <template x-if="platform.type === 'facebook'">
                                        <div class="w-10 h-10 bg-blue-800 rounded-full flex items-center justify-center">
                                            <span class="text-white text-lg font-bold">F</span>
                                        </div>
                                    </template>
                                </div>

                                <div>
                                    <h3 class="font-medium text-gray-900" x-text="platform.name"></h3>
                                    <p class="text-sm text-gray-500 capitalize" x-text="platform.type"></p>
                                </div>
                            </div>

                            <!-- Toggle Switch -->
                            <div class="flex items-center space-x-3">
                                <span class="text-sm text-gray-500"
                                      x-text="userPlatforms.includes(platform.id) ? 'Connected' : 'Disconnected'"></span>

                                <button type="button"
                                        @click="togglePlatform(platform.id)"
                                        :class="{
                                            'bg-blue-600': userPlatforms.includes(platform.id),
                                            'bg-gray-200': !userPlatforms.includes(platform.id)
                                        }"
                                        class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                                    <span :class="{
                                              'translate-x-5': userPlatforms.includes(platform.id),
                                              'translate-x-0': !userPlatforms.includes(platform.id)
                                          }"
                                          class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"></span>
                                </button>
                            </div>
                        </div>
                    </template>

                    <!-- Save Button -->
                    <div class="pt-4 border-t">
                        <button @click="savePlatforms"
                                :disabled="saving"
                                :class="{ 'opacity-50 cursor-not-allowed': saving }"
                                class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                            <span x-show="!saving">Save Platform Settings</span>
                            <span x-show="saving" class="flex items-center">
                                <div class="spinner mr-2"></div>
                                Saving...
                            </span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Platform Information -->
            <div class="mt-8 bg-blue-50 border border-blue-200 rounded-lg p-6">
                <h3 class="text-lg font-medium text-blue-900 mb-4">Platform Information</h3>
                <div class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="bg-white rounded p-4">
                            <h4 class="font-medium text-gray-900 mb-2">Twitter</h4>
                            <p class="text-sm text-gray-600 mb-2">Character limit: 280</p>
                            <p class="text-xs text-gray-500">Best for short updates, news, and real-time engagement.</p>
                        </div>
                        <div class="bg-white rounded p-4">
                            <h4 class="font-medium text-gray-900 mb-2">Instagram</h4>
                            <p class="text-sm text-gray-600 mb-2">Character limit: 2,200</p>
                            <p class="text-xs text-gray-500">Perfect for visual content with longer captions.</p>
                        </div>
                        <div class="bg-white rounded p-4">
                            <h4 class="font-medium text-gray-900 mb-2">LinkedIn</h4>
                            <p class="text-sm text-gray-600 mb-2">Character limit: 3,000</p>
                            <p class="text-xs text-gray-500">Ideal for professional content and thought leadership.</p>
                        </div>
                        <div class="bg-white rounded p-4">
                            <h4 class="font-medium text-gray-900 mb-2">Facebook</h4>
                            <p class="text-sm text-gray-600 mb-2">Character limit: 63,206</p>
                            <p class="text-xs text-gray-500">Great for longer form content and community building.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Account Information -->
            <div class="bg-white rounded-lg shadow-sm border p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Account Information</h2>

                <template x-if="$store.auth.user">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Name</label>
                            <p class="mt-1 text-sm text-gray-900" x-text="$store.auth.user.name"></p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Email</label>
                            <p class="mt-1 text-sm text-gray-900" x-text="$store.auth.user.email"></p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Member Since</label>
                            <p class="mt-1 text-sm text-gray-900"
                               x-text="new Date($store.auth.user.created_at).toLocaleDateString()"></p>
                        </div>
                    </div>
                </template>

                <div class="mt-6 pt-4 border-t">
                    <a href="{{ route('profile') }}"
                       class="text-sm text-blue-600 hover:text-blue-500">
                        Edit Profile →
                    </a>
                </div>
            </div>

            <!-- Usage Statistics -->
            <div class="bg-white rounded-lg shadow-sm border p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Usage Stats</h2>

                <div class="space-y-4">
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Posts Today</span>
                        <span class="text-sm font-medium text-gray-900">3 / 10</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-blue-600 h-2 rounded-full" style="width: 30%"></div>
                    </div>

                    <div class="text-xs text-gray-500 mt-2">
                        You have 7 posts remaining today. Rate limiting helps maintain quality content.
                    </div>
                </div>

                <div class="mt-6 pt-4 border-t space-y-3">
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600">Total Posts</span>
                        <span class="text-sm font-medium text-gray-900">47</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600">Scheduled</span>
                        <span class="text-sm font-medium text-gray-900">12</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600">Published</span>
                        <span class="text-sm font-medium text-gray-900">35</span>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white rounded-lg shadow-sm border p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h2>

                <div class="space-y-3">
                    <a href="{{ route('posts.create') }}"
                       class="block w-full text-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700">
                        Create New Post
                    </a>

                    <a href="{{ route('analytics') }}"
                       class="block w-full text-center px-4 py-2 border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50">
                        View Analytics
                    </a>

                    <a href="{{ route('activity-logs') }}"
                       class="block w-full text-center px-4 py-2 border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50">
                        Activity Log
                    </a>
                </div>
            </div>

            <!-- Danger Zone -->
            <div class="bg-red-50 border border-red-200 rounded-lg p-6">
                <h2 class="text-lg font-semibold text-red-900 mb-4">Danger Zone</h2>

                <div class="space-y-4">
                    <div>
                        <h3 class="text-sm font-medium text-red-900">Clear All Scheduled Posts</h3>
                        <p class="text-sm text-red-700 mt-1">
                            This will remove all scheduled posts from your queue.
                        </p>
                        <button class="mt-2 px-3 py-1 bg-red-600 text-white text-sm rounded hover:bg-red-700">
                            Clear Scheduled Posts
                        </button>
                    </div>

                    <div class="pt-4 border-t border-red-200">
                        <h3 class="text-sm font-medium text-red-900">Delete Account</h3>
                        <p class="text-sm text-red-700 mt-1">
                            Permanently delete your account and all associated data.
                        </p>
                        <button class="mt-2 px-3 py-1 bg-red-600 text-white text-sm rounded hover:bg-red-700">
                            Delete Account
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
