@extends('layouts.app')

@section('title', 'Experiencias - Turiscovery')

@section('content')
    <!-- Hero Section -->
    <div class="hero" style="background: linear-gradient(135deg, #0ea5e9, #0284c7);">
        <div class="container">
            <h1 class="fade-in">🚣 Experiencias Únicas en Puno</h1>
            <p class="fade-in" style="animation-delay: 0.2s;">Turismo vivencial, aventura y cultura en el lago navegable más
                alto del mundo</p>
        </div>
    </div>

    <div class="container" style="margin-top: 3rem;">
        <!-- Filters -->
        <div
            style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-bottom: 2rem;">
            <div>
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Dificultad</label>
                <select id="difficulty-filter" class="btn btn-outline" style="width: 100%;">
                    <option value="">Todas</option>
                    <option value="easy">Fácil</option>
                    <option value="medium">Media</option>
                    <option value="hard">Difícil</option>
                </select>
            </div>
            <div>
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Precio Máximo (S/)</label>
                <input type="number" id="price-filter" class="btn btn-outline" style="width: 100%;" placeholder="Ej: 200">
            </div>
            <div>
                <button class="btn btn-primary" onclick="applyFilters()" style="margin-top: 1.75rem; width: 100%;">Aplicar
                    Filtros</button>
            </div>
        </div>

        <!-- Content Grid -->
        <div class="grid grid-3" id="experiencias-content">
            <div class="card" style="text-align: center; padding: 3rem;">
                <p class="text-secondary">Cargando experiencias...</p>
            </div>
        </div>

        <!-- Pagination -->
        <div id="pagination" style="margin-top: 2rem; text-align: center;"></div>
    </div>
@endsection

@push('scripts')
    <script>
        let currentFilters = {};
        let currentPage = 1;

        async function loadExperiencias(filters = {}, page = 1) {
            try {
                const params = {
                    per_page: 9,
                    page,
                    ...filters
                };

                const response = await api.getExperiencias(params);

                if (response.success && response.data.data.length > 0) {
                    const container = document.getElementById('experiencias-content');
                    container.innerHTML = '';

                    response.data.data.forEach(item => {
                        const card = document.createElement('div');
                        card.className = 'card fade-in';

                        const difficultyBadge = getDifficultyBadge(item.difficulty);
                        const tags = item.tags ? item.tags.slice(0, 3).map(tag =>
                            `<span class="badge" style="background: #6366f1; color: white; font-size: 0.75rem;">${tag}</span>`
                        ).join('') : '';

                        card.innerHTML = `
                    ${item.images && item.images[0] ? `<img src="${item.images[0]}" alt="${item.title}" class="card-image" onerror="this.src='https://via.placeholder.com/400x200?text=Experiencia'">` : '<div class="card-image" style="background: linear-gradient(135deg, #0ea5e9, #0284c7); display: flex; align-items: center; justify-content: center; color: white; font-size: 3rem;">🚣</div>'}
                    <div style="display: flex; gap: 0.5rem; margin-bottom: 1rem; flex-wrap: wrap;">
                        ${difficultyBadge}
                        ${tags}
                    </div>
                    <h3 style="margin-bottom: 0.5rem;">${item.title}</h3>
                    <p class="text-secondary" style="margin-bottom: 1rem;">${item.description.substring(0, 100)}...</p>
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; font-size: 0.875rem;">
                        ${item.duration_hours ? `<span>⏱️ ${item.duration_hours}h</span>` : ''}
                        ${item.max_participants ? `<span>👥 Max ${item.max_participants}</span>` : ''}
                    </div>
                    ${item.price_pen ? `<p style="color: var(--primary); font-size: 1.5rem; font-weight: 700; margin-bottom: 1rem;">${formatPrice(item.price_pen)}</p>` : '<p style="color: var(--success); font-weight: 600; margin-bottom: 1rem;">Precio a consultar</p>'}
                    <a href="/experiencias/${item.id}" class="btn btn-primary" style="width: 100%;">Ver Detalles</a>
                `;
                        container.appendChild(card);
                    });

                    renderPagination(response.data);
                } else {
                    document.getElementById('experiencias-content').innerHTML = `
                <div class="card" style="text-align: center; padding: 3rem; grid-column: 1 / -1;">
                    <p class="text-secondary">No se encontraron experiencias con estos filtros.</p>
                </div>
            `;
                }
            } catch (error) {
                console.error('Error loading Experiencias:', error);
                document.getElementById('experiencias-content').innerHTML = `
            <div class="card" style="text-align: center; padding: 3rem; grid-column: 1 / -1;">
                <p style="color: var(--secondary);">Error al cargar el contenido.</p>
            </div>
        `;
            }
        }

        function getDifficultyBadge(difficulty) {
            const badges = {
                easy: '<span class="badge badge-success">✓ Fácil</span>',
                medium: '<span class="badge badge-warning">⚡ Media</span>',
                hard: '<span class="badge" style="background: #dc2626; color: white;">🔥 Difícil</span>',
            };
            return badges[difficulty] || '';
        }

        function applyFilters() {
            const difficulty = document.getElementById('difficulty-filter').value;
            const maxPrice = document.getElementById('price-filter').value;

            currentFilters = {};
            if (difficulty) currentFilters.difficulty = difficulty;
            if (maxPrice) currentFilters.max_price = maxPrice;

            currentPage = 1;
            loadExperiencias(currentFilters, 1);
        }

        function renderPagination(data) {
            const container = document.getElementById('pagination');
            if (data.last_page <= 1) {
                container.innerHTML = '';
                return;
            }

            let html = '<div style="display: flex; gap: 0.5rem; justify-content: center;">';

            if (data.current_page > 1) {
                html +=
                `<button class="btn btn-outline" onclick="changePage(${data.current_page - 1})">← Anterior</button>`;
            }

            for (let i = 1; i <= data.last_page; i++) {
                if (i === data.current_page) {
                    html += `<button class="btn btn-primary">${i}</button>`;
                } else {
                    html += `<button class="btn btn-outline" onclick="changePage(${i})">${i}</button>`;
                }
            }

            if (data.current_page < data.last_page) {
                html +=
                    `<button class="btn btn-outline" onclick="changePage(${data.current_page + 1})">Siguiente →</button>`;
            }

            html += '</div>';
            container.innerHTML = html;
        }

        function changePage(page) {
            currentPage = page;
            loadExperiencias(currentFilters, page);
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        }

        document.addEventListener('DOMContentLoaded', () => {
            loadExperiencias();
        });
    </script>
@endpush
