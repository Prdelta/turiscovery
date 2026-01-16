@extends('layouts.dashboard')

@section('title', 'Crear Nuevo Evento')

@section('content')
    <div class="fade-in">
        <div class="dashboard-header">
            <div>
                <h1 class="text-2xl font-bold text-slate-800">Crear Nuevo Evento</h1>
                <p class="text-slate-500">Publica un evento para atraer más visitantes.</p>
            </div>
            <a href="{{ url('/dashboard/eventos') }}" class="btn btn-outline">
                <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i>
                Cancelar
            </a>
        </div>

        <form action="{{ url('/dashboard/eventos') }}" method="POST" enctype="multipart/form-data" class="max-w-4xl">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="md:col-span-2 space-y-6">
                    <!-- Información del Evento -->
                    <div class="card p-6 bg-white border border-gray-200">
                        <h2 class="text-lg font-semibold mb-4 text-slate-800 flex items-center gap-2">
                            <div class="w-8 h-8 rounded-lg bg-purple-100 text-purple-600 flex items-center justify-center">
                                <i data-lucide="calendar" class="w-5 h-5"></i>
                            </div>
                            Detalles del Evento
                        </h2>

                        <div class="grid grid-cols-1 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">Título del Evento</label>
                                <input type="text" name="title"
                                    class="form-input w-full rounded-lg border-gray-300 focus:ring-purple-500"
                                    placeholder="Ej. Noche de Jazz en Vivo" required>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">Descripción</label>
                                <textarea name="description" rows="4" class="form-input w-full rounded-lg border-gray-300 focus:ring-purple-500"
                                    placeholder="Detalles del evento..."></textarea>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 mb-1">Fecha de Inicio</label>
                                    <input type="datetime-local" name="start_date"
                                        class="form-input w-full rounded-lg border-gray-300 focus:ring-purple-500" required>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 mb-1">Fecha de Fin</label>
                                    <input type="datetime-local" name="end_date"
                                        class="form-input w-full rounded-lg border-gray-300 focus:ring-purple-500">
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">Lugar / Ubicación</label>
                                <input type="text" name="location"
                                    class="form-input w-full rounded-lg border-gray-300 focus:ring-purple-500"
                                    placeholder="Ej. Plaza de Armas o Dirección del Local">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Imagen y Estado -->
                <div class="space-y-6">
                    <div class="card p-6 bg-white border border-gray-200">
                        <h2 class="text-lg font-semibold mb-4 text-slate-800">Banner del Evento</h2>
                        <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:bg-gray-50 transition-colors cursor-pointer relative"
                            id="dropzone-event">
                            <input type="file" name="image"
                                class="absolute inset-0 w-full h-full opacity-0 cursor-pointer"
                                onchange="previewImage(this)">
                            <div id="image-preview" class="hidden w-full h-40 bg-cover bg-center rounded-lg mb-2"></div>
                            <div id="upload-placeholder">
                                <i data-lucide="image" class="w-10 h-10 text-slate-400 mx-auto mb-2"></i>
                                <p class="text-sm text-slate-500">Subir Banner</p>
                            </div>
                        </div>
                    </div>

                    <div class="card p-6 bg-white border border-gray-200">
                        <button type="submit" class="btn btn-primary w-full justify-center">
                            <i data-lucide="save" class="w-4 h-4 mr-2"></i>
                            Publicar Evento
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
