@extends('layouts.app')

@section('title', 'Activity Log')

@section('content')
<div x-data="{
    activities: [],
    loading: true,
    searchTerm: '',
    typeFilter: 'all',
    dateFilter: '',
    pagination: {
        current_page: 1,
        last_page: 1,
        per_page: 20,
        total: 0
    },

    async init() {
        await this.loadActivities();
    },

    async loadActivities(page = 1) {
        this.loading = true;
        try {
            const params = new URLSearchParams({
                page: page,
                search: this.searchTerm,
                type: this.typeFilter !== 'all' ? this.typeFilter : '',
                date: this.dateFilter
            });

            const response = await window.axios.get(`/api/activity-logs?${params}`);
            this.activities = response.data.data;
            this.pagination = {
                current_page: response.data.current_page,
                last_page: response.data.last_page,
                per_page: response.data.per_page,
                total: response.data.total
            };
        } catch (error) {
            console.error('Failed to load activities:', error);
        } finally {
            this.loading = false;
        }
    },

    formatDate(dateString) {
        return new Date(dateString).toLocaleString();
    },

    getActivityIcon(type) {
        const icons = {
            'post_created': 'M12 4v16m8-8H4',
            'post_updated': 'M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z',
            'post_published': 'M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z',
            'post_scheduled': 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z',
            'post_deleted': 'M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16',
            'platform_connected': 'M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1',
            'platform_disconnected': 'M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1',
            'login': 'M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1',
            'logout': 'M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1'
        };
        return icons[type] || 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z';
    },

    getActivityColor(type) {
        const colors = {
            'post_created': 'bg-green-100 text-green-600',
            'post_updated': 'bg-blue-100 text-blue-600',
            'post_published': 'bg-purple-100 text-purple-600',
            'post_scheduled': 'bg-yellow-100 text-yellow-600',
            'post_deleted': 'bg-red-100 text-red-600',
            'platform_connected': 'bg-green-100 text-green-600',
            'platform_disconnected': 'bg-red-100 text-red-600',
            'login': 'bg-blue-100 text-blue-600',
            'logout': 'bg-gray-100 text-gray-600'
        };
        return colors[type] || 'bg-gray-100 text-gray-600';
    },

    async clearFilters() {
        this.searchTerm = '';
        this.typeFilter = 'all';
        this.dateFilter = '';
        await this.loadActivities(1);
    }
}"
x-init="init()">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Activity Log</h1>
                <p class="mt-1 text-sm text-gray-600">
                    Track all actions and events in your account
                </p>
            </div>

            <div class="mt-4 sm:mt-0 flex items-center space-x-4">
                <button @click="loadActivities(pagination.current_page)"
                        class="inline-flex items-center px-3 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    Refresh
                </button>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-sm border p-4 mb-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-4 sm:space-y-0 sm:space-x-4">
            <!-- Search -->
            <div class="flex-1">
                <div class="relative">
                    <input type="text"
                           x-model="searchTerm"
                           @input.debounce.300ms="loadActivities(1)"
                           placeholder="Search activities..."
                           class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Type Filter -->
            <div>
                <select x-model="typeFilter"
                        @change="loadActivities(1)"
                        class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    <option value="all">All Types</option>
                    <option value="post_created">Post Created</option>
                    <option value="post_updated">Post Updated</option>
                    <option value="post_published">Post Published</option>
                    <option value="post_scheduled">Post Scheduled</option>
                    <option value="post_deleted">Post Deleted</option>
                    <option value="platform_connected">Platform Connected</option>
                    <option value="platform_disconnected">Platform Disconnected</option>
                    <option value="login">Login</option>
                    <option value="logout">Logout</option>
                </select>
            </div>

            <!-- Date Filter -->
            <div>
                <input type="date"
                       x-model="dateFilter"
                       @change="loadActivities(1)"
                       class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
            </div>

            <!-- Clear Filters -->
            <button @click="clearFilters"
                    class="px-4 py-2 text-sm text-gray-600 hover:text-gray-900 whitespace-nowrap">
                Clear Filters
            </button>
        </div>
    </div>

    <!-- Loading State -->
    <div x-show="loading" class="flex justify-center py-12">
        <div class="spinner w-8 h-8"></div>
    </div>

    <!-- Activity List -->
    <div x-show="!loading" class="bg-white rounded-lg shadow-sm border overflow-hidden">
        <!-- Empty State -->
        <template x-if="activities.length === 0">
            <div class="text-center py-12">
                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No activities found</h3>
                <p class="text-gray-600 mb-6">
                    <template x-if="searchTerm || typeFilter !== 'all' || dateFilter">
                        <span>Try adjusting your filters to see more results.</span>
                    </template>
                    <template x-if="!searchTerm && typeFilter === 'all' && !dateFilter">
                        <span>Start using the application to see your activity here.</span>
                    </template>
                </p>
                <button @click="clearFilters"
                        class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700">
                    Clear Filters
                </button>
            </div>
        </template>

        <!-- Activity Items -->
        <div class="divide-y divide-gray-200">
            <template x-for="activity in activities" :key="activity.id">
                <div class="activity-item p-6">
                    <div class="activity-icon"
                         :class="getActivityColor(activity.type)">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="getActivityIcon(activity.type)"/>
                        </svg>
                    </div>

                    <div class="flex-1">
                        <div class="flex items-center justify-between">
                            <p class="text-sm font-medium text-gray-900" x-text="activity.description"></p>
                            <p class="text-xs text-gray-500" x-text="formatDate(activity.created_at)"></p>
                        </div>

                        <template x-if="activity.properties && Object.keys(activity.properties).length > 0">
                            <div class="mt-2">
                                <details class="group">
                                    <summary class="text-xs text-blue-600 hover:text-blue-500 cursor-pointer">
                                        View details
                                    </summary>
                                    <div class="mt-2 p-3 bg-gray-50 rounded text-xs">
                                        <pre class="whitespace-pre-wrap text-gray-700" x-text="JSON.stringify(activity.properties, null, 2)"></pre>
                                    </div>
                                </details>
                            </div>
                        </template>

                        <!-- Activity Metadata -->
                        <div class="flex items-center mt-2 space-x-4 text-xs text-gray-500">
                            <template x-if="activity.ip_address">
                                <span class="flex items-center">
                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9v-9m0-9v9"/>
                                    </svg>
                                    <span x-text="activity.ip_address"></span>
                                </span>
                            </template>

                            <template x-if="activity.user_agent">
                                <span class="flex items-center truncate">
                                    <svg class="w-3 h-3 mr-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                    </svg>
                                    <span class="truncate" x-text="activity.user_agent"></span>
                                </span>
                            </template>
                        </div>
                    </div>
                </div>
            </template>
        </div>

        <!-- Pagination -->
        <template x-if="pagination.last_page > 1">
            <div class="bg-gray-50 px-6 py-3 border-t border-gray-200">
                <div class="flex items-center justify-between">
                    <div class="text-sm text-gray-700">
                        Showing
                        <span class="font-medium" x-text="((pagination.current_page - 1) * pagination.per_page) + 1"></span>
                        to
                        <span class="font-medium" x-text="Math.min(pagination.current_page * pagination.per_page, pagination.total)"></span>
                        of
                        <span class="font-medium" x-text="pagination.total"></span>
                        results
                    </div>

                    <div class="flex items-center space-x-2">
                        <button @click="loadActivities(pagination.current_page - 1)"
                                :disabled="pagination.current_page === 1"
                                :class="{ 'opacity-50 cursor-not-allowed': pagination.current_page === 1 }"
                                class="px-3 py-1 text-sm border border-gray-300 rounded-md hover:bg-gray-50">
                            Previous
                        </button>

                        <!-- Page Numbers -->
                        <template x-for="page in Array.from({length: Math.min(5, pagination.last_page)}, (_, i) => {
                            let start = Math.max(1, pagination.current_page - 2);
                            let end = Math.min(pagination.last_page, start + 4);
                            start = Math.max(1, end - 4);
                            return start + i;
                        }).filter(page => page <= pagination.last_page)" :key="page">
                            <button @click="loadActivities(page)"
                                    :class="{
                                        'bg-blue-600 text-white': page === pagination.current_page,
                                        'text-gray-700 hover:bg-gray-50': page !== pagination.current_page
                                    }"
                                    class="px-3 py-1 text-sm border border-gray-300 rounded-md"
                                    x-text="page">
                            </button>
                        </template>

                        <button @click="loadActivities(pagination.current_page + 1)"
                                :disabled="pagination.current_page === pagination.last_page"
                                :class="{ 'opacity-50 cursor-not-allowed': pagination.current_page === pagination.last_page }"
                                class="px-3 py-1 text-sm border border-gray-300 rounded-md hover:bg-gray-50">
                            Next
                        </button>
                    </div>
                </div>
            </div>
        </template>
    </div>

    <!-- Activity Statistics -->
    <div class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white rounded-lg shadow-sm border p-6">
            <div class="flex items-center">
                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M2 10a8 8 0 018-8v8h8a8 8 0 11-16 0z"/>
                        <path d="M12 2.252A8.014 8.014 0 0117.748 8H12V2.252z"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total Activities</p>
                    <p class="text-2xl font-bold text-gray-900" x-text="pagination.total || 0"></p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border p-6">
            <div class="flex items-center">
                <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Today's Activities</p>
                    <p class="text-2xl font-bold text-gray-900">
                        <span x-text="activities.filter(a => new Date(a.created_at).toDateString() === new Date().toDateString()).length"></span>
                    </p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border p-6">
            <div class="flex items-center">
                <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">This Week</p>
                    <p class="text-2xl font-bold text-gray-900">
                        <span x-text="activities.filter(a => {
                            const activityDate = new Date(a.created_at);
                            const weekAgo = new Date();
                            weekAgo.setDate(weekAgo.getDate() - 7);
                            return activityDate >= weekAgo;
                        }).length"></span>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="mt-8 flex flex-col sm:flex-row gap-4">
        <a href="{{ route('dashboard') }}"
           class="inline-flex items-center justify-center px-6 py-3 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
            Back to Dashboard
        </a>

        <a href="{{ route('analytics') }}"
           class="inline-flex items-center justify-center px-6 py-3 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
            </svg>
            View Analytics
        </a>
    </div>
</div>
@endsection
