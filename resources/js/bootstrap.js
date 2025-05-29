import axios from 'axios';

window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
window.axios.defaults.headers.common['Accept'] = 'application/json';
window.axios.defaults.headers.common['Content-Type'] = 'application/json';

// Set base URL for API requests
window.axios.defaults.baseURL = window.location.origin;

// Add CSRF token if available
const token = document.head.querySelector('meta[name="csrf-token"]');
if (token) {
    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
} else {
    console.error('CSRF token not found: https://laravel.com/docs/csrf#csrf-x-csrf-token');
}

// Add authorization token from localStorage if available
const authToken = localStorage.getItem('auth_token');
if (authToken) {
    window.axios.defaults.headers.common['Authorization'] = `Bearer ${authToken}`;
}

// Response interceptor for handling auth errors
window.axios.interceptors.response.use(
    response => response,
    error => {
        if (error.response && error.response.status === 401) {
            // Clear auth data and redirect to login
            localStorage.removeItem('auth_token');
            localStorage.removeItem('auth_user');
            window.location.href = '/login';
        }
        return Promise.reject(error);
    }
);
