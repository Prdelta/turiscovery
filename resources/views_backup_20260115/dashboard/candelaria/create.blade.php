@extends('layouts.dashboard')

@section('title', 'Crear Contenido de Candelaria - Dashboard')

@section('content')
    <div class="dashboard-header">
        <div>
            <h1 style="margin: 0;">Crear Contenido de Candelaria</h1>
            <p style="color: var(--text-secondary); margin: 0.5rem 0 0 0;">Comparte información cultural sobre la Festividad
            </p>
        </div>
        <div>
            <a href="/dashboard/candelaria" class="btn btn-outline">← Volver</a>
        </div>
    </div>

    <div style="background: white; padding: 2rem; border-radius: 1rem; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
        <div id="error-message"
            style="display: none; background: #fee2e2; color: #dc2626; padding: 1rem; border-radius: 0.5rem; margin-bottom: 1rem;">
        </div>

        <form id="candelaria-form">
            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Título *</label>
                <input type="text" id="title" required
                    style="width: 100%; padding: 0.75rem; border: 2px solid #e5e7eb; border-radius: 0.5rem; font-size: 1rem;">
            </div>

            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Categoría *</label>
                <select id="category" required
                    style="width: 100%; padding: 0.75rem; border: 2px solid #e5e7eb; border-radius: 0.5rem; font-size: 1rem;">
                    <option value="">Selecciona una categoría</option>
                    <option value="dance">💃 Danzas Folklóricas</option>
                    <option value="history">📜 Historia y Origen</option>
                    <option value="costume">👗 Trajes Típicos</option>
                    <option value="music">🎵 Música Tradicional</option>
                    <option value="tradition">🎭 Tradiciones y Costumbres</option>
                </select>
            </div>

            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Descripción Breve *</label>
                <textarea id="description" required rows="3"
                    style="width: 100%; padding: 0.75rem; border: 2px solid #e5e7eb; border-radius: 0.5rem; font-size: 1rem; resize: vertical;"></textarea>
            </div>

            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Contenido Completo</label>
                <textarea id="content" rows="8"
                    style="width: 100%; padding: 0.75rem; border: 2px solid #e5e7eb; border-radius: 0.5rem; font-size: 1rem; resize: vertical;"></textarea>
                <p style="font-size: 0.875rem; color: var(--text-secondary); margin-top: 0.25rem;">Historia detallada,
                    significado, etc.</p>
            </div>

            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Fecha del Evento</label>
                <input type="date" id="event_date"
                    style="width: 100%; padding: 0.75rem; border: 2px solid #e5e7eb; border-radius: 0.5rem; font-size: 1rem;">
                <p style="font-size: 0.875rem; color: var(--text-secondary); margin-top: 0.25rem;">Si aplica (ej: día de la
                    presentación)</p>
            </div>

            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Local Asociado (Opcional)</label>
                <select id="locale_id"
                    style="width: 100%; padding: 0.75rem; border: 2px solid #e5e7eb; border-radius: 0.5rem; font-size: 1rem;">
                    <option value="">Ninguno</option>
                </select>
                <p style="font-size: 0.875rem; color: var(--text-secondary); margin-top: 0.25rem;">Si está asociado a uno de
                    tus locales</p>
            </div>

            <div style="margin-bottom: 1.5rem;">
                <label style="display: flex; align-items: center; gap: 0.5rem;">
                    <input type="checkbox" id="is_featured">
                    <span style="font-weight: 600;">⭐ Marcar como destacado</span>
                </label>
            </div>

            <div
                style="margin-top: 2rem; padding-top: 2rem; border-top: 2px solid var(--bg-light); display: flex; gap: 1rem; justify-content: flex-end;">
                <a href="/dashboard/candelaria" class="btn btn-outline">Cancelar</a>
                <button type="submit" class="btn btn-primary">Crear Contenido</button>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
    <script>
        async function loadMyLocales() {
            try {
                const userResponse = await axios.get('/api/me');
                const user = userResponse.data.data;

                const response = await axios.get('/api/locales');
                const allLocales = response.data.data.data;
                const myLocales = allLocales.filter(l => l.user_id === user.id);

                const select = document.getElementById('locale_id');
                myLocales.forEach(locale => {
                    const option = document.createElement('option');
                    option.value = locale.id;
                    option.textContent = locale.name;
                    select.appendChild(option);
                });
            } catch (error) {
                console.error('Error loading locales:', error);
            }
        }

        document.getElementById('candelaria-form').addEventListener('submit', async (e) => {
            e.preventDefault();

            const errorDiv = document.getElementById('error-message');
            errorDiv.style.display = 'none';

            const data = {
                title: document.getElementById('title').value,
                category: document.getElementById('category').value,
                description: document.getElementById('description').value,
                content: document.getElementById('content').value,
                event_date: document.getElementById('event_date').value || null,
                locale_id: document.getElementById('locale_id').value || null,
                is_featured: document.getElementById('is_featured').checked,
            };

            try {
                const response = await axios.post('/api/candelaria', data);

                if (response.data.success) {
                    showNotification('¡Contenido creado exitosamente!', 'success');
                    window.location.href = '/dashboard/candelaria';
                }
            } catch (error) {
                errorDiv.style.display = 'block';

                if (error.response?.data?.errors) {
                    const errors = error.response.data.errors;
                    const errorMessages = Object.values(errors).flat();
                    errorDiv.innerHTML = errorMessages.map(msg => `<p>• ${msg}</p>`).join('');
                } else if (error.response?.data?.message) {
                    errorDiv.textContent = error.response.data.message;
                } else {
                    errorDiv.textContent = 'Error al crear el contenido. Por favor, intenta nuevamente.';
                }
            }
        });

        document.addEventListener('DOMContentLoaded', () => {
            loadMyLocales();
        });
    </script>
@endpush
