/**
 * Utilidades de Manejo de API
 *
 * Funciones helper para manejar respuestas de API de Laravel de forma segura y consistente.
 */

/**
 * Extrae datos de respuesta paginada de Laravel
 *
 * Laravel devuelve paginación en el formato: { data: { data: [...], meta: {...} } }
 * Esta función normaliza diferentes formatos de respuesta.
 *
 * @param {Object} response - Respuesta de Axios
 * @returns {Object} { data: Array, meta: Object|null }
 *
 * @example
 * const response = await axios.get('/api/eventos');
 * const { data: events, meta } = extractPaginatedData(response);
 *
 * if (events.length > 0) {
 *   renderEvents(events);
 * }
 *
 * if (meta) {
 *   renderPagination(meta);
 * }
 */
export function extractPaginatedData(response) {
    if (!response?.data) {
        return { data: [], meta: null };
    }

    const payload = response.data;

    // Formato estándar de Laravel con success flag
    if (payload.success === true) {
        // Laravel pagination: { success: true, data: { data: [...], current_page: 1, ... } }
        if (payload.data && Array.isArray(payload.data.data)) {
            return {
                data: payload.data.data,
                meta: {
                    current_page: payload.data.current_page,
                    last_page: payload.data.last_page,
                    per_page: payload.data.per_page,
                    total: payload.data.total,
                    from: payload.data.from,
                    to: payload.data.to,
                }
            };
        }

        // Array simple: { success: true, data: [...] }
        if (Array.isArray(payload.data)) {
            return { data: payload.data, meta: null };
        }

        // Objeto único: { success: true, data: {...} }
        if (payload.data && typeof payload.data === 'object') {
            return { data: [payload.data], meta: null };
        }
    }

    // Formato directo de paginación (sin success wrapper)
    if (payload.data && Array.isArray(payload.data)) {
        return {
            data: payload.data,
            meta: payload.current_page ? {
                current_page: payload.current_page,
                last_page: payload.last_page,
                per_page: payload.per_page,
                total: payload.total,
                from: payload.from,
                to: payload.to,
            } : null
        };
    }

    // Array directo
    if (Array.isArray(payload)) {
        return { data: payload, meta: null };
    }

    return { data: [], meta: null };
}

/**
 * Extrae un solo objeto de la respuesta
 *
 * @param {Object} response - Respuesta de Axios
 * @returns {Object|null} Objeto extraído o null
 *
 * @example
 * const response = await axios.get('/api/eventos/123');
 * const event = extractSingleData(response);
 */
export function extractSingleData(response) {
    if (!response?.data) {
        return null;
    }

    const payload = response.data;

    // { success: true, data: {...} }
    if (payload.success === true && payload.data) {
        return payload.data;
    }

    // Objeto directo
    if (payload && typeof payload === 'object' && !Array.isArray(payload)) {
        return payload;
    }

    return null;
}

/**
 * Manejo de errores con mensajes amigables
 *
 * Convierte errores técnicos de API en mensajes comprensibles para el usuario.
 *
 * @param {Error} error - Error de Axios
 * @returns {string} Mensaje de error amigable
 *
 * @example
 * try {
 *   await axios.post('/api/eventos', data);
 * } catch (error) {
 *   const message = handleApiError(error);
 *   showNotification(message, 'error');
 * }
 */
export function handleApiError(error) {
    console.error('API Error:', error);

    // Error de red (sin respuesta del servidor)
    if (!error.response) {
        return 'Error de conexión. Verifica tu internet y vuelve a intentarlo.';
    }

    const status = error.response.status;
    const data = error.response.data;

    // Errores de validación (422)
    if (status === 422 && data.errors) {
        // Obtener el primer error de validación
        const firstError = Object.values(data.errors)[0];
        return Array.isArray(firstError) ? firstError[0] : firstError;
    }

    // Si la API devuelve un mensaje personalizado
    if (data.message) {
        return data.message;
    }

    // Mensajes predeterminados por código de estado
    const errorMessages = {
        400: 'Solicitud incorrecta. Verifica los datos ingresados.',
        401: 'Sesión expirada. Inicia sesión nuevamente.',
        403: 'No tienes permisos para realizar esta acción.',
        404: 'El recurso solicitado no fue encontrado.',
        409: 'Conflicto. El recurso ya existe.',
        422: 'Los datos proporcionados son inválidos.',
        429: 'Demasiadas solicitudes. Espera un momento e intenta de nuevo.',
        500: 'Error del servidor. Por favor intenta más tarde.',
        502: 'Servicio no disponible. Intenta más tarde.',
        503: 'Servicio en mantenimiento. Vuelve pronto.',
    };

    return errorMessages[status] || 'Ocurrió un error inesperado. Por favor intenta de nuevo.';
}

/**
 * Verifica si una respuesta fue exitosa
 *
 * @param {Object} response - Respuesta de Axios
 * @returns {boolean} true si la respuesta fue exitosa
 *
 * @example
 * const response = await axios.post('/api/login', credentials);
 * if (isSuccessResponse(response)) {
 *   redirectToDashboard();
 * }
 */
export function isSuccessResponse(response) {
    if (!response) return false;

    // Verificar status HTTP (2xx)
    if (response.status < 200 || response.status >= 300) {
        return false;
    }

    // Verificar flag success si existe
    if (response.data && typeof response.data.success === 'boolean') {
        return response.data.success;
    }

    // Si no hay flag success, asumir éxito si el status es 2xx
    return true;
}

/**
 * Crea query string desde objeto de parámetros
 *
 * @param {Object} params - Parámetros a convertir
 * @returns {string} Query string
 *
 * @example
 * const query = buildQueryString({ page: 2, per_page: 20, search: 'Puno' });
 * // Resultado: "?page=2&per_page=20&search=Puno"
 */
export function buildQueryString(params) {
    const filtered = Object.entries(params)
        .filter(([_, value]) => value !== null && value !== undefined && value !== '')
        .map(([key, value]) => `${encodeURIComponent(key)}=${encodeURIComponent(value)}`)
        .join('&');

    return filtered ? `?${filtered}` : '';
}

/**
 * Wrapper seguro para llamadas GET
 *
 * @param {string} url - URL del endpoint
 * @param {Object} params - Parámetros de query
 * @returns {Promise<{data: Array, meta: Object|null, error: string|null}>}
 *
 * @example
 * const { data, meta, error } = await safeGet('/api/eventos', { page: 1 });
 *
 * if (error) {
 *   showError(error);
 * } else {
 *   renderData(data);
 * }
 */
export async function safeGet(url, params = {}) {
    try {
        const queryString = buildQueryString(params);
        const response = await axios.get(`${url}${queryString}`);
        const { data, meta } = extractPaginatedData(response);

        return { data, meta, error: null };
    } catch (error) {
        const errorMessage = handleApiError(error);
        return { data: [], meta: null, error: errorMessage };
    }
}

/**
 * Wrapper seguro para llamadas POST/PUT/PATCH
 *
 * @param {string} url - URL del endpoint
 * @param {Object} data - Datos a enviar
 * @param {string} method - Método HTTP (post, put, patch)
 * @returns {Promise<{data: Object|null, error: string|null}>}
 *
 * @example
 * const { data, error } = await safeMutate('/api/eventos', eventData, 'post');
 *
 * if (error) {
 *   showError(error);
 * } else {
 *   showSuccess('Evento creado exitosamente');
 * }
 */
export async function safeMutate(url, data, method = 'post') {
    try {
        const response = await axios[method](url, data);
        const result = extractSingleData(response);

        return { data: result, error: null };
    } catch (error) {
        const errorMessage = handleApiError(error);
        return { data: null, error: errorMessage };
    }
}

/**
 * Wrapper seguro para llamadas DELETE
 *
 * @param {string} url - URL del endpoint
 * @returns {Promise<{success: boolean, error: string|null}>}
 *
 * @example
 * const { success, error } = await safeDelete(`/api/eventos/${eventId}`);
 *
 * if (success) {
 *   removeFromUI(eventId);
 * } else {
 *   showError(error);
 * }
 */
export async function safeDelete(url) {
    try {
        const response = await axios.delete(url);
        const isSuccess = isSuccessResponse(response);

        return { success: isSuccess, error: null };
    } catch (error) {
        const errorMessage = handleApiError(error);
        return { success: false, error: errorMessage };
    }
}
