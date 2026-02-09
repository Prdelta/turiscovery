@extends('layouts.dashboard')

@section('title', 'Registrar Nuevo Local')
@section('page-title', 'Nuevo Local')
@section('page-subtitle', 'Registra tu negocio en Turiscovery')

@section('content')
    <div class="max-w-7xl mx-auto">
        @if ($errors->any())
            <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-lg">
                <div class="flex items-start">
                    <i data-lucide="alert-circle" class="w-5 h-5 text-red-500 mt-0.5 mr-3"></i>
                    <div>
                        <p class="font-bold text-red-800">Hay errores en el formulario:</p>
                        <ul class="list-disc list-inside text-sm text-red-700 mt-2">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        <form action="{{ route('locales.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Columna Principal (2/3) -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Información Básica -->
                    <div class="card p-6 bg-white border border-gray-200">
                        <h2 class="text-lg font-semibold mb-4 text-slate-800 flex items-center gap-2">
                            <div class="w-8 h-8 rounded-lg bg-blue-100 text-blue-600 flex items-center justify-center">
                                <i data-lucide="store" class="w-5 h-5"></i>
                            </div>
                            Información Básica
                        </h2>

                        <div class="space-y-4">
                            <!-- Nombre -->
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">
                                    Nombre del Negocio <span class="text-red-500">*</span>
                                </label>
                                <input
                                    type="text"
                                    name="name"
                                    value="{{ old('name') }}"
                                    class="form-input w-full rounded-lg border-gray-300 focus:ring-blue-500"
                                    placeholder="Ej: Hotel Vista del Lago"
                                    required>
                            </div>

                            <!-- Descripción -->
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">
                                    Descripción <span class="text-red-500">*</span>
                                </label>
                                <textarea
                                    name="description"
                                    rows="4"
                                    class="form-input w-full rounded-lg border-gray-300 focus:ring-blue-500"
                                    placeholder="Describe tu negocio, servicios y lo que te hace especial..."
                                    required>{{ old('description') }}</textarea>
                                <p class="text-xs text-slate-500 mt-1">Mínimo 50 caracteres</p>
                            </div>

                            <!-- Categoría -->
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">
                                    Categoría <span class="text-red-500">*</span>
                                </label>
                                <select
                                    name="category"
                                    class="form-select w-full rounded-lg border-gray-300 focus:ring-blue-500"
                                    required>
                                    <option value="">Seleccionar categoría...</option>
                                    <option value="restaurant" {{ old('category') == 'restaurant' ? 'selected' : '' }}>🍽️ Restaurante</option>
                                    <option value="hotel" {{ old('category') == 'hotel' ? 'selected' : '' }}>🏨 Hotel / Hospedaje</option>
                                    <option value="tour_agency" {{ old('category') == 'tour_agency' ? 'selected' : '' }}>🎒 Agencia de Turismo</option>
                                    <option value="craft_shop" {{ old('category') == 'craft_shop' ? 'selected' : '' }}>🎨 Artesanía</option>
                                    <option value="museum" {{ old('category') == 'museum' ? 'selected' : '' }}>🏛️ Museo</option>
                                    <option value="cultural_center" {{ old('category') == 'cultural_center' ? 'selected' : '' }}>🎭 Centro Cultural</option>
                                    <option value="other" {{ old('category') == 'other' ? 'selected' : '' }}>📍 Otro</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Componente de Dirección -->
                    <x-address-input
                        :street="old('street', '')"
                        :streetNumber="old('street_number', '')"
                        :district="old('district', '')"
                        :city="old('city', 'Puno')"
                        :region="old('region', 'Puno')"
                        :country="old('country', 'Perú')"
                        :postalCode="old('postal_code', '')"
                        :addressNotes="old('address_notes', '')"
                        :latitude="old('latitude', -15.8402)"
                        :longitude="old('longitude', -70.0219)"
                        :required="true"
                    />

                    <!-- Contacto -->
                    <div class="card p-6 bg-white border border-gray-200">
                        <h2 class="text-lg font-semibold mb-4 text-slate-800 flex items-center gap-2">
                            <div class="w-8 h-8 rounded-lg bg-purple-100 text-purple-600 flex items-center justify-center">
                                <i data-lucide="phone" class="w-5 h-5"></i>
                            </div>
                            Información de Contacto
                        </h2>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">Teléfono / WhatsApp</label>
                                <input
                                    type="tel"
                                    name="phone"
                                    value="{{ old('phone') }}"
                                    class="form-input w-full rounded-lg border-gray-300 focus:ring-blue-500"
                                    placeholder="+51 999 999 999">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">Email</label>
                                <input
                                    type="email"
                                    name="email"
                                    value="{{ old('email') }}"
                                    class="form-input w-full rounded-lg border-gray-300 focus:ring-blue-500"
                                    placeholder="contacto@negocio.com">
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-slate-700 mb-1">Sitio Web</label>
                                <input
                                    type="url"
                                    name="website"
                                    value="{{ old('website') }}"
                                    class="form-input w-full rounded-lg border-gray-300 focus:ring-blue-500"
                                    placeholder="https://www.minegocio.com">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sidebar (1/3) -->
                <div class="space-y-6">
                    <!-- Imagen Principal -->
                    <div class="card p-6 bg-white border border-gray-200">
                        <h2 class="text-lg font-semibold mb-4 text-slate-800">Imagen de Portada</h2>
                        <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:bg-gray-50 transition-colors cursor-pointer relative"
                            id="dropzone">
                            <input type="file" name="image" accept="image/*"
                                class="absolute inset-0 w-full h-full opacity-0 cursor-pointer"
                                onchange="previewImage(this)">
                            <div id="image-preview" class="hidden w-full h-40 bg-cover bg-center rounded-lg mb-2"></div>
                            <div id="upload-placeholder">
                                <i data-lucide="image-plus" class="w-10 h-10 text-slate-400 mx-auto mb-2"></i>
                                <p class="text-sm text-slate-500">Arrastra o haz clic</p>
                                <p class="text-xs text-slate-400 mt-1">PNG, JPG hasta 5MB</p>
                            </div>
                        </div>
                        <p class="text-xs text-slate-500 mt-2">
                            💡 Usa una imagen atractiva de tu negocio o fachada
                        </p>
                    </div>

                    <!-- Estado y Publicación -->
                    <div class="card p-6 bg-white border border-gray-200">
                        <h2 class="text-lg font-semibold mb-4 text-slate-800">Publicación</h2>

                        <div class="space-y-4">
                            <div class="flex items-start gap-3">
                                <input
                                    type="checkbox"
                                    name="is_active"
                                    id="is_active"
                                    checked
                                    class="mt-1 w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                <div class="flex-1">
                                    <label for="is_active" class="text-sm font-medium text-slate-700 cursor-pointer">
                                        Publicar ahora
                                    </label>
                                    <p class="text-xs text-slate-500 mt-1">
                                        Tu local será visible inmediatamente en la plataforma
                                    </p>
                                </div>
                            </div>

                            <hr class="border-gray-200">

                            <button type="submit" class="btn btn-primary w-full justify-center">
                                <i data-lucide="save" class="w-4 h-4"></i>
                                Guardar Local
                            </button>

                            <a href="{{ route('locales.index') }}" class="btn btn-outline w-full justify-center">
                                <i data-lucide="x" class="w-4 h-4"></i>
                                Cancelar
                            </a>
                        </div>
                    </div>

                    <!-- Tips -->
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <h3 class="font-semibold text-blue-900 text-sm mb-2">💡 Consejos para destacar</h3>
                        <ul class="text-xs text-blue-800 space-y-1">
                            <li>✓ Usa fotos de alta calidad</li>
                            <li>✓ Describe claramente tus servicios</li>
                            <li>✓ Verifica que la ubicación sea exacta</li>
                            <li>✓ Agrega referencias para facilitar el acceso</li>
                        </ul>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <script>
        function previewImage(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const preview = document.getElementById('image-preview');
                    const placeholder = document.getElementById('upload-placeholder');

                    preview.style.backgroundImage = 'url(' + e.target.result + ')';
                    preview.classList.remove('hidden');
                    placeholder.classList.add('hidden');
                }
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
@endsection
