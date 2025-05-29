import './bootstrap';
import Alpine from 'alpinejs';
import persist from '@alpinejs/persist';
import flatpickr from 'flatpickr';
import { Chart, registerables } from 'chart.js';

// Register Chart.js components
Chart.register(...registerables);

// Initialize Alpine.js plugins
Alpine.plugin(persist);

// Global Alpine data and utilities
Alpine.data('auth', () => ({
    user: Alpine.$persist(null),
    token: Alpine.$persist(null),

    async login(credentials) {
        try {
            const response = await window.axios.post('/api/login', credentials);
            this.token = response.data.token;
            this.user = response.data.user;

            // Set axios default authorization header
            window.axios.defaults.headers.common['Authorization'] = `Bearer ${this.token}`;

            return { success: true };
        } catch (error) {
            return {
                success: false,
                message: error.response?.data?.message || 'Login failed'
            };
        }
    },

    async register(userData) {
        try {
            const response = await window.axios.post('/api/register', userData);
            this.token = response.data.token;
            this.user = response.data.user;

            // Set axios default authorization header
            window.axios.defaults.headers.common['Authorization'] = `Bearer ${this.token}`;

            return { success: true };
        } catch (error) {
            return {
                success: false,
                message: error.response?.data?.message || 'Registration failed'
            };
        }
    },

    logout() {
        this.user = null;
        this.token = null;
        delete window.axios.defaults.headers.common['Authorization'];
        window.location.href = '/login';
    },

    isAuthenticated() {
        return this.token !== null;
    }
}));

Alpine.data('postEditor', () => ({
    post: {
        title: '',
        content: '',
        image_url: '',
        scheduled_time: '',
        status: 'draft',
        platforms: []
    },
    platforms: [],
    availablePlatforms: [],
    errors: {},
    loading: false,
    characterCount: 0,
    characterLimits: {
        twitter: 280,
        instagram: 2200,
        linkedin: 3000,
        facebook: 63206
    },

    async init() {
        await this.loadPlatforms();
        this.initDatePicker();
        this.$watch('post.content', () => this.updateCharacterCount());
    },

    async loadPlatforms() {
        try {
            const response = await window.axios.get('/api/platforms');
            this.availablePlatforms = response.data;
        } catch (error) {
            console.error('Failed to load platforms:', error);
        }
    },

    initDatePicker() {
        flatpickr('#scheduled_time', {
            enableTime: true,
            dateFormat: 'Y-m-d H:i',
            minDate: 'today',
            onChange: (selectedDates, dateStr) => {
                this.post.scheduled_time = dateStr;
            }
        });
    },

    updateCharacterCount() {
        this.characterCount = this.post.content.length;
    },

    getCharacterLimit() {
        if (this.post.platforms.length === 0) return 0;

        const limits = this.post.platforms.map(platformId => {
            const platform = this.availablePlatforms.find(p => p.id === platformId);
            return this.characterLimits[platform?.type] || 0;
        });

        return Math.min(...limits);
    },

    isCharacterLimitExceeded() {
        const limit = this.getCharacterLimit();
        return limit > 0 && this.characterCount > limit;
    },

    async savePost() {
        if (this.loading) return;

        this.loading = true;
        this.errors = {};

        try {
            const postData = {
                ...this.post,
                platforms: this.post.platforms
            };

            const response = await window.axios.post('/api/posts', postData);

            // Redirect to dashboard on success
            window.location.href = '/dashboard';
        } catch (error) {
            this.errors = error.response?.data?.errors || {};
        } finally {
            this.loading = false;
        }
    },

    handleImageUpload(event) {
        const file = event.target.files[0];
        if (file) {
            // In a real app, you'd upload to a file storage service
            // For now, we'll create a local URL
            this.post.image_url = URL.createObjectURL(file);
        }
    }
}));

Alpine.data('dashboard', () => ({
    posts: [],
    platforms: [],
    filters: {
        status: 'all',
        date: '',
        platform: 'all'
    },
    view: 'list', // 'list' or 'calendar'
    loading: false,
    analytics: null,

    async init() {
        await this.loadPosts();
        await this.loadAnalytics();
    },

    async loadPosts() {
        this.loading = true;
        try {
            const params = new URLSearchParams();
            if (this.filters.status !== 'all') params.append('status', this.filters.status);
            if (this.filters.date) params.append('date', this.filters.date);

            const response = await window.axios.get(`/api/posts?${params}`);
            this.posts = response.data;
        } catch (error) {
            console.error('Failed to load posts:', error);
        } finally {
            this.loading = false;
        }
    },

    async loadAnalytics() {
        try {
            const response = await window.axios.get('/api/analytics');
            this.analytics = response.data;
        } catch (error) {
            console.error('Failed to load analytics:', error);
        }
    },

    async deletePost(postId) {
        if (!confirm('Are you sure you want to delete this post?')) return;

        try {
            await window.axios.delete(`/api/posts/${postId}`);
            await this.loadPosts();
        } catch (error) {
            console.error('Failed to delete post:', error);
        }
    },

    getStatusColor(status) {
        const colors = {
            draft: 'bg-gray-100 text-gray-800',
            scheduled: 'bg-blue-100 text-blue-800',
            published: 'bg-green-100 text-green-800'
        };
        return colors[status] || 'bg-gray-100 text-gray-800';
    },

    formatDate(dateString) {
        return new Date(dateString).toLocaleString();
    }
}));

Alpine.data('settings', () => ({
    userPlatforms: [],
    availablePlatforms: [],
    loading: false,
    saving: false,

    async init() {
        await this.loadPlatforms();
        await this.loadUserPlatforms();
    },

    async loadPlatforms() {
        try {
            const response = await window.axios.get('/api/platforms');
            this.availablePlatforms = response.data;
        } catch (error) {
            console.error('Failed to load platforms:', error);
        }
    },

    async loadUserPlatforms() {
        this.loading = true;
        try {
            const response = await window.axios.get('/api/user/platforms');
            this.userPlatforms = response.data.map(p => p.id);
        } catch (error) {
            console.error('Failed to load user platforms:', error);
        } finally {
            this.loading = false;
        }
    },

    async savePlatforms() {
        this.saving = true;
        try {
            await window.axios.put('/api/user/platforms', {
                platforms: this.userPlatforms
            });
            alert('Platforms updated successfully!');
        } catch (error) {
            console.error('Failed to save platforms:', error);
            alert('Failed to update platforms');
        } finally {
            this.saving = false;
        }
    },

    togglePlatform(platformId) {
        const index = this.userPlatforms.indexOf(platformId);
        if (index > -1) {
            this.userPlatforms.splice(index, 1);
        } else {
            this.userPlatforms.push(platformId);
        }
    }
}));

// Initialize Alpine
window.Alpine = Alpine;
Alpine.start();

// Make flatpickr available globally
window.flatpickr = flatpickr;
