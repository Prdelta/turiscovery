/**
 * Componentes de Tarjetas Reutilizables
 *
 * Funciones para crear tarjetas de forma segura (sin XSS) usando DOM API.
 * Todos los componentes usan textContent en lugar de innerHTML para prevenir XSS.
 */

import {
    createElementWithText,
    createSafeElement,
    safeSetAttribute,
    appendChildren,
    createButton
} from '../utils/dom-helpers.js';

/**
 * Crea una tarjeta de evento
 *
 * @param {Object} evento - Objeto con datos del evento
 * @returns {HTMLElement} Elemento article con la tarjeta
 *
 * @example
 * const card = createEventCard({
 *   id: 1,
 *   title: 'Festival de la Candelaria',
 *   description: 'Gran celebración...',
 *   image_url: '/images/candelaria.jpg',
 *   start_date: '2024-02-02',
 *   location: 'Plaza de Armas'
 * });
 */
export function createEventCard(evento) {
    const article = document.createElement('article');
    article.className = 'card card-hover group relative overflow-hidden';

    // Imagen del evento
    const imageDiv = document.createElement('div');
    imageDiv.className = 'relative h-56 overflow-hidden rounded-t-lg';

    if (evento.image_url) {
        const img = createSafeElement('img', {
            src: evento.image_url,
            alt: evento.title || 'Evento',
            class: 'w-full h-full object-cover group-hover:scale-110 transition-transform duration-300'
        });
        imageDiv.appendChild(img);
    } else {
        // Placeholder si no hay imagen
        const placeholder = document.createElement('div');
        placeholder.className = 'w-full h-full bg-gradient-to-br from-primary/20 to-secondary/20 flex items-center justify-center';
        const icon = document.createElement('i');
        icon.setAttribute('data-lucide', 'calendar');
        icon.className = 'w-16 h-16 text-primary/30';
        placeholder.appendChild(icon);
        imageDiv.appendChild(placeholder);
    }

    // Contenido de la tarjeta
    const content = document.createElement('div');
    content.className = 'p-6';

    // Fecha
    if (evento.start_date) {
        const dateDiv = document.createElement('div');
        dateDiv.className = 'flex items-center gap-2 text-sm text-slate-500 mb-3';

        const calendarIcon = document.createElement('i');
        calendarIcon.setAttribute('data-lucide', 'calendar');
        calendarIcon.className = 'w-4 h-4';

        const dateText = createElementWithText('span', new Date(evento.start_date).toLocaleDateString('es-PE'));

        dateDiv.appendChild(calendarIcon);
        dateDiv.appendChild(dateText);
        content.appendChild(dateDiv);
    }

    // Título
    const title = createElementWithText('h3', evento.title || 'Sin título', 'text-xl font-bold mb-3 text-slate-800 line-clamp-2');
    content.appendChild(title);

    // Descripción
    if (evento.description) {
        const desc = createElementWithText(
            'p',
            evento.description,
            'text-sm text-slate-600 mb-4 line-clamp-3'
        );
        content.appendChild(desc);
    }

    // Ubicación
    if (evento.location) {
        const locationDiv = document.createElement('div');
        locationDiv.className = 'flex items-center gap-2 text-sm text-slate-500 mb-4';

        const mapIcon = document.createElement('i');
        mapIcon.setAttribute('data-lucide', 'map-pin');
        mapIcon.className = 'w-4 h-4';

        const locationText = createElementWithText('span', evento.location);

        locationDiv.appendChild(mapIcon);
        locationDiv.appendChild(locationText);
        content.appendChild(locationDiv);
    }

    // Botón de acción
    const button = document.createElement('button');
    button.textContent = 'Confirmar Asistencia';
    button.className = 'btn btn-primary w-full';
    button.dataset.eventoId = evento.id;
    button.dataset.action = 'attend';

    content.appendChild(button);

    // Ensamblar tarjeta
    article.appendChild(imageDiv);
    article.appendChild(content);

    return article;
}

/**
 * Crea una tarjeta de experiencia
 *
 * @param {Object} experiencia - Objeto con datos de la experiencia
 * @returns {HTMLElement} Elemento article con la tarjeta
 */
export function createExperienceCard(experiencia) {
    const article = document.createElement('article');
    article.className = 'card card-hover group relative overflow-hidden';

    // Imagen
    const imageDiv = document.createElement('div');
    imageDiv.className = 'relative h-64 overflow-hidden rounded-t-lg';

    if (experiencia.image_url) {
        const img = createSafeElement('img', {
            src: experiencia.image_url,
            alt: experiencia.title || 'Experiencia',
            class: 'w-full h-full object-cover group-hover:scale-110 transition-transform duration-300'
        });
        imageDiv.appendChild(img);
    }

    // Badge de categoría
    if (experiencia.category) {
        const badge = createElementWithText(
            'span',
            experiencia.category,
            'absolute top-4 right-4 bg-white/90 backdrop-blur-sm px-3 py-1 rounded-full text-xs font-semibold text-slate-700'
        );
        imageDiv.appendChild(badge);
    }

    // Contenido
    const content = document.createElement('div');
    content.className = 'p-6';

    // Título
    const title = createElementWithText(
        'h3',
        experiencia.title || 'Sin título',
        'text-xl font-bold mb-3 text-slate-800'
    );
    content.appendChild(title);

    // Descripción
    if (experiencia.description) {
        const desc = createElementWithText(
            'p',
            experiencia.description,
            'text-sm text-slate-600 mb-4 line-clamp-2'
        );
        content.appendChild(desc);
    }

    // Footer con precio y rating
    const footer = document.createElement('div');
    footer.className = 'flex items-center justify-between mt-4 pt-4 border-t border-slate-200';

    // Precio
    if (experiencia.price) {
        const priceDiv = document.createElement('div');
        priceDiv.className = 'flex flex-col';

        const priceLabel = createElementWithText('span', 'Desde', 'text-xs text-slate-500');
        const priceValue = createElementWithText(
            'span',
            `S/ ${parseFloat(experiencia.price).toFixed(2)}`,
            'text-lg font-bold text-primary'
        );

        priceDiv.appendChild(priceLabel);
        priceDiv.appendChild(priceValue);
        footer.appendChild(priceDiv);
    }

    // Rating
    if (experiencia.rating) {
        const ratingDiv = document.createElement('div');
        ratingDiv.className = 'flex items-center gap-1';

        const starIcon = document.createElement('i');
        starIcon.setAttribute('data-lucide', 'star');
        starIcon.className = 'w-4 h-4 fill-yellow-400 text-yellow-400';

        const ratingText = createElementWithText(
            'span',
            experiencia.rating.toFixed(1),
            'text-sm font-semibold text-slate-700'
        );

        ratingDiv.appendChild(starIcon);
        ratingDiv.appendChild(ratingText);
        footer.appendChild(ratingDiv);
    }

    content.appendChild(footer);

    // Botón de ver más
    const button = document.createElement('button');
    button.textContent = 'Ver Detalles';
    button.className = 'btn btn-primary w-full mt-4';
    button.dataset.experienciaId = experiencia.id;
    button.dataset.action = 'view';

    content.appendChild(button);

    // Ensamblar
    article.appendChild(imageDiv);
    article.appendChild(content);

    return article;
}

/**
 * Crea una tarjeta de promoción
 *
 * @param {Object} promocion - Objeto con datos de la promoción
 * @returns {HTMLElement} Elemento article con la tarjeta
 */
export function createPromocionCard(promocion) {
    const article = document.createElement('article');
    article.className = 'card card-hover group relative overflow-hidden';

    // Imagen
    const imageDiv = document.createElement('div');
    imageDiv.className = 'relative h-56 overflow-hidden rounded-t-lg';

    if (promocion.image_url) {
        const img = createSafeElement('img', {
            src: promocion.image_url,
            alt: promocion.title || 'Promoción',
            class: 'w-full h-full object-cover'
        });
        imageDiv.appendChild(img);
    }

    // Badge de descuento
    if (promocion.discount) {
        const discountBadge = createElementWithText(
            'div',
            `-${promocion.discount}%`,
            'absolute top-4 right-4 bg-red-500 text-white px-4 py-2 rounded-full text-lg font-bold shadow-lg'
        );
        imageDiv.appendChild(discountBadge);
    }

    // Contenido
    const content = document.createElement('div');
    content.className = 'p-6';

    // Título
    const title = createElementWithText(
        'h3',
        promocion.title || 'Sin título',
        'text-xl font-bold mb-3 text-slate-800'
    );
    content.appendChild(title);

    // Descripción
    if (promocion.description) {
        const desc = createElementWithText(
            'p',
            promocion.description,
            'text-sm text-slate-600 mb-4 line-clamp-3'
        );
        content.appendChild(desc);
    }

    // Fechas de validez
    if (promocion.valid_until) {
        const validDiv = document.createElement('div');
        validDiv.className = 'flex items-center gap-2 text-sm text-slate-500 mb-4';

        const clockIcon = document.createElement('i');
        clockIcon.setAttribute('data-lucide', 'clock');
        clockIcon.className = 'w-4 h-4';

        const validText = createElementWithText(
            'span',
            `Válido hasta ${new Date(promocion.valid_until).toLocaleDateString('es-PE')}`
        );

        validDiv.appendChild(clockIcon);
        validDiv.appendChild(validText);
        content.appendChild(validDiv);
    }

    // Botón
    const button = document.createElement('button');
    button.textContent = 'Aplicar Promoción';
    button.className = 'btn btn-primary w-full';
    button.dataset.promocionId = promocion.id;
    button.dataset.action = 'apply';

    content.appendChild(button);

    // Ensamblar
    article.appendChild(imageDiv);
    article.appendChild(content);

    return article;
}

/**
 * Crea una tarjeta de local turístico
 *
 * @param {Object} locale - Objeto con datos del local
 * @returns {HTMLElement} Elemento article con la tarjeta
 */
export function createLocaleCard(locale) {
    const article = document.createElement('article');
    article.className = 'card card-hover group relative overflow-hidden cursor-pointer';

    // Imagen
    const imageDiv = document.createElement('div');
    imageDiv.className = 'relative h-64 overflow-hidden rounded-t-lg';

    if (locale.image_url) {
        const img = createSafeElement('img', {
            src: locale.image_url,
            alt: locale.name || 'Local',
            class: 'w-full h-full object-cover group-hover:scale-110 transition-transform duration-300'
        });
        imageDiv.appendChild(img);
    }

    // Contenido
    const content = document.createElement('div');
    content.className = 'p-6';

    // Título
    const title = createElementWithText(
        'h3',
        locale.name || 'Sin nombre',
        'text-xl font-bold mb-3 text-slate-800'
    );
    content.appendChild(title);

    // Descripción
    if (locale.description) {
        const desc = createElementWithText(
            'p',
            locale.description,
            'text-sm text-slate-600 mb-4 line-clamp-3'
        );
        content.appendChild(desc);
    }

    // Categoría y ubicación
    const metaDiv = document.createElement('div');
    metaDiv.className = 'flex flex-col gap-2 mb-4';

    if (locale.category) {
        const catDiv = document.createElement('div');
        catDiv.className = 'flex items-center gap-2 text-sm text-slate-500';

        const tagIcon = document.createElement('i');
        tagIcon.setAttribute('data-lucide', 'tag');
        tagIcon.className = 'w-4 h-4';

        const catText = createElementWithText('span', locale.category);

        catDiv.appendChild(tagIcon);
        catDiv.appendChild(catText);
        metaDiv.appendChild(catDiv);
    }

    if (locale.address) {
        const addressDiv = document.createElement('div');
        addressDiv.className = 'flex items-center gap-2 text-sm text-slate-500';

        const mapIcon = document.createElement('i');
        mapIcon.setAttribute('data-lucide', 'map-pin');
        mapIcon.className = 'w-4 h-4';

        const addressText = createElementWithText('span', locale.address);

        addressDiv.appendChild(mapIcon);
        addressDiv.appendChild(addressText);
        metaDiv.appendChild(addressDiv);
    }

    content.appendChild(metaDiv);

    // Rating
    if (locale.rating) {
        const ratingDiv = document.createElement('div');
        ratingDiv.className = 'flex items-center gap-2 mb-4';

        for (let i = 0; i < 5; i++) {
            const star = document.createElement('i');
            star.setAttribute('data-lucide', 'star');
            if (i < Math.floor(locale.rating)) {
                star.className = 'w-4 h-4 fill-yellow-400 text-yellow-400';
            } else {
                star.className = 'w-4 h-4 text-slate-300';
            }
            ratingDiv.appendChild(star);
        }

        const ratingText = createElementWithText(
            'span',
            `(${locale.rating.toFixed(1)})`,
            'text-sm text-slate-600 ml-1'
        );
        ratingDiv.appendChild(ratingText);
        content.appendChild(ratingDiv);
    }

    // Botón
    const button = document.createElement('button');
    button.textContent = 'Ver Detalles';
    button.className = 'btn btn-primary w-full';
    button.dataset.localeId = locale.id;
    button.dataset.action = 'view';

    content.appendChild(button);

    // Ensamblar
    article.appendChild(imageDiv);
    article.appendChild(content);

    return article;
}

/**
 * Crea un estado vacío
 *
 * @param {string} message - Mensaje a mostrar
 * @param {string} icon - Nombre del icono de Lucide
 * @returns {HTMLElement} Elemento div con el estado vacío
 */
export function createEmptyState(message, icon = 'inbox') {
    const div = document.createElement('div');
    div.className = 'empty-state text-center py-12';

    const iconEl = document.createElement('i');
    iconEl.setAttribute('data-lucide', icon);
    iconEl.className = 'w-16 h-16 mx-auto mb-4 text-slate-300';

    const text = createElementWithText('p', message, 'text-slate-500 text-lg');

    div.appendChild(iconEl);
    div.appendChild(text);

    return div;
}

/**
 * Crea un estado de carga
 *
 * @param {string} message - Mensaje opcional
 * @returns {HTMLElement} Elemento div con el spinner
 */
export function createLoadingState(message = 'Cargando...') {
    const div = document.createElement('div');
    div.className = 'loading-state text-center py-12';

    const spinner = document.createElement('div');
    spinner.className = 'spinner mx-auto mb-4';

    const text = createElementWithText('p', message, 'text-slate-500');

    div.appendChild(spinner);
    div.appendChild(text);

    return div;
}
