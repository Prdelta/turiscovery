@extends('layouts.dashboard')

@section('title', 'Crear Local - Dashboard')

@section('content')
    <div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Crear Nuevo Local</h1>
            <p class="text-gray-500 mt-1">Registra tu ubicación comercial</p>
        </div>
        <div>
            <a href="/dashboard/locales"
                class="inline-flex items-center gap-2 text-gray-600 hover:text-gray-900 font-medium transition-colors">
                <i class="fa-solid fa-arrow-left"></i> Volver a mis locales
            </a>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 md:p-8">
        <div id="error-message" class="hidden mb-6 bg-red-50 text-red-600 p-4 rounded-xl border border-red-100"></div>

        <form id="locale-form" class="space-y-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Left Column -->
                <div class="space-y-6">
                    <div>
                        <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">Nombre del Local
                            *</label>
                        <input type="text" id="name" required
                            class="w-full px-4 py-3 rounded-lg border border-gray-200 focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all outline-none"
                            placeholder="Ej. Restaurante Las Olas">
                    </div>

                    <div>
                        <label for="category" class="block text-sm font-semibold text-gray-700 mb-2">Categoría *</label>
                        <div class="relative">
                            <select id="category" required
                                class="w-full px-4 py-3 rounded-lg border border-gray-200 focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all outline-none appearance-none bg-white">
                                <option value="">Selecciona una categoría</option>
                                <option value="restaurant">🍽️ Restaurante</option>
                                <option value="hotel">🏨 Hotel</option>
                                <option value="tour_agency">🗺️ Agencia de Turismo</option>
                                <option value="craft_shop">🎨 Tienda de Artesanías</option>
                                <option value="museum">🏛️ Museo</option>
                                <option value="cultural_center">🎭 Centro Cultural</option>
                                <option value="other">📍 Otro</option>
                            </select>
                            <div
                                class="absolute inset-y-0 right-0 flex items-center px-4 pointer-events-none text-gray-500">
                                <i class="fa-solid fa-chevron-down text-xs"></i>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label for="description" class="block text-sm font-semibold text-gray-700 mb-2">Descripción
                            *</label>
                        <textarea id="description" required rows="4"
                            class="w-full px-4 py-3 rounded-lg border border-gray-200 focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all outline-none resize-none"
                            placeholder="Describe tu negocio..."></textarea>
                    </div>

                    <div>
                        <label for="address" class="block text-sm font-semibold text-gray-700 mb-2">Dirección *</label>
                        <div class="relative">
                            <input type="text" id="address" required
                                class="w-full pl-10 pr-4 py-3 rounded-lg border border-gray-200 focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all outline-none"
                                placeholder="Dirección completa">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none text-gray-400">
                                <i class="fa-solid fa-location-dot"></i>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div>
                            <label for="phone" class="block text-sm font-semibold text-gray-700 mb-2">Teléfono</label>
                            <input type="tel" id="phone"
                                class="w-full px-4 py-3 rounded-lg border border-gray-200 focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all outline-none"
                                placeholder="+51 999 999 999">
                        </div>
                        <div>
                            <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">Email</label>
                            <input type="email" id="email"
                                class="w-full px-4 py-3 rounded-lg border border-gray-200 focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all outline-none"
                                placeholder="contacto@ejemplo.com">
                        </div>
                    </div>

                    <div>
                        <label for="website" class="block text-sm font-semibold text-gray-700 mb-2">Sitio Web</label>
                        <div class="relative">
                            <input type="url" id="website"
                                class="w-full pl-10 pr-4 py-3 rounded-lg border border-gray-200 focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all outline-none"
                                placeholder="https://...">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none text-gray-400">
                                <i class="fa-solid fa-globe"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column - Map -->
                <div class="space-y-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Ubicación en el Mapa *</label>
                        <p
                            class="text-sm text-gray-500 mb-4 bg-blue-50 text-blue-700 px-4 py-3 rounded-lg border border-blue-100 flex items-center gap-2">
                            <i class="fa-solid fa-circle-info"></i>
                            Arrastra el marcador rojo a la ubicación exacta de tu local.
                        </p>
                        <div id="map" class="h-[400px] w-full rounded-xl border-2 border-gray-200 shadow-inner z-0">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label
                                class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Latitud</label>
                            <input type="number" id="latitude" step="0.000001" required readonly
                                class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-gray-600 font-mono text-sm">
                        </div>
                        <div>
                            <label
                                class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Longitud</label>
                            <input type="number" id="longitude" step="0.000001" required readonly
                                class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-gray-600 font-mono text-sm">
                        </div>
                    </div>
                </div>
            </div>

            <div class="pt-8 border-t border-gray-100 flex items-center justify-end gap-4">
                <a href="/dashboard/locales"
                    class="px-6 py-2.5 rounded-lg text-gray-600 hover:bg-gray-50 font-medium transition-colors">
                    Cancelar
                </a>
                <button type="submit"
                    class="bg-primary text-white px-8 py-2.5 rounded-lg font-medium hover:bg-primary-dark transition-all shadow-lg shadow-primary/30 flex items-center gap-2">
                    <i class="fa-solid fa-check"></i>
                    Crear Local
                </button>
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

        // Initialize map centered on Puno
        function initMap() {
            map = L.map('map').setView([-15.8402, -70.0219], 13);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap contributors'
            }).addTo(map);

            // Create draggable marker
            marker = L.marker([-15.8402, -70.0219], {
                draggable: true,
                title: 'Arrastra para ubicar tu local'
            }).addTo(map);

            // Update coordinates when marker is dragged
            marker.on('dragend', function(e) {
                const latlng = e.target.getLatLng();
                updateCoordinates(latlng.lat, latlng.lng);
            });

            // Allow clicking on map to move marker
            map.on('click', function(e) {
                marker.setLatLng(e.latlng);
                updateCoordinates(e.latlng.lat, e.latlng.lng);
            });

            // Set initial coordinates
            updateCoordinates(-15.8402, -70.0219);
        }

        function updateCoordinates(lat, lng) {
            document.getElementById('latitude').value = lat.toFixed(6);
            document.getElementById('longitude').value = lng.toFixed(6);
        }

        document.getElementById('locale-form').addEventListener('submit', async (e) => {
            e.preventDefault();

            const errorDiv = document.getElementById('error-message');
            errorDiv.classList.add('hidden');
            errorDiv.classList.remove('block');

            const data = {
                name: document.getElementById('name').value,
                category: document.getElementById('category').value,
                description: document.getElementById('description').value,
                address: document.getElementById('address').value,
                phone: document.getElementById('phone').value,
                email: document.getElementById('email').value,
                website: document.getElementById('website').value,
                latitude: parseFloat(document.getElementById('latitude').value),
                longitude: parseFloat(document.getElementById('longitude').value),
            };

            try {
                const response = await axios.post('/api/locales', data);

                if (response.data.success) {
                    showNotification('¡Local creado exitosamente!', 'success');
                    window.location.href = '/dashboard/locales';
                }
            } catch (error) {
                errorDiv.classList.remove('hidden');
                errorDiv.classList.add('block');

                if (error.response?.data?.errors) {
                    const errors = error.response.data.errors;
                    const errorMessages = Object.values(errors).flat();
                    errorDiv.innerHTML = errorMessages.map(msg => `<p>• ${msg}</p>`).join('');
                } else if (error.response?.data?.message) {
                    errorDiv.textContent = error.response.data.message;
                } else {
                    errorDiv.textContent = 'Error al crear el local. Por favor, intenta nuevamente.';
                }
            }
        });

        document.addEventListener('DOMContentLoaded', () => {
            initMap();
        });
    </script>
@endpush
