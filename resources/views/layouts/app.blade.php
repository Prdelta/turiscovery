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

    <nav
        class="bg-white/80 backdrop-blur-md shadow-sm sticky top-0 z-50 transition-all duration-300 border-b border-slate-100">
        <div class="container">
            <div class="flex justify-between items-center h-20">
                <!-- Logo -->
                <a href="/" class="flex items-center gap-3 no-underline group">
                    <img src="/img/logo.png" alt="TurisCovery Logo"
                        class="h-10 w-10 max-h-10 max-w-10 object-contain group-hover:scale-105 transition-transform duration-300">
                    <div>
                        <span
                            class="font-bold text-2xl text-blue-600 tracking-tight group-hover:text-blue-700 transition-colors">TurisCovery</span>
                    </div>
                </a>

                <!-- Desktop Navigation -->
                <div id="desktop-nav" class="hidden lg:flex items-center gap-8">
                    <a href="/candelaria"
                        class="font-semibold text-slate-600 hover:text-blue-600 transition-colors duration-200 relative group">
                        Candelaria
                        <span
                            class="absolute inset-x-0 -bottom-1 h-0.5 bg-blue-600 scale-x-0 group-hover:scale-x-100 transition-transform duration-200"></span>
                    </a>
                    <a href="/experiencias"
                        class="font-semibold text-slate-600 hover:text-blue-600 transition-colors duration-200 relative group">
                        Experiencias
                        <span
                            class="absolute inset-x-0 -bottom-1 h-0.5 bg-blue-600 scale-x-0 group-hover:scale-x-100 transition-transform duration-200"></span>
                    </a>
                    <a href="/eventos"
                        class="font-semibold text-slate-600 hover:text-blue-600 transition-colors duration-200 relative group">
                        Eventos
                        <span
                            class="absolute inset-x-0 -bottom-1 h-0.5 bg-blue-600 scale-x-0 group-hover:scale-x-100 transition-transform duration-200"></span>
                    </a>
                    <a href="/promociones"
                        class="font-semibold text-slate-600 hover:text-blue-600 transition-colors duration-200 relative group">
                        Promociones
                        <span
                            class="absolute inset-x-0 -bottom-1 h-0.5 bg-blue-600 scale-x-0 group-hover:scale-x-100 transition-transform duration-200"></span>
                    </a>
                    <a href="/locales"
                        class="font-semibold text-slate-600 hover:text-blue-600 transition-colors duration-200 relative group">
                        Locales
                        <span
                            class="absolute inset-x-0 -bottom-1 h-0.5 bg-blue-600 scale-x-0 group-hover:scale-x-100 transition-transform duration-200"></span>
                    </a>
                </div>

                <!-- Right Side Actions -->
                <div class="flex items-center gap-3">
                    <!-- Search Button -->
                    <a href="/search"
                        class="p-2 text-slate-500 hover:text-blue-600 hover:bg-blue-50 rounded-full transition-all duration-200">
                        <i data-lucide="search" class="w-5 h-5"></i>
                    </a>

                    <!-- Auth Buttons (Desktop) -->
                    <div id="auth-buttons" class="hidden lg:flex items-center gap-3">
                        <a href="/login"
                            class="px-4 py-2 font-semibold text-slate-600 hover:text-blue-600 transition-colors duration-200">
                            Iniciar Sesión
                        </a>
                        <a href="/register"
                            class="px-5 py-2 font-semibold text-white bg-blue-600 hover:bg-blue-700 rounded-lg shadow-md hover:shadow-lg transition-all duration-200">
                            Registrarse
                        </a>
                    </div>

                    <!-- User Menu (Desktop) -->
                    <div id="user-menu" class="hidden lg:flex items-center gap-3">
                        <a href="/dashboard"
                            class="flex items-center gap-2 px-4 py-2 text-sm font-medium text-blue-700 bg-blue-50 hover:bg-blue-100 rounded-lg transition-colors duration-200">
                            <i data-lucide="layout-dashboard" class="w-4 h-4"></i>
                            Dashboard
                        </a>
                        <button onclick="handleLogout()"
                            class="p-2 text-slate-500 hover:text-red-600 hover:bg-red-50 rounded-full transition-all duration-200">
                            <i data-lucide="log-out" class="w-5 h-5"></i>
                        </button>
                    </div>

                    <!-- Mobile Menu Button -->
                    <button id="mobile-menu-btn"
                        class="lg:hidden p-2 text-slate-600 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-all duration-200">
                        <i data-lucide="menu" class="w-6 h-6"></i>
                    </button>
                </div>
            </div>
        </div>
    </nav>

    <!-- Mobile Menu -->
    <div id="mobile-menu" class="hidden fixed inset-0 z-[60]" style="display: none;">
        <!-- Backdrop -->
        <div class="absolute inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity" onclick="closeMobileMenu()">
        </div>

        <!-- Panel -->
        <div
            class="absolute right-0 top-0 bottom-0 w-[85%] max-w-sm bg-white shadow-2xl overflow-y-auto transform transition-transform duration-300">
            <!-- Header -->
            <div class="flex justify-between items-center p-6 border-b border-slate-100">
                <span class="font-bold text-xl text-slate-800">Menú</span>
                <button onclick="closeMobileMenu()"
                    class="p-2 text-slate-400 hover:text-slate-600 hover:bg-slate-100 rounded-lg transition-colors">
                    <i data-lucide="x" class="w-6 h-6"></i>
                </button>
            </div>

            <!-- Navigation Links -->
            <nav class="p-6 space-y-2">
                <a href="/candelaria"
                    class="flex items-center justify-between px-4 py-3 font-semibold text-slate-700 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-all duration-200 group">
                    <span>Candelaria</span>
                    <i data-lucide="chevron-right"
                        class="w-5 h-5 text-slate-400 group-hover:translate-x-1 transition-transform"></i>
                </a>
                <a href="/experiencias"
                    class="flex items-center justify-between px-4 py-3 font-semibold text-slate-700 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-all duration-200 group">
                    <span>Experiencias</span>
                    <i data-lucide="chevron-right"
                        class="w-5 h-5 text-slate-400 group-hover:translate-x-1 transition-transform"></i>
                </a>
                <a href="/eventos"
                    class="flex items-center justify-between px-4 py-3 font-semibold text-slate-700 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-all duration-200 group">
                    <span>Eventos</span>
                    <i data-lucide="chevron-right"
                        class="w-5 h-5 text-slate-400 group-hover:translate-x-1 transition-transform"></i>
                </a>
                <a href="/promociones"
                    class="flex items-center justify-between px-4 py-3 font-semibold text-slate-700 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-all duration-200 group">
                    <span>Promociones</span>
                    <i data-lucide="chevron-right"
                        class="w-5 h-5 text-slate-400 group-hover:translate-x-1 transition-transform"></i>
                </a>
                <a href="/locales"
                    class="flex items-center justify-between px-4 py-3 font-semibold text-slate-700 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-all duration-200 group">
                    <span>Locales</span>
                    <i data-lucide="chevron-right"
                        class="w-5 h-5 text-slate-400 group-hover:translate-x-1 transition-transform"></i>
                </a>
            </nav>

            <!-- Auth Section -->
            <div class="p-6 pt-0 mt-4 border-t border-slate-100">
                <!-- Auth Buttons -->
                <div id="mobile-auth-buttons" class="flex flex-col gap-3">
                    <a href="/login"
                        class="w-full px-5 py-3 font-semibold text-center text-slate-600 border border-slate-300 hover:border-blue-600 hover:text-blue-600 rounded-lg transition-all duration-200">
                        Iniciar Sesión
                    </a>
                    <a href="/register"
                        class="w-full px-5 py-3 font-semibold text-center text-white bg-blue-600 hover:bg-blue-700 rounded-lg shadow-md hover:shadow-lg transition-all duration-200">
                        Registrarse
                    </a>
                </div>

                <!-- User Menu -->
                <div id="mobile-user-menu" class="hidden flex-col gap-3">
                    <a href="/dashboard"
                        class="w-full px-5 py-3 font-semibold text-center text-blue-700 bg-blue-50 hover:bg-blue-100 rounded-lg transition-colors duration-200">
                        Dashboard
                    </a>
                    <button onclick="handleLogout()"
                        class="w-full px-5 py-3 font-semibold text-red-600 border border-red-300 hover:bg-red-50 rounded-lg transition-all duration-200">
                        Cerrar Sesión
                    </button>
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

        // Mobile Menu Logic
        const mobileMenu = document.getElementById('mobile-menu');
        const mobileMenuBtn = document.getElementById('mobile-menu-btn');

        function openMobileMenu() {
            if (mobileMenu) {
                mobileMenu.classList.remove('hidden');
                mobileMenu.style.display = 'block';
                document.body.style.overflow = 'hidden';
                // Re-initialize icons for mobile menu
                setTimeout(() => lucide.createIcons(), 10);
            }
        }

        function closeMobileMenu() {
            if (mobileMenu) {
                mobileMenu.classList.add('hidden');
                mobileMenu.style.display = 'none';
                document.body.style.overflow = '';
            }
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
                if (mobileUserMenu) {
                    mobileUserMenu.classList.remove('hidden');
                    mobileUserMenu.classList.add('flex');
                }

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
                if (mobileAuthButtons) {
                    mobileAuthButtons.classList.remove('hidden');
                    mobileAuthButtons.classList.add('flex');
                }
                if (mobileUserMenu) {
                    mobileUserMenu.classList.add('hidden');
                    mobileUserMenu.classList.remove('flex');
                }
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
