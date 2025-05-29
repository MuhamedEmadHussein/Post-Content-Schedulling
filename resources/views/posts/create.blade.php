@extends('layouts.app')

@section('title', 'Create Post')

@section('content')
<div x-data="postEditor" x-init="init()">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Create New Post</h1>
                <p class="mt-1 text-sm text-gray-600">
                    Schedule your content across multiple social platforms
                </p>
            </div>
            <a href="{{ route('dashboard') }}"
               class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to Dashboard
            </a>
        </div>
    </div>

    <form @submit.prevent="savePost" class="space-y-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Post Title -->
                <div class="bg-white rounded-lg shadow-sm border p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Post Details</h2>

                    <div class="space-y-4">
                        <div :class="{ 'form-field error': errors.title }">
                            <label for="title" class="block text-sm font-medium text-gray-700">
                                Post Title
                            </label>
                            <input
                                type="text"
                                id="title"
                                x-model="post.title"
                                placeholder="Enter a catchy title for your post"
                                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            <template x-if="errors.title">
                                <p class="error-message" x-text="errors.title[0]"></p>
                            </template>
                        </div>

                        <!-- Post Content -->
                        <div :class="{ 'form-field error': errors.content }">
                            <label for="content" class="block text-sm font-medium text-gray-700">
                                Content
                            </label>
                            <textarea
                                id="content"
                                rows="6"
                                x-model="post.content"
                                @input="updateCharacterCount"
                                placeholder="What would you like to share with your audience?"
                                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"></textarea>

                            <!-- Character Counter -->
                            <div class="flex justify-between items-center mt-2">
                                <template x-if="errors.content">
                                    <p class="error-message" x-text="errors.content[0]"></p>
                                </template>
                                <div class="text-sm">
                                    <span class="character-counter"
                                          :class="{
                                              'warning': getCharacterLimit() > 0 && characterCount > getCharacterLimit() * 0.8,
                                              'danger': isCharacterLimitExceeded()
                                          }">
                                        <span x-text="characterCount"></span>
                                        <template x-if="getCharacterLimit() > 0">
                                            <span>/<span x-text="getCharacterLimit()"></span></span>
                                        </template>
                                        characters
                                    </span>
                                    <template x-if="isCharacterLimitExceeded()">
                                        <span class="ml-2 text-red-600 text-xs">
                                            Character limit exceeded for selected platforms
                                        </span>
                                    </template>
                                </div>
                            </div>
                        </div>

                        <!-- Image Upload -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Image (Optional)
                            </label>
                            <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-gray-400 transition-colors">
                                <template x-if="!post.image_url">
                                    <div>
                                        <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                            <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                        <div class="mt-4">
                                            <label for="image-upload" class="cursor-pointer">
                                                <span class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-blue-600 bg-blue-100 hover:bg-blue-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                                                    Upload Image
                                                </span>
                                                <input id="image-upload"
                                                       type="file"
                                                       accept="image/*"
                                                       @change="handleImageUpload"
                                                       class="sr-only">
                                            </label>
                                        </div>
                                        <p class="mt-2 text-sm text-gray-500">
                                            PNG, JPG, GIF up to 10MB
                                        </p>
                                    </div>
                                </template>

                                <template x-if="post.image_url">
                                    <div class="relative">
                                        <img :src="post.image_url"
                                             alt="Post image preview"
                                             class="max-h-64 mx-auto rounded-lg">
                                        <button type="button"
                                                @click="post.image_url = ''"
                                                class="absolute top-2 right-2 p-1 bg-red-600 text-white rounded-full hover:bg-red-700">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                            </svg>
                                        </button>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Platform Selection -->
                <div class="bg-white rounded-lg shadow-sm border p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Select Platforms</h2>

                    <template x-if="availablePlatforms.length === 0">
                        <div class="text-center py-8">
                            <p class="text-gray-500 mb-4">No platforms configured</p>
                            <a href="{{ route('settings') }}"
                               class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700">
                                Configure Platforms
                            </a>
                        </div>
                    </template>

                    <div class="platform-selector" x-show="availablePlatforms.length > 0">
                        <template x-for="platform in availablePlatforms" :key="platform.id">
                            <div class="platform-card"
                                 :class="{ 'selected': post.platforms.includes(platform.id) }"
                                 @click="togglePlatform(platform.id)">

                                <!-- Platform Icon -->
                                <div class="w-8 h-8 mb-2">
                                    <template x-if="platform.type === 'twitter'">
                                        <div class="w-8 h-8 bg-blue-400 rounded-full flex items-center justify-center">
                                            <span class="text-white text-sm font-bold">T</span>
                                        </div>
                                    </template>
                                    <template x-if="platform.type === 'instagram'">
                                        <div class="w-8 h-8 bg-gradient-to-r from-purple-400 to-pink-400 rounded-full flex items-center justify-center">
                                            <span class="text-white text-sm font-bold">I</span>
                                        </div>
                                    </template>
                                    <template x-if="platform.type === 'linkedin'">
                                        <div class="w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center">
                                            <span class="text-white text-sm font-bold">L</span>
                                        </div>
                                    </template>
                                    <template x-if="platform.type === 'facebook'">
                                        <div class="w-8 h-8 bg-blue-800 rounded-full flex items-center justify-center">
                                            <span class="text-white text-sm font-bold">F</span>
                                        </div>
                                    </template>
                                </div>

                                <span class="text-sm font-medium text-gray-900" x-text="platform.name"></span>
                                <span class="text-xs text-gray-500 capitalize" x-text="platform.type"></span>

                                <!-- Character Limit -->
                                <template x-if="characterLimits[platform.type]">
                                    <span class="text-xs text-gray-400 mt-1"
                                          x-text="`${characterLimits[platform.type]} chars`"></span>
                                </template>

                                <!-- Selection Indicator -->
                                <template x-if="post.platforms.includes(platform.id)">
                                    <div class="absolute top-2 right-2 w-5 h-5 bg-blue-600 rounded-full flex items-center justify-center">
                                        <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                        </svg>
                                    </div>
                                </template>
                            </div>
                        </template>
                    </div>

                    <template x-if="errors.platforms">
                        <p class="error-message mt-2" x-text="errors.platforms[0]"></p>
                    </template>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Scheduling -->
                <div class="bg-white rounded-lg shadow-sm border p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Schedule</h2>

                    <div class="space-y-4">
                        <!-- Status Selection -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Post Status
                            </label>
                            <div class="space-y-2">
                                <label class="flex items-center">
                                    <input type="radio"
                                           x-model="post.status"
                                           value="draft"
                                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300">
                                    <span class="ml-2 text-sm text-gray-700">Save as Draft</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="radio"
                                           x-model="post.status"
                                           value="scheduled"
                                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300">
                                    <span class="ml-2 text-sm text-gray-700">Schedule for Later</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="radio"
                                           x-model="post.status"
                                           value="published"
                                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300">
                                    <span class="ml-2 text-sm text-gray-700">Publish Immediately</span>
                                </label>
                            </div>
                        </div>

                        <!-- Date/Time Picker -->
                        <div x-show="post.status === 'scheduled'"
                             x-transition
                             :class="{ 'form-field error': errors.scheduled_time }">
                            <label for="scheduled_time" class="block text-sm font-medium text-gray-700">
                                Schedule Date & Time
                            </label>
                            <input
                                type="text"
                                id="scheduled_time"
                                x-model="post.scheduled_time"
                                placeholder="Select date and time"
                                readonly
                                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm cursor-pointer">
                            <template x-if="errors.scheduled_time">
                                <p class="error-message" x-text="errors.scheduled_time[0]"></p>
                            </template>
                        </div>
                    </div>
                </div>

                <!-- Rate Limiting Info -->
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                    <div class="flex items-start">
                        <svg class="w-5 h-5 text-yellow-600 mt-0.5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                        <div>
                            <h3 class="text-sm font-medium text-yellow-800">Rate Limiting</h3>
                            <p class="text-sm text-yellow-700 mt-1">
                                You can schedule up to 10 posts per day. This helps maintain quality and prevents spam.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Preview -->
                <div class="bg-white rounded-lg shadow-sm border p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Preview</h2>

                    <div class="border rounded-lg p-4 bg-gray-50">
                        <template x-if="post.title">
                            <h3 class="font-semibold text-gray-900 mb-2" x-text="post.title"></h3>
                        </template>

                        <template x-if="post.content">
                            <p class="text-gray-700 text-sm mb-3" x-text="post.content"></p>
                        </template>

                        <template x-if="post.image_url">
                            <img :src="post.image_url"
                                 alt="Preview"
                                 class="w-full h-32 object-cover rounded mb-3">
                        </template>

                        <template x-if="!post.title && !post.content">
                            <p class="text-gray-400 text-sm italic">
                                Your post preview will appear here...
                            </p>
                        </template>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="space-y-3">
                    <button type="submit"
                            :disabled="loading || isCharacterLimitExceeded()"
                            :class="{
                                'opacity-50 cursor-not-allowed': loading || isCharacterLimitExceeded(),
                                'bg-blue-600 hover:bg-blue-700': !loading && !isCharacterLimitExceeded()
                            }"
                            class="w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                        <span x-show="!loading">
                            <span x-show="post.status === 'draft'">Save Draft</span>
                            <span x-show="post.status === 'scheduled'">Schedule Post</span>
                            <span x-show="post.status === 'published'">Publish Now</span>
                        </span>
                        <span x-show="loading" class="flex items-center">
                            <div class="spinner mr-2"></div>
                            Saving...
                        </span>
                    </button>

                    <button type="button"
                            @click="window.location.href = '{{ route('dashboard') }}'"
                            class="w-full py-2 px-4 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
// Helper function to toggle platform selection
function togglePlatform(platformId) {
    const index = this.post.platforms.indexOf(platformId);
    if (index > -1) {
        this.post.platforms.splice(index, 1);
    } else {
        this.post.platforms.push(platformId);
    }
}
</script>
@endsection
