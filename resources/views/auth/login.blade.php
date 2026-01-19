<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Iniciar Sesión | Turiscovery</title>
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
        }

        .auth-image-side {
            display: none;
            width: 50%;
            /* Adjusted for desktop in media query */
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
            /* Adjusted based on label height */
            color: #94a3b8;
            width: 1.25rem;
            height: 1.25rem;
        }

        .form-group {
            position: relative;
            margin-bottom: 1rem;
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
            <div style="max-width: 400px; width: 100%; margin: 0 auto;">
                <a href="/" class="flex items-center gap-3 mb-8 group w-fit no-underline">
                    <img src="/img/logo.png" alt="Turiscovery" style="height: 32px; width: auto;"
                        class="group-hover:scale-105 transition-transform">
                    <span class="font-bold text-2xl text-slate-800 tracking-tight">Turiscovery</span>
                </a>

                <h1 class="text-3xl font-bold text-slate-900 mb-2">Bienvenido de nuevo</h1>
                <p class="text-slate-500 mb-8">Ingresa tus credenciales para acceder a tu cuenta.</p>

                <form action="{{ url('/login') }}" method="POST" id="loginForm">
                    @csrf

                    <div class="form-group">
                        <label class="form-label">Correo Electrónico</label>
                        <i data-lucide="mail" class="input-icon"></i>
                        <input type="email" name="email" class="form-input" placeholder="nombre@ejemplo.com"
                            required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Contraseña</label>
                        <i data-lucide="lock" class="input-icon"></i>
                        <input type="password" name="password" class="form-input" placeholder="••••••••" required>
                    </div>

                    <div class="flex items-center justify-between mb-6">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            <span class="text-sm text-slate-600">Recordarme</span>
                        </label>
                        <a href="#" class="text-sm font-medium text-blue-600 hover:text-blue-700">¿Olvidaste tu
                            contraseña?</a>
                    </div>

                    <button type="submit" class="btn btn-primary w-full justify-center mb-4 py-3 text-base">
                        Iniciar Sesión
                    </button>

                    <div class="relative py-4 mb-4">
                        <div class="absolute inset-0 flex items-center">
                            <div class="w-full border-t border-gray-200"></div>
                        </div>
                        <div class="relative flex justify-center"><span class="bg-white px-4 text-sm text-gray-400">O
                                continúa con</span></div>
                    </div>

                    <button type="button"
                        class="btn btn-outline w-full justify-center gap-3 py-2.5 border-gray-300 hover:bg-gray-50 text-slate-700">
                        <img src="https://www.svgrepo.com/show/475656/google-color.svg" alt="Google"
                            style="width: 20px; height: 20px;">
                        <span>Google</span>
                    </button>
                </form>

                <p class="mt-8 text-center text-sm text-slate-600">
                    ¿No tienes una cuenta?
                    <a href="/register" class="font-semibold text-blue-600 hover:text-blue-700">Regístrate gratis</a>
                </p>
            </div>
        </div>

        <!-- Image Side -->
        <div class="auth-image-side" style="background-image: url('/img/login_bg.png');">
            <div class="auth-overlay">
                <blockquote class="text-2xl font-medium mb-4">
                    "Descubrir Puno fue una experiencia mágica. La calidez de su gente y la riqueza de su cultura
                    superaron todas mis expectativas."
                </blockquote>
                <div class="flex items-center gap-4">
                    <img src="https://images.unsplash.com/photo-1494790108377-be9c29b29330?auto=format&fit=crop&w=100&q=80"
                        class="w-10 h-10 rounded-full border-2 border-white/50">
                    <div>
                        <div class="font-semibold">Sofia M.</div>
                        <div class="text-sm text-white/80">Viajera frecuente</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        lucide.createIcons();

        // Real API Login Logic
        document.getElementById('loginForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const btn = e.target.querySelector('button[type="submit"]');
            const email = e.target.querySelector('input[name="email"]').value;
            const password = e.target.querySelector('input[name="password"]').value;
            const originalText = btn.innerHTML;

            btn.disabled = true;
            btn.innerHTML = '<i data-lucide="loader" class="animate-spin w-5 h-5"></i> Procesando...';
            lucide.createIcons();

            try {
                // Call the API
                const response = await axios.post('/api/login', {
                    email: email,
                    password: password
                });

                if (response.data.success) {
                    // Store token and user info
                    const token = response.data.data.access_token;
                    const user = response.data.data.user;

                    localStorage.setItem('auth_token', token);
                    localStorage.setItem('user_role', user.role);

                    // Set default headers for future requests
                    axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;

                    // Redirect based on role
                    // Check for redirect param
                    const urlParams = new URLSearchParams(window.location.search);
                    const redirectUrl = urlParams.get('redirect');

                    if (redirectUrl) {
                        window.location.href = decodeURIComponent(redirectUrl);
                    } else {
                        // Redirect based on role
                        if (user.role === 'admin' || user.role === 'socio') {
                            window.location.href = '/dashboard';
                        } else {
                            // Tourist
                            window.location.href = '/';
                        }
                    }
                } else {
                    throw new Error(response.data.message || 'Error al iniciar sesión');
                }

            } catch (error) {
                console.error(error);
                btn.innerHTML = originalText;
                btn.disabled = false;

                let msg = 'Error al iniciar sesión. Verifique sus credenciales.';
                if (error.response && error.response.data && error.response.data.message) {
                    msg = error.response.data.message;
                }

                alert(msg);
            }
        });
    </script>
</body>

</html>
