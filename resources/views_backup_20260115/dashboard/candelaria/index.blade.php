@extends('layouts.dashboard')

@section('title', 'Mis Contenidos de Candelaria - Dashboard')

@section('content')
    <div class="dashboard-header">
        <div>
            <h1 style="margin: 0;">Mis Contenidos de Candelaria</h1>
            <p style="color: var(--text-secondary); margin: 0.5rem 0 0 0;">Gestiona tu contenido cultural</p>
        </div>
        <div>
            <a href="/dashboard/candelaria/create" class="btn btn-primary">+ Nuevo Contenido</a>
        </div>
    </div>

    <div id="candelaria-list">
        <div class="card" style="text-align: center; padding: 3rem;">
            <p class="text-secondary">Cargando contenido...</p>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        async function loadMyCandelaria() {
            try {
                const userResponse = await axios.get('/api/me');
                const user = userResponse.data.data;

                const response = await axios.get('/api/candelaria');
                const allItems = response.data.data.data;
                const myItems = allItems.filter(item => item.user_id === user.id);

                const container = document.getElementById('candelaria-list');

                if (myItems.length === 0) {
                    container.innerHTML = `
                <div class="card" style="text-align: center; padding: 3rem;">
                    <div style="font-size: 4rem; margin-bottom: 1rem;">🎭</div>
                    <h3>No tienes contenido publicado</h3>
                    <p class="text-secondary">Comparte la cultura de Candelaria</p>
                    <a href="/dashboard/candelaria/create" class="btn btn-primary" style="margin-top: 1rem;">Crear Contenido</a>
                </div>
            `;
                    return;
                }

                container.innerHTML = '<div class="grid grid-2">' + myItems.map(item => `
            <div class="card">
                <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 1rem;">
                    <div style="flex: 1;">
                        <h3 style="margin: 0 0 0.5rem 0;">${item.title}</h3>
                        <span class="badge badge-primary">${getCategoryName(item.category)}</span>
                        ${item.is_featured ? '<span class="badge" style="background: #f59e0b; color: white; margin-left: 0.5rem;">⭐ Destacado</span>' : ''}
                        ${item.is_active ? '<span class="badge badge-success" style="margin-left: 0.5rem;">✓ Activo</span>' : '<span class="badge" style="background: #6b7280; color: white; margin-left: 0.5rem;">Inactivo</span>'}
                    </div>
                </div>
                
                <p class="text-secondary" style="margin-bottom: 1rem;">${item.description.substring(0, 120)}...</p>
                
                ${item.event_date ? `<p style="margin-bottom: 1rem;"><strong>📅 Fecha:</strong> ${formatDate(item.event_date)}</p>` : ''}
                
                <div style="display: flex; gap: 0.5rem;">
                    <a href="/dashboard/candelaria/${item.id}/edit" class="btn btn-outline" style="flex: 1;">✏️ Editar</a>
                    <button onclick="deleteItem(${item.id})" class="btn btn-secondary">🗑️ Eliminar</button>
                </div>
            </div>
        `).join('') + '</div>';

            } catch (error) {
                console.error('Error loading candelaria:', error);
                document.getElementById('candelaria-list').innerHTML = `
            <div class="card" style="text-align: center; padding: 3rem;">
                <p style="color: var(--secondary);">Error al cargar contenido</p>
            </div>
        `;
            }
        }

        function getCategoryName(category) {
            const names = {
                dance: '💃 Danzas',
                history: '📜 Historia',
                costume: '👗 Trajes',
                music: '🎵 Música',
                tradition: '🎭 Tradiciones'
            };
            return names[category] || category;
        }

        async function deleteItem(id) {
            if (!confirm('¿Estás seguro de eliminar este contenido?')) return;

            try {
                await axios.delete(`/api/candelaria/${id}`);
                showNotification('Contenido eliminado correctamente', 'success');
                loadMyCandelaria();
            } catch (error) {
                showNotification('Error al eliminar contenido', 'error');
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            loadMyCandelaria();
        });
    </script>
@endpush
