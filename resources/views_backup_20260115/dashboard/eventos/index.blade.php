@extends('layouts.dashboard')

@section('title', 'Mis Eventos - Dashboard')

@section('content')
    <div class="dashboard-header">
        <div>
            <h1 style="margin: 0;">Mis Eventos</h1>
            <p style="color: var(--text-secondary); margin: 0.5rem 0 0 0;">Gestiona los eventos que has publicado</p>
        </div>
        <div>
            <a href="/dashboard/eventos/create" class="btn btn-primary">+ Nuevo Evento</a>
        </div>
    </div>

    <div id="eventos-list">
        <div class="card" style="text-align: center; padding: 3rem;">
            <p class="text-secondary">Cargando eventos...</p>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        async function loadMyEventos() {
            try {
                const userResponse = await axios.get('/api/me');
                const user = userResponse.data.data;

                const response = await axios.get('/api/eventos');
                const allEventos = response.data.data.data;
                const myEventos = allEventos.filter(e => e.user_id === user.id);

                const container = document.getElementById('eventos-list');

                if (myEventos.length === 0) {
                    container.innerHTML = `
                <div class="card" style="text-align: center; padding: 3rem;">
                    <div style="font-size: 4rem; margin-bottom: 1rem;">🎪</div>
                    <h3>No tienes eventos publicados</h3>
                    <p class="text-secondary">Crea tu primer evento para atraer más visitantes</p>
                    <a href="/dashboard/eventos/create" class="btn btn-primary" style="margin-top: 1rem;">Crear Evento</a>
                </div>
            `;
                    return;
                }

                container.innerHTML = '<div class="grid grid-2">' + myEventos.map(evento => {
                    const now = new Date();
                    const endTime = new Date(evento.end_time);
                    const isExpired = endTime < now;

                    return `
                <div class="card" style="border-left: 4px solid ${isExpired ? '#6b7280' : 'var(--primary)'};">
                    <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 1rem;">
                        <div style="flex: 1;">
                            <h3 style="margin: 0 0 0.5rem 0;">${evento.title}</h3>
                            <span class="badge badge-primary">${getCategoryName(evento.category)}</span>
                            ${evento.is_active && !isExpired ? '<span class="badge badge-success" style="margin-left: 0.5rem;">✓ Activo</span>' : '<span class="badge" style="background: #6b7280; color: white; margin-left: 0.5rem;">Finalizado</span>'}
                        </div>
                    </div>
                    
                    <p class="text-secondary" style="margin-bottom: 1rem;">${evento.description.substring(0, 120)}...</p>
                    
                    <div style="background: var(--bg-light); padding: 1rem; border-radius: 0.5rem; margin-bottom: 1rem;">
                        <p style="font-size: 0.875rem; margin: 0.25rem 0;"><strong>📅 Inicio:</strong> ${formatDate(evento.start_time)}</p>
                        <p style="font-size: 0.875rem; margin: 0.25rem 0;"><strong>🕐 Fin:</strong> ${formatDate(evento.end_time)}</p>
                        ${evento.ticket_price ? `<p style="font-size: 0.875rem; margin: 0.25rem 0;"><strong>💵 Entrada:</strong> ${formatPrice(evento.ticket_price)}</p>` : '<p style="font-size: 0.875rem; margin: 0.25rem 0;"><strong>🎉 Entrada:</strong> GRATIS</p>'}
                    </div>
                    
                    <div style="display: flex; gap: 0.5rem;">
                        <a href="/dashboard/eventos/${evento.id}/edit" class="btn btn-outline" style="flex: 1;">✏️ Editar</a>
                        <button onclick="deleteEvento(${evento.id})" class="btn btn-secondary">🗑️ Eliminar</button>
                    </div>
                </div>
            `;
                }).join('') + '</div>';

            } catch (error) {
                console.error('Error loading eventos:', error);
                document.getElementById('eventos-list').innerHTML = `
            <div class="card" style="text-align: center; padding: 3rem;">
                <p style="color: var(--secondary);">Error al cargar eventos</p>
            </div>
        `;
            }
        }

        function getCategoryName(category) {
            const categories = {
                concert: '🎵 Concierto',
                festival: '🎉 Festival',
                cultural: '🎭 Cultural',
                nightlife: '🌙 Nocturno',
                sports: '⚽ Deportivo',
                exhibition: '🖼️ Exposición',
                other: '📅 Otro'
            };
            return categories[category] || category;
        }

        async function deleteEvento(id) {
            if (!confirm('¿Estás seguro de eliminar este evento?')) return;

            try {
                await axios.delete(`/api/eventos/${id}`);
                showNotification('Evento eliminado correctamente', 'success');
                loadMyEventos();
            } catch (error) {
                showNotification('Error al eliminar evento', 'error');
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            loadMyEventos();
        });
    </script>
@endpush
