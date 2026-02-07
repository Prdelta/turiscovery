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
            <form action="{{ route('admin.candelaria.gallery.update', $gallery) }}" method="POST">
                @csrf
                @method('PUT')

                <!-- Título -->
                <div class="form-group mb-6">
                    <label for="title" class="form-label">Título de la Fotografía <span class="text-red-500">*</span></label>
                    <input type="text" id="title" name="title" value="{{ old('title', $gallery->title) }}"
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
                        placeholder="Describe la fotografía y el momento histórico que captura...">{{ old('description', $gallery->description) }}</textarea>
                    @error('description')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- URL de Imagen -->
                <div class="form-group mb-6">
                    <label for="image_url" class="form-label">URL de la Imagen <span class="text-red-500">*</span></label>
                    <input type="url" id="image_url" name="image_url" value="{{ old('image_url', $gallery->image_url) }}"
                        class="form-input @error('image_url') border-red-500 @enderror"
                        placeholder="https://ejemplo.com/imagen.jpg" required>
                    @error('image_url')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror

                    <!-- Preview de imagen -->
                    <div class="mt-3">
                        <img src="{{ $gallery->image_url }}" alt="{{ $gallery->title }}"
                            class="w-full h-48 object-cover rounded-lg border border-gray-200">
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
        });
    </script>
@endpush
