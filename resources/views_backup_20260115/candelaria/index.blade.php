@extends('layouts.app')

@section('title', 'Candelaria - Turiscovery')

@section('content')
    <!-- Hero Section -->
    <div class="hero" style="background: linear-gradient(135deg, #dc2626, #991b1b);">
        <div class="container">
            <h1 class="fade-in">🎭 Festividad de la Virgen de la Candelaria</h1>
            <p class="fade-in" style="animation-delay: 0.2s;">Patrimonio Cultural Inmaterial de la Humanidad - UNESCO</p>
        </div>
    </div>

    <div class="container" style="margin-top: 3rem;">
        <!-- Filters -->
        <div style="display: flex; gap: 1rem; flex-wrap: wrap; margin-bottom: 2rem;">
            <button class="btn btn-outline filter-btn" data-category="all">Todos</button>
            <button class="btn btn-outline filter-btn" data-category="dance">Danzas</button>
            <button class="btn btn-outline filter-btn" data-category="history">Historia</button>
            <button class="btn btn-outline filter-btn" data-category="costume">Trajes</button>
            <button class="btn btn-outline filter-btn" data-category="music">Música</button>
            <button class="btn btn-outline filter-btn" data-category="tradition">Tradiciones</button>
        </div>

        <!-- Content Grid -->
        <div class="grid grid-3" id="candelaria-content">
            <div class="card" style="text-align: center; padding: 3rem;">
                <p class="text-secondary">Cargando contenido...</p>
            </div>
        </div>

        <!-- Pagination -->
        <div id="pagination" style="margin-top: 2rem; text-align: center;"></div>
    </div>
@endsection

@push('scripts')
    <script>
        let currentCategory = 'all';
        let currentPage = 1;

        async function loadCandelaria(category = 'all', page = 1) {
            try {
                const params = {
                    per_page: 9,
                    page
                };
                if (category !== 'all') {
                    params.category = category;
                }

                const response = await api.getCandelaria(params);

                if (response.success && response.data.data.length > 0) {
                    const container = document.getElementById('candelaria-content');
                    container.innerHTML = '';

                    response.data.data.forEach(item => {
                        const card = document.createElement('div');
                        card.className = 'card fade-in';

                        const categoryBadge = getCategoryBadge(item.category);
                        const featuredBadge = item.is_featured ?
                            '<span class="badge badge-warning" style="margin-left: 0.5rem;">⭐ Destacado</span>' :
                            '';

                        card.innerHTML = `
                    ${item.images && item.images[0] ? `<img src="${item.images[0]}" alt="${item.title}" class="card-image" onerror="this.src='https://via.placeholder.com/400x200?text=Candelaria'">` : '<div class="card-image" style="background: linear-gradient(135deg, #dc2626, #991b1b); display: flex; align-items: center; justify-content: center; color: white; font-size: 3rem;">🎭</div>'}
                    <div style="display: flex; gap: 0.5rem; margin-bottom: 1rem;">
                        ${categoryBadge}
                        ${featuredBadge}
                    </div>
                    <h3 style="margin-bottom: 0.5rem;">${item.title}</h3>
                    <p class="text-secondary" style="margin-bottom: 1rem;">${item.description.substring(0, 120)}...</p>
                    ${item.event_date ? `<p style="color: var(--accent); font-size: 0.875rem; margin-bottom: 1rem;">📅 ${formatDate(item.event_date)}</p>` : ''}
                    <a href="/candelaria/${item.id}" class="btn btn-primary" style="width: 100%;">Ver Detalles</a>
                `;
                        container.appendChild(card);
                    });

                    // Pagination
                    renderPagination(response.data);
                } else {
                    document.getElementById('candelaria-content').innerHTML = `
                <div class="card" style="text-align: center; padding: 3rem; grid-column: 1 / -1;">
                    <p class="text-secondary">No se encontró contenido en esta categoría.</p>
                </div>
            `;
                }
            } catch (error) {
                console.error('Error loading Candelaria:', error);
                document.getElementById('candelaria-content').innerHTML = `
            <div class="card" style="text-align: center; padding: 3rem; grid-column: 1 / -1;">
                <p style="color: var(--secondary);">Error al cargar el contenido. Por favor, intenta nuevamente.</p>
            </div>
        `;
            }
        }

        function getCategoryBadge(category) {
            const badges = {
                dance: '<span class="badge" style="background: #8b5cf6; color: white;">💃 Danza</span>',
                history: '<span class="badge" style="background: #0ea5e9; color: white;">📚 Historia</span>',
                costume: '<span class="badge" style="background: #ec4899; color: white;">👗 Traje</span>',
                music: '<span class="badge" style="background: #f59e0b; color: white;">🎵 Música</span>',
                tradition: '<span class="badge" style="background: #10b981; color: white;">🎎 Tradición</span>',
                procession: '<span class="badge" style="background: #6366f1; color: white;">⛪ Procesión</span>',
            };
            return badges[category] || '<span class="badge badge-primary">Candelaria</span>';
        }

        function renderPagination(data) {
            const container = document.getElementById('pagination');
            if (data.last_page <= 1) {
                container.innerHTML = '';
                return;
            }

            let html = '<div style="display: flex; gap: 0.5rem; justify-content: center;">';

            // Previous
            if (data.current_page > 1) {
                html +=
                `<button class="btn btn-outline" onclick="changePage(${data.current_page - 1})">← Anterior</button>`;
            }

            // Page numbers
            for (let i = 1; i <= data.last_page; i++) {
                if (i === data.current_page) {
                    html += `<button class="btn btn-primary">${i}</button>`;
                } else {
                    html += `<button class="btn btn-outline" onclick="changePage(${i})">${i}</button>`;
                }
            }

            // Next
            if (data.current_page < data.last_page) {
                html +=
                    `<button class="btn btn-outline" onclick="changePage(${data.current_page + 1})">Siguiente →</button>`;
            }

            html += '</div>';
            container.innerHTML = html;
        }

        function changePage(page) {
            currentPage = page;
            loadCandelaria(currentCategory, page);
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        }

        // Filter buttons
        document.addEventListener('DOMContentLoaded', () => {
            loadCandelaria();

            document.querySelectorAll('.filter-btn').forEach(btn => {
                btn.addEventListener('click', () => {
                    // Update active state
                    document.querySelectorAll('.filter-btn').forEach(b => {
                        b.classList.remove('btn-primary');
                        b.classList.add('btn-outline');
                    });
                    btn.classList.remove('btn-outline');
                    btn.classList.add('btn-primary');

                    // Load filtered content
                    currentCategory = btn.dataset.category;
                    currentPage = 1;
                    loadCandelaria(currentCategory, 1);
                });
            });
        });
    </script>
@endpush
