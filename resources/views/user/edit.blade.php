@extends('layouts.app')

@section('title', 'Editar Perfil')

@section('content')
    <!-- Header Section -->
    <section class="text-white py-12" style="background: linear-gradient(135deg, var(--color-primary) 0%, var(--color-primary-dark) 100%);">
        <div class="container">
            <div class="flex items-center justify-between flex-wrap gap-4">
                <div>
                    <h1 class="text-4xl font-bold mb-2">Configuración de Perfil</h1>
                    <p class="text-white/80">Actualiza tu información personal</p>
                </div>
                <a href="/user" class="px-4 py-2 bg-white/20 hover:bg-white/30 backdrop-blur-md rounded-lg transition-colors border border-white/30 font-medium">
                    <i data-lucide="arrow-left" class="w-4 h-4 inline mr-2"></i>
                    Volver al Perfil
                </a>
            </div>
        </div>
    </section>

    <div class="container py-12">
        <div class="max-w-3xl mx-auto">

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
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Teléfono</label>
                            <input type="tel" name="phone" value="{{ Auth::user()->phone ?? '' }}"
                                class="form-input w-full rounded-lg border-gray-300 focus:ring-blue-500"
                                placeholder="+51 900 000 000">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Biografía</label>
                        <textarea name="bio" rows="3" class="form-input w-full rounded-lg border-gray-300 focus:ring-blue-500"
                            placeholder="Cuéntanos un poco sobre ti...">{{ Auth::user()->bio ?? '' }}</textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-3">Intereses de Viaje</label>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                            @php
                                $preferences = Auth::user()->preferences ?? [];
                                $options = ['Aventura', 'Cultura', 'Gastronomía', 'Naturaleza', 'Fotografía', 'Relax'];
                            @endphp
                            @foreach ($options as $option)
                                <label class="flex items-center space-x-2 cursor-pointer">
                                    <input type="checkbox" name="preferences[]" value="{{ $option }}"
                                        class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                        {{ in_array($option, $preferences) ? 'checked' : '' }}>
                                    <span class="text-sm text-slate-700">{{ $option }}</span>
                                </label>
                            @endforeach
                        </div>
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
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                lucide.createIcons();
            });
        </script>
    @endpush
@endsection
