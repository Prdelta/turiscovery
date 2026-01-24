import './bootstrap';
import axios from 'axios';

window.axios = axios;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
window.axios.defaults.headers.common['Accept'] = 'application/json';
window.axios.defaults.withCredentials = true;

const token = document.head.querySelector('meta[name="csrf-token"]');
if (token) {
    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
}

const API_BASE = '/api';

export const api = {
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

    initAuth() {
        const token = this.getToken();
        if (token) {
            axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
        }
    },
};

api.initAuth();
window.api = api;
