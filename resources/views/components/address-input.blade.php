@props([
    'street' => '',
    'streetNumber' => '',
    'district' => '',
    'city' => 'Puno',
    'region' => 'Puno',
    'country' => 'Perú',
    'postalCode' => '',
    'addressNotes' => '',
    'latitude' => -15.8402,
    'longitude' => -70.0219,
    'required' => false
])

<div class="card p-6 bg-white border border-gray-200">
    <h2 class="text-lg font-semibold mb-4 text-slate-800 flex items-center gap-2">
        <div class="w-8 h-8 rounded-lg bg-green-100 text-green-600 flex items-center justify-center">
            <i data-lucide="map-pin" class="w-5 h-5"></i>
        </div>
        Ubicación y Dirección
    </h2>

    <!-- Buscador de Dirección con Autocompletado -->
    <div class="mb-4">
        <label class="block text-sm font-medium text-slate-700 mb-2">
            Buscar Dirección 🔍
            <span class="text-xs text-slate-500 font-normal">(Escribe para buscar)</span>
        </label>
        <div class="relative">
            <input
                type="text"
                id="address-search"
                class="form-input w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500"
                placeholder="Ej: Jr. Lima 132, Puno, Perú"
                autocomplete="off">
            <div class="absolute right-3 top-3 text-slate-400">
                <i data-lucide="search" class="w-5 h-5"></i>
            </div>
        </div>
        <!-- Resultados del autocompletado -->
        <div id="address-suggestions" class="hidden mt-2 bg-white border border-slate-200 rounded-lg shadow-lg max-h-60 overflow-y-auto z-50"></div>
        <p class="text-xs text-slate-500 mt-1">💡 Busca y selecciona, o completa manualmente los campos abajo</p>
    </div>

    <!-- Campos de Dirección Estructurados -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
        <!-- Calle -->
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">
                Calle/Jirón/Avenida
                @if($required)<span class="text-red-500">*</span>@endif
            </label>
            <input
                type="text"
                name="street"
                id="street"
                value="{{ $street }}"
                class="form-input w-full rounded-lg border-gray-300 focus:ring-blue-500"
                placeholder="Ej: Jr. Lima"
                {{ $required ? 'required' : '' }}>
        </div>

        <!-- Número -->
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">
                Número
                @if($required)<span class="text-red-500">*</span>@endif
            </label>
            <input
                type="text"
                name="street_number"
                id="street_number"
                value="{{ $streetNumber }}"
                class="form-input w-full rounded-lg border-gray-300 focus:ring-blue-500"
                placeholder="Ej: 132"
                {{ $required ? 'required' : '' }}>
        </div>

        <!-- Distrito/Barrio -->
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Distrito/Barrio</label>
            <input
                type="text"
                name="district"
                id="district"
                value="{{ $district }}"
                class="form-input w-full rounded-lg border-gray-300 focus:ring-blue-500"
                placeholder="Ej: Centro de Puno">
        </div>

        <!-- Ciudad -->
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Ciudad</label>
            <input
                type="text"
                name="city"
                id="city"
                value="{{ $city }}"
                class="form-input w-full rounded-lg border-gray-300 focus:ring-blue-500"
                placeholder="Puno">
        </div>

        <!-- Región/Departamento -->
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Región/Departamento</label>
            <input
                type="text"
                name="region"
                id="region"
                value="{{ $region }}"
                class="form-input w-full rounded-lg border-gray-300 focus:ring-blue-500"
                placeholder="Puno">
        </div>

        <!-- País -->
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">País</label>
            <input
                type="text"
                name="country"
                id="country"
                value="{{ $country }}"
                class="form-input w-full rounded-lg border-gray-300 focus:ring-blue-500"
                placeholder="Perú">
        </div>
    </div>

    <!-- Referencias/Notas -->
    <div class="mb-4">
        <label class="block text-sm font-medium text-slate-700 mb-1">
            Referencias Adicionales
            <span class="text-xs text-slate-500 font-normal">(Opcional)</span>
        </label>
        <textarea
            name="address_notes"
            id="address_notes"
            rows="2"
            class="form-input w-full rounded-lg border-gray-300 focus:ring-blue-500"
            placeholder="Ej: Al frente del Parque Pino, tercer piso">{{ $addressNotes }}</textarea>
        <p class="text-xs text-slate-500 mt-1">Ayuda a los visitantes a encontrarte más fácil</p>
    </div>

    <!-- Mapa Interactivo -->
    <div class="mb-4">
        <label class="block text-sm font-medium text-slate-700 mb-2">
            📍 Ubicación en el Mapa
            <span class="text-xs text-slate-500 font-normal">(Arrastra el marcador)</span>
        </label>
        <div id="map" class="w-full h-80 rounded-lg border-2 border-slate-200 shadow-sm"></div>
        <p class="text-xs text-slate-500 mt-2">
            🎯 <strong>Tip:</strong> Arrastra el marcador rojo a tu ubicación exacta o haz clic en el mapa
        </p>
    </div>

    <!-- Coordenadas (hidden inputs + display) -->
    <div class="grid grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Latitud</label>
            <input
                type="text"
                name="latitude"
                id="latitude"
                value="{{ $latitude }}"
                readonly
                class="form-input w-full rounded-lg border-gray-200 bg-gray-50 text-slate-600"
                placeholder="-15.8402">
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Longitud</label>
            <input
                type="text"
                name="longitude"
                id="longitude"
                value="{{ $longitude }}"
                readonly
                class="form-input w-full rounded-lg border-gray-200 bg-gray-50 text-slate-600"
                placeholder="-70.0219">
        </div>
    </div>
</div>

@push('styles')
<!-- Leaflet CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
    .leaflet-container {
        font-family: 'Inter', sans-serif;
    }
    #address-suggestions {
        position: absolute;
        width: 100%;
        z-index: 1000;
    }
    .address-suggestion-item {
        padding: 12px;
        cursor: pointer;
        border-bottom: 1px solid #e2e8f0;
        transition: background-color 0.2s;
    }
    .address-suggestion-item:hover {
        background-color: #f1f5f9;
    }
    .address-suggestion-item:last-child {
        border-bottom: none;
    }
</style>
@endpush

@push('scripts')
<!-- Leaflet JS -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Inicializar mapa
    const defaultLat = {{ $latitude }};
    const defaultLng = {{ $longitude }};

    const map = L.map('map').setView([defaultLat, defaultLng], 15);

    // Usar OpenStreetMap tiles
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '© OpenStreetMap'
    }).addTo(map);

    // Marcador draggable
    const marker = L.marker([defaultLat, defaultLng], {
        draggable: true,
        icon: L.icon({
            iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-red.png',
            shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/images/marker-shadow.png',
            iconSize: [25, 41],
            iconAnchor: [12, 41],
            popupAnchor: [1, -34],
            shadowSize: [41, 41]
        })
    }).addTo(map);

    marker.bindPopup('<strong>📍 Tu ubicación</strong><br>Arrastra para ajustar').openPopup();

    // Actualizar coordenadas cuando se arrastra el marcador
    marker.on('dragend', function(e) {
        const pos = e.target.getLatLng();
        updateCoordinates(pos.lat, pos.lng);
        reverseGeocode(pos.lat, pos.lng);
    });

    // Click en el mapa para mover el marcador
    map.on('click', function(e) {
        marker.setLatLng(e.latlng);
        updateCoordinates(e.latlng.lat, e.latlng.lng);
        reverseGeocode(e.latlng.lat, e.latlng.lng);
    });

    // Actualizar inputs de coordenadas
    function updateCoordinates(lat, lng) {
        document.getElementById('latitude').value = lat.toFixed(8);
        document.getElementById('longitude').value = lng.toFixed(8);
    }

    // Autocompletado de direcciones (Nominatim)
    const searchInput = document.getElementById('address-search');
    const suggestionsDiv = document.getElementById('address-suggestions');
    let searchTimeout;

    searchInput.addEventListener('input', function(e) {
        const query = e.target.value.trim();

        clearTimeout(searchTimeout);

        if (query.length < 3) {
            suggestionsDiv.classList.add('hidden');
            suggestionsDiv.innerHTML = '';
            return;
        }

        searchTimeout = setTimeout(() => {
            searchAddress(query);
        }, 500);
    });

    async function searchAddress(query) {
        try {
            // Priorizar resultados en Puno, Perú
            const response = await fetch(
                `https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query + ', Puno, Perú')}&limit=5&addressdetails=1`
            );
            const results = await response.json();

            if (results.length === 0) {
                suggestionsDiv.classList.add('hidden');
                return;
            }

            displaySuggestions(results);
        } catch (error) {
            console.error('Error searching address:', error);
        }
    }

    function displaySuggestions(results) {
        suggestionsDiv.innerHTML = results.map(result => `
            <div class="address-suggestion-item" data-lat="${result.lat}" data-lng="${result.lon}" data-address='${JSON.stringify(result.address)}'>
                <div class="flex items-start gap-2">
                    <i data-lucide="map-pin" class="w-4 h-4 text-blue-600 flex-shrink-0 mt-1"></i>
                    <div class="flex-1">
                        <p class="text-sm font-medium text-slate-800">${result.display_name}</p>
                    </div>
                </div>
            </div>
        `).join('');

        suggestionsDiv.classList.remove('hidden');

        // Re-initialize Lucide icons
        lucide.createIcons();

        // Agregar event listeners
        suggestionsDiv.querySelectorAll('.address-suggestion-item').forEach(item => {
            item.addEventListener('click', function() {
                const lat = parseFloat(this.dataset.lat);
                const lng = parseFloat(this.dataset.lng);
                const address = JSON.parse(this.dataset.address);

                selectAddress(lat, lng, address);
            });
        });
    }

    function selectAddress(lat, lng, address) {
        // Mover marcador
        marker.setLatLng([lat, lng]);
        map.setView([lat, lng], 16);
        updateCoordinates(lat, lng);

        // Llenar formulario
        if (address.road) document.getElementById('street').value = address.road;
        if (address.house_number) document.getElementById('street_number').value = address.house_number;
        if (address.suburb || address.neighbourhood) {
            document.getElementById('district').value = address.suburb || address.neighbourhood;
        }
        if (address.city || address.town || address.village) {
            document.getElementById('city').value = address.city || address.town || address.village;
        }
        if (address.state) document.getElementById('region').value = address.state;
        if (address.country) document.getElementById('country').value = address.country;
        if (address.postcode) document.getElementById('postal_code').value = address.postcode;

        // Ocultar sugerencias
        suggestionsDiv.classList.add('hidden');
        searchInput.value = '';
    }

    // Reverse Geocoding (coordenadas -> dirección)
    async function reverseGeocode(lat, lng) {
        try {
            const response = await fetch(
                `https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}&addressdetails=1`
            );
            const result = await response.json();

            if (result.address) {
                // Auto-rellenar solo si los campos están vacíos
                const streetInput = document.getElementById('street');
                const streetNumberInput = document.getElementById('street_number');
                const districtInput = document.getElementById('district');
                const cityInput = document.getElementById('city');

                if (!streetInput.value && result.address.road) {
                    streetInput.value = result.address.road;
                }
                if (!streetNumberInput.value && result.address.house_number) {
                    streetNumberInput.value = result.address.house_number;
                }
                if (!districtInput.value && (result.address.suburb || result.address.neighbourhood)) {
                    districtInput.value = result.address.suburb || result.address.neighbourhood;
                }
                if (!cityInput.value && (result.address.city || result.address.town)) {
                    cityInput.value = result.address.city || result.address.town;
                }
            }
        } catch (error) {
            console.error('Error in reverse geocoding:', error);
        }
    }

    // Cerrar sugerencias al hacer clic fuera
    document.addEventListener('click', function(e) {
        if (!searchInput.contains(e.target) && !suggestionsDiv.contains(e.target)) {
            suggestionsDiv.classList.add('hidden');
        }
    });
});
</script>
@endpush
