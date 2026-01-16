<!DOCTYPE html>
<html lang="es" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Turiscovery') | Descubre Puno</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.min.js"></script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            font-family: 'Inter', sans-serif;
        }

        /* Scrollbar personalizada para consistencia con el dashboard */
        ::-webkit-scrollbar {
            width: 8px;
        }

        ::-webkit-scrollbar-track {
            background: transparent;
        }

        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
    </style>
    @stack('styles')
</head>

<body
    class="bg-slate-50 text-slate-600 antialiased flex flex-col min-h-screen selection:bg-blue-500 selection:text-white">

    <nav class="bg-white shadow-sm sticky top-0 z-50 transition-all duration-300">
        <div class="container">
            <div style="display: flex; justify-content: space-between; align-items: center; height: 80px;">
                <a href="/" style="display: flex; align-items: center; gap: 12px; text-decoration: none;">
                    <img src="/img/logo.png" alt="TurisCovery Logo"
                        style="height: 40px; width: auto; object-fit: contain;">
                    <div>
                        <span
                            style="font-family: var(--font-primary); font-weight: 700; font-size: 1.35rem; color: var(--color-primary); letter-spacing: -0.5px;">TurisCovery</span>
                    </div>
                </a>

                <div id="desktop-nav" class="items-center gap-6">
                    <a href="/candelaria" style="font-weight: 600;">Candelaria</a>
                    <a href="/experiencias" style="font-weight: 600;">Experiencias</a>
                    <a href="/eventos" style="font-weight: 600;">Eventos</a>
                    <a href="/promociones" style="font-weight: 600;">Promociones</a>
                    <a href="/locales" style="font-weight: 600;">Locales</a>
                </div>

                <div style="display: flex; items-center; gap: var(--spacing-sm);">
                    <a href="/search" style="color: var(--color-text-light); padding: 8px;">
                        <i data-lucide="search" style="width: 20px; height: 20px;"></i>
                    </a>

                    <div id="auth-buttons" class="hidden md:flex items-center gap-3">
                        <a href="/login" class="btn btn-outline" style="border: none;">Iniciar Sesión</a>
                        <a href="/register" class="btn btn-primary">Registrarse</a>
                    </div>

                    <div id="user-menu" style="display: none;" class="hidden md:flex items-center gap-3">
                        <a href="/dashboard" class="btn btn-secondary"
                            style="padding: 0.5rem 1rem; font-size: 0.875rem;">
                            <i data-lucide="layout-dashboard" style="width: 16px; height: 16px;"></i>
                            Dashboard
                        </a>
                        <button onclick="handleLogout()"
                            style="background: none; border: none; cursor: pointer; color: var(--color-text-muted);">
                            <i data-lucide="log-out" style="width: 20px; height: 20px;"></i>
                        </button>
                    </div>

                </div>
            </div>
        </div>
    </nav>

    <!-- Mobile Menu -->
    <div id="mobile-menu" style="display: none; position: fixed; inset: 0; z-index: 2000;">
        <div style="position: absolute; inset: 0; background: rgba(0,0,0,0.5);" onclick="closeMobileMenu()"></div>
        <div
            style="position: absolute; right: 0; top: 0; bottom: 0; width: 80%; max-width: 300px; background: white; padding: var(--spacing-lg); overflow-y: auto;">
            <div
                style="display: flex; justify-content: space-between; align-items: center; margin-bottom: var(--spacing-xl);">
                <span style="font-weight: 700; font-size: 1.25rem;">Menú</span>
                <button onclick="closeMobileMenu()" style="background: none; border: none; cursor: pointer;">
                    <i data-lucide="x" style="width: 24px; height: 24px;"></i>
                </button>
            </div>

            <nav style="display: flex; flex-direction: column; gap: var(--spacing-md);">
                <a href="/candelaria" style="font-weight: 600; font-size: 1.1rem;">Candelaria</a>
                <a href="/experiencias" style="font-weight: 600; font-size: 1.1rem;">Experiencias</a>
                <a href="/eventos" style="font-weight: 600; font-size: 1.1rem;">Eventos</a>
                <a href="/promociones" style="font-weight: 600; font-size: 1.1rem;">Promociones</a>
                <a href="/locales" style="font-weight: 600; font-size: 1.1rem;">Locales</a>
            </nav>

            <div
                style="margin-top: var(--spacing-xl); padding-top: var(--spacing-md); border-top: 1px solid var(--color-gray-light);">
                <div id="mobile-auth-buttons" style="display: flex; flex-direction: column; gap: var(--spacing-sm);">
                    <a href="/login" class="btn btn-outline" style="width: 100%; justify-content: center;">Iniciar
                        Sesión</a>
                    <a href="/register" class="btn btn-primary"
                        style="width: 100%; justify-content: center;">Registrarse</a>
                </div>

                <div id="mobile-user-menu" style="display: none;">
                    <a href="/dashboard" class="btn btn-secondary"
                        style="width: 100%; justify-content: center; margin-bottom: var(--spacing-sm);">Dashboard</a>
                    <button onclick="handleLogout()" class="btn btn-outline"
                        style="width: 100%; justify-content: center; color: var(--color-danger); border-color: var(--color-danger);">Cerrar
                        Sesión</button>
                </div>
            </div>
        </div>
    </div>

    <main class="flex-grow">
        @yield('content')
    </main>

    <footer style="background: #1a1a1a; color: white; padding: var(--spacing-2xl) 0;">
        <div class="container">
            <div class="grid grid-4">
                <div>
                    <div style="display: flex; align-items: center; gap: 10px; margin-bottom: var(--spacing-md);">
                        <div
                            style="width: 32px; height: 32px; background: var(--color-primary); border-radius: var(--radius-sm); display: flex; align-items: center; justify-content: center;">
                            <span>🏔️</span>
                        </div>
                        <span style="font-weight: 700; font-size: 1.25rem;">Turiscovery</span>
                    </div>
                    <p style="color: #999; font-size: 0.9rem;">
                        Tu puerta digital a las maravillas de Puno.
                    </p>
                </div>

                <div>
                    <h4 style="color: white; margin-bottom: var(--spacing-md);">Explorar</h4>
                    <ul style="list-style: none; padding: 0;">
                        <li style="margin-bottom: 0.5rem;"><a href="/candelaria" style="color: #999;">Candelaria</a>
                        </li>
                        <li style="margin-bottom: 0.5rem;"><a href="/experiencias"
                                style="color: #999;">Experiencias</a></li>
                        <li style="margin-bottom: 0.5rem;"><a href="/eventos" style="color: #999;">Eventos</a></li>
                    </ul>
                </div>

                <div>
                    <h4 style="color: white; margin-bottom: var(--spacing-md);">Negocios</h4>
                    <ul style="list-style: none; padding: 0;">
                        <li style="margin-bottom: 0.5rem;"><a href="/register" style="color: #999;">Registrar
                                negocio</a></li>
                        <li style="margin-bottom: 0.5rem;"><a href="/dashboard" style="color: #999;">Panel de
                                control</a></li>
                    </ul>
                </div>

                <div>
                    <h4 style="color: white; margin-bottom: var(--spacing-md);">Contacto</h4>
                    <ul style="list-style: none; padding: 0;">
                        <li style="margin-bottom: 0.5rem; display: flex; gap: 10px; color: #999;">
                            <i data-lucide="map-pin" style="width: 16px;"></i> Puno, Perú
                        </li>
                        <li style="margin-bottom: 0.5rem; display: flex; gap: 10px; color: #999;">
                            <i data-lucide="mail" style="width: 16px;"></i> info@turiscovery.pe
                        </li>
                    </ul>
                </div>
            </div>

            <div
                style="margin-top: var(--spacing-xl); padding-top: var(--spacing-lg); border-top: 1px solid #333; text-align: center; color: #666; font-size: 0.875rem;">
                &copy; {{ date('Y') }} Turiscovery. Todos los derechos reservados.
            </div>
        </div>
    </footer>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            lucide.createIcons();
            checkAuthState();
        });

        // Mobile Menu Logic with Animations
        const mobileMenu = document.getElementById('mobile-menu');
        const mobileBackdrop = document.getElementById('mobile-backdrop');
        const mobilePanel = document.getElementById('mobile-panel');
        const mobileMenuBtn = document.getElementById('mobile-menu-btn');

        function openMobileMenu() {
            mobileMenu.classList.remove('hidden');
            // Small delay to allow display:block to apply before opacity transition
            setTimeout(() => {
                mobileBackdrop.classList.remove('opacity-0');
                mobilePanel.classList.remove('translate-x-full');
            }, 10);
            document.body.style.overflow = 'hidden';
        }

        function closeMobileMenu() {
            mobileBackdrop.classList.add('opacity-0');
            mobilePanel.classList.add('translate-x-full');

            // Wait for transition to finish before hiding
            setTimeout(() => {
                mobileMenu.classList.add('hidden');
                document.body.style.overflow = '';
            }, 300);
        }

        mobileMenuBtn?.addEventListener('click', openMobileMenu);

        // Auth Logic
        function checkAuthState() {
            const token = localStorage.getItem('auth_token');
            const authButtons = document.getElementById('auth-buttons');
            const userMenu = document.getElementById('user-menu');
            const mobileAuthButtons = document.getElementById('mobile-auth-buttons');
            const mobileUserMenu = document.getElementById('mobile-user-menu');

            if (token) {
                // Desktop
                if (authButtons) {
                    authButtons.classList.remove('flex');
                    authButtons.classList.add('hidden');
                }
                if (userMenu) {
                    userMenu.classList.remove('hidden');
                    userMenu.classList.add('flex');
                }

                // Mobile
                if (mobileAuthButtons) mobileAuthButtons.classList.add('hidden');
                if (mobileUserMenu) mobileUserMenu.classList.remove('hidden');

            } else {
                // Desktop
                if (authButtons) {
                    authButtons.classList.remove('hidden');
                    authButtons.classList.add('flex');
                }
                if (userMenu) {
                    userMenu.classList.remove('flex');
                    userMenu.classList.add('hidden');
                }

                // Mobile
                if (mobileAuthButtons) mobileAuthButtons.classList.remove('hidden');
                if (mobileUserMenu) mobileUserMenu.classList.add('hidden');
            }
        }

        async function handleLogout() {
            try {
                const token = localStorage.getItem('auth_token');
                if (token) {
                    // Si usas axios
                    // await axios.post('/api/logout', {}, { headers: { Authorization: `Bearer ${token}` } });
                    // Si usas fetch nativo:
                    await fetch('/api/logout', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Authorization': `Bearer ${token}`
                        }
                    });
                }
            } catch (e) {
                console.error('Logout error:', e);
            }
            localStorage.removeItem('auth_token');
            window.location.href = '/';
        }
    </script>

    @stack('scripts')
</body>

</html>
