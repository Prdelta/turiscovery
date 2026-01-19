<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Crear Cuenta | Turiscovery</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.min.js"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body {
            margin: 0;
            font-family: 'Inter', sans-serif;
            height: 100vh;
            overflow: hidden;
        }

        .auth-container {
            display: flex;
            height: 100%;
            width: 100%;
        }

        .auth-form-side {
            width: 100%;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 2rem;
            background: white;
            position: relative;
            overflow-y: auto;
            /* Allow scroll if form is tall */
        }

        .auth-image-side {
            display: none;
            width: 50%;
            background-size: cover;
            background-position: center;
            position: relative;
        }

        .auth-overlay {
            position: absolute;
            inset: 0;
            background: linear-gradient(to top, rgba(0, 0, 0, 0.8), rgba(0, 0, 0, 0.2));
            display: flex;
            flex-direction: column;
            justify-content: flex-end;
            padding: 4rem;
            color: white;
        }

        .form-input {
            width: 100%;
            padding: 0.75rem 1rem;
            padding-left: 2.5rem;
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

        .input-icon {
            position: absolute;
            left: 0.75rem;
            top: 2.6rem;
            color: #94a3b8;
            width: 1.25rem;
            height: 1.25rem;
        }

        .form-group {
            position: relative;
            margin-bottom: 0.5rem;
        }

        .form-label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            font-size: 0.9rem;
            color: #334155;
        }

        @media (min-width: 1024px) {
            .auth-form-side {
                width: 50%;
                max-width: 600px;
            }

            .auth-image-side {
                display: block;
                flex: 1;
            }
        }
    </style>
</head>

<body>

    <div class="auth-container">
        <!-- Form Side -->
        <div class="auth-form-side animate-fade-in-up">
            <div style="max-width: 400px; width: 100%; margin: 0 auto; padding-top: 2rem; padding-bottom: 2rem;">
                <a href="/" class="flex items-center gap-3 mb-8 group w-fit no-underline">
                    <img src="/img/logo.png" alt="Turiscovery" style="height: 32px; width: auto;"
                        class="group-hover:scale-105 transition-transform">
                    <span class="font-bold text-2xl text-slate-800 tracking-tight">Turiscovery</span>
                </a>

                <h1 class="text-3xl font-bold text-slate-900 mb-2">Crear nueva cuenta</h1>
                <p class="text-slate-500 mb-8">Únete a nuestra comunidad hoy mismo.</p>

                <form action="{{ url('/register') }}" method="POST" id="registerForm">
                    @csrf

                    <div class="form-group">
                        <label class="form-label">Nombre Completo</label>
                        <i data-lucide="user" class="input-icon"></i>
                        <input type="text" name="name" class="form-input" placeholder="Juan Pérez" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Correo Electrónico</label>
                        <i data-lucide="mail" class="input-icon"></i>
                        <input type="email" name="email" class="form-input" placeholder="nombre@ejemplo.com"
                            required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Contraseña</label>
                        <i data-lucide="lock" class="input-icon"></i>
                        <input type="password" name="password" class="form-input" placeholder="Mínimo 8 caracteres"
                            required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Confirmar Contraseña</label>
                        <i data-lucide="check-circle" class="input-icon"></i>
                        <input type="password" name="password_confirmation" class="form-input" placeholder="••••••••"
                            required>
                    </div>

                    <div class="flex items-start gap-2 mb-6 mt-4">
                        <div class="flex items-center h-5">
                            <input type="checkbox" required
                                class="rounded border-gray-300 text-blue-600 focus:ring-blue-500 w-4 h-4">
                        </div>
                        <label class="text-sm text-slate-600">
                            Acepto los <a href="#" class="text-blue-600 hover:underline">Términos de Servicio</a>
                            y la <a href="#" class="text-blue-600 hover:underline">Política de Privacidad</a>.
                        </label>
                    </div>

                    <button type="submit" class="btn btn-primary w-full justify-center mb-4 py-3 text-base">
                        Crear Cuenta
                    </button>

                    <div class="relative py-4 mb-4">
                        <div class="absolute inset-0 flex items-center">
                            <div class="w-full border-t border-gray-200"></div>
                        </div>
                        <div class="relative flex justify-center"><span class="bg-white px-4 text-sm text-gray-400">O
                                regístrate con</span></div>
                    </div>

                    <button type="button"
                        class="btn btn-outline w-full justify-center gap-3 py-2.5 border-gray-300 hover:bg-gray-50 text-slate-700">
                        <img src="https://www.svgrepo.com/show/475656/google-color.svg" alt="Google"
                            style="width: 20px; height: 20px;">
                        <span>Google</span>
                    </button>
                </form>

                <p class="mt-8 text-center text-sm text-slate-600">
                    ¿Ya tienes una cuenta?
                    <a href="/login" class="font-semibold text-blue-600 hover:text-blue-700">Inicia sesión</a>
                </p>
            </div>
        </div>

        <!-- Image Side -->
        <div class="auth-image-side" style="background-image: url('/img/login_bg.png');">
            <div class="auth-overlay">
                <blockquote class="text-2xl font-medium mb-4">
                    "Conectar mi negocio con turistas de todo el mundo nunca había sido tan fácil. Una plataforma
                    indispensable."
                </blockquote>
                <div class="flex items-center gap-4">
                    <img src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&w=100&q=80"
                        class="w-10 h-10 rounded-full border-2 border-white/50">
                    <div>
                        <div class="font-semibold">Carlos R.</div>
                        <div class="text-sm text-white/80">Propietario de Hotel</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        lucide.createIcons();

        document.getElementById('registerForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const btn = e.target.querySelector('button[type="submit"]');
            const originalText = btn.innerHTML;

            btn.disabled = true;
            btn.innerHTML = '<i data-lucide="loader" class="animate-spin w-5 h-5"></i> Creando cuenta...';
            lucide.createIcons();

            try {
                await new Promise(r => setTimeout(r, 1500));
                window.location.href = '/login';
            } catch (error) {
                btn.innerHTML = originalText;
                btn.disabled = false;
            }
        });
    </script>
</body>

</html>
