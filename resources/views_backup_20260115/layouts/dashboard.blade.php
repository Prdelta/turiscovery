<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard - Turiscovery')</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>

    @stack('styles')
</head>


<div class="min-h-screen bg-gray-50 flex" x-data="{ sidebarOpen: false }">
    <!-- Overlay -->
    <div x-show="sidebarOpen" @click="sidebarOpen = false"
        x-transition:enter="transition-opacity ease-linear duration-300" x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100" x-transition:leave="transition-opacity ease-linear duration-300"
        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
        class="fixed inset-0 bg-gray-900/80 z-40 lg:hidden" style="display: none;"></div>

    <!-- Sidebar -->
    <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
        class="fixed inset-y-0 left-0 z-50 w-64 bg-white border-r border-gray-200 transition-transform duration-300 lg:static lg:translate-x-0">
        <!-- Logo -->
        <div class="flex items-center justify-center h-16 border-b border-gray-100">
            <a href="/"
                class="flex items-center gap-2 text-2xl font-bold text-gray-800 hover:text-primary transition-colors">
                <span>🏔️</span>
                <span
                    class="bg-gradient-to-r from-primary to-primary-light bg-clip-text text-transparent">Turiscovery</span>
            </a>
        </div>

        <!-- User Profile Summary -->
        {{-- <div class="p-4 border-b border-gray-100 bg-gray-50/50">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-primary/10 flex items-center justify-center text-primary font-bold">
                        {{ substr(auth()->user()->name ?? 'U', 0, 1) }}
                    </div>
                    <div class="overflow-hidden">
                        <p class="font-medium text-gray-900 truncate">{{ auth()->user()->name ?? 'Usuario' }}</p>
                        <p class="text-xs text-gray-500 truncate">Panel de Socio</p>
                    </div>
                </div>
            </div> --}}

        <!-- Navigation -->
        <!-- Navigation -->
        <nav class="p-4 space-y-1 overflow-y-auto h-[calc(100vh-4rem)] scrollbar-thin scrollbar-thumb-slate-700">
            <div class="text-xs font-bold text-slate-500 uppercase tracking-widest mb-4 mt-2 pl-3">Principal</div>

            <a href="/dashboard"
                class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all duration-200 group {{ request()->is('dashboard') ? 'bg-primary text-white shadow-lg shadow-primary/30' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                <i
                    class="fa-solid fa-chart-pie w-5 text-center {{ request()->is('dashboard') ? 'text-white' : 'text-slate-500 group-hover:text-slate-300' }}"></i>
                <span class="font-medium">Dashboard</span>
            </a>

            <div class="text-xs font-bold text-slate-500 uppercase tracking-widest mb-4 mt-8 pl-3">Gestión</div>

            <a href="/dashboard/locales"
                class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all duration-200 group {{ request()->is('dashboard/locales*') ? 'bg-primary text-white shadow-lg shadow-primary/30' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                <i
                    class="fa-solid fa-store w-5 text-center {{ request()->is('dashboard/locales*') ? 'text-white' : 'text-slate-500 group-hover:text-slate-300' }}"></i>
                <span class="font-medium">Mis Locales</span>
            </a>

            <a href="/dashboard/eventos"
                class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all duration-200 group {{ request()->is('dashboard/eventos*') ? 'bg-primary text-white shadow-lg shadow-primary/30' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                <i
                    class="fa-solid fa-calendar-days w-5 text-center {{ request()->is('dashboard/eventos*') ? 'text-white' : 'text-slate-500 group-hover:text-slate-300' }}"></i>
                <span class="font-medium">Mis Eventos</span>
            </a>

            <a href="/dashboard/promociones"
                class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all duration-200 group {{ request()->is('dashboard/promociones*') ? 'bg-primary text-white shadow-lg shadow-primary/30' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                <i
                    class="fa-solid fa-tags w-5 text-center {{ request()->is('dashboard/promociones*') ? 'text-white' : 'text-slate-500 group-hover:text-slate-300' }}"></i>
                <span class="font-medium">Mis Promociones</span>
            </a>

            <a href="/dashboard/experiencias"
                class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all duration-200 group {{ request()->is('dashboard/experiencias*') ? 'bg-primary text-white shadow-lg shadow-primary/30' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                <i
                    class="fa-solid fa-person-hiking w-5 text-center {{ request()->is('dashboard/experiencias*') ? 'text-white' : 'text-slate-500 group-hover:text-slate-300' }}"></i>
                <span class="font-medium">Mis Experiencias</span>
            </a>

            <a href="/dashboard/candelaria"
                class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all duration-200 group {{ request()->is('dashboard/candelaria*') ? 'bg-primary text-white shadow-lg shadow-primary/30' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                <i
                    class="fa-solid fa-masks-theater w-5 text-center {{ request()->is('dashboard/candelaria*') ? 'text-white' : 'text-slate-500 group-hover:text-slate-300' }}"></i>
                <span class="font-medium">Candelaria</span>
            </a>

            <div class="mt-auto pt-8 border-t border-slate-800">
                <a href="/"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-slate-400 hover:bg-slate-800 hover:text-indigo-400 transition-all duration-200 group">
                    <i class="fa-solid fa-globe w-5 text-center text-slate-500 group-hover:text-indigo-400"></i>
                    <span class="font-medium">Ver Sitio Web</span>
                </a>

                <button onclick="logout()"
                    class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-red-500 hover:bg-red-500/10 hover:text-red-400 transition-all duration-200 group mt-2">
                    <i class="fa-solid fa-right-from-bracket w-5 text-center text-red-500 group-hover:text-red-400"></i>
                    <span class="font-medium">Cerrar Sesión</span>
                </button>
            </div>
        </nav>
    </aside>

    <!-- Main Content Wrapper -->
    <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
        <!-- Mobile Header -->
        <header class="bg-white border-b border-gray-200 lg:hidden h-16 flex items-center justify-between px-4">
            <button @click="sidebarOpen = true" class="text-gray-500 hover:text-gray-700 focus:outline-none">
                <i class="fa-solid fa-bars text-xl"></i>
            </button>
            <span class="font-bold text-gray-800">Turiscovery</span>
            <div class="w-6"></div> <!-- Spacer for centering -->
        </header>

        <!-- Content -->
        <main class="flex-1 overflow-y-auto bg-gray-50 p-4 md:p-8">
            <div class="max-w-7xl mx-auto">
                @yield('content')
            </div>
        </main>
    </div>
</div>

@stack('scripts')

<script>
    async function logout() {
        try {
            await api.logout();
            showNotification('Sesión cerrada', 'success');
            window.location.href = '/';
        } catch (error) {
            console.error('Error logging out:', error);
        }
    }

    // Check if user is authenticated and has proper role
    document.addEventListener('DOMContentLoaded', async () => {
        const token = api.getToken();
        if (!token) {
            window.location.href = '/login';
            return;
        }

        try {
            const response = await axios.get('/api/me');
            const user = response.data.data;

            if (user.role !== 'socio' && user.role !== 'admin') {
                showNotification('No tienes permisos para acceder al dashboard', 'error');
                window.location.href = '/';
            }
        } catch (error) {
            window.location.href = '/login';
        }
    });
</script>
</body>

</html>
