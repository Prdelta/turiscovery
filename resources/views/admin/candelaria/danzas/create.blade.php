@extends('layouts.dashboard')

@section('title', 'Nueva Danza')

@section('content')
    <div class="fade-in max-w-4xl mx-auto">
        <div class="mb-6">
            <a href="{{ route('admin.candelaria.danzas.index') }}" class="text-primary hover:underline flex items-center mb-4">
                <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i>
                Volver a Danzas
            </a>
            <h1 class="text-2xl font-bold text-slate-800">Agregar Nueva Danza</h1>
            <p class="text-slate-500">Completa el formulario para agregar una danza tradicional.</p>
        </div>

        <div class="card p-6">
            <form action="{{ route('admin.candelaria.danzas.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <!-- Nombre -->
                <div class="form-group mb-6">
                    <label for="name" class="form-label text-base font-semibold">Nombre de la Danza <span class="text-red-500">*</span></label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}"
                        class="form-input text-lg py-3 @error('name') border-red-500 @enderror"
                        placeholder="Ej: Diablada Puneña" required>
                    @error('name')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Tipo y Región -->
                <div class="grid grid-cols-2 gap-6 mb-6">
                    <div class="form-group">
                        <label for="type" class="form-label">Tipo <span class="text-red-500">*</span></label>
                        <select id="type" name="type" class="form-select @error('type') border-red-500 @enderror" required>
                            <option value="">Seleccionar...</option>
                            <option value="mestiza" {{ old('type') === 'mestiza' ? 'selected' : '' }}>🎭 Mestiza</option>
                            <option value="autoctona" {{ old('type') === 'autoctona' ? 'selected' : '' }}>🗿 Autóctona</option>
                        </select>
                        @error('type')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                        <p class="text-sm text-slate-500 mt-1">Mestiza: fusión colonial | Autóctona: preincaica</p>
                    </div>

                    <div class="form-group">
                        <label for="region" class="form-label">Región</label>
                        <input type="text" id="region" name="region" value="{{ old('region') }}"
                            class="form-input @error('region') border-red-500 @enderror"
                            placeholder="Ej: Puno, Altiplano">
                        @error('region')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Descripción -->
                <div class="form-group mb-6">
                    <label for="description" class="form-label text-base font-semibold">Descripción Breve <span class="text-red-500">*</span></label>
                    <textarea id="description" name="description" rows="5"
                        class="form-input text-base py-3 @error('description') border-red-500 @enderror"
                        placeholder="Descripción breve de la danza (2-3 líneas)..." required>{{ old('description') }}</textarea>
                    @error('description')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                    <p class="text-sm text-slate-500 mt-1">Esta descripción aparecerá en las tarjetas de vista previa.</p>
                </div>

                <!-- Historia -->
                <div class="form-group mb-6">
                    <label for="history" class="form-label text-base font-semibold">Historia y Origen</label>
                    <textarea id="history" name="history" rows="8"
                        class="form-input text-base py-3 @error('history') border-red-500 @enderror"
                        placeholder="Origen histórico, evolución, contexto cultural, época...">{{ old('history') }}</textarea>
                    @error('history')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                    <p class="text-sm text-slate-500 mt-1">Contexto histórico completo de la danza.</p>
                </div>

                <!-- Características -->
                <div class="form-group mb-6">
                    <label for="characteristics" class="form-label text-base font-semibold">Características</label>
                    <textarea id="characteristics" name="characteristics" rows="7"
                        class="form-input text-base py-3 @error('characteristics') border-red-500 @enderror"
                        placeholder="Trajes: descripción...&#10;Movimientos: descripción...&#10;Música: instrumentos...&#10;Personajes: roles principales...">{{ old('characteristics') }}</textarea>
                    @error('characteristics')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                    <p class="text-sm text-slate-500 mt-1">Vestimenta, coreografía, música, personajes principales.</p>
                </div>

                <!-- Imagen de la Danza -->
                <div class="form-group mb-6">
                    <div class="bg-gradient-to-br from-slate-700 to-slate-900 rounded-t-xl p-4">
                        <div class="flex items-center gap-3 text-white">
                            <div class="w-12 h-12 bg-white bg-opacity-20 backdrop-blur-sm rounded-xl flex items-center justify-center">
                                <i data-lucide="image" class="w-6 h-6"></i>
                            </div>
                            <div>
                                <h3 class="font-bold text-lg">Imagen de la Danza</h3>
                                <p class="text-sm text-slate-300">Fotografía representativa de la danza</p>
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

                <!-- URL de Video -->
                <div class="form-group mb-6">
                    <label for="video_url" class="form-label flex items-center gap-2">
                        <i data-lucide="video" class="w-4 h-4"></i>
                        URL de Video (Opcional)
                    </label>
                    <input type="url" id="video_url" name="video_url" value="{{ old('video_url') }}"
                        class="form-input @error('video_url') border-red-500 @enderror"
                        placeholder="https://youtube.com/watch?v=...">
                    @error('video_url')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                    <p class="text-sm text-slate-500 mt-1">Video demostrativo de YouTube, Vimeo, etc.</p>
                </div>

                <!-- Orden -->
                <div class="form-group mb-6">
                    <label for="order" class="form-label">Orden de Visualización</label>
                    <input type="number" id="order" name="order" value="{{ old('order', 0) }}"
                        class="form-input @error('order') border-red-500 @enderror" min="0">
                    @error('order')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                    <p class="text-sm text-slate-500 mt-1">Orden en el catálogo (0 = primero, números mayores después).</p>
                </div>

                <!-- Checkboxes -->
                <div class="space-y-3 mb-6 p-4 bg-gray-50 rounded-lg border border-gray-200">
                    <label class="flex items-center cursor-pointer group">
                        <input type="checkbox" name="is_featured" value="1" {{ old('is_featured') ? 'checked' : '' }}
                            class="form-checkbox rounded text-yellow-500">
                        <span class="ml-2 text-slate-700 group-hover:text-slate-900">
                            <span class="inline-flex items-center">
                                <i data-lucide="star" class="w-4 h-4 mr-1 text-yellow-500"></i>
                                <strong>Danza destacada</strong>
                            </span>
                            <br>
                            <span class="text-sm text-slate-500">Se mostrará en la sección principal de la festividad</span>
                        </span>
                    </label>

                    <label class="flex items-center cursor-pointer group">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}
                            class="form-checkbox rounded text-green-500">
                        <span class="ml-2 text-slate-700 group-hover:text-slate-900">
                            <span class="inline-flex items-center">
                                <i data-lucide="check-circle" class="w-4 h-4 mr-1 text-green-500"></i>
                                <strong>Danza activa</strong>
                            </span>
                            <br>
                            <span class="text-sm text-slate-500">Visible en el catálogo público de danzas</span>
                        </span>
                    </label>
                </div>

                <!-- Botones -->
                <div class="flex gap-3 pt-4 border-t">
                    <button type="submit" class="btn btn-primary flex-1">
                        <i data-lucide="save" class="w-4 h-4 mr-2"></i>
                        Guardar Danza
                    </button>
                    <a href="{{ route('admin.candelaria.danzas.index') }}" class="btn btn-outline flex-1 text-center">
                        Cancelar
                    </a>
                </div>
            </form>
        </div>

        <!-- Ayuda -->
        <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
            <h3 class="font-semibold text-blue-900 mb-2 flex items-center">
                <i data-lucide="info" class="w-5 h-5 mr-2"></i>
                Consejos para agregar danzas
            </h3>
            <ul class="text-sm text-blue-800 space-y-1 list-disc list-inside">
                <li><strong>Descripción:</strong> Debe ser concisa (2-3 líneas) para las tarjetas</li>
                <li><strong>Historia:</strong> Contexto histórico completo (origen, época, evolución)</li>
                <li><strong>Características:</strong> Detalla trajes, movimientos, música y personajes</li>
                <li><strong>Tipo Mestiza:</strong> Fusión de elementos coloniales (español, africano, indígena)</li>
                <li><strong>Tipo Autóctona:</strong> Danzas preincaicas puras, ancestrales</li>
            </ul>
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
