@extends('layouts.dashboard')

@section('title', 'Editar Socio')

@section('content')
    <div class="fade-in max-w-3xl mx-auto">
        <div class="mb-6">
            <a href="{{ route('admin.socios.index') }}" class="text-primary hover:underline flex items-center mb-4">
                <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i>
                Volver a Socios
            </a>
            <h1 class="text-2xl font-bold text-slate-800">Editar Socio: {{ $socio->name }}</h1>
            <p class="text-slate-500">Actualiza la información del socio</p>
        </div>

        <div class="card p-6">
            <form action="{{ route('admin.socios.update', $socio) }}" method="POST">
                @csrf
                @method('PUT')

                <!-- Nombre -->
                <div class="form-group mb-6">
                    <label for="name" class="form-label">Nombre Completo <span class="text-red-500">*</span></label>
                    <input type="text" id="name" name="name" value="{{ old('name', $socio->name) }}"
                        class="form-input @error('name') border-red-500 @enderror"
                        placeholder="Ej: Juan Pérez García" required autofocus>
                    @error('name')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email y Teléfono -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div class="form-group">
                        <label for="email" class="form-label">Email <span class="text-red-500">*</span></label>
                        <input type="email" id="email" name="email" value="{{ old('email', $socio->email) }}"
                            class="form-input @error('email') border-red-500 @enderror"
                            placeholder="socio@ejemplo.com" required>
                        @error('email')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                        <p class="text-sm text-slate-500 mt-1">Se usará para iniciar sesión</p>
                    </div>

                    <div class="form-group">
                        <label for="phone" class="form-label">Teléfono</label>
                        <input type="tel" id="phone" name="phone" value="{{ old('phone', $socio->phone) }}"
                            class="form-input @error('phone') border-red-500 @enderror"
                            placeholder="+51 999 999 999">
                        @error('phone')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Contraseña -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div class="form-group">
                        <label for="password" class="form-label">Nueva Contraseña</label>
                        <input type="password" id="password" name="password"
                            class="form-input @error('password') border-red-500 @enderror"
                            placeholder="Dejar en blanco para mantener la actual">
                        @error('password')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                        <p class="text-sm text-slate-500 mt-1">Solo si deseas cambiarla</p>
                    </div>

                    <div class="form-group">
                        <label for="password_confirmation" class="form-label">Confirmar Nueva Contraseña</label>
                        <input type="password" id="password_confirmation" name="password_confirmation"
                            class="form-input @error('password_confirmation') border-red-500 @enderror"
                            placeholder="Repite la nueva contraseña">
                        @error('password_confirmation')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Bio -->
                <div class="form-group mb-6">
                    <label for="bio" class="form-label">Biografía / Descripción</label>
                    <textarea id="bio" name="bio" rows="4"
                        class="form-input @error('bio') border-red-500 @enderror"
                        placeholder="Breve descripción sobre el socio, su experiencia, tipo de negocio, etc...">{{ old('bio', $socio->bio) }}</textarea>
                    @error('bio')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                    <p class="text-sm text-slate-500 mt-1">Máximo 500 caracteres</p>
                </div>

                <!-- Metadata -->
                <div class="bg-slate-50 border border-slate-200 rounded-lg p-4 mb-6">
                    <h3 class="font-semibold text-slate-800 mb-2 flex items-center">
                        <i data-lucide="info" class="w-4 h-4 mr-2"></i>
                        Información de la Cuenta
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-slate-600">
                        <div>
                            <span class="font-medium">Rol:</span> Socio
                        </div>
                        <div>
                            <span class="font-medium">Locales:</span> {{ $socio->locales()->count() }}
                        </div>
                        <div>
                            <span class="font-medium">Fecha de registro:</span> {{ $socio->created_at->format('d/m/Y') }}
                        </div>
                        <div>
                            <span class="font-medium">Última actualización:</span> {{ $socio->updated_at->format('d/m/Y H:i') }}
                        </div>
                    </div>
                </div>

                <!-- Botones -->
                <div class="flex gap-3 pt-4 border-t">
                    <button type="submit" class="btn btn-primary flex-1">
                        <i data-lucide="save" class="w-4 h-4 mr-2"></i>
                        Actualizar Socio
                    </button>
                    <a href="{{ route('admin.socios.index') }}" class="btn btn-outline flex-1 text-center">
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
