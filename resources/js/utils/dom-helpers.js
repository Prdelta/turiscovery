/**
 * Utilidades DOM Seguras - Prevención de XSS
 *
 * Estas funciones ayudan a crear y manipular elementos DOM de forma segura,
 * previniendo vulnerabilidades de Cross-Site Scripting (XSS).
 */

/**
 * Crea un elemento DOM con texto seguro (previene XSS)
 *
 * @param {string} tag - Tag HTML del elemento (ej: 'div', 'h3', 'p')
 * @param {string} text - Texto a insertar (será escapado automáticamente)
 * @param {string} className - Clases CSS opcionales
 * @returns {HTMLElement} Elemento DOM creado
 *
 * @example
 * const title = createElementWithText('h3', userInput, 'text-xl font-bold');
 */
export function createElementWithText(tag, text, className = '') {
    const el = document.createElement(tag);
    el.textContent = text || ''; // textContent escapa HTML automáticamente
    if (className) el.className = className;
    return el;
}

/**
 * Establece atributos de forma segura (valida URLs)
 *
 * Esta función valida URLs antes de establecerlas en atributos como href y src,
 * previniendo inyecciones de javascript: o data: maliciosos.
 *
 * @param {HTMLElement} element - Elemento DOM
 * @param {string} attr - Nombre del atributo
 * @param {string} value - Valor del atributo
 *
 * @example
 * const link = document.createElement('a');
 * safeSetAttribute(link, 'href', userProvidedUrl);
 */
export function safeSetAttribute(element, attr, value) {
    if (attr === 'href' || attr === 'src') {
        // Solo permitir URLs HTTP(S) o rutas relativas
        if (value && (
            value.startsWith('http://') ||
            value.startsWith('https://') ||
            value.startsWith('/')
        )) {
            element.setAttribute(attr, value);
        }
    } else {
        element.setAttribute(attr, value || '');
    }
}

/**
 * Crea un elemento con múltiples atributos de forma segura
 *
 * @param {string} tag - Tag HTML del elemento
 * @param {Object} attributes - Objeto con atributos a establecer
 * @param {string} textContent - Texto opcional del elemento
 * @returns {HTMLElement} Elemento DOM creado
 *
 * @example
 * const img = createSafeElement('img', {
 *   src: '/images/photo.jpg',
 *   alt: 'Descripción de la imagen',
 *   class: 'w-full h-auto'
 * });
 */
export function createSafeElement(tag, attributes = {}, textContent = '') {
    const el = document.createElement(tag);

    if (textContent) {
        el.textContent = textContent;
    }

    Object.entries(attributes).forEach(([attr, value]) => {
        if (attr === 'class') {
            el.className = value;
        } else {
            safeSetAttribute(el, attr, value);
        }
    });

    return el;
}

/**
 * Limpia el contenido de un contenedor de forma segura
 *
 * @param {HTMLElement|string} container - Elemento o selector
 *
 * @example
 * clearContainer('#events-list');
 */
export function clearContainer(container) {
    const el = typeof container === 'string'
        ? document.querySelector(container)
        : container;

    if (el) {
        el.innerHTML = '';
    }
}

/**
 * Anexa múltiples elementos hijos a un contenedor
 *
 * @param {HTMLElement} parent - Elemento padre
 * @param {HTMLElement[]} children - Array de elementos hijos
 *
 * @example
 * appendChildren(container, [title, description, button]);
 */
export function appendChildren(parent, children) {
    children.forEach(child => {
        if (child instanceof HTMLElement) {
            parent.appendChild(child);
        }
    });
}

/**
 * Crea un botón con event listener (sin onclick inline)
 *
 * @param {string} text - Texto del botón
 * @param {Function} handler - Función a ejecutar al hacer click
 * @param {string} className - Clases CSS opcionales
 * @param {Object} dataset - Atributos data-* opcionales
 * @returns {HTMLButtonElement} Botón creado
 *
 * @example
 * const btn = createButton('Reservar', handleBooking, 'btn btn-primary', { id: '123' });
 */
export function createButton(text, handler, className = '', dataset = {}) {
    const button = document.createElement('button');
    button.textContent = text;
    button.className = className;

    // Establecer data attributes
    Object.entries(dataset).forEach(([key, value]) => {
        button.dataset[key] = value;
    });

    // Agregar event listener en lugar de onclick inline
    if (handler && typeof handler === 'function') {
        button.addEventListener('click', handler);
    }

    return button;
}

/**
 * Sanitiza HTML manteniendo solo tags permitidos
 * Úsalo solo cuando realmente necesites insertar HTML (ej: contenido de editor)
 *
 * @param {string} html - HTML a sanitizar
 * @param {string[]} allowedTags - Tags permitidos
 * @returns {string} HTML sanitizado
 *
 * @example
 * const clean = sanitizeHTML(userHTML, ['p', 'br', 'strong', 'em']);
 */
export function sanitizeHTML(html, allowedTags = ['p', 'br', 'strong', 'em', 'a']) {
    const temp = document.createElement('div');
    temp.innerHTML = html;

    // Eliminar todos los elementos no permitidos
    const allElements = temp.querySelectorAll('*');
    allElements.forEach(el => {
        if (!allowedTags.includes(el.tagName.toLowerCase())) {
            el.replaceWith(el.textContent);
        }
    });

    // Eliminar atributos peligrosos
    temp.querySelectorAll('[onclick], [onerror], [onload]').forEach(el => {
        el.removeAttribute('onclick');
        el.removeAttribute('onerror');
        el.removeAttribute('onload');
    });

    return temp.innerHTML;
}
