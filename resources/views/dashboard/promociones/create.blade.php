@extends('layouts.dashboard')

@section('title', 'Crear Nueva Promoción')

@section('content')
    <div class="fade-in">
        <div class="dashboard-header">
            <div>
                <h1 class="text-2xl font-bold text-slate-800">Crear Nueva Promoción</h1>
                <p class="text-slate-500">Oferta descuentos atractivos para atraer clientes.</p>
            </div>
            <a href="{{ url('/dashboard/promociones') }}" class="btn btn-outline">
                <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i>
                Cancelar
            </a>
        </div>

        <form action="{{ url('/dashboard/promociones') }}" method="POST" enctype="multipart/form-data" class="max-w-4xl">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="md:col-span-2 space-y-6">
                    <!-- Información de la Promo -->
                    <div class="card p-6 bg-white border border-gray-200">
                        <h2 class="text-lg font-semibold mb-4 text-slate-800 flex items-center gap-2">
                            <div class="w-8 h-8 rounded-lg bg-orange-100 text-orange-600 flex items-center justify-center">
                                <i data-lucide="percent" class="w-5 h-5"></i>
                            </div>
                            Detalles de la Oferta
                        </h2>

                        <div class="grid grid-cols-1 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">Título de la Promoción</label>
                                <input type="text" name="title"
                                    class="form-input w-full rounded-lg border-gray-300 focus:ring-orange-500"
                                    placeholder="Ej. 2x1 en Bebidas o 20% Descuento" required>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">Descripción /
                                    Condiciones</label>
                                <textarea name="description" rows="3" class="form-input w-full rounded-lg border-gray-300 focus:ring-orange-500"
                                    placeholder="Válido de Lunes a Jueves..."></textarea>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 mb-1">Porcentaje
                                        Descuento</label>
                                    <div class="relative">
                                        <input type="number" name="discount_percent"
                                            class="form-input w-full pr-8 rounded-lg border-gray-300 focus:ring-orange-500"
                                            placeholder="20">
                                        <span class="absolute right-3 top-2.5 text-slate-400">%</span>
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 mb-1">Código Cupón
                                        (Opcional)</label>
                                    <input type="text" name="code"
                                        class="form-input w-full rounded-lg border-gray-300 focus:ring-orange-500"
                                        placeholder="VERANO2024">
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">Válido Hasta</label>
                                <input type="date" name="valid_until"
                                    class="form-input w-full rounded-lg border-gray-300 focus:ring-orange-500">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Imagen y Guardar -->
                <div class="space-y-6">
                    <div class="card p-6 bg-white border border-gray-200">
                        <h2 class="text-lg font-semibold mb-4 text-slate-800">Imagen Promocional</h2>
                        <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:bg-gray-50 transition-colors cursor-pointer relative"
                            id="dropzone-promo">
                            <input type="file" name="image"
                                class="absolute inset-0 w-full h-full opacity-0 cursor-pointer"
                                onchange="previewImage(this)">
                            <div id="image-preview" class="hidden w-full h-40 bg-cover bg-center rounded-lg mb-2"></div>
                            <div id="upload-placeholder">
                                <i data-lucide="image" class="w-10 h-10 text-slate-400 mx-auto mb-2"></i>
                                <p class="text-sm text-slate-500">Subir Imagen</p>
                            </div>
                        </div>
                    </div>

                    <div class="card p-6 bg-white border border-gray-200">
                        <button type="submit"
                            class="btn btn-warning w-full text-white justify-center shadow-lg shadow-orange-500/20">
                            <i data-lucide="save" class="w-4 h-4 mr-2"></i>
                            Guardar Promoción
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
