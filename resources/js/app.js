import './bootstrap';
import axios from 'axios';

// Configure Axios defaults
window.axios = axios;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
window.axios.defaults.headers.common['Accept'] = 'application/json';

// Get CSRF token if available
const token = document.head.querySelector('meta[name="csrf-token"]');
if (token) {
    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
} else {
    console.error('CSRF token not found');
}

// API Helper Functions
const API_BASE = '/api';

export const api = {
    // Authentication
    async register(data) {
        const response = await axios.post(`${API_BASE}/register`, data);
        if (response.data.success) {
            this.setToken(response.data.data.access_token);
        }
        return response.data;
    },

    async login(email, password) {
        const response = await axios.post(`${API_BASE}/login`, { email, password });
        if (response.data.success) {
            this.setToken(response.data.data.access_token);
        }
        return response.data;
    },

    async logout() {
        await axios.post(`${API_BASE}/logout`);
        this.removeToken();
    },

    // Token management
    setToken(token) {
        localStorage.setItem('auth_token', token);
        axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
    },

    getToken() {
        return localStorage.getItem('auth_token');
    },

    removeToken() {
        localStorage.removeItem('auth_token');
        delete axios.defaults.headers.common['Authorization'];
    },

    // Initialize token from storage
    initAuth() {
        const token = this.getToken();
        if (token) {
            axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
        }
    },

    // Content fetching
    async getCandelaria(params = {}) {
        const response = await axios.get(`${API_BASE}/candelaria`, { params });
        return response.data;
    },

    async getExperiencias(params = {}) {
        const response = await axios.get(`${API_BASE}/experiencias`, { params });
        return response.data;
    },

    async getEventos(params = {}) {
        const response = await axios.get(`${API_BASE}/eventos`, { params });
        return response.data;
    },

    async getPromociones(params = {}) {
        const response = await axios.get(`${API_BASE}/promociones`, { params });
        return response.data;
    },

    async getLocales(params = {}) {
        const response = await axios.get(`${API_BASE}/locales`, { params });
        return response.data;
    },

    // Favorites
    async toggleFavorite(type, id) {
        const response = await axios.post(`${API_BASE}/favorites/toggle`, {
            favoritable_type: type,
            favoritable_id: id
        });
        return response.data;
    },

    async getFavorites() {
        const response = await axios.get(`${API_BASE}/favorites`);
        return response.data;
    },

    // Reviews
    async addReview(data) {
        const response = await axios.post(`${API_BASE}/reviews`, data);
        return response.data;
    }
};

// Initialize auth on page load
api.initAuth();

// Make api globally available
window.api = api;

// Make utility functions globally available
window.showNotification = showNotification;
window.formatDate = formatDate;
window.formatPrice = formatPrice;
window.renderStars = renderStars;

// UI Helper Functions
export function showNotification(message, type = 'success') {
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.textContent = message;
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 1rem 1.5rem;
        background: ${type === 'success' ? '#10b981' : '#ef4444'};
        color: white;
        border-radius: 0.5rem;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        z-index: 9999;
        animation: slideIn 0.3s ease-out;
    `;

    document.body.appendChild(notification);

    setTimeout(() => {
        notification.style.animation = 'slideOut 0.3s ease-out';
        setTimeout(() => notification.remove(), 300);
    }, 3000);
}

export function formatDate(dateString) {
    const options = { year: 'numeric', month: 'long', day: 'numeric' };
    return new Date(dateString).toLocaleDateString('es-PE', options);
}

export function formatPrice(price) {
    return `S/ ${parseFloat(price).toFixed(2)}`;
}

// Star rating component
export function renderStars(rating) {
    let stars = '';
    for (let i = 1; i <= 5; i++) {
        stars += i <= rating ? '★' : '☆';
    }
    return stars;
}

// Add CSS animations
const style = document.createElement('style');
style.textContent = `
    @keyframes slideIn {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    
    @keyframes slideOut {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(100%);
            opacity: 0;
        }
    }
`;
document.head.appendChild(style);
