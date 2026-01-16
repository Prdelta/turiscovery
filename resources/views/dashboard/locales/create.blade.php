@extends('layouts.dashboard')

@section('title', 'Registrar Nuevo Local')

@section('content')
    <div class="fade-in">
        <div class="dashboard-header">
            <div>
                <h1 class="text-2xl font-bold text-slate-800">Registrar Nuevo Local</h1>
                <p class="text-slate-500">Completa la información para publicar tu negocio en Turiscovery.</p>
            </div>
            <a href="{{ url('/dashboard/locales') }}" class="btn btn-outline">
                <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i>
                Cancelar
            </a>
        </div>

        <form action="{{ url('/dashboard/locales') }}" method="POST" enctype="multipart/form-data" class="max-w-4xl">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Columna Izquierda: Datos Principales -->
                <div class="md:col-span-2 space-y-6">
                    <!-- Información Básica -->
                    <div class="card p-6 bg-white border border-gray-200">
                        <h2 class="text-lg font-semibold mb-4 text-slate-800 flex items-center gap-2">
                            <div class="w-8 h-8 rounded-lg bg-blue-100 text-blue-600 flex items-center justify-center">
                                <i data-lucide="store" class="w-5 h-5"></i>
                            </div>
                            Información Básica
                        </h2>

                        <div class="grid grid-cols-1 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">Nombre del Negocio</label>
                                <input type="text" name="name"
                                    class="form-input w-full rounded-lg border-gray-300 focus:ring-blue-500"
                                    placeholder="Ej. Restaurante Los Balcones" required>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">Descripción Corta</label>
                                <textarea name="description" rows="3" class="form-input w-full rounded-lg border-gray-300 focus:ring-blue-500"
                                    placeholder="Describe tu negocio en pocas palabras..."></textarea>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 mb-1">Categoría</label>
                                    <select name="category_id"
                                        class="form-select w-full rounded-lg border-gray-300 focus:ring-blue-500">
                                        <option value="">Seleccionar...</option>
                                        <option value="restaurante">Restaurante</option>
                                        <option value="hotel">Hotel/Hospedaje</option>
                                        <option value="artesania">Artesanía</option>
                                        <option value="agencia">Agencia de Turismo</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 mb-1">Precio Promedio
                                        (S/)</label>
                                    <div class="relative">
                                        <span class="absolute left-3 top-2.5 text-slate-400">S/</span>
                                        <input type="number" name="price_range"
                                            class="form-input w-full pl-8 rounded-lg border-gray-300 focus:ring-blue-500"
                                            placeholder="0.00">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Ubicación y Contacto -->
                    <div class="card p-6 bg-white border border-gray-200">
                        <h2 class="text-lg font-semibold mb-4 text-slate-800 flex items-center gap-2">
                            <div class="w-8 h-8 rounded-lg bg-green-100 text-green-600 flex items-center justify-center">
                                <i data-lucide="map-pin" class="w-5 h-5"></i>
                            </div>
                            Ubicación y Contacto
                        </h2>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-slate-700 mb-1">Dirección Completa</label>
                                <input type="text" name="address"
                                    class="form-input w-full rounded-lg border-gray-300 focus:ring-blue-500"
                                    placeholder="Calle Lima 123, Puno" required>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">Teléfono / WhatsApp</label>
                                <input type="tel" name="phone"
                                    class="form-input w-full rounded-lg border-gray-300 focus:ring-blue-500"
                                    placeholder="+51 900 000 000">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">Sitio Web (Opcional)</label>
                                <input type="url" name="website"
                                    class="form-input w-full rounded-lg border-gray-300 focus:ring-blue-500"
                                    placeholder="https://mi-negocio.com">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Columna Derecha: Imagen y Estado -->
                <div class="space-y-6">
                    <!-- Imagen Principal -->
                    <div class="card p-6 bg-white border border-gray-200">
                        <h2 class="text-lg font-semibold mb-4 text-slate-800">Imagen de Portada</h2>
                        <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:bg-gray-50 transition-colors cursor-pointer relative"
                            id="dropzone">
                            <input type="file" name="image"
                                class="absolute inset-0 w-full h-full opacity-0 cursor-pointer"
                                onchange="previewImage(this)">
                            <div id="image-preview" class="hidden w-full h-40 bg-cover bg-center rounded-lg mb-2"></div>
                            <div id="upload-placeholder">
                                <i data-lucide="image-plus" class="w-10 h-10 text-slate-400 mx-auto mb-2"></i>
                                <p class="text-sm text-slate-500">Arrastra una imagen o haz clic</p>
                                <p class="text-xs text-slate-400 mt-1">PNG, JPG hasta 5MB</p>
                            </div>
                        </div>
                    </div>

                    <!-- Estado -->
                    <div class="card p-6 bg-white border border-gray-200">
                        <h2 class="text-lg font-semibold mb-4 text-slate-800">Publicación</h2>

                        <div class="flex items-center gap-3 mb-4">
                            <div class="flex items-center h-5">
                                <input type="checkbox" name="is_active" checked
                                    class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                            </div>
                            <div class="text-sm">
                                <label class="font-medium text-slate-700">Activo</label>
                                <p class="text-slate-500 text-xs">Visible para el público inmediatamente.</p>
                            </div>
                        </div>

                        <hr class="border-gray-100 my-4">

                        <button type="submit" class="btn btn-primary w-full justify-center">
                            <i data-lucide="save" class="w-4 h-4 mr-2"></i>
                            Guardar Local
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <script>
        function previewImage(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
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
