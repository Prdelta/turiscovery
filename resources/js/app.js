import './bootstrap';
import axios from 'axios';

// Importar utilidades seguras
import * as domHelpers from './utils/dom-helpers.js';
import * as apiHelpers from './utils/api-helpers.js';
import * as cards from './components/cards.js';

window.axios = axios;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
window.axios.defaults.headers.common['Accept'] = 'application/json';
window.axios.defaults.withCredentials = true;

// Exportar utilidades globalmente para uso en blade templates
window.domHelpers = domHelpers;
window.apiHelpers = apiHelpers;
window.cards = cards;

// También exportar funciones individuales para facilidad de uso
Object.assign(window, {
    // DOM Helpers
    createElementWithText: domHelpers.createElementWithText,
    safeSetAttribute: domHelpers.safeSetAttribute,
    createSafeElement: domHelpers.createSafeElement,
    clearContainer: domHelpers.clearContainer,
    appendChildren: domHelpers.appendChildren,
    createButton: domHelpers.createButton,
    sanitizeHTML: domHelpers.sanitizeHTML,

    // API Helpers
    extractPaginatedData: apiHelpers.extractPaginatedData,
    handleApiError: apiHelpers.handleApiError,
    safeGet: apiHelpers.safeGet,

    // Cards
    createEventCard: cards.createEventCard,
    createExperienceCard: cards.createExperienceCard,
    createPromocionCard: cards.createPromocionCard,
    createLocaleCard: cards.createLocaleCard,
    createEmptyState: cards.createEmptyState,
    createLoadingState: cards.createLoadingState,
});

const token = document.head.querySelector('meta[name="csrf-token"]');
if (token) {
    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
}

const API_BASE = '/api';

/**
 * API Helper - Autenticación con Cookies httpOnly (SEGURO)
 *
 * Este objeto proporciona métodos para interactuar con la API de autenticación.
 * Usa cookies httpOnly automáticas en lugar de tokens en localStorage,
 * previniendo vulnerabilidades XSS de robo de tokens.
 */
export const api = {
    /**
     * Registrar nuevo usuario
     * Nota: Actualmente usa el sistema legacy de tokens.
     * TODO: Migrar a sesiones cuando el backend esté listo.
     */
    async register(data) {
        const response = await axios.post(`${API_BASE}/register`, data);
        return response.data;
    },

    /**
     * Login con sesión (cookies httpOnly - SEGURO)
     *
     * @param {string} email
     * @param {string} password
     * @param {boolean} remember - Recordar sesión
     * @returns {Promise<Object>} Respuesta con datos del usuario
     */
    async login(email, password, remember = false) {
        // Obtener cookie CSRF primero (requerido por Sanctum)
        await axios.get('/sanctum/csrf-cookie');

        // Login usando sesión (no token)
        const response = await axios.post(`${API_BASE}/session/login`, {
            email,
            password,
            remember
        });

        // La cookie de sesión se establece automáticamente por el servidor
        // y es httpOnly (NO accesible desde JavaScript - previene XSS)
        return response.data;
    },

    /**
     * Logout - Cierra la sesión en el servidor
     */
    async logout() {
        try {
            await axios.post(`${API_BASE}/session/logout`);
        } catch (error) {
            console.error('Error al cerrar sesión:', error);
        }
    },

    /**
     * Obtener información del usuario autenticado
     *
     * @returns {Promise<Object>} Datos del usuario o null
     */
    async me() {
        try {
            const response = await axios.get(`${API_BASE}/session/me`);
            return response.data.success ? response.data.data : null;
        } catch (error) {
            if (error.response?.status === 401) {
                return null; // No autenticado
            }
            throw error;
        }
    },

    /**
     * Verificar si el usuario está autenticado
     *
     * @returns {Promise<boolean>}
     */
    async isAuthenticated() {
        try {
            const response = await axios.get(`${API_BASE}/session/check`);
            return response.data.authenticated === true;
        } catch (error) {
            return false;
        }
    },

    /**
     * Obtener usuario actual (alias de me())
     */
    async getCurrentUser() {
        return this.me();
    },

    // ========== Métodos Legacy (Deprecados - NO USAR) ==========
    // Estos métodos se mantienen temporalmente para compatibilidad
    // pero NO se deben usar en código nuevo.

    /**
     * @deprecated Usa login() en su lugar (ahora usa sesiones)
     */
    async loginLegacy(email, password) {
        console.warn('loginLegacy() está deprecado. Usa login() en su lugar.');
        const response = await axios.post(`${API_BASE}/login`, { email, password });
        if (response.data.success && response.data.data.access_token) {
            this.setToken(response.data.data.access_token);
        }
        return response.data;
    },

    /**
     * @deprecated No se usa más - Las cookies httpOnly se gestionan automáticamente
     */
    setToken(token) {
        console.warn('setToken() está deprecado. Las cookies se gestionan automáticamente.');
        localStorage.setItem('auth_token', token);
        axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
    },

    /**
     * @deprecated No se usa más - Las cookies httpOnly se gestionan automáticamente
     */
    getToken() {
        console.warn('getToken() está deprecado. Las cookies se gestionan automáticamente.');
        return localStorage.getItem('auth_token');
    },

    /**
     * @deprecated No se usa más - Las cookies httpOnly se gestionan automáticamente
     */
    removeToken() {
        console.warn('removeToken() está deprecado. Las cookies se gestionan automáticamente.');
        localStorage.removeItem('auth_token');
        delete axios.defaults.headers.common['Authorization'];
    },

    /**
     * @deprecated No se usa más - Las cookies httpOnly se gestionan automáticamente
     */
    initAuth() {
        // No hacer nada - las cookies se envían automáticamente
        // Solo mantener por compatibilidad
    },
};

// No ejecutar initAuth() - ya no es necesario con cookies httpOnly
// api.initAuth();

// Limpiar cualquier token legacy que pueda existir en localStorage
if (localStorage.getItem('auth_token')) {
    console.warn('⚠️ Token legacy detectado en localStorage. Se recomienda iniciar sesión de nuevo para usar el sistema seguro de cookies httpOnly.');
    // Opcional: descomentar para limpiar automáticamente
    // localStorage.removeItem('auth_token');
}

window.api = api;
