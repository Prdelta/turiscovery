@extends('layouts.dashboard')

@section('title', 'Crear Nuevo Socio')

@section('content')
    <div class="fade-in max-w-3xl mx-auto">
        <div class="mb-6">
            <a href="{{ route('admin.socios.index') }}" class="text-primary hover:underline flex items-center mb-4">
                <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i>
                Volver a Socios
            </a>
            <h1 class="text-2xl font-bold text-slate-800">Crear Nueva Cuenta de Socio</h1>
            <p class="text-slate-500">Completa el formulario para registrar un nuevo socio en la plataforma</p>
        </div>

        <div class="card p-6">
            <form action="{{ route('admin.socios.store') }}" method="POST">
                @csrf

                <!-- Nombre -->
                <div class="form-group mb-6">
                    <label for="name" class="form-label">Nombre Completo <span class="text-red-500">*</span></label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}"
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
                        <input type="email" id="email" name="email" value="{{ old('email') }}"
                            class="form-input @error('email') border-red-500 @enderror"
                            placeholder="socio@ejemplo.com" required>
                        @error('email')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                        <p class="text-sm text-slate-500 mt-1">Se usará para iniciar sesión</p>
                    </div>

                    <div class="form-group">
                        <label for="phone" class="form-label">Teléfono</label>
                        <input type="tel" id="phone" name="phone" value="{{ old('phone') }}"
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
                        <label for="password" class="form-label">Contraseña <span class="text-red-500">*</span></label>
                        <input type="password" id="password" name="password"
                            class="form-input @error('password') border-red-500 @enderror"
                            placeholder="Mínimo 8 caracteres" required>
                        @error('password')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                        <p class="text-sm text-slate-500 mt-1">Mínimo 8 caracteres</p>
                    </div>

                    <div class="form-group">
                        <label for="password_confirmation" class="form-label">Confirmar Contraseña <span class="text-red-500">*</span></label>
                        <input type="password" id="password_confirmation" name="password_confirmation"
                            class="form-input @error('password_confirmation') border-red-500 @enderror"
                            placeholder="Repite la contraseña" required>
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
                        placeholder="Breve descripción sobre el socio, su experiencia, tipo de negocio, etc...">{{ old('bio') }}</textarea>
                    @error('bio')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                    <p class="text-sm text-slate-500 mt-1">Máximo 500 caracteres</p>
                </div>

                <!-- Información adicional -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                    <h3 class="font-semibold text-blue-900 mb-2 flex items-center">
                        <i data-lucide="info" class="w-5 h-5 mr-2"></i>
                        Información Importante
                    </h3>
                    <ul class="text-sm text-blue-800 space-y-1 list-disc list-inside">
                        <li>El socio podrá gestionar sus propios locales, eventos, promociones y experiencias</li>
                        <li>Tendrá acceso al panel de dashboard con funciones limitadas</li>
                        <li>No podrá ver ni editar el contenido de otros socios</li>
                        <li>Las credenciales deben compartirse de forma segura con el socio</li>
                    </ul>
                </div>

                <!-- Botones -->
                <div class="flex gap-3 pt-4 border-t">
                    <button type="submit" class="btn btn-primary flex-1">
                        <i data-lucide="user-plus" class="w-4 h-4 mr-2"></i>
                        Crear Cuenta de Socio
                    </button>
                    <a href="{{ route('admin.socios.index') }}" class="btn btn-outline flex-1 text-center">
                        Cancelar
                    </a>
                </div>
            </form>
        </div>

        <!-- Tips -->
        <div class="mt-6 bg-yellow-50 border border-yellow-200 rounded-lg p-4">
            <h3 class="font-semibold text-yellow-900 mb-2 flex items-center">
                <i data-lucide="lightbulb" class="w-5 h-5 mr-2"></i>
                Consejos de Seguridad
            </h3>
            <ul class="text-sm text-yellow-800 space-y-1 list-disc list-inside">
                <li>Usa contraseñas seguras con combinación de letras, números y símbolos</li>
                <li>No compartas las credenciales por correo electrónico no cifrado</li>
                <li>Recomienda al socio cambiar su contraseña después del primer inicio de sesión</li>
                <li>Verifica que el email sea correcto antes de guardar</li>
            </ul>
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
