@extends('layouts.auth')

@section('title', 'Sign Up')
@section('subtitle', 'Create your account to start scheduling content')

@section('content')
<div x-data="{
    form: {
        name: '',
        email: '',
        password: '',
        password_confirmation: ''
    },
    errors: {},
    loading: false,

    async submitForm() {
        if (this.loading) return;

        this.loading = true;
        this.errors = {};

        try {
            const result = await $store.auth.register(this.form);

            if (result.success) {
                this.$dispatch('notify', {
                    message: 'Account created successfully!',
                    type: 'success'
                });
                setTimeout(() => {
                    window.location.href = '{{ route('dashboard') }}';
                }, 1000);
            } else {
                this.$dispatch('notify', {
                    message: result.message,
                    type: 'error'
                });
            }
        } catch (error) {
            this.errors = error.response?.data?.errors || {};
            this.$dispatch('notify', {
                message: 'Registration failed. Please check your information.',
                type: 'error'
            });
        } finally {
            this.loading = false;
        }
    }
}">
    <form @submit.prevent="submitForm" class="space-y-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 text-center">
                Create your account
            </h2>
        </div>

        <!-- Name Field -->
        <div>
            <label for="name" class="block text-sm font-medium text-gray-700">
                Full name
            </label>
            <div class="mt-1">
                <input
                    id="name"
                    name="name"
                    type="text"
                    autocomplete="name"
                    required
                    x-model="form.name"
                    :class="{ 'border-red-500 focus:ring-red-500 focus:border-red-500': errors.name }"
                    class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                    placeholder="Enter your full name">
            </div>
            <template x-if="errors.name">
                <p class="mt-1 text-sm text-red-600" x-text="errors.name[0]"></p>
            </template>
        </div>

        <!-- Email Field -->
        <div>
            <label for="email" class="block text-sm font-medium text-gray-700">
                Email address
            </label>
            <div class="mt-1">
                <input
                    id="email"
                    name="email"
                    type="email"
                    autocomplete="email"
                    required
                    x-model="form.email"
                    :class="{ 'border-red-500 focus:ring-red-500 focus:border-red-500': errors.email }"
                    class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                    placeholder="Enter your email">
            </div>
            <template x-if="errors.email">
                <p class="mt-1 text-sm text-red-600" x-text="errors.email[0]"></p>
            </template>
        </div>

        <!-- Password Field -->
        <div>
            <label for="password" class="block text-sm font-medium text-gray-700">
                Password
            </label>
            <div class="mt-1">
                <input
                    id="password"
                    name="password"
                    type="password"
                    autocomplete="new-password"
                    required
                    x-model="form.password"
                    :class="{ 'border-red-500 focus:ring-red-500 focus:border-red-500': errors.password }"
                    class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                    placeholder="Enter your password">
            </div>
            <template x-if="errors.password">
                <p class="mt-1 text-sm text-red-600" x-text="errors.password[0]"></p>
            </template>
        </div>

        <!-- Confirm Password Field -->
        <div>
            <label for="password_confirmation" class="block text-sm font-medium text-gray-700">
                Confirm password
            </label>
            <div class="mt-1">
                <input
                    id="password_confirmation"
                    name="password_confirmation"
                    type="password"
                    autocomplete="new-password"
                    required
                    x-model="form.password_confirmation"
                    :class="{ 'border-red-500 focus:ring-red-500 focus:border-red-500': errors.password_confirmation }"
                    class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                    placeholder="Confirm your password">
            </div>
            <template x-if="errors.password_confirmation">
                <p class="mt-1 text-sm text-red-600" x-text="errors.password_confirmation[0]"></p>
            </template>
        </div>

        <!-- Password Requirements -->
        <div class="text-xs text-gray-600 bg-gray-50 p-3 rounded-md">
            <p class="font-medium text-gray-700 mb-1">Password requirements:</p>
            <ul class="space-y-1 ml-4 list-disc">
                <li>At least 8 characters long</li>
                <li>Contains uppercase and lowercase letters</li>
                <li>Contains at least one number</li>
            </ul>
        </div>

        <!-- Terms and Conditions -->
        <div class="flex items-start">
            <div class="flex items-center h-5">
                <input
                    id="terms"
                    name="terms"
                    type="checkbox"
                    required
                    class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
            </div>
            <div class="ml-3 text-sm">
                <label for="terms" class="text-gray-600">
                    I agree to the
                    <a href="#" class="text-blue-600 hover:text-blue-500 underline">Terms of Service</a>
                    and
                    <a href="#" class="text-blue-600 hover:text-blue-500 underline">Privacy Policy</a>
                </label>
            </div>
        </div>

        <!-- Submit Button -->
        <div>
            <button
                type="submit"
                :disabled="loading"
                :class="{ 'opacity-50 cursor-not-allowed': loading }"
                class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">

                <span x-show="!loading">Create account</span>
                <span x-show="loading" class="flex items-center">
                    <div class="spinner mr-2"></div>
                    Creating account...
                </span>
            </button>
        </div>

        <!-- Features Preview -->
        <div class="mt-6 p-4 bg-blue-50 rounded-md">
            <h3 class="text-sm font-medium text-blue-800 mb-2">What you'll get:</h3>
            <ul class="text-xs text-blue-700 space-y-1">
                <li class="flex items-center">
                    <svg class="w-3 h-3 mr-2 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                    </svg>
                    Schedule posts across multiple platforms
                </li>
                <li class="flex items-center">
                    <svg class="w-3 h-3 mr-2 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                    </svg>
                    Analytics and performance tracking
                </li>
                <li class="flex items-center">
                    <svg class="w-3 h-3 mr-2 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                    </svg>
                    Calendar view of scheduled content
                </li>
                <li class="flex items-center">
                    <svg class="w-3 h-3 mr-2 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                    </svg>
                    Activity logging and rate limiting
                </li>
            </ul>
        </div>
    </form>
</div>
@endsection

@section('links')
<div class="text-center">
    <p class="text-sm text-gray-600">
        Already have an account?
        <a href="{{ route('login') }}" class="font-medium text-blue-600 hover:text-blue-500">
            Sign in here
        </a>
    </p>
</div>
@endsection
