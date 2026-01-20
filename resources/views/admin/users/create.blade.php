<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Crear Usuario | Turiscovery Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.min.js"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body {
            margin: 0;
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            background: #f8fafc;
        }

        .admin-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 2rem 1rem;
        }

        .card {
            background: white;
            border-radius: 1rem;
            padding: 2rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .form-input {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 1px solid #e2e8f0;
            border-radius: 0.5rem;
            margin-bottom: 1rem;
            font-size: 0.95rem;
            transition: all 0.2s;
        }

        .form-input:focus {
            outline: none;
            border-color: var(--color-primary);
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
        }

        .form-input.error {
            border-color: #ef4444;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            font-size: 0.9rem;
            color: #334155;
        }

        .form-label.required::after {
            content: '*';
            color: #ef4444;
            margin-left: 0.25rem;
        }

        .error-message {
            color: #ef4444;
            font-size: 0.875rem;
            margin-top: 0.25rem;
        }

        .success-message {
            background: #dcfce7;
            border: 1px solid #86efac;
            color: #166534;
            padding: 1rem;
            border-radius: 0.5rem;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .header-nav {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 2rem;
        }

        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            color: #64748b;
            text-decoration: none;
            transition: color 0.2s;
        }

        .back-link:hover {
            color: #334155;
        }

        select.form-input {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3e%3c/svg%3e");
            background-position: right 0.5rem center;
            background-repeat: no-repeat;
            background-size: 1.5em 1.5em;
            padding-right: 2.5rem;
            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;
        }
    </style>
</head>

<body>
    <div class="admin-container">
        <div class="header-nav">
            <a href="/dashboard" class="back-link">
                <i data-lucide="arrow-left" class="w-5 h-5"></i>
                Volver al Dashboard
            </a>
            <div class="text-sm text-slate-500">
                Sesión: <strong>{{ auth()->user()->name }}</strong> ({{ ucfirst(auth()->user()->role) }})
            </div>
        </div>

        <div class="card">
            <h1 class="text-3xl font-bold text-slate-900 mb-2">Crear Nuevo Usuario</h1>
            <p class="text-slate-500 mb-6">Crea perfiles para Socios o Administradores del sistema.</p>

            @if (session('success'))
                <div class="success-message">
                    <i data-lucide="check-circle" class="w-5 h-5"></i>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            <form action="{{ route('admin.users.store') }}" method="POST">
                @csrf

                <div class="form-group">
                    <label class="form-label required">Nombre Completo</label>
                    <input type="text" name="name" class="form-input @error('name') error @enderror"
                        value="{{ old('name') }}" placeholder="Juan Pérez" required>
                    @error('name')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label required">Correo Electrónico</label>
                    <input type="email" name="email" class="form-input @error('email') error @enderror"
                        value="{{ old('email') }}" placeholder="usuario@ejemplo.com" required>
                    @error('email')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label required">Contraseña</label>
                    <input type="password" name="password" class="form-input @error('password') error @enderror"
                        placeholder="Mínimo 8 caracteres, mayúsculas, números y símbolos" required>
                    @error('password')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                    <p class="text-xs text-slate-500 mt-1">Debe contener al menos 8 caracteres, mayúsculas, minúsculas,
                        números y símbolos.</p>
                </div>

                <div class="form-group">
                    <label class="form-label required">Confirmar Contraseña</label>
                    <input type="password" name="password_confirmation" class="form-input"
                        placeholder="Confirma la contraseña" required>
                </div>

                <div class="form-group">
                    <label class="form-label required">Rol</label>
                    <select name="role" class="form-input @error('role') error @enderror" required>
                        <option value="">Selecciona un rol</option>
                        <option value="socio" {{ old('role') == 'socio' ? 'selected' : '' }}>Socio</option>
                        <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Administrador</option>
                    </select>
                    @error('role')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                    <p class="text-xs text-slate-500 mt-1">
                        <strong>Socio:</strong> Puede gestionar sus propios negocios y contenido.<br>
                        <strong>Administrador:</strong> Tiene acceso completo al sistema.
                    </p>
                </div>

                <div class="flex gap-3">
                    <button type="submit" class="btn btn-primary flex-1 justify-center py-3">
                        <i data-lucide="user-plus" class="w-5 h-5"></i>
                        Crear Usuario
                    </button>
                    <a href="/dashboard" class="btn btn-outline flex-1 justify-center py-3 no-underline">
                        <i data-lucide="x" class="w-5 h-5"></i>
                        Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>

    <script>
        lucide.createIcons();
    </script>
</body>

</html>
