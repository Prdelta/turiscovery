@extends('layouts.dashboard')

@section('title', isset($experiencia) ? 'Editar Experiencia' : 'Nueva Experiencia')

@section('content')
    <div class="fade-in">
        <div class="dashboard-header mb-6">
            <div>
                <h1 class="text-2xl font-bold text-slate-800">
                    {{ isset($experiencia) ? 'Editar Experiencia' : 'Nueva Experiencia' }}
                </h1>
                <p class="text-slate-500">Publica un turo o actividad para los turistas.</p>
            </div>
            <a href="{{ route('experiencias.index') }}" class="btn btn-outline">
                <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i>
                Cancelar
            </a>
        </div>

        <form
            action="{{ isset($experiencia) ? route('experiencias.update', $experiencia->id) : route('experiencias.store') }}"
            method="POST" enctype="multipart/form-data" class="max-w-4xl">
            @csrf
            @if (isset($experiencia))
                @method('PUT')
            @endif

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="md:col-span-2 space-y-6">
                    <!-- Información Principal -->
                    <div class="card p-6 bg-white border border-gray-200">
                        <h2 class="text-lg font-semibold mb-4 text-slate-800 flex items-center gap-2">
                            <div class="w-8 h-8 rounded-lg bg-blue-100 text-blue-600 flex items-center justify-center">
                                <i data-lucide="compass" class="w-5 h-5"></i>
                            </div>
                            Detalles de la Experiencia
                        </h2>

                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">Título</label>
                                <input type="text" name="title" value="{{ $experiencia->title ?? old('title') }}"
                                    class="form-input w-full rounded-lg border-gray-300 focus:ring-blue-500"
                                    placeholder="Ej. Tour Uros Medio Día" required>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">Descripción</label>
                                <textarea name="description" rows="5" class="form-input w-full rounded-lg border-gray-300 focus:ring-blue-500"
                                    placeholder="Describe lo que incluye la experiencia...">{{ $experiencia->description ?? old('description') }}</textarea>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 mb-1">Precio (S/)</label>
                                    <div class="relative">
                                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500">S/</span>
                                        <input type="number" name="price" step="0.01"
                                            value="{{ $experiencia->price ?? old('price') }}"
                                            class="form-input w-full rounded-lg border-gray-300 pl-8 focus:ring-blue-500"
                                            required>
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 mb-1">Duración</label>
                                    <input type="text" name="duration"
                                        value="{{ $experiencia->duration ?? old('duration') }}"
                                        class="form-input w-full rounded-lg border-gray-300 focus:ring-blue-500"
                                        placeholder="Ej. 3 Horas, Full Day" required>
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">Ubicación / Punto de
                                    Encuentro</label>
                                <input type="text" name="location"
                                    value="{{ $experiencia->location ?? old('location') }}"
                                    class="form-input w-full rounded-lg border-gray-300 focus:ring-blue-500"
                                    placeholder="Ej. Puerto Puno" required>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Imagen y Publicación -->
                <div class="space-y-6">
                    <div class="card p-6 bg-white border border-gray-200">
                        <h2 class="text-lg font-semibold mb-4 text-slate-800">Imagen Principal</h2>

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-slate-700 mb-1">URL de la Imagen</label>
                            <input type="url" name="image_url" id="image_url"
                                value="{{ $experiencia->image_url ?? old('image_url') }}"
                                class="form-input w-full rounded-lg border-gray-300 focus:ring-blue-500"
                                placeholder="https://..." onchange="updatePreview(this.value)">
                        </div>

                        <div id="image-preview"
                            class="w-full h-40 bg-gray-100 rounded-lg overflow-hidden flex items-center justify-center border border-dashed border-gray-300">
                            @if (isset($experiencia) && $experiencia->image_url)
                                <img src="{{ $experiencia->image_url }}" class="w-full h-full object-cover">
                            @else
                                <div class="text-center text-gray-400">
                                    <i data-lucide="image" class="w-8 h-8 mx-auto mb-1"></i>
                                    <span class="text-xs">Vista previa</span>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="card p-6 bg-white border border-gray-200">
                        <button type="submit" class="btn btn-primary w-full justify-center">
                            <i data-lucide="save" class="w-4 h-4 mr-2"></i>
                            {{ isset($experiencia) ? 'Actualizar' : 'Publicar' }}
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <script>
        function updatePreview(url) {
            const preview = document.getElementById('image-preview');
            if (url) {
                preview.innerHTML =
                    `<img src="${url}" class="w-full h-full object-cover" onerror="this.src='https://via.placeholder.com/400x300?text=Error+Imagen'">`;
            } else {
                preview.innerHTML = `
                    <div class="text-center text-gray-400">
                        <i data-lucide="image" class="w-8 h-8 mx-auto mb-1"></i>
                        <span class="text-xs">Vista previa</span>
                    </div>`;
                lucide.createIcons();
            }
        }
    </script>
@endsection
