@extends('layouts.dashboard')

@section('title', 'Crear Evento - Dashboard')

@section('content')
    <div class="dashboard-header">
        <div>
            <h1 style="margin: 0;">Crear Nuevo Evento</h1>
            <p style="color: var(--text-secondary); margin: 0.5rem 0 0 0;">Publica un evento cultural, concierto o actividad
            </p>
        </div>
        <div>
            <a href="/dashboard/eventos" class="btn btn-outline">← Volver</a>
        </div>
    </div>

    <div style="background: white; padding: 2rem; border-radius: 1rem; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
        <div id="error-message"
            style="display: none; background: #fee2e2; color: #dc2626; padding: 1rem; border-radius: 0.5rem; margin-bottom: 1rem;">
        </div>

        <form id="evento-form">
            <div class="grid grid-2" style="gap: 2rem;">
                <!-- Left Column -->
                <div>
                    <div style="margin-bottom: 1.5rem;">
                        <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Título del Evento *</label>
                        <input type="text" id="title" required
                            style="width: 100%; padding: 0.75rem; border: 2px solid #e5e7eb; border-radius: 0.5rem; font-size: 1rem;">
                    </div>

                    <div style="margin-bottom: 1.5rem;">
                        <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Categoría *</label>
                        <select id="category" required
                            style="width: 100%; padding: 0.75rem; border: 2px solid #e5e7eb; border-radius: 0.5rem; font-size: 1rem;">
                            <option value="">Selecciona una categoría</option>
                            <option value="concert">🎵 Concierto</option>
                            <option value="festival">🎉 Festival</option>
                            <option value="cultural">🎭 Cultural</option>
                            <option value="nightlife">🌙 Vida Nocturna</option>
                            <option value="sports">⚽ Deportivo</option>
                            <option value="exhibition">🖼️ Exposición</option>
                            <option value="other">📅 Otro</option>
                        </select>
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
                        <p style="font-size: 0.875rem; color: var(--text-secondary); margin-top: 0.25rem;">Detalles
                            adicionales del evento</p>
                    </div>

                    <div class="grid grid-2">
                        <div style="margin-bottom: 1.5rem;">
                            <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Fecha y Hora Inicio
                                *</label>
                            <input type="datetime-local" id="start_time" required
                                style="width: 100%; padding: 0.75rem; border: 2px solid #e5e7eb; border-radius: 0.5rem; font-size: 1rem;">
                        </div>

                        <div style="margin-bottom: 1.5rem;">
                            <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Fecha y Hora Fin
                                *</label>
                            <input type="datetime-local" id="end_time" required
                                style="width: 100%; padding: 0.75rem; border: 2px solid #e5e7eb; border-radius: 0.5rem; font-size: 1rem;">
                        </div>
                    </div>

                    <div style="margin-bottom: 1.5rem;">
                        <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Precio de Entrada
                            (S/)</label>
                        <input type="number" id="ticket_price" step="0.01" min="0"
                            style="width: 100%; padding: 0.75rem; border: 2px solid #e5e7eb; border-radius: 0.5rem; font-size: 1rem;">
                        <p style="font-size: 0.875rem; color: var(--text-secondary); margin-top: 0.25rem;">Dejar vacío si es
                            gratis</p>
                    </div>

                    <div style="margin-bottom: 1.5rem;">
                        <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Dirección del Evento</label>
                        <input type="text" id="address"
                            style="width: 100%; padding: 0.75rem; border: 2px solid #e5e7eb; border-radius: 0.5rem; font-size: 1rem;">
                    </div>
                </div>

                <!-- Right Column - Map -->
                <div>
                    <div style="margin-bottom: 1.5rem;">
                        <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Ubicación en el Mapa
                            (Opcional)</label>
                        <p style="font-size: 0.875rem; color: var(--text-secondary); margin-bottom: 1rem;">
                            📍 Arrastra el marcador a la ubicación del evento (opcional)
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
                        <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Local Asociado
                            (Opcional)</label>
                        <select id="locale_id"
                            style="width: 100%; padding: 0.75rem; border: 2px solid #e5e7eb; border-radius: 0.5rem; font-size: 1rem;">
                            <option value="">Ninguno</option>
                        </select>
                        <p style="font-size: 0.875rem; color: var(--text-secondary); margin-top: 0.25rem;">Si el evento es
                            en uno de tus locales</p>
                    </div>
                </div>
            </div>

            <div
                style="margin-top: 2rem; padding-top: 2rem; border-top: 2px solid var(--bg-light); display: flex; gap: 1rem; justify-content: flex-end;">
                <a href="/dashboard/eventos" class="btn btn-outline">Cancelar</a>
                <button type="submit" class="btn btn-primary">Crear Evento</button>
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

        function initMap() {
            map = L.map('map').setView([-15.8402, -70.0219], 13);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap contributors'
            }).addTo(map);

            marker = L.marker([-15.8402, -70.0219], {
                draggable: true,
                title: 'Arrastra para ubicar el evento'
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

        document.getElementById('evento-form').addEventListener('submit', async (e) => {
            e.preventDefault();

            const errorDiv = document.getElementById('error-message');
            errorDiv.style.display = 'none';

            const useLocation = document.getElementById('use_location').checked;

            const data = {
                title: document.getElementById('title').value,
                category: document.getElementById('category').value,
                description: document.getElementById('description').value,
                content: document.getElementById('content').value,
                start_time: document.getElementById('start_time').value,
                end_time: document.getElementById('end_time').value,
                ticket_price: document.getElementById('ticket_price').value || null,
                address: document.getElementById('address').value,
                locale_id: document.getElementById('locale_id').value || null,
            };

            if (useLocation) {
                data.latitude = parseFloat(document.getElementById('latitude').value);
                data.longitude = parseFloat(document.getElementById('longitude').value);
            }

            try {
                const response = await axios.post('/api/eventos', data);

                if (response.data.success) {
                    showNotification('¡Evento creado exitosamente!', 'success');
                    window.location.href = '/dashboard/eventos';
                }
            } catch (error) {
                errorDiv.style.display = 'block';

                if (error.response?.data?.errors) {
                    const errors = error.response.data.errors;
                    const errorMessages = Object.values(errors).flat();
                    errorDiv.innerHTML = errorMessages.map(msg => `<p>• ${msg}</p>`).join('');
                } else {
                    errorDiv.textContent = 'Error al crear el evento. Por favor, intenta nuevamente.';
                }
            }
        });

        document.addEventListener('DOMContentLoaded', () => {
            initMap();
            loadMyLocales();

            // Set minimum date to now
            const now = new Date();
            now.setMinutes(now.getMinutes() - now.getTimezoneOffset());
            document.getElementById('start_time').min = now.toISOString().slice(0, 16);
            document.getElementById('end_time').min = now.toISOString().slice(0, 16);
        });
    </script>
@endpush
