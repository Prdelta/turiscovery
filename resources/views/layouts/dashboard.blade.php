<!DOCTYPE html>
<html lang="es" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Dashboard') | Turiscovery</title>

    <link rel="icon" type="image/svg+xml"
        href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>🏔️</text></svg>">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.min.js"></script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            font-family: var(--font-primary), sans-serif;
            background-color: var(--color-bg-light);
            color: var(--color-text);
        }

        /* Scrollbar personalizada sutil */
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }

        ::-webkit-scrollbar-track {
            background: transparent;
        }

        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: var(--radius-full);
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }

        /* Estilos del Sidebar Active State */
        .sidebar-link {
            position: relative;
            transition: all 0.2s ease-in-out;
            color: var(--color-text);
        }

        .sidebar-link:hover {
            transform: translateX(4px);
            color: var(--color-primary);
        }

        .sidebar-link.active {
            background-color: rgba(94, 92, 232, 0.1);
            /* primary with opacity */
            color: var(--color-primary);
            font-weight: 600;
        }

        .sidebar-link.active i {
            color: var(--color-primary);
        }

        /* Indicador lateral activo */
        .sidebar-link.active::before {
            content: '';
            position: absolute;
            left: 0;
            top: 50%;
            transform: translateY(-50%);
            height: 20px;
            width: 3px;
            background-color: var(--color-primary);
            border-top-right-radius: 4px;
            border-bottom-right-radius: 4px;
        }
    </style>

    @stack('styles')
</head>

<body class="text-slate-600 bg-slate-50 antialiased selection:bg-blue-500 selection:text-white">

    <div class="flex min-h-screen relative">

        <aside id="sidebar"
            class="fixed inset-y-0 left-0 z-50 w-72 bg-white border-r border-slate-200/60 shadow-[4px_0_24px_-12px_rgba(0,0,0,0.1)] transform -translate-x-full lg:translate-x-0 transition-transform duration-300 ease-in-out flex flex-col">

            <div class="h-20 flex items-center justify-between px-6 border-b border-slate-100/50">
                <a href="/" class="flex items-center gap-3 group">
                    <div
                        class="w-9 h-9 bg-blue-600 rounded-lg flex items-center justify-center shadow-lg shadow-blue-600/20 group-hover:scale-105 transition-transform duration-300">
                        <span class="text-white text-lg">🏔️</span>
                    </div>
                    <div class="flex flex-col">
                        <span
                            class="font-bold text-lg text-slate-800 tracking-tight leading-none group-hover:text-blue-600 transition-colors">Turiscovery</span>
                        <span
                            class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider mt-1">Dashboard</span>
                    </div>
                </a>
                <button id="close-sidebar" class="lg:hidden text-slate-400 hover:text-slate-600">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <div class="flex-1 overflow-y-auto py-6 px-4 space-y-8">

                <div class="bg-slate-50 border border-slate-100 rounded-xl p-3 flex items-center gap-3 mb-6">
                    <div id="user-avatar"
                        class="w-10 h-10 rounded-full bg-white border border-slate-200 flex items-center justify-center text-blue-600 font-bold shadow-sm">
                        U
                    </div>
                    <div class="flex-1 min-w-0">
                        <p id="user-name" class="text-sm font-semibold text-slate-700 truncate">Cargando...</p>
                        <p id="user-role"
                            class="text-xs text-slate-500 truncate bg-slate-200/50 inline-block px-1.5 rounded mt-0.5">
                            ...</p>
                    </div>
                </div>

                <nav class="space-y-8">

                    <div>
                        <p class="px-2 text-xs font-bold text-slate-400 uppercase tracking-wider mb-3">Principal</p>
                        <ul class="space-y-1">
                            <li>
                                <a href="/dashboard"
                                    class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-600 hover:bg-slate-50 hover:text-blue-600 {{ request()->is('dashboard') && !request()->is('dashboard/*') ? 'active' : '' }}">
                                    <i data-lucide="layout-dashboard" class="w-5 h-5 stroke-[1.5]"></i>
                                    <span class="text-sm font-medium">Dashboard General</span>
                                </a>
                            </li>
                        </ul>
                    </div>

                    <div id="content-management">
                        <p class="px-2 text-xs font-bold text-slate-400 uppercase tracking-wider mb-3">Gestión</p>
                        <ul class="space-y-1">
                            <li>
                                <a href="/dashboard/locales"
                                    class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-600 hover:bg-slate-50 hover:text-blue-600 {{ request()->is('dashboard/locales*') ? 'active' : '' }}">
                                    <i data-lucide="store" class="w-5 h-5 stroke-[1.5]"></i>
                                    <span class="text-sm font-medium">Mis Locales</span>
                                </a>
                            </li>
                            <li>
                                <a href="/dashboard/eventos"
                                    class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-600 hover:bg-slate-50 hover:text-blue-600 {{ request()->is('dashboard/eventos*') ? 'active' : '' }}">
                                    <i data-lucide="calendar" class="w-5 h-5 stroke-[1.5]"></i>
                                    <span class="text-sm font-medium">Mis Eventos</span>
                                </a>
                            </li>
                            <li>
                                <a href="/dashboard/promociones"
                                    class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-600 hover:bg-slate-50 hover:text-blue-600 {{ request()->is('dashboard/promociones*') ? 'active' : '' }}">
                                    <i data-lucide="percent" class="w-5 h-5 stroke-[1.5]"></i>
                                    <span class="text-sm font-medium">Mis Promociones</span>
                                </a>
                            </li>
                            <li>
                                <a href="/dashboard/experiencias"
                                    class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-600 hover:bg-slate-50 hover:text-blue-600 {{ request()->is('dashboard/experiencias*') ? 'active' : '' }}">
                                    <i data-lucide="compass" class="w-5 h-5 stroke-[1.5]"></i>
                                    <span class="text-sm font-medium">Experiencias</span>
                                </a>
                            </li>
                        </ul>
                    </div>

                    <div id="user-section">
                        <p class="px-2 text-xs font-bold text-slate-400 uppercase tracking-wider mb-3">Personal</p>
                        <ul class="space-y-1">
                            <li>
                                <a href="/user"
                                    class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-600 hover:bg-slate-50 hover:text-blue-600 {{ request()->is('user') ? 'active' : '' }}">
                                    <i data-lucide="user" class="w-5 h-5 stroke-[1.5]"></i>
                                    <span class="text-sm font-medium">Mi Perfil</span>
                                </a>
                            </li>
                            <li>
                                <a href="/user/favorites"
                                    class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-600 hover:bg-slate-50 hover:text-blue-600 {{ request()->is('user/favorites') ? 'active' : '' }}">
                                    <i data-lucide="heart" class="w-5 h-5 stroke-[1.5]"></i>
                                    <span class="text-sm font-medium">Favoritos</span>
                                </a>
                            </li>
                            <li>
                                <a href="/user/reviews"
                                    class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-600 hover:bg-slate-50 hover:text-blue-600 {{ request()->is('user/reviews') ? 'active' : '' }}">
                                    <i data-lucide="star" class="w-5 h-5 stroke-[1.5]"></i>
                                    <span class="text-sm font-medium">Reseñas</span>
                                </a>
                            </li>
                            <li>
                                <a href="/user/edit"
                                    class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-600 hover:bg-slate-50 hover:text-blue-600 {{ request()->is('user/edit') ? 'active' : '' }}">
                                    <i data-lucide="settings-2" class="w-5 h-5 stroke-[1.5]"></i>
                                    <span class="text-sm font-medium">Configuración</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </nav>
            </div>

            <div class="p-4 border-t border-slate-100">
                <button onclick="handleLogout()"
                    class="group w-full flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg text-slate-500 hover:bg-red-50 hover:text-red-600 transition-all duration-200">
                    <i data-lucide="log-out" class="w-5 h-5 group-hover:-translate-x-1 transition-transform"></i>
                    <span class="text-sm font-medium">Cerrar Sesión</span>
                </button>
            </div>
        </aside>

        <div id="sidebar-overlay"
            class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-40 lg:hidden hidden transition-opacity opacity-0"
            onclick="closeSidebar()"></div>

        <div class="flex-1 lg:ml-72 flex flex-col min-h-screen">

            <header
                class="sticky top-0 z-30 bg-white/80 backdrop-blur-md border-b border-slate-200/60 supports-[backdrop-filter]:bg-white/60">
                <div class="flex items-center justify-between h-16 lg:h-20 px-4 sm:px-6 lg:px-8">

                    <div class="flex items-center gap-4">
                        <button id="open-sidebar"
                            class="lg:hidden p-2 text-slate-500 hover:text-blue-600 transition-colors">
                            <i data-lucide="menu" class="w-6 h-6"></i>
                        </button>

                        <div class="flex flex-col">
                            <h1 class="text-xl font-bold text-slate-800 leading-tight">@yield('page-title', 'Dashboard')</h1>
                            <p class="text-xs text-slate-500 hidden sm:block">@yield('page-subtitle', 'Resumen de actividad')</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-3 sm:gap-4">

                        <div class="hidden md:flex items-center gap-2 mr-2" id="quick-actions">
                            <a href="/dashboard/eventos/create"
                                class="flex items-center gap-2 px-3 py-1.5 text-xs font-medium text-blue-700 bg-blue-50 hover:bg-blue-100 rounded-full transition-colors border border-blue-100">
                                <i data-lucide="plus" class="w-3.5 h-3.5"></i> Evento
                            </a>
                            <a href="/dashboard/promociones/create"
                                class="flex items-center gap-2 px-3 py-1.5 text-xs font-medium text-purple-700 bg-purple-50 hover:bg-purple-100 rounded-full transition-colors border border-purple-100">
                                <i data-lucide="plus" class="w-3.5 h-3.5"></i> Promo
                            </a>
                        </div>

                        <div class="h-8 w-px bg-slate-200 hidden sm:block"></div>

                        <button
                            class="relative p-2 text-slate-400 hover:text-slate-600 hover:bg-slate-100 rounded-full transition-all">
                            <i data-lucide="bell" class="w-5 h-5"></i>
                            <span
                                class="absolute top-2 right-2.5 w-2 h-2 bg-red-500 rounded-full ring-2 ring-white"></span>
                        </button>

                        <a href="/"
                            class="hidden sm:flex items-center gap-2 text-sm font-medium text-slate-500 hover:text-blue-600 transition-colors">
                            <span class="hover:underline decoration-2 underline-offset-4">Ver Sitio</span>
                            <i data-lucide="external-link" class="w-4 h-4"></i>
                        </a>
                    </div>
                </div>
            </header>

            <main class="flex-1 p-4 sm:p-6 lg:p-8 overflow-x-hidden">
                <div class="max-w-7xl mx-auto animate-fade-in-up">
                    @yield('content')
                </div>
            </main>

            <footer class="border-t border-slate-200 bg-white py-6">
                <div class="px-6 text-center">
                    <p class="text-sm text-slate-400">
                        © {{ date('Y') }} Turiscovery. <span class="hidden sm:inline">Todos los derechos
                            reservados.</span>
                    </p>
                </div>
            </footer>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            lucide.createIcons();
            updateUserInfo();
        });

        // Sidebar UI Logic
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebar-overlay');
        const openBtn = document.getElementById('open-sidebar');
        const closeBtn = document.getElementById('close-sidebar');

        function toggleSidebar(show) {
            if (show) {
                sidebar.classList.remove('-translate-x-full');
                overlay.classList.remove('hidden');
                setTimeout(() => overlay.classList.remove('opacity-0'), 10);
                document.body.classList.add('overflow-hidden');
            } else {
                sidebar.classList.add('-translate-x-full');
                overlay.classList.add('opacity-0');
                setTimeout(() => overlay.classList.add('hidden'), 300);
                document.body.classList.remove('overflow-hidden');
            }
        }

        openBtn?.addEventListener('click', () => toggleSidebar(true));
        closeBtn?.addEventListener('click', () => toggleSidebar(false));
        overlay?.addEventListener('click', () => toggleSidebar(false));

        // Auth Logic - Server-Side Session (No API Tokens)
        // Si llegaste aquí, ya estás autenticado (protegido por middleware 'auth')
        function updateUserInfo() {
            @if (auth()->check())
                const user = {
                    name: "{{ auth()->user()->name }}",
                    role: "{{ auth()->user()->role }}",
                    avatar: "{{ auth()->user()->avatar ?? '' }}"
                };

                const userName = document.getElementById('user-name');
                const userRole = document.getElementById('user-role');
                const userAvatar = document.getElementById('user-avatar');

                if (userName) userName.textContent = user.name;
                if (userRole) userRole.textContent = getRoleLabel(user.role);
                if (userAvatar) userAvatar.textContent = user.name.charAt(0).toUpperCase();

                updateMenuVisibility(user.role);
            @endif
        }

        function getRoleLabel(role) {
            const roleLabels = {
                'admin': 'Admin',
                'socio': 'Socio',
                'tourist': 'Turista'
            };
            return roleLabels[role] || role;
        }

        function updateMenuVisibility(role) {
            const contentManagement = document.getElementById('content-management');
            const quickActions = document.getElementById('quick-actions');

            if (role === 'tourist') {
                if (contentManagement) contentManagement.style.display = 'none';
                if (quickActions) quickActions.style.display = 'none';
            }
        }

        // Logout usando formulario web (POST con CSRF)
        function handleLogout() {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '{{ route('logout') }}';

            const csrf = document.createElement('input');
            csrf.type = 'hidden';
            csrf.name = '_token';
            csrf.value = '{{ csrf_token() }}';
            form.appendChild(csrf);

            document.body.appendChild(form);
            form.submit();
        }
    </script>

    @stack('scripts')
</body>

</html>
