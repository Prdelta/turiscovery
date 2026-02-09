@extends('layouts.dashboard')

@section('title', 'Nueva Fotografía')

@section('content')
    <div class="fade-in max-w-3xl mx-auto">
        <div class="mb-6">
            <a href="{{ route('admin.candelaria.gallery.index') }}" class="text-primary hover:underline flex items-center mb-4">
                <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i>
                Volver a Galería
            </a>
            <h1 class="text-2xl font-bold text-slate-800">Agregar Nueva Fotografía</h1>
            <p class="text-slate-500">Completa el formulario para agregar una fotografía a la galería histórica.</p>
        </div>

        <div class="card p-6">
            <form action="{{ route('admin.candelaria.gallery.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <!-- Título -->
                <div class="form-group mb-6">
                    <label for="title" class="form-label text-base font-semibold">Título de la Fotografía <span class="text-red-500">*</span></label>
                    <input type="text" id="title" name="title" value="{{ old('title') }}"
                        class="form-input text-lg py-3 @error('title') border-red-500 @enderror"
                        placeholder="Ej: Gran Corso de la Festividad 2024" required>
                    @error('title')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Descripción -->
                <div class="form-group mb-6">
                    <label for="description" class="form-label text-base font-semibold">Descripción</label>
                    <textarea id="description" name="description" rows="6"
                        class="form-input text-base py-3 @error('description') border-red-500 @enderror"
                        placeholder="Describe la fotografía y el momento histórico que captura...">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                    <p class="text-sm text-slate-500 mt-1">Explica el contexto histórico de la fotografía.</p>
                </div>

                <!-- Imagen de la Galería -->
                <div class="form-group mb-6">
                    <div class="bg-gradient-to-br from-slate-700 to-slate-900 rounded-t-xl p-4">
                        <div class="flex items-center gap-3 text-white">
                            <div class="w-12 h-12 bg-white bg-opacity-20 backdrop-blur-sm rounded-xl flex items-center justify-center">
                                <i data-lucide="camera" class="w-6 h-6"></i>
                            </div>
                            <div>
                                <h3 class="font-bold text-lg">Fotografía <span class="text-red-400">*</span></h3>
                                <p class="text-sm text-slate-300">Imagen histórica de la Festividad</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-slate-50 border-2 border-slate-300 border-t-0 rounded-b-xl p-6">
                        <!-- Subir Archivo -->
                        <div class="p-5 bg-white rounded-xl shadow-sm border-2 border-slate-300 hover:border-slate-500 transition-colors">
                            <label for="image" class="flex items-center gap-2 text-base font-bold text-slate-800 mb-3">
                                <div class="w-8 h-8 bg-gradient-to-br from-slate-600 to-slate-800 rounded-lg flex items-center justify-center">
                                    <i data-lucide="upload" class="w-5 h-5 text-white"></i>
                                </div>
                                Subir imagen desde tu computadora
                            </label>
                            <input type="file" id="image" name="image" accept="image/*" required
                                class="form-input @error('image') border-red-500 @enderror"
                                onchange="previewImageFile(event)">
                            @error('image')
                                <p class="text-red-500 text-sm mt-2 flex items-center gap-1">
                                    <i data-lucide="x-circle" class="w-4 h-4"></i>
                                    {{ $message }}
                                </p>
                            @enderror
                            <div class="mt-2 flex items-center gap-2 text-xs text-slate-600">
                                <i data-lucide="info" class="w-4 h-4"></i>
                                <span>Formatos: <strong>JPG, PNG, GIF, WEBP</strong> • Tamaño máximo: <strong>5MB</strong></span>
                            </div>
                        </div>

                        <!-- Preview de imagen -->
                        <div id="imagePreview" class="hidden mt-6 p-5 bg-green-50 border-2 border-green-300 rounded-xl">
                            <div class="flex items-center gap-2 mb-3">
                                <div class="w-8 h-8 bg-green-500 rounded-lg flex items-center justify-center">
                                    <i data-lucide="eye" class="w-5 h-5 text-white"></i>
                                </div>
                                <p class="text-sm font-bold text-green-800">Vista previa:</p>
                            </div>
                            <img id="previewImg" src="" alt="Preview" class="w-full rounded-lg border-4 border-white shadow-xl">
                        </div>
                    </div>
                </div>

                <!-- Año y Orden -->
                <div class="grid grid-cols-2 gap-6 mb-6">
                    <!-- Año -->
                    <div class="form-group">
                        <label for="year" class="form-label">Año <span class="text-red-500">*</span></label>
                        <input type="number" id="year" name="year" value="{{ old('year', date('Y')) }}"
                            class="form-input @error('year') border-red-500 @enderror"
                            min="1900" max="{{ date('Y') + 1 }}" required>
                        @error('year')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Orden -->
                    <div class="form-group">
                        <label for="order" class="form-label">Orden de Visualización</label>
                        <input type="number" id="order" name="order" value="{{ old('order', 0) }}"
                            class="form-input @error('order') border-red-500 @enderror"
                            min="0">
                        @error('order')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                        <p class="text-sm text-slate-500 mt-1">0 = primero</p>
                    </div>
                </div>

                <!-- Estado Activo -->
                <div class="form-group mb-6">
                    <label class="flex items-center cursor-pointer">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}
                            class="form-checkbox rounded text-primary">
                        <span class="ml-2 text-slate-700">Fotografía activa (visible en la galería pública)</span>
                    </label>
                </div>

                <!-- Botones -->
                <div class="flex gap-3 pt-4 border-t">
                    <button type="button" onclick="showPreview()" class="btn btn-secondary flex-1">
                        <i data-lucide="eye" class="w-4 h-4 mr-2"></i>
                        Vista Previa
                    </button>
                    <button type="submit" class="btn btn-primary flex-1">
                        <i data-lucide="save" class="w-4 h-4 mr-2"></i>
                        Guardar Fotografía
                    </button>
                    <a href="{{ route('admin.candelaria.gallery.index') }}" class="btn btn-outline flex-1 text-center">
                        Cancelar
                    </a>
                </div>
            </form>
        </div>

        <!-- Link al Buscador de Recursos -->
        <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
            <h3 class="font-semibold text-blue-900 mb-2 flex items-center">
                <i data-lucide="search" class="w-5 h-5 mr-2"></i>
                ¿Necesitas imágenes o información?
            </h3>
            <p class="text-sm text-blue-800 mb-3">
                Usa nuestro buscador de recursos para encontrar imágenes de alta calidad e información histórica.
            </p>
            <a href="{{ route('admin.candelaria.resources.search') }}" target="_blank" class="btn btn-outline btn-sm">
                <i data-lucide="external-link" class="w-4 h-4 mr-2"></i>
                Abrir Buscador de Recursos
            </a>
        </div>
    </div>

    <!-- Modal de Vista Previa -->
    <div id="previewModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
        <div class="card max-w-2xl w-full max-h-[90vh] overflow-y-auto">
            <div class="p-4 bg-slate-50 border-b border-slate-200 flex justify-between items-center">
                <h3 class="font-bold text-lg flex items-center gap-2">
                    <i data-lucide="eye" class="w-5 h-5"></i>
                    Vista Previa de la Fotografía
                </h3>
                <button onclick="closePreview()" class="text-slate-400 hover:text-slate-600">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>
            <div class="p-6">
                <p class="text-sm text-slate-500 mb-4">Así se verá tu fotografía en la galería pública:</p>
                <div class="card overflow-hidden hover:shadow-xl transition-all border border-slate-200">
                    <div class="aspect-video overflow-hidden bg-slate-100" id="previewImageContainer">
                        <img id="previewImage" src="" alt="" class="w-full h-full object-cover" crossorigin="anonymous">
                    </div>
                    <div class="p-4 bg-white">
                        <span id="previewYear" class="inline-block bg-slate-800 text-white px-2 py-1 rounded text-xs font-bold mb-2">2024</span>
                        <h4 id="previewTitle" class="font-bold text-sm mb-1 text-slate-800">Título de la fotografía</h4>
                        <p id="previewDescription" class="text-xs text-slate-500 line-clamp-2"></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            lucide.createIcons();

            // Auto-resize textareas
            const textareas = document.querySelectorAll('textarea');
            textareas.forEach(textarea => {
                // Auto-resize on input
                textarea.addEventListener('input', function() {
                    this.style.height = 'auto';
                    this.style.height = (this.scrollHeight) + 'px';
                });

                // Initial resize
                textarea.style.height = 'auto';
                textarea.style.height = (textarea.scrollHeight) + 'px';
            });
        });

        function showPreview() {
            const title = document.getElementById('title').value || 'Título de la fotografía';
            const description = document.getElementById('description').value || 'Sin descripción';
            const imageUrl = document.getElementById('image_url').value;
            const year = document.getElementById('year').value || new Date().getFullYear();

            if (!imageUrl) {
                alert('Por favor ingresa una URL de imagen primero');
                return;
            }

            // Actualizar preview
            document.getElementById('previewTitle').textContent = title;
            document.getElementById('previewDescription').textContent = description;
            document.getElementById('previewYear').textContent = year;
            document.getElementById('previewImage').src = imageUrl;
            document.getElementById('previewImage').alt = title;

            // Mostrar modal
            document.getElementById('previewModal').classList.remove('hidden');
            lucide.createIcons();
        }

        function closePreview() {
            document.getElementById('previewModal').classList.add('hidden');
        }

        function previewImageFile(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('previewImg').src = e.target.result;
                    document.getElementById('imagePreview').classList.remove('hidden');
                }
                reader.readAsDataURL(file);
            }
        }
    </script>
@endpush
