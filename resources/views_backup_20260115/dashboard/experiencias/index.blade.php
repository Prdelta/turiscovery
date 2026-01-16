@extends('layouts.dashboard')

@section('title', 'Mis Experiencias - Dashboard')

@section('content')
    <div class="dashboard-header">
        <div>
            <h1 style="margin: 0;">Mis Experiencias</h1>
            <p style="color: var(--text-secondary); margin: 0.5rem 0 0 0;">Gestiona las actividades que ofreces</p>
        </div>
        <div>
            <a href="/dashboard/experiencias/create" class="btn btn-primary">+ Nueva Experiencia</a>
        </div>
    </div>

    <div id="experiencias-list">
        <div class="card" style="text-align: center; padding: 3rem;">
            <p class="text-secondary">Cargando experiencias...</p>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        async function loadMyExperiencias() {
            try {
                const userResponse = await axios.get('/api/me');
                const user = userResponse.data.data;

                const response = await axios.get('/api/experiencias');
                const allExperiencias = response.data.data.data;
                const myExperiencias = allExperiencias.filter(e => e.user_id === user.id);

                const container = document.getElementById('experiencias-list');

                if (myExperiencias.length === 0) {
                    container.innerHTML = `
                <div class="card" style="text-align: center; padding: 3rem;">
                    <div style="font-size: 4rem; margin-bottom: 1rem;">🚣</div>
                    <h3>No tienes experiencias publicadas</h3>
                    <p class="text-secondary">Crea tu primera experiencia para ofrecer actividades únicas</p>
                    <a href="/dashboard/experiencias/create" class="btn btn-primary" style="margin-top: 1rem;">Crear Experiencia</a>
                </div>
            `;
                    return;
                }

                container.innerHTML = '<div class="grid grid-2">' + myExperiencias.map(exp => `
            <div class="card">
                <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 1rem;">
                    <div style="flex: 1;">
                        <h3 style="margin: 0 0 0.5rem 0;">${exp.title}</h3>
                        <span class="badge ${getDifficultyClass(exp.difficulty)}">${getDifficultyName(exp.difficulty)}</span>
                        ${exp.is_active ? '<span class="badge badge-success" style="margin-left: 0.5rem;">✓ Activa</span>' : '<span class="badge" style="background: #6b7280; color: white; margin-left: 0.5rem;">Inactiva</span>'}
                    </div>
                </div>
                
                <p class="text-secondary" style="margin-bottom: 1rem;">${exp.description.substring(0, 120)}...</p>
                
                <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 0.5rem; margin-bottom: 1rem;">
                    ${exp.duration_hours ? `<div style="background: var(--bg-light); padding: 0.5rem; border-radius: 0.25rem; text-align: center;"><p style="margin: 0; font-size: 0.875rem; color: var(--text-secondary);">⏱️ Duración</p><p style="margin: 0; font-weight: 600;">${exp.duration_hours}h</p></div>` : ''}
                    ${exp.max_participants ? `<div style="background: var(--bg-light); padding: 0.5rem; border-radius: 0.25rem; text-align: center;"><p style="margin: 0; font-size: 0.875rem; color: var(--text-secondary);">👥 Capacidad</p><p style="margin: 0; font-weight: 600;">${exp.max_participants} personas</p></div>` : ''}
                </div>
                
                ${exp.price_pen ? `<p style="color: var(--primary); font-size: 1.5rem; font-weight: 700; margin-bottom: 1rem; text-align: center;">${formatPrice(exp.price_pen)}</p>` : '<p style="color: var(--success); font-weight: 600; margin-bottom: 1rem; text-align: center;">Precio a consultar</p>'}
                
                <div style="display: flex; gap: 0.5rem;">
                    <a href="/dashboard/experiencias/${exp.id}/edit" class="btn btn-outline" style="flex: 1;">✏️ Editar</a>
                    <button onclick="deleteExperiencia(${exp.id})" class="btn btn-secondary">🗑️ Eliminar</button>
                </div>
            </div>
        `).join('') + '</div>';

            } catch (error) {
                console.error('Error loading experiencias:', error);
                document.getElementById('experiencias-list').innerHTML = `
            <div class="card" style="text-align: center; padding: 3rem;">
                <p style="color: var(--secondary);">Error al cargar experiencias</p>
            </div>
        `;
            }
        }

        function getDifficultyClass(difficulty) {
            const classes = {
                easy: 'badge-success',
                medium: 'badge-warning',
                hard: 'badge-secondary'
            };
            return classes[difficulty] || 'badge-primary';
        }

        function getDifficultyName(difficulty) {
            const names = {
                easy: '✓ Fácil',
                medium: '⚡ Media',
                hard: '🔥 Difícil'
            };
            return names[difficulty] || difficulty;
        }

        async function deleteExperiencia(id) {
            if (!confirm('¿Estás seguro de eliminar esta experiencia?')) return;

            try {
                await axios.delete(`/api/experiencias/${id}`);
                showNotification('Experiencia eliminada correctamente', 'success');
                loadMyExperiencias();
            } catch (error) {
                showNotification('Error al eliminar experiencia', 'error');
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            loadMyExperiencias();
        });
    </script>
@endpush
