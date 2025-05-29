@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div x-data="dashboard" x-init="init()">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Dashboard</h1>
                <p class="mt-1 text-sm text-gray-600">
                    Manage and schedule your content across multiple platforms
                </p>
            </div>
            <div class="mt-4 sm:mt-0">
                <a href="{{ route('posts.create') }}"
                   class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Create Post
                </a>
            </div>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <template x-if="analytics">
            <template x-for="(value, key) in analytics.summary" :key="key">
                <div class="bg-white rounded-lg shadow-sm border p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600 capitalize" x-text="key.replace('_', ' ')"></p>
                            <p class="text-2xl font-bold text-gray-900" x-text="value"></p>
                        </div>
                        <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z"/>
                            </svg>
                        </div>
                    </div>
                </div>
            </template>
        </template>
    </div>

    <!-- Filters & View Toggle -->
    <div class="bg-white rounded-lg shadow-sm border p-4 mb-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-4 sm:space-y-0">
            <!-- Filters -->
            <div class="flex flex-wrap items-center gap-4">
                <!-- Status Filter -->
                <div>
                    <select x-model="filters.status"
                            @change="loadPosts()"
                            class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        <option value="all">All Status</option>
                        <option value="draft">Draft</option>
                        <option value="scheduled">Scheduled</option>
                        <option value="published">Published</option>
                    </select>
                </div>

                <!-- Date Filter -->
                <div>
                    <input type="date"
                           x-model="filters.date"
                           @change="loadPosts()"
                           class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                </div>

                <!-- Clear Filters -->
                <button @click="filters = { status: 'all', date: '', platform: 'all' }; loadPosts()"
                        class="px-3 py-2 text-sm text-gray-600 hover:text-gray-900">
                    Clear Filters
                </button>
            </div>

            <!-- View Toggle -->
            <div class="flex items-center bg-gray-100 rounded-lg p-1">
                <button @click="view = 'list'"
                        :class="view === 'list' ? 'bg-white shadow-sm' : ''"
                        class="px-3 py-1 text-sm rounded-md transition-all duration-200">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                    </svg>
                </button>
                <button @click="view = 'calendar'"
                        :class="view === 'calendar' ? 'bg-white shadow-sm' : ''"
                        class="px-3 py-1 text-sm rounded-md transition-all duration-200">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Loading State -->
    <div x-show="loading" class="flex justify-center py-12">
        <div class="spinner w-8 h-8"></div>
    </div>

    <!-- List View -->
    <div x-show="view === 'list' && !loading" class="space-y-4">
        <template x-if="posts.length === 0">
            <div class="text-center py-12">
                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No posts found</h3>
                <p class="text-gray-600 mb-6">Get started by creating your first post.</p>
                <a href="{{ route('posts.create') }}"
                   class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700">
                    Create Your First Post
                </a>
            </div>
        </template>

        <template x-for="post in posts" :key="post.id">
            <div class="post-card">
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <div class="flex items-center space-x-3 mb-2">
                            <h3 class="text-lg font-semibold text-gray-900" x-text="post.title"></h3>
                            <span class="status-badge"
                                  :class="getStatusColor(post.status)"
                                  x-text="post.status.charAt(0).toUpperCase() + post.status.slice(1)"></span>
                        </div>

                        <p class="text-gray-600 mb-3 line-clamp-2" x-text="post.content"></p>

                        <div class="flex items-center text-sm text-gray-500 space-x-4">
                            <span x-text="formatDate(post.scheduled_time)"></span>
                            <div class="flex items-center space-x-2">
                                <span>Platforms:</span>
                                <template x-for="platform in post.platforms" :key="platform.id">
                                    <span class="px-2 py-1 bg-gray-100 text-gray-700 rounded text-xs"
                                          x-text="platform.name"></span>
                                </template>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center space-x-2 ml-4">
                        <a :href="`/posts/${post.id}/edit`"
                           class="p-2 text-gray-400 hover:text-blue-600 rounded-lg hover:bg-blue-50">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                        </a>
                        <button @click="deletePost(post.id)"
                                class="p-2 text-gray-400 hover:text-red-600 rounded-lg hover:bg-red-50">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <template x-if="post.image_url">
                    <div class="mt-4">
                        <img :src="post.image_url"
                             :alt="post.title"
                             class="w-full h-48 object-cover rounded-lg">
                    </div>
                </template>
            </div>
        </template>
    </div>

    <!-- Calendar View -->
    <div x-show="view === 'calendar' && !loading"
         x-data="calendar()"
         x-init="generateCalendar()">

        <!-- Calendar Header -->
        <div class="bg-white rounded-lg shadow-sm border p-4 mb-6">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-semibold text-gray-900" x-text="currentMonthYear"></h2>
                <div class="flex items-center space-x-2">
                    <button @click="previousMonth()"
                            class="p-2 hover:bg-gray-100 rounded-lg">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                        </svg>
                    </button>
                    <button @click="nextMonth()"
                            class="p-2 hover:bg-gray-100 rounded-lg">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Calendar Grid -->
        <div class="bg-white rounded-lg shadow-sm border overflow-hidden">
            <!-- Days Header -->
            <div class="calendar-grid bg-gray-50 border-b">
                <template x-for="day in ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat']" :key="day">
                    <div class="p-3 text-center text-sm font-medium text-gray-700" x-text="day"></div>
                </template>
            </div>

            <!-- Calendar Days -->
            <div class="calendar-grid">
                <template x-for="day in calendarDays" :key="day.date">
                    <div class="calendar-day"
                         :class="{ 'other-month': !day.currentMonth }">
                        <div class="text-sm font-medium mb-1" x-text="day.day"></div>
                        <template x-for="post in getPostsForDate(day.date)" :key="post.id">
                            <div class="calendar-post"
                                 :class="getStatusColor(post.status)"
                                 :title="post.title">
                                <span x-text="post.title.substring(0, 20) + (post.title.length > 20 ? '...' : '')"></span>
                            </div>
                        </template>
                    </div>
                </template>
            </div>
        </div>
    </div>
</div>

<script>
function calendar() {
    return {
        currentDate: new Date(),
        calendarDays: [],

        get currentMonthYear() {
            return this.currentDate.toLocaleDateString('en-US', {
                month: 'long',
                year: 'numeric'
            });
        },

        generateCalendar() {
            const year = this.currentDate.getFullYear();
            const month = this.currentDate.getMonth();

            // First day of the month
            const firstDay = new Date(year, month, 1);
            // Last day of the month
            const lastDay = new Date(year, month + 1, 0);

            // Start from Sunday of the week containing the first day
            const startDate = new Date(firstDay);
            startDate.setDate(startDate.getDate() - startDate.getDay());

            this.calendarDays = [];
            let currentDay = new Date(startDate);

            // Generate 42 days (6 weeks)
            for (let i = 0; i < 42; i++) {
                this.calendarDays.push({
                    date: new Date(currentDay),
                    day: currentDay.getDate(),
                    currentMonth: currentDay.getMonth() === month
                });
                currentDay.setDate(currentDay.getDate() + 1);
            }
        },

        previousMonth() {
            this.currentDate.setMonth(this.currentDate.getMonth() - 1);
            this.generateCalendar();
        },

        nextMonth() {
            this.currentDate.setMonth(this.currentDate.getMonth() + 1);
            this.generateCalendar();
        },

        getPostsForDate(date) {
            return Alpine.store('dashboard').posts.filter(post => {
                const postDate = new Date(post.scheduled_time);
                return postDate.toDateString() === date.toDateString();
            });
        }
    }
}
</script>
@endsection
