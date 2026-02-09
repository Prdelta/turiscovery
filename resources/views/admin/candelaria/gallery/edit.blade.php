@extends('layouts.dashboard')

@section('title', 'Editar Fotografía')

@section('content')
    <div class="fade-in max-w-3xl mx-auto">
        <div class="mb-6">
            <a href="{{ route('admin.candelaria.gallery.index') }}" class="text-primary hover:underline flex items-center mb-4">
                <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i>
                Volver a Galería
            </a>
            <h1 class="text-2xl font-bold text-slate-800">Editar Fotografía</h1>
            <p class="text-slate-500">Modifica los datos de la fotografía.</p>
        </div>

        <div class="card p-6">
            <form action="{{ route('admin.candelaria.gallery.update', $gallery) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <!-- Título -->
                <div class="form-group mb-6">
                    <label for="title" class="form-label text-base font-semibold">Título de la Fotografía <span class="text-red-500">*</span></label>
                    <input type="text" id="title" name="title" value="{{ old('title', $gallery->title) }}"
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
                        placeholder="Describe la fotografía y el momento histórico que captura...">{{ old('description', $gallery->description) }}</textarea>
                    @error('description')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Imagen de la Galería -->
                <div class="form-group mb-6">
                    <div class="bg-gradient-to-br from-slate-700 to-slate-900 rounded-t-xl p-4">
                        <div class="flex items-center gap-3 text-white">
                            <div class="w-12 h-12 bg-white bg-opacity-20 backdrop-blur-sm rounded-xl flex items-center justify-center">
                                <i data-lucide="camera" class="w-6 h-6"></i>
                            </div>
                            <div>
                                <h3 class="font-bold text-lg">Fotografía Histórica</h3>
                                <p class="text-sm text-slate-300">Actualizar imagen de la galería</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-slate-50 border-2 border-slate-300 border-t-0 rounded-b-xl p-6">
                        <!-- Imagen Actual -->
                        @if($gallery->image_url)
                            <div class="mb-6 p-4 bg-white rounded-lg shadow-md border border-slate-200">
                                <div class="flex items-center gap-2 mb-3">
                                    <div class="w-8 h-8 bg-slate-600 rounded-lg flex items-center justify-center">
                                        <i data-lucide="image" class="w-5 h-5 text-white"></i>
                                    </div>
                                    <p class="text-sm font-bold text-slate-800">Imagen Actual</p>
                                </div>
                                <div class="relative group">
                                    <img src="{{ $gallery->image_url }}" alt="{{ $gallery->title }}"
                                        class="w-full rounded-lg border-4 border-white shadow-xl transition-transform group-hover:scale-105"
                                        crossorigin="anonymous"
                                        onerror="this.onerror=null; this.src='data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%22400%22 height=%22300%22%3E%3Crect fill=%22%23f3f4f6%22 width=%22400%22 height=%22300%22/%3E%3Ctext fill=%22%239ca3af%22 font-family=%22Arial%22 font-size=%2218%22 text-anchor=%22middle%22 x=%22200%22 y=%22150%22%3EImagen no disponible%3C/text%3E%3C/svg%3E';">
                                    <div class="absolute top-2 right-2 bg-slate-700 text-white px-3 py-1 rounded-full text-xs font-bold shadow-lg">
                                        Actual
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="mb-6 p-6 bg-yellow-50 border-2 border-yellow-300 rounded-lg">
                                <div class="flex items-center gap-3">
                                    <i data-lucide="alert-circle" class="w-6 h-6 text-yellow-600"></i>
                                    <p class="text-sm font-medium text-yellow-800">Esta fotografía no tiene imagen asignada</p>
                                </div>
                            </div>
                        @endif

                        <!-- Subir Nueva Imagen -->
                        <div class="p-5 bg-white rounded-xl shadow-sm border-2 border-slate-300 hover:border-slate-500 transition-colors">
                            <label for="image" class="flex items-center gap-2 text-base font-bold text-slate-800 mb-3">
                                <div class="w-8 h-8 bg-gradient-to-br from-slate-600 to-slate-800 rounded-lg flex items-center justify-center">
                                    <i data-lucide="upload" class="w-5 h-5 text-white"></i>
                                </div>
                                Subir nueva imagen
                            </label>
                            <input type="file" id="image" name="image" accept="image/*"
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

                        <!-- Preview de nueva imagen -->
                        <div id="imagePreview" class="hidden mt-6 p-5 bg-green-50 border-2 border-green-300 rounded-xl">
                            <div class="flex items-center gap-2 mb-3">
                                <div class="w-8 h-8 bg-green-500 rounded-lg flex items-center justify-center">
                                    <i data-lucide="eye" class="w-5 h-5 text-white"></i>
                                </div>
                                <p class="text-sm font-bold text-green-800">Vista previa de la nueva imagen:</p>
                            </div>
                            <div class="relative">
                                <img id="previewImg" src="" alt="Preview" class="w-full rounded-lg border-4 border-white shadow-xl">
                                <div class="absolute top-2 right-2 bg-green-600 text-white px-3 py-1 rounded-full text-xs font-bold shadow-lg animate-pulse">
                                    Nueva
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Año y Orden -->
                <div class="grid grid-cols-2 gap-6 mb-6">
                    <!-- Año -->
                    <div class="form-group">
                        <label for="year" class="form-label">Año <span class="text-red-500">*</span></label>
                        <input type="number" id="year" name="year" value="{{ old('year', $gallery->year) }}"
                            class="form-input @error('year') border-red-500 @enderror"
                            min="1900" max="{{ date('Y') + 1 }}" required>
                        @error('year')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Orden -->
                    <div class="form-group">
                        <label for="order" class="form-label">Orden de Visualización</label>
                        <input type="number" id="order" name="order" value="{{ old('order', $gallery->order) }}"
                            class="form-input @error('order') border-red-500 @enderror"
                            min="0">
                        @error('order')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Estado Activo -->
                <div class="form-group mb-6">
                    <label class="flex items-center cursor-pointer">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $gallery->is_active) ? 'checked' : '' }}
                            class="form-checkbox rounded text-primary">
                        <span class="ml-2 text-slate-700">Fotografía activa (visible en la galería pública)</span>
                    </label>
                </div>

                <!-- Botones -->
                <div class="flex gap-3 pt-4 border-t">
                    <button type="submit" class="btn btn-primary flex-1">
                        <i data-lucide="save" class="w-4 h-4 mr-2"></i>
                        Actualizar Fotografía
                    </button>
                    <a href="{{ route('admin.candelaria.gallery.index') }}" class="btn btn-outline flex-1 text-center">
                        Cancelar
                    </a>
                </div>
            </form>
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
