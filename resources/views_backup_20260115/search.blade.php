@extends('layouts.app')

@section('title', 'Búsqueda - Turiscovery')

@section('content')
    <div class="container" style="margin-top: 2rem; margin-bottom: 4rem;">
        <div
            style="background: linear-gradient(135deg, var(--primary), var(--secondary)); color: white; padding: 3rem; border-radius: 1rem; margin-bottom: 2rem; text-align: center;">
            <h1 style="margin: 0 0 1rem 0;">🔍 Buscar en Turiscovery</h1>
            <p style="margin: 0; opacity: 0.9;">Encuentra eventos, experiencias, promociones y más</p>
        </div>

        <div
            style="background: white; padding: 2rem; border-radius: 1rem; box-shadow: 0 2px 4px rgba(0,0,0,0.1); margin-bottom: 2rem;">
            <div style="display: flex; gap: 1rem;">
                <input type="text" id="search-input" placeholder="¿Qué estás buscando?"
                    style="flex: 1; padding: 1rem; border: 2px solid #e5e7eb; border-radius: 0.5rem; font-size: 1rem;">
                <button onclick="performSearch()" class="btn btn-primary" style="padding: 1rem 2rem;">Buscar</button>
            </div>

            <div style="display: flex; gap: 0.5rem; flex-wrap: wrap; margin-top: 1rem;">
                <button onclick="setFilter('all')" class="filter-btn active" data-filter="all">📋 Todo</button>
                <button onclick="setFilter('candelaria')" class="filter-btn" data-filter="candelaria">🎭 Candelaria</button>
                <button onclick="setFilter('experiencias')" class="filter-btn" data-filter="experiencias">🚣
                    Experiencias</button>
                <button onclick="setFilter('eventos')" class="filter-btn" data-filter="eventos">🎪 Eventos</button>
                <button onclick="setFilter('promociones')" class="filter-btn" data-filter="promociones">💰
                    Promociones</button>
            </div>
        </div>

        <div id="search-results"></div>
    </div>
@endsection

@push('styles')
    <style>
        .filter-btn {
            padding: 0.5rem 1rem;
            border: 2px solid #e5e7eb;
            background: white;
            border-radius: 0.5rem;
            cursor: pointer;
            transition: all 0.2s;
        }

        .filter-btn:hover {
            border-color: var(--primary);
            background: var(--bg-light);
        }

        .filter-btn.active {
            background: var(--primary);
            color: white;
            border-color: var(--primary);
        }

        .result-card {
            background: white;
            padding: 1.5rem;
            border-radius: 0.5rem;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            margin-bottom: 1rem;
            transition: transform 0.2s, box-shadow 0.2s;
            cursor: pointer;
        }

        .result-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
        }
    </style>
@endpush

@push('scripts')
    <script>
        let currentFilter = 'all';
        let searchResults = {
            candelaria: [],
            experiencias: [],
            eventos: [],
            promociones: []
        };

        function setFilter(filter) {
            currentFilter = filter;

            // Update button states
            document.querySelectorAll('.filter-btn').forEach(btn => {
                btn.classList.remove('active');
            });
            document.querySelector(`[data-filter="${filter}"]`).classList.add('active');

            // Re-render results
            renderResults();
        }

        async function performSearch() {
            const query = document.getElementById('search-input').value.trim();

            if (!query) {
                showNotification('Por favor ingresa un término de búsqueda', 'error');
                return;
            }

            document.getElementById('search-results').innerHTML = `
        <div class="card" style="text-align: center; padding: 3rem;">
            <p class="text-secondary">Buscando...</p>
        </div>
    `;

            try {
                // Search in all endpoints
                const [candelaria, experiencias, eventos, promociones] = await Promise.all([
                    axios.get('/api/candelaria'),
                    axios.get('/api/experiencias'),
                    axios.get('/api/eventos'),
                    axios.get('/api/promociones')
                ]);

                // Filter results by search query
                const queryLower = query.toLowerCase();

                searchResults = {
                    candelaria: candelaria.data.data.data.filter(item =>
                        item.title.toLowerCase().includes(queryLower) ||
                        item.description.toLowerCase().includes(queryLower)
                    ),
                    experiencias: experiencias.data.data.data.filter(item =>
                        item.title.toLowerCase().includes(queryLower) ||
                        item.description.toLowerCase().includes(queryLower) ||
                        (item.tags && item.tags.some(tag => tag.toLowerCase().includes(queryLower)))
                    ),
                    eventos: eventos.data.data.data.filter(item =>
                        item.title.toLowerCase().includes(queryLower) ||
                        item.description.toLowerCase().includes(queryLower)
                    ),
                    promociones: promociones.data.data.data.filter(item =>
                        item.title.toLowerCase().includes(queryLower) ||
                        item.description.toLowerCase().includes(queryLower)
                    )
                };

                renderResults();

            } catch (error) {
                console.error('Error searching:', error);
                document.getElementById('search-results').innerHTML = `
            <div class="card" style="text-align: center; padding: 3rem;">
                <p style="color: var(--secondary);">Error al buscar. Por favor intenta nuevamente.</p>
            </div>
        `;
            }
        }

        function renderResults() {
            const container = document.getElementById('search-results');
            let results = [];

            if (currentFilter === 'all') {
                results = [
                    ...searchResults.candelaria.map(item => ({
                        ...item,
                        type: 'candelaria'
                    })),
                    ...searchResults.experiencias.map(item => ({
                        ...item,
                        type: 'experiencias'
                    })),
                    ...searchResults.eventos.map(item => ({
                        ...item,
                        type: 'eventos'
                    })),
                    ...searchResults.promociones.map(item => ({
                        ...item,
                        type: 'promociones'
                    }))
                ];
            } else {
                results = searchResults[currentFilter].map(item => ({
                    ...item,
                    type: currentFilter
                }));
            }

            if (results.length === 0) {
                container.innerHTML = `
            <div class="card" style="text-align: center; padding: 3rem;">
                <div style="font-size: 4rem; margin-bottom: 1rem;">🔍</div>
                <h3>No se encontraron resultados</h3>
                <p class="text-secondary">Intenta con otros términos de búsqueda</p>
            </div>
        `;
                return;
            }

            container.innerHTML = `
        <p style="margin-bottom: 1rem; color: var(--text-secondary);">
            Se encontraron <strong>${results.length}</strong> resultados
        </p>
        ${results.map(item => renderResultCard(item)).join('')}
    `;
        }

        function renderResultCard(item) {
            const typeLabels = {
                candelaria: '🎭 Candelaria',
                experiencias: '🚣 Experiencia',
                eventos: '🎪 Evento',
                promociones: '💰 Promoción'
            };

            return `
        <div class="result-card" onclick="window.location.href='/${item.type}/${item.id}'">
            <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 0.5rem;">
                <div style="flex: 1;">
                    <span class="badge badge-primary" style="margin-bottom: 0.5rem;">${typeLabels[item.type]}</span>
                    <h3 style="margin: 0.25rem 0;">${item.title}</h3>
                </div>
                ${item.price_pen ? `<p style="color: var(--primary); font-size: 1.25rem; font-weight: 700; margin: 0;">${formatPrice(item.price_pen)}</p>` : ''}
            </div>
            <p style="color: var(--text-secondary); margin: 0.5rem 0;">
                ${item.description.substring(0, 150)}${item.description.length > 150 ? '...' : ''}
            </p>
            ${item.tags && item.tags.length > 0 ? `
                    <div style="display: flex; gap: 0.25rem; flex-wrap: wrap; margin-top: 0.5rem;">
                        ${item.tags.slice(0, 3).map(tag => `<span class="badge" style="background: var(--bg-light); color: var(--text-secondary); font-size: 0.75rem;">${tag}</span>`).join('')}
                    </div>
                ` : ''}
        </div>
    `;
        }

        // Search on Enter key
        document.addEventListener('DOMContentLoaded', () => {
            document.getElementById('search-input').addEventListener('keypress', (e) => {
                if (e.key === 'Enter') {
                    performSearch();
                }
            });

            // Check for query parameter
            const params = new URLSearchParams(window.location.search);
            const q = params.get('q');
            if (q) {
                document.getElementById('search-input').value = q;
                performSearch();
            }
        });
    </script>
@endpush
