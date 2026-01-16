@extends('layouts.dashboard')

@section('title', 'Crear Experiencia - Dashboard')

@section('content')
    <div class="dashboard-header">
        <div>
            <h1 style="margin: 0;">Crear Nueva Experiencia</h1>
            <p style="color: var(--text-secondary); margin: 0.5rem 0 0 0;">Ofrece actividades únicas a los turistas</p>
        </div>
        <div>
            <a href="/dashboard/experiencias" class="btn btn-outline">← Volver</a>
        </div>
    </div>

    <div style="background: white; padding: 2rem; border-radius: 1rem; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
        <div id="error-message"
            style="display: none; background: #fee2e2; color: #dc2626; padding: 1rem; border-radius: 0.5rem; margin-bottom: 1rem;">
        </div>

        <form id="experiencia-form">
            <div class="grid grid-2" style="gap: 2rem;">
                <!-- Left Column -->
                <div>
                    <div style="margin-bottom: 1.5rem;">
                        <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Título de la Experiencia
                            *</label>
                        <input type="text" id="title" required
                            style="width: 100%; padding: 0.75rem; border: 2px solid #e5e7eb; border-radius: 0.5rem; font-size: 1rem;">
                    </div>

                    <div style="margin-bottom: 1.5rem;">
                        <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Descripción Breve *</label>
                        <textarea id="description" required rows="3"
                            style="width: 100%; padding: 0.75rem; border: 2px solid #e5e7eb; border-radius: 0.5rem; font-size: 1rem; resize: vertical;"></textarea>
                    </div>

                    <div style="margin-bottom: 1.5rem;">
                        <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Contenido Detallado</label>
                        <textarea id="content" rows="4"
                            style="width: 100%; padding: 0.75rem; border: 2px solid #e5e7eb; border-radius: 0.5rem; font-size: 1rem; resize: vertical;"></textarea>
                        <p style="font-size: 0.875rem; color: var(--text-secondary); margin-top: 0.25rem;">Itinerario, qué
                            incluye, qué llevar, etc.</p>
                    </div>

                    <div style="margin-bottom: 1.5rem;">
                        <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Dificultad *</label>
                        <select id="difficulty" required
                            style="width: 100%; padding: 0.75rem; border: 2px solid #e5e7eb; border-radius: 0.5rem; font-size: 1rem;">
                            <option value="">Selecciona la dificultad</option>
                            <option value="easy">✓ Fácil - Para todos</option>
                            <option value="medium">⚡ Media - Requiere condición física básica</option>
                            <option value="hard">🔥 Difícil - Solo para experimentados</option>
                        </select>
                    </div>

                    <div class="grid grid-2">
                        <div style="margin-bottom: 1.5rem;">
                            <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Duración (horas)</label>
                            <input type="number" id="duration_hours" min="1" step="0.5"
                                style="width: 100%; padding: 0.75rem; border: 2px solid #e5e7eb; border-radius: 0.5rem; font-size: 1rem;">
                        </div>

                        <div style="margin-bottom: 1.5rem;">
                            <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Máx.
                                Participantes</label>
                            <input type="number" id="max_participants" min="1"
                                style="width: 100%; padding: 0.75rem; border: 2px solid #e5e7eb; border-radius: 0.5rem; font-size: 1rem;">
                        </div>
                    </div>

                    <div style="margin-bottom: 1.5rem;">
                        <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Precio por Persona
                            (S/)</label>
                        <input type="number" id="price_pen" step="0.01" min="0"
                            style="width: 100%; padding: 0.75rem; border: 2px solid #e5e7eb; border-radius: 0.5rem; font-size: 1rem;">
                        <p style="font-size: 0.875rem; color: var(--text-secondary); margin-top: 0.25rem;">Dejar vacío si el
                            precio es a consultar</p>
                    </div>

                    <div style="margin-bottom: 1.5rem;">
                        <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Tags / Etiquetas</label>
                        <input type="text" id="tags-input"
                            style="width: 100%; padding: 0.75rem; border: 2px solid #e5e7eb; border-radius: 0.5rem; font-size: 1rem;"
                            placeholder="Ej: aventura, naturaleza, cultura">
                        <p style="font-size: 0.875rem; color: var(--text-secondary); margin-top: 0.25rem;">Separar con comas
                        </p>
                        <div id="tags-display" style="display: flex; gap: 0.5rem; flex-wrap: wrap; margin-top: 0.5rem;">
                        </div>
                    </div>
                </div>

                <!-- Right Column - Map -->
                <div>
                    <div style="margin-bottom: 1.5rem;">
                        <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Ubicación (Opcional)</label>
                        <p style="font-size: 0.875rem; color: var(--text-secondary); margin-bottom: 1rem;">
                            📍 Punto de encuentro o inicio de la actividad
                        </p>
                        <div id="map" style="height: 350px; border-radius: 0.5rem; border: 2px solid #e5e7eb;"></div>
                    </div>

                    <div class="grid grid-2" style="margin-bottom: 1.5rem;">
                        <div>
                            <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Latitud</label>
                            <input type="number" id="latitude" step="0.000001" readonly
                                style="width: 100%; padding: 0.75rem; border: 2px solid #e5e7eb; border-radius: 0.5rem; background: var(--bg-light);">
                        </div>
                        <div>
                            <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Longitud</label>
                            <input type="number" id="longitude" step="0.000001" readonly
                                style="width: 100%; padding: 0.75rem; border: 2px solid #e5e7eb; border-radius: 0.5rem; background: var(--bg-light);">
                        </div>
                    </div>

                    <div style="margin-bottom: 1.5rem;">
                        <label>
                            <input type="checkbox" id="use_location" style="margin-right: 0.5rem;">
                            <span style="font-weight: 600;">Usar ubicación marcada en el mapa</span>
                        </label>
                    </div>

                    <div style="margin-bottom: 1.5rem;">
                        <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Dirección del Punto de
                            Encuentro</label>
                        <input type="text" id="address"
                            style="width: 100%; padding: 0.75rem; border: 2px solid #e5e7eb; border-radius: 0.5rem; font-size: 1rem;">
                    </div>

                    <div style="margin-bottom: 1.5rem;">
                        <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Local Asociado
                            (Opcional)</label>
                        <select id="locale_id"
                            style="width: 100%; padding: 0.75rem; border: 2px solid #e5e7eb; border-radius: 0.5rem; font-size: 1rem;">
                            <option value="">Ninguno</option>
                        </select>
                    </div>
                </div>
            </div>

            <div
                style="margin-top: 2rem; padding-top: 2rem; border-top: 2px solid var(--bg-light); display: flex; gap: 1rem; justify-content: flex-end;">
                <a href="/dashboard/experiencias" class="btn btn-outline">Cancelar</a>
                <button type="submit" class="btn btn-primary">Crear Experiencia</button>
            </div>
        </form>
    </div>
@endsection

@push('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
@endpush

@push('scripts')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        let map, marker;
        let tags = [];

        function initMap() {
            map = L.map('map').setView([-15.8402, -70.0219], 13);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap contributors'
            }).addTo(map);

            marker = L.marker([-15.8402, -70.0219], {
                draggable: true,
                title: 'Punto de encuentro'
            }).addTo(map);

            marker.on('dragend', function(e) {
                const latlng = e.target.getLatLng();
                updateCoordinates(latlng.lat, latlng.lng);
            });

            map.on('click', function(e) {
                marker.setLatLng(e.latlng);
                updateCoordinates(e.latlng.lat, e.latlng.lng);
            });

            updateCoordinates(-15.8402, -70.0219);
        }

        function updateCoordinates(lat, lng) {
            document.getElementById('latitude').value = lat.toFixed(6);
            document.getElementById('longitude').value = lng.toFixed(6);
        }

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

        // Tags handling
        document.getElementById('tags-input').addEventListener('keypress', function(e) {
            if (e.key === 'Enter' || e.key === ',') {
                e.preventDefault();
                addTag(this.value.trim());
                this.value = '';
            }
        });

        document.getElementById('tags-input').addEventListener('blur', function() {
            if (this.value.trim()) {
                addTag(this.value.trim());
                this.value = '';
            }
        });

        function addTag(tag) {
            if (tag && !tags.includes(tag)) {
                tags.push(tag);
                renderTags();
            }
        }

        function removeTag(tag) {
            tags = tags.filter(t => t !== tag);
            renderTags();
        }

        function renderTags() {
            const container = document.getElementById('tags-display');
            container.innerHTML = tags.map(tag => `
        <span style="background: var(--primary); color: white; padding: 0.25rem 0.75rem; border-radius: 9999px; font-size: 0.875rem; display: inline-flex; align-items: center; gap: 0.5rem;">
            ${tag}
            <button type="button" onclick="removeTag('${tag}')" style="background: none; border: none; color: white; cursor: pointer; font-size: 1rem;">×</button>
        </span>
    `).join('');
        }

        document.getElementById('experiencia-form').addEventListener('submit', async (e) => {
            e.preventDefault();

            const errorDiv = document.getElementById('error-message');
            errorDiv.style.display = 'none';

            const useLocation = document.getElementById('use_location').checked;

            const data = {
                title: document.getElementById('title').value,
                description: document.getElementById('description').value,
                content: document.getElementById('content').value,
                difficulty: document.getElementById('difficulty').value,
                duration_hours: document.getElementById('duration_hours').value || null,
                max_participants: document.getElementById('max_participants').value || null,
                price_pen: document.getElementById('price_pen').value || null,
                address: document.getElementById('address').value,
                locale_id: document.getElementById('locale_id').value || null,
                tags: tags.length > 0 ? tags : null,
            };

            if (useLocation) {
                data.latitude = parseFloat(document.getElementById('latitude').value);
                data.longitude = parseFloat(document.getElementById('longitude').value);
            }

            try {
                const response = await axios.post('/api/experiencias', data);

                if (response.data.success) {
                    showNotification('¡Experiencia creada exitosamente!', 'success');
                    window.location.href = '/dashboard/experiencias';
                }
            } catch (error) {
                errorDiv.style.display = 'block';

                if (error.response?.data?.errors) {
                    const errors = error.response.data.errors;
                    const errorMessages = Object.values(errors).flat();
                    errorDiv.innerHTML = errorMessages.map(msg => `<p>• ${msg}</p>`).join('');
                } else {
                    errorDiv.textContent = 'Error al crear la experiencia. Por favor, intenta nuevamente.';
                }
            }
        });

        document.addEventListener('DOMContentLoaded', () => {
            initMap();
            loadMyLocales();
        });
    </script>
@endpush
