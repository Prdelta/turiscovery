@extends('layouts.app')

@section('title', 'Promociones - Turiscovery')

@section('content')
    <!-- Hero Section -->
    <div class="hero" style="background: linear-gradient(135deg, #f59e0b, #d97706);">
        <div class="container">
            <h1 class="fade-in">💰 Promociones Exclusivas</h1>
            <p class="fade-in" style="animation-delay: 0.2s;">Ofertas especiales de nuestros socios locales</p>
        </div>
    </div>

    <div class="container" style="margin-top: 3rem;">
        <!-- Filters -->
        <div style="display: flex; gap: 1rem; flex-wrap: wrap; margin-bottom: 2rem;">
            <button class="btn btn-outline filter-btn" data-type="">Todas</button>
            <button class="btn btn-outline filter-btn" data-type="2x1">🎁 2x1</button>
            <button class="btn btn-outline filter-btn" data-type="percentage">💯 Porcentaje</button>
            <button class="btn btn-outline filter-btn" data-type="fixed">💵 Descuento Fijo</button>
        </div>

        <!-- Content Grid -->
        <div class="grid grid-3" id="promociones-content">
            <div class="card" style="text-align: center; padding: 3rem;">
                <p class="text-secondary">Cargando promociones...</p>
            </div>
        </div>

        <!-- Pagination -->
        <div id="pagination" style="margin-top: 2rem; text-align: center;"></div>
    </div>
@endsection

@push('scripts')
    <script>
        let currentType = '';
        let currentPage = 1;

        async function loadPromociones(type = '', page = 1) {
            try {
                const params = {
                    per_page: 9,
                    page
                };
                if (type) params.type = type;

                const response = await api.getPromociones(params);

                if (response.success && response.data.data.length > 0) {
                    const container = document.getElementById('promociones-content');
                    container.innerHTML = '';

                    response.data.data.forEach(item => {
                        const card = document.createElement('div');
                        card.className = 'card fade-in';
                        card.style.border = '3px solid var(--accent)';

                        const typeBadge = getTypeBadge(item.discount_type);
                        const endDate = new Date(item.end_date);
                        const now = new Date();
                        const daysLeft = Math.ceil((endDate - now) / (1000 * 60 * 60 * 24));

                        let urgencyBadge = '';
                        if (daysLeft <= 3 && daysLeft > 0) {
                            urgencyBadge =
                                '<span class="badge" style="background: #dc2626; color: white; animation: pulse 2s infinite;">⏰ ¡ÚLTIMOS DÍAS!</span>';
                        } else if (daysLeft <= 7) {
                            urgencyBadge = '<span class="badge badge-warning">⏱️ Termina pronto</span>';
                        }

                        card.innerHTML = `
                    ${item.images && item.images[0] ? `<img src="${item.images[0]}" alt="${item.title}" class="card-image" onerror="this.src='https://via.placeholder.com/400x200?text=Promoción'">` : '<div class="card-image" style="background: linear-gradient(135deg, #f59e0b, #d97706); display: flex; align-items: center; justify-content: center; color: white; font-size: 3rem;">💰</div>'}
                    <div style="display: flex; gap: 0.5rem; margin-bottom: 1rem; flex-wrap: wrap;">
                        ${typeBadge}
                        ${urgencyBadge}
                    </div>
                    <h3 style="margin-bottom: 0.5rem;">${item.title}</h3>
                    <p class="text-secondary" style="margin-bottom: 1rem;">${item.description.substring(0, 100)}...</p>
                    
                    ${getDiscountDisplay(item)}
                    
                    <div style="margin: 1rem 0; padding: 1rem; background: var(--bg-light); border-radius: 0.5rem;">
                        <p style="font-size: 0.875rem;"><strong>📅 Válido hasta:</strong> ${formatDate(item.end_date)}</p>
                        ${item.redemption_code ? `<p style="font-size: 0.875rem; margin-top: 0.5rem;"><strong>🎟️ Código:</strong> <span style="background: var(--primary); color: white; padding: 0.25rem 0.5rem; border-radius: 0.25rem; font-family: monospace;">${item.redemption_code}</span></p>` : ''}
                    </div>
                    
                    <a href="/promociones/${item.id}" class="btn btn-primary" style="width: 100%; background: linear-gradient(135deg, var(--accent), #d97706);">Ver Promoción</a>
                `;
                        container.appendChild(card);
                    });

                    renderPagination(response.data);
                } else {
                    document.getElementById('promociones-content').innerHTML = `
                <div class="card" style="text-align: center; padding: 3rem; grid-column: 1 / -1;">
                    <p class="text-secondary">No se encontraron promociones activas.</p>
                </div>
            `;
                }
            } catch (error) {
                console.error('Error loading Promociones:', error);
                document.getElementById('promociones-content').innerHTML = `
            <div class="card" style="text-align: center; padding: 3rem; grid-column: 1 / -1;">
                <p style="color: var(--secondary);">Error al cargar promociones.</p>
            </div>
        `;
            }
        }

        function getTypeBadge(type) {
            const badges = {
                '2x1': '<span class="badge" style="background: #ec4899; color: white; font-size: 1rem;">🎁 2x1</span>',
                'percentage': '<span class="badge" style="background: #8b5cf6; color: white; font-size: 1rem;">💯 % DESC</span>',
                'fixed': '<span class="badge" style="background: #10b981; color: white; font-size: 1rem;">💵 DESC FIJO</span>',
            };
            return badges[type] || '';
        }

        function getDiscountDisplay(item) {
            if (item.discount_type === '2x1') {
                return `
            <div style="text-align: center; margin: 1.5rem 0;">
                <p style="font-size: 2.5rem; font-weight: 800; color: var(--accent); line-height: 1;">2×1</p>
                ${item.original_price ? `<p style="text-decoration: line-through; color: var(--text-secondary);">Precio normal: ${formatPrice(item.original_price)}</p>` : ''}
            </div>
        `;
            } else if (item.discount_type === 'percentage') {
                return `
            <div style="text-align: center; margin: 1.5rem 0;">
                <p style="font-size: 3rem; font-weight: 800; color: var(--accent); line-height: 1;">${item.discount_percentage}%</p>
                <p style="color: var(--text-secondary); font-size: 0.875rem;">DE DESCUENTO</p>
                ${item.original_price ? `
                        <div style="margin-top: 1rem;">
                            <span style="text-decoration: line-through; color: var(--text-secondary); margin-right: 1rem;">${formatPrice(item.original_price)}</span>
                            <span style="color: var(--success); font-size: 1.5rem; font-weight: 700;">${formatPrice(item.final_price)}</span>
                        </div>
                    ` : ''}
            </div>
        `;
            } else if (item.discount_type === 'fixed') {
                return `
            <div style="text-align: center; margin: 1.5rem 0;">
                <p style="font-size: 2rem; font-weight: 800; color: var(--accent); line-height: 1;">-${formatPrice(item.discount_amount)}</p>
                <p style="color: var(--text-secondary); font-size: 0.875rem;">DE DESCUENTO</p>
                ${item.original_price ? `
                        <div style="margin-top: 1rem;">
                            <span style="text-decoration: line-through; color: var(--text-secondary); margin-right: 1rem;">${formatPrice(item.original_price)}</span>
                            <span style="color: var(--success); font-size: 1.5rem; font-weight: 700;">${formatPrice(item.final_price)}</span>
                        </div>
                    ` : ''}
            </div>
        `;
            }
            return '';
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
            loadPromociones(currentType, page);
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        }

        // Add pulse animation
        const style = document.createElement('style');
        style.textContent = `
    @keyframes pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.7; }
    }
`;
        document.head.appendChild(style);

        document.addEventListener('DOMContentLoaded', () => {
            loadPromociones();

            document.querySelectorAll('.filter-btn').forEach(btn => {
                btn.addEventListener('click', () => {
                    document.querySelectorAll('.filter-btn').forEach(b => {
                        b.classList.remove('btn-primary');
                        b.classList.add('btn-outline');
                    });
                    btn.classList.remove('btn-outline');
                    btn.classList.add('btn-primary');

                    currentType = btn.dataset.type;
                    currentPage = 1;
                    loadPromociones(currentType, 1);
                });
            });
        });
    </script>
@endpush
