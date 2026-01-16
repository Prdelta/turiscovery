@extends('layouts.dashboard')

@section('title', 'Editar Perfil')
@section('page-title', 'Configuración de Perfil')
@section('page-subtitle', 'Actualiza tu información personal')

@section('content')
    <div class="max-w-3xl fade-in">

        <div class="card p-8 bg-white border border-gray-200">
            <form action="{{ url('/user/profile') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <!-- Foto de Perfil -->
                <div class="mb-8 text-center">
                    <div class="relative w-24 h-24 mx-auto mb-4 group cursor-pointer">
                        <div
                            class="w-full h-full rounded-full bg-blue-100 flex items-center justify-center text-blue-600 text-4xl font-bold border-4 border-white shadow-lg overflow-hidden">
                            {{ substr(Auth::user()->name ?? 'U', 0, 1) }}
                            <!-- <img src="..." class="w-full h-full object-cover"> -->
                        </div>
                        <div
                            class="absolute inset-0 bg-black/40 rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                            <i data-lucide="camera" class="text-white w-6 h-6"></i>
                        </div>
                    </div>
                    <button type="button" class="text-sm text-blue-600 font-medium hover:underline">Cambiar foto</button>
                </div>

                <div class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Nombre Completo</label>
                            <input type="text" name="name" value="{{ Auth::user()->name ?? '' }}"
                                class="form-input w-full rounded-lg border-gray-300 focus:ring-blue-500" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Correo Electrónico</label>
                            <input type="email" name="email" value="{{ Auth::user()->email ?? '' }}"
                                class="form-input w-full rounded-lg border-gray-300 bg-gray-50 text-gray-500 cursor-not-allowed"
                                readonly>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Biografía (Opcional)</label>
                        <textarea name="bio" rows="3" class="form-input w-full rounded-lg border-gray-300 focus:ring-blue-500"
                            placeholder="Cuéntanos un poco sobre ti..."></textarea>
                    </div>

                    <hr class="border-gray-100">

                    <div>
                        <h3 class="font-semibold text-slate-800 mb-4">Seguridad</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">Nueva Contraseña</label>
                                <input type="password" name="password"
                                    class="form-input w-full rounded-lg border-gray-300 focus:ring-blue-500"
                                    placeholder="Dejar en blanco si no deseas cambiarla">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">Confirmar Contraseña</label>
                                <input type="password" name="password_confirmation"
                                    class="form-input w-full rounded-lg border-gray-300 focus:ring-blue-500"
                                    placeholder="Repite la nueva contraseña">
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end pt-4">
                        <button type="submit" class="btn btn-primary px-6">
                            <i data-lucide="save" class="w-4 h-4 mr-2"></i>
                            Guardar Cambios
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
