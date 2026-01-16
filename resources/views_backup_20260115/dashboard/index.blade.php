@extends('layouts.dashboard')

@section('title', 'Dashboard - Turiscovery')

@section('content')
    <!-- Dashboard Header -->
    <div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Bienvenido, <span id="user-name"
                    class="bg-gradient-to-r from-primary to-primary-light bg-clip-text text-transparent">Socio</span>! 👋
            </h1>
            <p class="text-gray-500 mt-1">Gestiona tu contenido y monitorea tu impacto.</p>
        </div>
        <div>
            <a href="/dashboard/locales/create"
                class="inline-flex items-center gap-2 bg-primary text-white px-5 py-2.5 rounded-lg font-medium hover:bg-primary-dark transition-all shadow-lg shadow-primary/30">
                <i class="fa-solid fa-plus"></i>
                Nuevo Local
            </a>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Locales Stat -->
        <div
            class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center justify-between group hover:shadow-md transition-all">
            <div>
                <p class="text-sm font-medium text-gray-500 mb-1">Mis Locales</p>
                <h3 class="text-3xl font-bold text-gray-800" id="locales-count">0</h3>
            </div>
            <div
                class="w-12 h-12 bg-blue-50 rounded-xl flex items-center justify-center text-blue-500 group-hover:scale-110 transition-transform">
                <i class="fa-solid fa-store text-xl"></i>
            </div>
        </div>

        <!-- Eventos Stat -->
        <div
            class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center justify-between group hover:shadow-md transition-all">
            <div>
                <p class="text-sm font-medium text-gray-500 mb-1">Eventos Activos</p>
                <h3 class="text-3xl font-bold text-gray-800" id="eventos-count">0</h3>
            </div>
            <div
                class="w-12 h-12 bg-purple-50 rounded-xl flex items-center justify-center text-purple-500 group-hover:scale-110 transition-transform">
                <i class="fa-solid fa-calendar-star text-xl"></i>
            </div>
        </div>

        <!-- Promociones Stat -->
        <div
            class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center justify-between group hover:shadow-md transition-all">
            <div>
                <p class="text-sm font-medium text-gray-500 mb-1">Promociones</p>
                <h3 class="text-3xl font-bold text-gray-800" id="promociones-count">0</h3>
            </div>
            <div
                class="w-12 h-12 bg-green-50 rounded-xl flex items-center justify-center text-green-500 group-hover:scale-110 transition-transform">
                <i class="fa-solid fa-tags text-xl"></i>
            </div>
        </div>

        <!-- Experiencias Stat -->
        <div
            class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center justify-between group hover:shadow-md transition-all">
            <div>
                <p class="text-sm font-medium text-gray-500 mb-1">Experiencias</p>
                <h3 class="text-3xl font-bold text-gray-800" id="experiencias-count">0</h3>
            </div>
            <div
                class="w-12 h-12 bg-orange-50 rounded-xl flex items-center justify-center text-orange-500 group-hover:scale-110 transition-transform">
                <i class="fa-solid fa-person-hiking text-xl"></i>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Quick Actions -->
        <div class="lg:col-span-2 space-y-8">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                    <i class="fa-solid fa-bolt text-yellow-500"></i> Acciones Rápidas
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <a href="/dashboard/locales/create"
                        class="flex flex-col items-center justify-center p-6 rounded-xl bg-gray-50 border-2 border-dashed border-gray-200 hover:border-primary hover:bg-primary/5 cursor-pointer transition-all group text-center">
                        <div
                            class="w-12 h-12 rounded-full bg-white shadow-sm flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                            <i class="fa-solid fa-store text-primary text-xl"></i>
                        </div>
                        <span class="font-semibold text-gray-700 group-hover:text-primary">Registrar Local</span>
                        <span class="text-xs text-gray-400 mt-1">Negocio físico</span>
                    </a>

                    <a href="/dashboard/eventos/create"
                        class="flex flex-col items-center justify-center p-6 rounded-xl bg-gray-50 border-2 border-dashed border-gray-200 hover:border-purple-500 hover:bg-purple-50 cursor-pointer transition-all group text-center">
                        <div
                            class="w-12 h-12 rounded-full bg-white shadow-sm flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                            <i class="fa-solid fa-calendar-plus text-purple-500 text-xl"></i>
                        </div>
                        <span class="font-semibold text-gray-700 group-hover:text-purple-600">Crear Evento</span>
                        <span class="text-xs text-gray-400 mt-1">Festival o actividad</span>
                    </a>

                    <a href="/dashboard/promociones/create"
                        class="flex flex-col items-center justify-center p-6 rounded-xl bg-gray-50 border-2 border-dashed border-gray-200 hover:border-green-500 hover:bg-green-50 cursor-pointer transition-all group text-center">
                        <div
                            class="w-12 h-12 rounded-full bg-white shadow-sm flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                            <i class="fa-solid fa-tag text-green-500 text-xl"></i>
                        </div>
                        <span class="font-semibold text-gray-700 group-hover:text-green-600">Lanzar Promo</span>
                        <span class="text-xs text-gray-400 mt-1">Oferta especial</span>
                    </a>
                </div>
            </div>

            <!-- Recent Content Chart/List Placeholder -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-lg font-bold text-gray-800">Actividad Reciente</h2>
                    <a href="#" class="text-sm text-primary font-medium hover:underline">Ver todo</a>
                </div>
                <div id="recent-content" class="space-y-3">
                    <div class="animate-pulse flex space-x-4">
                        <div class="rounded-full bg-gray-200 h-10 w-10"></div>
                        <div class="flex-1 space-y-4 py-1">
                            <div class="h-4 bg-gray-200 rounded w-3/4"></div>
                            <div class="space-y-2">
                                <div class="h-4 bg-gray-200 rounded"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tips / Notifications Column -->
        <div class="space-y-6">
            <div class="bg-gradient-to-br from-primary to-blue-600 rounded-2xl p-6 text-white shadow-lg">
                <h3 class="font-bold text-lg mb-2">💡 Tip pro</h3>
                <p class="text-blue-100 text-sm mb-4">Las promociones con fotos de alta calidad tienen un <strong>40% más de
                        interacción</strong>.</p>
                <a href="/dashboard/promociones/create"
                    class="inline-block bg-white/20 hover:bg-white/30 backdrop-blur-sm text-white text-xs font-bold py-2 px-4 rounded-lg transition-colors">
                    Crear Promoción
                </a>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h3 class="font-bold text-gray-800 mb-4">Estado del Sistema</h3>
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-2 h-2 rounded-full bg-green-500"></div>
                            <span class="text-sm text-gray-600">Servidores</span>
                        </div>
                        <span class="text-xs text-green-600 font-medium bg-green-50 px-2 py-1 rounded-full">Operativo</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', async () => {
            try {
                // Get user info
                const userResponse = await axios.get('/api/me');
                const user = userResponse.data.data;
                document.getElementById('user-name').textContent = user.name;

                // Load stats (simplified - shows all user's content)
                const [locales, eventos, promociones, experiencias] = await Promise.all([
                    axios.get('/api/locales'),
                    axios.get('/api/eventos'),
                    axios.get('/api/promociones'),
                    axios.get('/api/experiencias')
                ]);

                // Filter by current user
                const myLocales = locales.data.data.data.filter(l => l.user_id === user.id);
                const myEventos = eventos.data.data.data.filter(e => e.user_id === user.id);
                const myPromociones = promociones.data.data.data.filter(p => p.user_id === user.id);
                const myExperiencias = experiencias.data.data.data.filter(e => e.user_id === user.id);

                document.getElementById('locales-count').textContent = myLocales.length;
                document.getElementById('eventos-count').textContent = myEventos.length;
                document.getElementById('promociones-count').textContent = myPromociones.length;
                document.getElementById('experiencias-count').textContent = myExperiencias.length;

                // Show recent content
                const allContent = [
                    ...myLocales.map(l => ({
                        ...l,
                        type: 'Local',
                        icon: '🏢'
                    })),
                    ...myEventos.map(e => ({
                        ...e,
                        type: 'Evento',
                        icon: '🎪'
                    })),
                    ...myPromociones.map(p => ({
                        ...p,
                        type: 'Promoción',
                        icon: '💰'
                    })),
                    ...myExperiencias.map(e => ({
                        ...e,
                        type: 'Experiencia',
                        icon: '🚣'
                    }))
                ].sort((a, b) => new Date(b.created_at) - new Date(a.created_at)).slice(0, 5);

                const container = document.getElementById('recent-content');
                if (allContent.length === 0) {
                    container.innerHTML =
                        '<p class="text-secondary">No tienes contenido aún. ¡Empieza creando tu primer local!</p>';
                } else {
                    container.innerHTML = '<div class="space-y-4">' + allContent.map(item => `
                <div class="p-4 border border-gray-100 rounded-lg flex justify-between items-center bg-white hover:shadow-md transition-shadow duration-200">
                    <div class="flex items-center gap-4">
                        <div class="text-3xl">${item.icon}</div>
                        <div>
                            <h4 class="font-semibold text-gray-800 m-0">${item.title || item.name}</h4>
                            <p class="text-sm text-gray-500 mt-1">${item.type}</p>
                        </div>
                    </div>
                    <span class="px-2.5 py-1 rounded-full text-xs font-medium ${item.is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-700'}">
                        ${item.is_active ? 'Activo' : 'Inactivo'}
                    </span>
                </div>
            `).join('') + '</div>';
                }

            } catch (error) {
                console.error('Error loading dashboard:', error);
            }
        });
    </script>
@endpush
