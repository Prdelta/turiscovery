@extends('layouts.app')

@section('title', 'Eventos - Turiscovery')

@section('content')
    <!-- Hero Section -->
    <div class="hero" style="background: linear-gradient(135deg, #8b5cf6, #7c3aed);">
        <div class="container">
            <h1 class="fade-in">🎪 Eventos Culturales en Puno</h1>
            <p class="fade-in" style="animation-delay: 0.2s;">Conciertos, festivales y vida cultural</p>
        </div>
    </div>

    <div class="container" style="margin-top: 3rem;">
        <!-- Filters -->
        <div style="display: flex; gap: 1rem; flex-wrap: wrap; margin-bottom: 2rem;">
            <button class="btn btn-outline filter-btn" data-category="all">Todos</button>
            <button class="btn btn-outline filter-btn" data-category="concert">Conciertos</button>
            <button class="btn btn-outline filter-btn" data-category="festival">Festivales</button>
            <button class="btn btn-outline filter-btn" data-category="cultural">Culturales</button>
            <button class="btn btn-outline filter-btn" data-category="nightlife">Vida Nocturna</button>
            <button class="btn btn-outline filter-btn" data-category="sports">Deportivos</button>
        </div>

        <!-- Status Filter -->
        <div style="display: flex; gap: 1rem; margin-bottom: 2rem;">
            <button class="btn btn-primary status-btn" data-status="">Todos los Eventos</button>
            <button class="btn btn-outline status-btn" data-status="upcoming">Próximos</button>
            <button class="btn btn-outline status-btn" data-status="ongoing">En Curso</button>
        </div>

        <!-- Content Grid -->
        <div class="grid grid-3" id="eventos-content">
            <div class="card" style="text-align: center; padding: 3rem;">
                <p class="text-secondary">Cargando eventos...</p>
            </div>
        </div>

        <!-- Pagination -->
        <div id="pagination" style="margin-top: 2rem; text-align: center;"></div>
    </div>
@endsection

@push('scripts')
    <script>
        let currentCategory = 'all';
        let currentStatus = '';
        let currentPage = 1;

        async function loadEventos(category = 'all', status = '', page = 1) {
            try {
                const params = {
                    per_page: 9,
                    page
                };
                if (category !== 'all') params.category = category;
                if (status) params.status = status;

                const response = await api.getEventos(params);

                if (response.success && response.data.data.length > 0) {
                    const container = document.getElementById('eventos-content');
                    container.innerHTML = '';

                    response.data.data.forEach(item => {
                        const card = document.createElement('div');
                        card.className = 'card fade-in';

                        const categoryBadge = getCategoryBadge(item.category);
                        const isFree = !item.ticket_price || item.ticket_price == 0;

                        // Calculate event status
                        const now = new Date();
                        const startTime = new Date(item.start_time);
                        const endTime = new Date(item.end_time);
                        let statusBadge = '';

                        if (now < startTime) {
                            statusBadge =
                                '<span class="badge" style="background: #10b981; color: white;">📅 Próximo</span>';
                        } else if (now >= startTime && now <= endTime) {
                            statusBadge =
                                '<span class="badge" style="background: #f59e0b; color: white;">🔴 En Vivo</span>';
                        }

                        card.innerHTML = `
                    ${item.images && item.images[0] ? `<img src="${item.images[0]}" alt="${item.title}" class="card-image" onerror="this.src='https://via.placeholder.com/400x200?text=Evento'">` : '<div class="card-image" style="background: linear-gradient(135deg, #8b5cf6, #7c3aed); display: flex; align-items: center; justify-content: center; color: white; font-size: 3rem;">🎪</div>'}
                    <div style="display: flex; gap: 0.5rem; margin-bottom: 1rem; flex-wrap: wrap;">
                        ${categoryBadge}
                        ${statusBadge}
                    </div>
                    <h3 style="margin-bottom: 0.5rem;">${item.title}</h3>
                    <p class="text-secondary" style="margin-bottom: 1rem;">${item.description.substring(0, 100)}...</p>
                    <div style="margin-bottom: 1rem;">
                        <p style="font-size: 0.875rem; color: var(--accent);">
                            <strong>📅 Inicio:</strong> ${formatDate(item.start_time)}
                        </p>
                        <p style="font-size: 0.875rem; color: var(--accent);">
                            <strong>🕐 Fin:</strong> ${formatDate(item.end_time)}
                        </p>
                    </div>
                    ${isFree ? 
                        '<p style="color: var(--success); font-size: 1.25rem; font-weight: 700; margin-bottom: 1rem;">🎉 GRATIS</p>' :
                        `<p style="color: var(--primary); font-size: 1.25rem; font-weight: 700; margin-bottom: 1rem;">Entrada: ${formatPrice(item.ticket_price)}</p>`
                    }
                    <a href="/eventos/${item.id}" class="btn btn-primary" style="width: 100%;">Ver Detalles</a>
                `;
                        container.appendChild(card);
                    });

                    renderPagination(response.data);
                } else {
                    document.getElementById('eventos-content').innerHTML = `
                <div class="card" style="text-align: center; padding: 3rem; grid-column: 1 / -1;">
                    <p class="text-secondary">No se encontraron eventos.</p>
                </div>
            `;
                }
            } catch (error) {
                console.error('Error loading Eventos:', error);
                document.getElementById('eventos-content').innerHTML = `
            <div class="card" style="text-align: center; padding: 3rem; grid-column: 1 / -1;">
                <p style="color: var(--secondary);">Error al cargar eventos.</p>
            </div>
        `;
            }
        }

        function getCategoryBadge(category) {
            const badges = {
                concert: '<span class="badge" style="background: #ec4899; color: white;">🎵 Concierto</span>',
                festival: '<span class="badge" style="background: #f59e0b; color: white;">🎉 Festival</span>',
                cultural: '<span class="badge" style="background: #0ea5e9; color: white;">🎭 Cultural</span>',
                nightlife: '<span class="badge" style="background: #8b5cf6; color: white;">🌙 Nocturno</span>',
                sports: '<span class="badge" style="background: #10b981; color: white;">⚽ Deportivo</span>',
                exhibition: '<span class="badge" style="background: #6366f1; color: white;">🖼️ Exposición</span>',
            };
            return badges[category] || '<span class="badge badge-primary">Evento</span>';
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

            for (let i = 1; i <= Math.min(data.last_page, 5); i++) {
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
            loadEventos(currentCategory, currentStatus, page);
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        }

        document.addEventListener('DOMContentLoaded', () => {
            loadEventos();

            // Category filters
            document.querySelectorAll('.filter-btn').forEach(btn => {
                btn.addEventListener('click', () => {
                    document.querySelectorAll('.filter-btn').forEach(b => {
                        b.classList.remove('btn-primary');
                        b.classList.add('btn-outline');
                    });
                    btn.classList.remove('btn-outline');
                    btn.classList.add('btn-primary');

                    currentCategory = btn.dataset.category;
                    currentPage = 1;
                    loadEventos(currentCategory, currentStatus, 1);
                });
            });

            // Status filters
            document.querySelectorAll('.status-btn').forEach(btn => {
                btn.addEventListener('click', () => {
                    document.querySelectorAll('.status-btn').forEach(b => {
                        b.classList.remove('btn-primary');
                        b.classList.add('btn-outline');
                    });
                    btn.classList.remove('btn-outline');
                    btn.classList.add('btn-primary');

                    currentStatus = btn.dataset.status;
                    currentPage = 1;
                    loadEventos(currentCategory, currentStatus, 1);
                });
            });
        });
    </script>
@endpush
