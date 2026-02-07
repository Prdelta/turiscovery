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
            <form action="{{ route('admin.candelaria.gallery.store') }}" method="POST">
                @csrf

                <!-- Título -->
                <div class="form-group mb-6">
                    <label for="title" class="form-label">Título de la Fotografía <span class="text-red-500">*</span></label>
                    <input type="text" id="title" name="title" value="{{ old('title') }}"
                        class="form-input @error('title') border-red-500 @enderror"
                        placeholder="Ej: Gran Corso de la Festividad 2024" required>
                    @error('title')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Descripción -->
                <div class="form-group mb-6">
                    <label for="description" class="form-label">Descripción</label>
                    <textarea id="description" name="description" rows="4"
                        class="form-input @error('description') border-red-500 @enderror"
                        placeholder="Describe la fotografía y el momento histórico que captura...">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                    <p class="text-sm text-slate-500 mt-1">Explica el contexto histórico de la fotografía.</p>
                </div>

                <!-- URL de Imagen -->
                <div class="form-group mb-6">
                    <label for="image_url" class="form-label">URL de la Imagen <span class="text-red-500">*</span></label>
                    <input type="url" id="image_url" name="image_url" value="{{ old('image_url') }}"
                        class="form-input @error('image_url') border-red-500 @enderror"
                        placeholder="https://ejemplo.com/imagen.jpg" required>
                    @error('image_url')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                    <p class="text-sm text-slate-500 mt-1">URL completa de la fotografía (debe iniciar con http:// o https://).</p>
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
                        <img id="previewImage" src="" alt="" class="w-full h-full object-cover">
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
    </script>
@endpush
