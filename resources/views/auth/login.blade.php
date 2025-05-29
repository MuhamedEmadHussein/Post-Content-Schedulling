@extends('layouts.auth')

@section('title', 'Sign In')
@section('subtitle', 'Sign in to your account to start scheduling content')

@section('content')
<div x-data="{
    form: {
        email: '',
        password: '',
        remember: false
    },
    errors: {},
    loading: false,

    async submitForm() {
        if (this.loading) return;

        this.loading = true;
        this.errors = {};

        try {
            const response = await fetch('{{ url('api/login') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
                },
                body: JSON.stringify({
                    email: this.form.email,
                    password: this.form.password,
                    remember: this.form.remember
                }),
                credentials: 'same-origin'
            });

            const result = await response.json();

            if (response.ok && result.token) {
                // Store token in localStorage
                localStorage.setItem('auth_token', result.token);

                // Show success message
                this.$dispatch('notify', {
                    message: 'Login successful! Redirecting...',
                    type: 'success'
                });

                // Redirect to dashboard after a short delay
                setTimeout(() => {
                    window.location.href = '{{ route('dashboard') }}';
                }, 1000);
            } else {
                this.errors = result.errors || {};
                this.$dispatch('notify', {
                    message: result.message || 'Login failed. Please check your credentials.',
                    type: 'error'
                });
            }
        } catch (error) {
            console.error('Login error:', error);
            this.errors = error.response?.data?.errors || {};
            this.$dispatch('notify', {
                message: 'An error occurred during login. Please try again.',
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
                Sign in to your account
            </h2>
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
                    autocomplete="current-password"
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

        <!-- Remember Me & Forgot Password -->
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <input
                    id="remember_me"
                    name="remember"
                    type="checkbox"
                    x-model="form.remember"
                    class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                <label for="remember_me" class="ml-2 block text-sm text-gray-900">
                    Remember me
                </label>
            </div>

            <div class="text-sm">
                <a href="#" class="font-medium text-blue-600 hover:text-blue-500">
                    Forgot your password?
                </a>
            </div>
        </div>

        <!-- Submit Button -->
        <div>
            <button
                type="submit"
                :disabled="loading"
                :class="{ 'opacity-50 cursor-not-allowed': loading }"
                class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                <span x-show="!loading">Sign in</span>
                <span x-show="loading" class="flex items-center">
                    <div class="spinner mr-2"></div>
                    Signing in...
                </span>
            </button>
        </div>

        <!-- Demo Credentials -->
        <div class="mt-6 p-4 bg-gray-50 rounded-md">
            <h3 class="text-sm font-medium text-gray-700 mb-2">Demo Credentials:</h3>
            <div class="text-xs text-gray-600 space-y-1">
                <p><strong>Email:</strong> demo@contentscheduler.com</p>
                <p><strong>Password:</strong> password123</p>
            </div>
            <button
                type="button"
                @click="form.email = 'demo@contentscheduler.com'; form.password = 'password123'"
                class="mt-2 text-xs text-blue-600 hover:text-blue-500 underline">
                Use demo credentials
            </button>
        </div>
    </form>
</div>
@endsection

@section('links')
<div class="text-center">
    <p class="text-sm text-gray-600">
        Don't have an account?
        <a href="{{ route('register') }}" class="font-medium text-blue-600 hover:text-blue-500">
            Sign up now
        </a>
    </p>
</div>
@endsection
