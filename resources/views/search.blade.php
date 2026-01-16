@extends('layouts.app')

@section('title', 'Buscar')

@section('content')

    <section class="hero">
        <div class="container">
            <h1>Busca tu Experiencia Perfecta</h1>
            <p>Explora locales, eventos, experiencias y promociones en Puno</p>
        </div>
    </section>

    <section class="section">
        <div class="container">
            <!-- Search Form -->
            <div class="card" style="margin-bottom: var(--spacing-xl);">
                <div style="padding: var(--spacing-xl);">
                    <form id="search-form">
                        <div class="grid grid-2">
                            <div>
                                <label for="search-query"
                                    style="display: block; margin-bottom: var(--spacing-xs); font-weight: 600; color: var(--color-text);">
                                    ¿Qué estás buscando?
                                </label>
                                <input type="text" id="search-query" name="query"
                                    placeholder="Ej: Restaurante, Tour Titicaca..."
                                    style="width: 100%; padding: 0.75rem 1rem; border: 2px solid var(--color-gray-light); border-radius: var(--radius-md); font-size: 1rem; transition: border-color 0.3s ease;"
                                    onfocus="this.style.borderColor='var(--color-primary)'"
                                    onblur="this.style.borderColor='var(--color-gray-light)'">
                            </div>
                            <div>
                                <label for="search-type"
                                    style="display: block; margin-bottom: var(--spacing-xs); font-weight: 600; color: var(--color-text);">
                                    Categoría
                                </label>
                                <select id="search-type" name="type"
                                    style="width: 100%; padding: 0.75rem 1rem; border: 2px solid var(--color-gray-light); border-radius: var(--radius-md); font-size: 1rem; background: white;">
                                    <option value="all">Todas las categorías</option>
                                    <option value="locales">Locales</option>
                                    <option value="eventos">Eventos</option>
                                    <option value="experiencias">Experiencias</option>
                                    <option value="promociones">Promociones</option>
                                </select>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary mt-3" style="width: 100%;">
                            <i data-lucide="search"></i>
                            Buscar
                        </button>
                    </form>
                </div>
            </div>

            <!-- Filter Badges -->
            <div style="display: flex; gap: var(--spacing-sm); margin-bottom: var(--spacing-lg); flex-wrap: wrap;">
                <button class="badge badge-primary filter-badge active" data-filter="all">
                    <i data-lucide="globe"></i>
                    Todos
                </button>
                <button class="badge badge-info filter-badge" data-filter="locales">
                    <i data-lucide="store"></i>
                    Locales
                </button>
                <button class="badge filter-badge" data-filter="eventos"
                    style="background: var(--color-info-dark); color: white;">
                    <i data-lucide="calendar"></i>
                    Eventos
                </button>
                <button class="badge filter-badge" data-filter="experiencias"
                    style="background: var(--color-secondary); color: white;">
                    <i data-lucide="compass"></i>
                    Experiencias
                </button>
                <button class="badge filter-badge" data-filter="promociones"
                    style="background: var(--color-accent); color: white;">
                    <i data-lucide="percent"></i>
                    Promociones
                </button>
            </div>

            <!-- Results Header -->
            <div class="dashboard-header">
                <div>
                    <h2 class="mb-0">Resultados</h2>
                    <p class="text-secondary mb-0" id="results-count">Cargando...</p>
                </div>
                <div style="display: flex; gap: var(--spacing-sm);">
                    <button class="btn btn-outline" id="grid-view">
                        <i data-lucide="grid"></i>
                    </button>
                    <button class="btn btn-outline" id="list-view">
                        <i data-lucide="list"></i>
                    </button>
                </div>
            </div>

            <!-- Results Grid -->
            <div id="results-container" class="grid grid-4">
                <div class="card">
                    <div style="padding: var(--spacing-xl); text-align: center;">
                        <i data-lucide="loader"
                            style="width: 48px; height: 48px; color: var(--color-primary); animation: spin 1s linear infinite;"></i>
                        <p class="text-secondary mt-2">Cargando resultados...</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

@endsection

@push('styles')
    <style>
        @keyframes spin {
            from {
                transform: rotate(0deg);
            }

            to {
                transform: rotate(360deg);
            }
        }

        .filter-badge {
            cursor: pointer;
            transition: all 0.3s ease;
            border: 2px solid transparent;
        }

        .filter-badge:hover {
            transform: scale(1.05);
        }

        .filter-badge.active {
            border-color: rgba(0, 0, 0, 0.2);
            box-shadow: 0 4px 12px rgba(94, 92, 232, 0.25);
        }

        input:focus,
        select:focus {
            outline: none;
            border-color: var(--color-primary) !important;
            box-shadow: 0 0 0 3px rgba(94, 92, 232, 0.1);
        }
    </style>
@endpush

@push('scripts')
    <script>
        let currentFilter = 'all';
        let currentQuery = '';

        document.addEventListener('DOMContentLoaded', () => {
            lucide.createIcons();
            loadResults();

            // Search form
            document.getElementById('search-form')?.addEventListener('submit', (e) => {
                e.preventDefault();
                currentQuery = document.getElementById('search-query').value;
                loadResults();
            });

            // Filter badges
            document.querySelectorAll('.filter-badge').forEach(badge => {
                badge.addEventListener('click', () => {
                    document.querySelectorAll('.filter-badge').forEach(b => b.classList.remove(
                        'active'));
                    badge.classList.add('active');
                    currentFilter = badge.dataset.filter;
                    loadResults();
                });
            });
        });

        async function loadResults() {
            const container = document.getElementById('results-container');
            const countEl = document.getElementById('results-count');

            container.innerHTML =
                '<div class="card"><div style="padding: var(--spacing-xl); text-align: center;"><i data-lucide="loader" style="width: 48px; height: 48px; color: var(--color-primary); animation: spin 1s linear infinite;"></i><p class="text-secondary mt-2">Buscando...</p></div></div>';

            try {
                let endpoints = [];
                if (currentFilter === 'all' || currentFilter === 'locales') {
                    endpoints.push({
                        type: 'locales',
                        url: '/api/locales?limit=10'
                    });
                }
                if (currentFilter === 'all' || currentFilter === 'eventos') {
                    endpoints.push({
                        type: 'eventos',
                        url: '/api/eventos?limit=10'
                    });
                }
                if (currentFilter === 'all' || currentFilter === 'experiencias') {
                    endpoints.push({
                        type: 'experiencias',
                        url: '/api/experiencias?limit=10'
                    });
                }
                if (currentFilter === 'all' || currentFilter === 'promociones') {
                    endpoints.push({
                        type: 'promociones',
                        url: '/api/promociones?limit=10'
                    });
                }

                const responses = await Promise.all(
                    endpoints.map(ep => axios.get(ep.url).then(res => ({
                        ...res,
                        contentType: ep.type
                    })))
                );

                let allResults = [];
                responses.forEach(response => {
                    if (response.data.success && response.data.data) {
                        response.data.data.forEach(item => {
                            allResults.push({
                                ...item,
                                contentType: response.contentType
                            });
                        });
                    }
                });

                // Filter by query if exists
                if (currentQuery) {
                    allResults = allResults.filter(item => {
                        const searchText = `${item.name || item.title} ${item.description}`.toLowerCase();
                        return searchText.includes(currentQuery.toLowerCase());
                    });
                }

                countEl.textContent =
                    `${allResults.length} resultado${allResults.length !== 1 ? 's' : ''} encontrado${allResults.length !== 1 ? 's' : ''}`;

                if (allResults.length === 0) {
                    container.innerHTML = `
                    <div class="card" style="grid-column: 1 / -1;">
                        <div style="padding: var(--spacing-xl); text-align: center;">
                            <i data-lucide="inbox" style="width: 64px; height: 64px; color: var(--color-gray); margin-bottom: var(--spacing-md);"></i>
                            <h3>No se encontraron resultados</h3>
                            <p class="text-secondary">Intenta con otros términos de búsqueda</p>
                        </div>
                    </div>
                `;
                    lucide.createIcons();
                    return;
                }

                container.innerHTML = allResults.map(item => `
                <article class="card fade-in">
                    <img src="${item.image_url || item.images?.[0] || 'https://via.placeholder.com/400x240?text=' + (item.name || item.title)}" 
                         alt="${item.name || item.title}" 
                         class="card-image">
                    <div style="padding: var(--spacing-md);">
                        <div class="badge badge-${getBadgeClass(item.contentType)}" style="margin-bottom: var(--spacing-sm);">
                            ${getTypeName(item.contentType)}
                        </div>
                        <h3 style="font-size: 1.125rem; margin-bottom: var(--spacing-sm);">${item.name || item.title}</h3>
                        <p class="text-secondary" style="font-size: 0.875rem; line-height: 1.5; margin-bottom: var(--spacing-md); display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                            ${item.description || 'Sin descripción'}
                        </p>
                        <div style="display: flex; align-items: center; gap: var(--spacing-xs); color: var(--color-text-muted); font-size: 0.875rem;">
                            <i data-lucide="map-pin" style="width: 16px; height: 16px;"></i>
                            <span>${item.location || item.address || 'Puno'}</span>
                        </div>
                    </div>
                </article>
            `).join('');

                lucide.createIcons();
            } catch (error) {
                console.error('Error loading results:', error);
                container.innerHTML = `
                <div class="card" style="grid-column: 1 / -1;">
                    <div style="padding: var(--spacing-xl); text-align: center;">
                        <i data-lucide="alert-circle" style="width: 64px; height: 64px; color: var(--color-danger); margin-bottom: var(--spacing-md);"></i>
                        <h3>Error al cargar resultados</h3>
                        <p class="text-secondary">Por favor, intenta de nuevo más tarde</p>
                    </div>
                </div>
            `;
                lucide.createIcons();
            }
        }

        function getBadgeClass(type) {
            const classes = {
                'locales': 'primary',
                'eventos': 'info',
                'experiencias': 'success',
                'promociones': 'warning'
            };
            return classes[type] || 'primary';
        }

        function getTypeName(type) {
            const names = {
                'locales': 'Local',
                'eventos': 'Evento',
                'experiencias': 'Experiencia',
                'promociones': 'Promoción'
            };
            return names[type] || type;
        }
    </script>
@endpush
