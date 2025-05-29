@extends('layouts.app')

@section('title', 'Analytics')

@section('content')
<div x-data="{
    analytics: null,
    loading: true,
    dateRange: '30',

    async init() {
        await this.loadAnalytics();
        this.initCharts();
    },

    async loadAnalytics() {
        this.loading = true;
        try {
            const response = await window.axios.get('/api/analytics', {
                params: { days: this.dateRange }
            });
            this.analytics = response.data;
        } catch (error) {
            console.error('Failed to load analytics:', error);
        } finally {
            this.loading = false;
        }
    },

    initCharts() {
        this.$nextTick(() => {
            this.createPlatformChart();
            this.createStatusChart();
            this.createTimelineChart();
        });
    },

    createPlatformChart() {
        if (!this.analytics?.platformStats) return;

        const ctx = document.getElementById('platformChart');
        if (!ctx) return;

        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: Object.keys(this.analytics.platformStats),
                datasets: [{
                    data: Object.values(this.analytics.platformStats),
                    backgroundColor: [
                        '#3B82F6', // Blue
                        '#8B5CF6', // Purple
                        '#10B981', // Green
                        '#F59E0B', // Yellow
                        '#EF4444'  // Red
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    },

    createStatusChart() {
        if (!this.analytics?.statusStats) return;

        const ctx = document.getElementById('statusChart');
        if (!ctx) return;

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: Object.keys(this.analytics.statusStats),
                datasets: [{
                    data: Object.values(this.analytics.statusStats),
                    backgroundColor: '#3B82F6'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    },

    createTimelineChart() {
        if (!this.analytics?.timeline) return;

        const ctx = document.getElementById('timelineChart');
        if (!ctx) return;

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: this.analytics.timeline.labels,
                datasets: [{
                    label: 'Posts Created',
                    data: this.analytics.timeline.data,
                    borderColor: '#3B82F6',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    }
}"
x-init="init()">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Analytics</h1>
                <p class="mt-1 text-sm text-gray-600">
                    Track your content performance and publishing statistics
                </p>
            </div>

            <!-- Date Range Filter -->
            <div class="mt-4 sm:mt-0">
                <select x-model="dateRange"
                        @change="loadAnalytics()"
                        class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    <option value="7">Last 7 days</option>
                    <option value="30">Last 30 days</option>
                    <option value="90">Last 90 days</option>
                    <option value="365">Last year</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Loading State -->
    <div x-show="loading" class="flex justify-center py-12">
        <div class="spinner w-8 h-8"></div>
    </div>

    <!-- Analytics Content -->
    <div x-show="!loading" class="space-y-8">
        <!-- Summary Stats -->
        <template x-if="analytics">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <div class="bg-white rounded-lg shadow-sm border p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Total Posts</p>
                            <p class="text-2xl font-bold text-gray-900" x-text="analytics.summary.total_posts || 0"></p>
                        </div>
                        <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-sm border p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Published</p>
                            <p class="text-2xl font-bold text-green-600" x-text="analytics.summary.published_posts || 0"></p>
                        </div>
                        <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-sm border p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Scheduled</p>
                            <p class="text-2xl font-bold text-blue-600" x-text="analytics.summary.scheduled_posts || 0"></p>
                        </div>
                        <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-sm border p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Success Rate</p>
                            <p class="text-2xl font-bold text-purple-600"
                               x-text="analytics.summary.success_rate ? analytics.summary.success_rate + '%' : '0%'"></p>
                        </div>
                        <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M2 10a8 8 0 018-8v8h8a8 8 0 11-16 0z"/>
                                <path d="M12 2.252A8.014 8.014 0 0117.748 8H12V2.252z"/>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
        </template>

        <!-- Charts Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Platform Distribution -->
            <div class="chart-container">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Posts by Platform</h2>
                <div class="h-64 relative">
                    <canvas id="platformChart"></canvas>
                </div>
            </div>

            <!-- Post Status -->
            <div class="chart-container">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Posts by Status</h2>
                <div class="h-64 relative">
                    <canvas id="statusChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Timeline Chart -->
        <div class="chart-container">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Posts Created Over Time</h2>
            <div class="h-64 relative">
                <canvas id="timelineChart"></canvas>
            </div>
        </div>

        <!-- Detailed Statistics -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Platform Performance -->
            <div class="bg-white rounded-lg shadow-sm border p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Platform Performance</h2>

                <template x-if="analytics && analytics.platformDetails">
                    <div class="space-y-4">
                        <template x-for="(platform, name) in analytics.platformDetails" :key="name">
                            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                                <div class="flex items-center space-x-3">
                                    <div class="w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center">
                                        <span class="text-white text-sm font-bold" x-text="name.charAt(0).toUpperCase()"></span>
                                    </div>
                                    <div>
                                        <h3 class="font-medium text-gray-900 capitalize" x-text="name"></h3>
                                        <p class="text-sm text-gray-500" x-text="`${platform.published} published, ${platform.scheduled} scheduled`"></p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="text-lg font-semibold text-gray-900" x-text="platform.total"></p>
                                    <p class="text-sm text-gray-500">total posts</p>
                                </div>
                            </div>
                        </template>
                    </div>
                </template>
            </div>

            <!-- Recent Activity -->
            <div class="bg-white rounded-lg shadow-sm border p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Recent Activity</h2>

                <template x-if="analytics && analytics.recentActivity">
                    <div class="space-y-4">
                        <template x-for="activity in analytics.recentActivity" :key="activity.id">
                            <div class="activity-item">
                                <div class="activity-icon bg-blue-100 text-blue-600">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <p class="text-sm text-gray-900" x-text="activity.description"></p>
                                    <p class="text-xs text-gray-500" x-text="activity.time"></p>
                                </div>
                            </div>
                        </template>
                    </div>
                </template>

                <!-- Fallback if no recent activity -->
                <template x-if="!analytics || !analytics.recentActivity || analytics.recentActivity.length === 0">
                    <div class="text-center py-8">
                        <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <p class="text-gray-500">No recent activity</p>
                        <p class="text-sm text-gray-400 mt-1">Start creating posts to see activity here</p>
                    </div>
                </template>
            </div>
        </div>

        <!-- Performance Insights -->
        <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-lg border border-blue-200 p-6">
            <h2 class="text-lg font-semibold text-blue-900 mb-4">Performance Insights</h2>

            <template x-if="analytics && analytics.insights">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <template x-for="insight in analytics.insights" :key="insight.title">
                        <div class="bg-white rounded-lg p-4">
                            <div class="flex items-center mb-2">
                                <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center mr-3">
                                    <svg class="w-4 h-4 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                                <h3 class="font-medium text-gray-900" x-text="insight.title"></h3>
                            </div>
                            <p class="text-sm text-gray-600" x-text="insight.description"></p>
                            <template x-if="insight.action">
                                <a :href="insight.action.url"
                                   class="inline-flex items-center text-sm text-blue-600 hover:text-blue-500 mt-2"
                                   x-text="insight.action.text"></a>
                            </template>
                        </div>
                    </template>
                </div>
            </template>

            <!-- Default insights if none provided -->
            <template x-if="!analytics || !analytics.insights">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="bg-white rounded-lg p-4">
                        <div class="flex items-center mb-2">
                            <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center mr-3">
                                <svg class="w-4 h-4 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <h3 class="font-medium text-gray-900">Consistency is Key</h3>
                        </div>
                        <p class="text-sm text-gray-600">Regular posting helps maintain audience engagement. Try to schedule posts consistently across platforms.</p>
                    </div>

                    <div class="bg-white rounded-lg p-4">
                        <div class="flex items-center mb-2">
                            <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center mr-3">
                                <svg class="w-4 h-4 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <h3 class="font-medium text-gray-900">Quality Content</h3>
                        </div>
                        <p class="text-sm text-gray-600">Focus on creating valuable content that resonates with your audience rather than posting frequently.</p>
                    </div>

                    <div class="bg-white rounded-lg p-4">
                        <div class="flex items-center mb-2">
                            <div class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center mr-3">
                                <svg class="w-4 h-4 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M2 10a8 8 0 018-8v8h8a8 8 0 11-16 0z"/>
                                    <path d="M12 2.252A8.014 8.014 0 0117.748 8H12V2.252z"/>
                                </svg>
                            </div>
                            <h3 class="font-medium text-gray-900">Platform Optimization</h3>
                        </div>
                        <p class="text-sm text-gray-600">Tailor your content to each platform's strengths and audience preferences for better engagement.</p>
                    </div>
                </div>
            </template>
        </div>

        <!-- Quick Actions -->
        <div class="flex flex-col sm:flex-row gap-4">
            <a href="{{ route('posts.create') }}"
               class="inline-flex items-center justify-center px-6 py-3 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Create New Post
            </a>

            <a href="{{ route('dashboard') }}"
               class="inline-flex items-center justify-center px-6 py-3 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                View Dashboard
            </a>

            <a href="{{ route('activity-logs') }}"
               class="inline-flex items-center justify-center px-6 py-3 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Activity Log
            </a>
        </div>
    </div>
</div>
@endsection
