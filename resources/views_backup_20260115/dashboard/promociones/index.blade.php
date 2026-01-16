@extends('layouts.dashboard')

@section('title', 'Mis Promociones - Dashboard')

@section('content')
    <div class="dashboard-header">
        <div>
            <h1 style="margin: 0;">Mis Promociones</h1>
            <p style="color: var(--text-secondary); margin: 0.5rem 0 0 0;">Gestiona tus ofertas y descuentos</p>
        </div>
        <div>
            <a href="/dashboard/promociones/create" class="btn btn-primary">+ Nueva Promoción</a>
        </div>
    </div>

    <div id="promociones-list">
        <div class="card" style="text-align: center; padding: 3rem;">
            <p class="text-secondary">Cargando promociones...</p>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        async function loadMyPromociones() {
            try {
                const userResponse = await axios.get('/api/me');
                const user = userResponse.data.data;

                const response = await axios.get('/api/promociones');
                const allPromociones = response.data.data.data;
                const myPromociones = allPromociones.filter(p => p.user_id === user.id);

                const container = document.getElementById('promociones-list');

                if (myPromociones.length === 0) {
                    container.innerHTML = `
                <div class="card" style="text-align: center; padding: 3rem;">
                    <div style="font-size: 4rem; margin-bottom: 1rem;">💰</div>
                    <h3>No tienes promociones activas</h3>
                    <p class="text-secondary">Crea promociones para atraer más clientes</p>
                    <a href="/dashboard/promociones/create" class="btn btn-primary" style="margin-top: 1rem;">Crear Promoción</a>
                </div>
            `;
                    return;
                }

                container.innerHTML = '<div class="grid grid-2">' + myPromociones.map(promo => {
                    const now = new Date();
                    const endDate = new Date(promo.end_date);
                    const isActive = endDate > now && promo.is_active;
                    const daysLeft = Math.ceil((endDate - now) / (1000 * 60 * 60 * 24));

                    return `
                <div class="card" style="border: 3px solid ${isActive ? 'var(--accent)' : '#6b7280'};">
                    <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 1rem;">
                        <div style="flex: 1;">
                            <h3 style="margin: 0 0 0.5rem 0;">${promo.title}</h3>
                            <span class="badge" style="background: var(--accent); color: white;">${getTypeName(promo.discount_type)}</span>
                            ${isActive ? '<span class="badge badge-success" style="margin-left: 0.5rem;">✓ Activa</span>' : '<span class="badge" style="background: #6b7280; color: white; margin-left: 0.5rem;">Expirada</span>'}
                        </div>
                    </div>
                    
                    <div style="text-align: center; padding: 1.5rem; background: linear-gradient(135deg, #f59e0b, #d97706); border-radius: 0.5rem; margin-bottom: 1rem; color: white;">
                        ${getDiscountDisplay(promo)}
                    </div>
                    
                    <div style="background: var(--bg-light); padding: 1rem; border-radius: 0.5rem; margin-bottom: 1rem;">
                        <p style="font-size: 0.875rem; margin: 0.25rem 0;"><strong>📅 Válido hasta:</strong> ${formatDate(promo.end_date)}</p>
                        ${isActive && daysLeft <= 7 ? `<p style="font-size: 0.875rem; margin: 0.25rem 0; color: var(--secondary);"><strong>⏰ ¡Quedan ${daysLeft} días!</strong></p>` : ''}
                        ${promo.redemption_code ? `<p style="font-size: 0.875rem; margin: 0.25rem 0;"><strong>🎟️ Código:</strong> ${promo.redemption_code}</p>` : ''}
                    </div>
                    
                    <div style="display: flex; gap: 0.5rem;">
                        <a href="/dashboard/promociones/${promo.id}/edit" class="btn btn-outline" style="flex: 1;">✏️ Editar</a>
                        <button onclick="deletePromocion(${promo.id})" class="btn btn-secondary">🗑️ Eliminar</button>
                    </div>
                </div>
            `;
                }).join('') + '</div>';

            } catch (error) {
                console.error('Error loading promociones:', error);
                document.getElementById('promociones-list').innerHTML = `
            <div class="card" style="text-align: center; padding: 3rem;">
                <p style="color: var(--secondary);">Error al cargar promociones</p>
            </div>
        `;
            }
        }

        function getTypeName(type) {
            const types = {
                '2x1': '🎁 2x1',
                'percentage': '💯 Porcentaje',
                'fixed': '💵 Descuento Fijo'
            };
            return types[type] || type;
        }

        function getDiscountDisplay(promo) {
            if (promo.discount_type === '2x1') {
                return '<h2 style="font-size: 3rem; margin: 0; font-weight: 800;">2×1</h2>';
            } else if (promo.discount_type === 'percentage') {
                return `<h2 style="font-size: 3rem; margin: 0; font-weight: 800;">${promo.discount_percentage}%</h2><p style="margin: 0; font-size: 0.875rem;">DE DESCUENTO</p>`;
            } else if (promo.discount_type === 'fixed') {
                return `<h2 style="font-size: 2rem; margin: 0; font-weight: 800;">-${formatPrice(promo.discount_amount)}</h2><p style="margin: 0; font-size: 0.875rem;">DE DESCUENTO</p>`;
            }
            return '';
        }

        async function deletePromocion(id) {
            if (!confirm('¿Estás seguro de eliminar esta promoción?')) return;

            try {
                await axios.delete(`/api/promociones/${id}`);
                showNotification('Promoción eliminada correctamente', 'success');
                loadMyPromociones();
            } catch (error) {
                showNotification('Error al eliminar promoción', 'error');
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            loadMyPromociones();
        });
    </script>
@endpush
