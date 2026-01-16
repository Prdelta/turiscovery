@extends('layouts.app')

@section('title', 'Promoción - Turiscovery')

@section('content')
    <div class="container" style="margin-top: 2rem; margin-bottom: 4rem;">
        <a href="/promociones" class="btn btn-outline" style="margin-bottom: 2rem;">← Volver a Promociones</a>

        <div id="promocion-detail">
            <div class="card" style="text-align: center; padding: 3rem;">
                <p class="text-secondary">Cargando promoción...</p>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        const promocionId = window.location.pathname.split('/').pop();

        async function loadPromocion() {
            try {
                const response = await axios.get(`/api/promociones/${promocionId}`);
                const promo = response.data.data;

                const container = document.getElementById('promocion-detail');

                const now = new Date();
                const endDate = new Date(promo.end_date);
                const isActive = endDate > now;
                const daysLeft = Math.ceil((endDate - now) / (1000 * 60 * 60 * 24));

                let urgencyBadge = '';
                if (isActive && daysLeft <= 3 && daysLeft > 0) {
                    urgencyBadge =
                        '<span class="badge" style="background: #dc2626; color: white; font-size: 1rem; animation: pulse 2s infinite;">⏰ ¡ÚLTIMOS DÍAS!</span>';
                } else if (isActive && daysLeft <= 7) {
                    urgencyBadge =
                    '<span class="badge badge-warning" style="font-size: 1rem;">⏱️ Termina pronto</span>';
                }

                container.innerHTML = `
            <div class="grid grid-3" style="gap: 2rem;">
                <div style="grid-column: span 2;">
                    ${promo.images && promo.images[0] ? `
                            <img src="${promo.images[0]}" alt="${promo.title}" style="width: 100%; height: 400px; object-fit: cover; border-radius: 1rem; margin-bottom: 2rem;">
                        ` : ''}
                    
                    <div style="margin-bottom: 2rem;">
                        <div style="display: flex; gap: 0.5rem; margin-bottom: 1rem;">
                            ${getTypeBadge(promo.discount_type)}
                            ${urgencyBadge}
                            ${isActive ? '<span class="badge badge-success" style="font-size: 1rem;">✓ Activa</span>' : '<span class="badge" style="background: #6b7280; color: white; font-size: 1rem;">Expirada</span>'}
                        </div>
                        <h1 style="margin: 0 0 1rem 0;">${promo.title}</h1>
                        <p style="font-size: 1.125rem; color: var(--text-secondary);">${promo.description}</p>
                    </div>
                    
                    <div style="background: linear-gradient(135deg, #f59e0b, #d97706); color: white; padding: 3rem; border-radius: 1rem; text-align: center; margin-bottom: 2rem;">
                        ${getDiscountDisplay(promo)}
                    </div>
                    
                    ${promo.terms_conditions ? `
                            <div style="background: white; padding: 2rem; border-radius: 1rem; box-shadow: 0 2px 4px rgba(0,0,0,0.1); margin-bottom: 2rem;">
                                <h2 style="margin: 0 0 1rem 0;">📋 Términos y Condiciones</h2>
                                <p style="white-space: pre-line;">${promo.terms_conditions}</p>
                            </div>
                        ` : ''}
                    
                    ${promo.locale ? `
                            <div style="background: white; padding: 2rem; border-radius: 1rem; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                                <h2 style="margin: 0 0 1rem 0;">🏢 Local Participante</h2>
                                <h3>${promo.locale.name}</h3>
                                <p>${promo.locale.description}</p>
                                ${promo.locale.address ? `<p>📍 ${promo.locale.address}</p>` : ''}
                                ${promo.locale.phone ? `<p>📞 ${promo.locale.phone}</p>` : ''}
                            </div>
                        ` : ''}
                </div>
                
                <div>
                    <div style="background: white; padding: 2rem; border-radius: 1rem; box-shadow: 0 2px 4px rgba(0,0,0,0.1); position: sticky; top: 2rem;">
                        <div style="margin-bottom: 1.5rem;">
                            <h3 style="margin: 0 0 1rem 0;">📅 Validez</h3>
                            <p style="margin: 0.5rem 0; font-size: 0.875rem;"><strong>Desde:</strong> ${formatDate(promo.start_date)}</p>
                            <p style="margin: 0.5rem 0; font-size: 0.875rem;"><strong>Hasta:</strong> ${formatDate(promo.end_date)}</p>
                            ${isActive ? `<p style="margin: 1rem 0 0 0; color: var(--success); font-weight: 600;">✓ Válida por ${daysLeft} días más</p>` : '<p style="margin: 1rem 0 0 0; color: var(--secondary); font-weight: 600;">✗ Promoción expirada</p>'}
                        </div>
                        
                        ${promo.redemption_code ? `
                                <div style="background: var(--bg-light); padding: 1.5rem; border-radius: 0.5rem; margin-bottom: 1.5rem; text-align: center;">
                                    <p style="margin: 0 0 0.5rem 0; font-size: 0.875rem; font-weight: 600;">🎟️ Código de Redención</p>
                                    <p style="margin: 0; font-size: 1.5rem; font-weight: 800; font-family: monospace; background: var(--primary); color: white; padding: 0.5rem 1rem; border-radius: 0.25rem; display: inline-block;">${promo.redemption_code}</p>
                                    <button onclick="copyCode('${promo.redemption_code}')" class="btn btn-outline" style="width: 100%; margin-top: 1rem; font-size: 0.875rem;">📋 Copiar Código</button>
                                </div>
                            ` : ''}
                        
                        ${promo.original_price && promo.final_price ? `
                                <div style="margin-bottom: 1.5rem;">
                                    <h3 style="margin: 0 0 0.5rem 0;">💰 Precio</h3>
                                    <p style="margin: 0; text-decoration: line-through; color: var(--text-secondary);">${formatPrice(promo.original_price)}</p>
                                    <p style="margin: 0.25rem 0 0 0; font-size: 1.5rem; color: var(--success); font-weight: 700;">${formatPrice(promo.final_price)}</p>
                                </div>
                            ` : ''}
                        
                        <button onclick="toggleFavorite()" id="favorite-btn" class="btn btn-outline" style="width: 100%;">
                            <span id="favorite-icon">♡</span> Guardar Promoción
                        </button>
                    </div>
                </div>
            </div>
        `;

                checkFavoriteStatus();

            } catch (error) {
                console.error('Error loading promocion:', error);
                document.getElementById('promocion-detail').innerHTML = `
            <div class="card" style="text-align: center; padding: 3rem;">
                <h2>Promoción no encontrada</h2>
                <p class="text-secondary">La promoción que buscas no existe o ha sido eliminada.</p>
                <a href="/promociones" class="btn btn-primary" style="margin-top: 1rem;">Ver Todas las Promociones</a>
            </div>
        `;
            }
        }

        function getTypeBadge(type) {
            const badges = {
                '2x1': '<span class="badge" style="background: #ec4899; color: white; font-size: 1rem;">🎁 2x1</span>',
                'percentage': '<span class="badge" style="background: #8b5cf6; color: white; font-size: 1rem;">💯 % Descuento</span>',
                'fixed': '<span class="badge" style="background: #10b981; color: white; font-size: 1rem;">💵 Descuento Fijo</span>',
            };
            return badges[type] || '';
        }

        function getDiscountDisplay(promo) {
            if (promo.discount_type === '2x1') {
                return '<h2 style="font-size: 5rem; margin: 0; font-weight: 900; line-height: 1;">2×1</h2><p style="margin: 0; font-size: 1.5rem;">PAGA UNO, LLEVA DOS</p>';
            } else if (promo.discount_type === 'percentage') {
                return `<h2 style="font-size: 6rem; margin: 0; font-weight: 900; line-height: 1;">${promo.discount_percentage}%</h2><p style="margin: 0; font-size: 1.5rem;">DE DESCUENTO</p>`;
            } else if (promo.discount_type === 'fixed') {
                return `<h2 style="font-size: 4rem; margin: 0; font-weight: 900; line-height: 1;">-${formatPrice(promo.discount_amount)}</h2><p style="margin: 0; font-size: 1.5rem;">DE DESCUENTO</p>`;
            }
            return '';
        }

        function copyCode(code) {
            navigator.clipboard.writeText(code);
            showNotification('Código copiado: ' + code, 'success');
        }

        async function checkFavoriteStatus() {
            const token = api.getToken();
            if (!token) return;

            try {
                const response = await axios.get(`/api/favorites/check`, {
                    params: {
                        favoritable_type: 'App\\\\Models\\\\Promocion',
                        favoritable_id: promocionId
                    }
                });

                if (response.data.data.is_favorite) {
                    document.getElementById('favorite-icon').textContent = '♥';
                    document.getElementById('favorite-btn').classList.add('btn-primary');
                    document.getElementById('favorite-btn').classList.remove('btn-outline');
                }
            } catch (error) {}
        }

        async function toggleFavorite() {
            const token = api.getToken();
            if (!token) {
                showNotification('Debes iniciar sesión para guardar favoritos', 'error');
                window.location.href = '/login';
                return;
            }

            try {
                await api.toggleFavorite('App\\\\Models\\\\Promocion', promocionId);

                const icon = document.getElementById('favorite-icon');
                const btn = document.getElementById('favorite-btn');

                if (icon.textContent === '♡') {
                    icon.textContent = '♥';
                    btn.classList.add('btn-primary');
                    btn.classList.remove('btn-outline');
                    showNotification('Agregado a favoritos', 'success');
                } else {
                    icon.textContent = '♡';
                    btn.classList.remove('btn-primary');
                    btn.classList.add('btn-outline');
                    showNotification('Eliminado de favoritos', 'success');
                }
            } catch (error) {
                showNotification('Error al actualizar favoritos', 'error');
            }
        }

        // Add pulse animation
        const style = document.createElement('style');
        style.textContent = '@keyframes pulse { 0%, 100% { opacity: 1; } 50% { opacity: 0.7; } }';
        document.head.appendChild(style);

        document.addEventListener('DOMContentLoaded', () => {
            loadPromocion();
        });
    </script>
@endpush
