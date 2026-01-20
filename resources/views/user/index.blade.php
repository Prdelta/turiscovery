@extends('layouts.dashboard')

@section('title', 'Mi Perfil')
@section('page-title', 'Mi Perfil')
@section('page-subtitle', 'Bienvenido a tu espacio personal')

@section('content')
    <div class="fade-in space-y-8">

        <!-- Premium Welcome Card with Gradient -->
        <div
            class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-blue-600 via-blue-500 to-indigo-600 text-white shadow-2xl shadow-blue-500/30">
            <!-- Decorative Background Pattern -->
            <div class="absolute inset-0 opacity-10">
                <div
                    class="absolute top-0 right-0 w-96 h-96 bg-white rounded-full blur-3xl transform translate-x-32 -translate-y-32">
                </div>
                <div
                    class="absolute bottom-0 left-0 w-80 h-80 bg-blue-300 rounded-full blur-3xl transform -translate-x-24 translate-y-24">
                </div>
            </div>

            <div class="relative p-8 md:p-10">
                <div class="flex flex-col md:flex-row items-center gap-6">
                    <!-- Avatar with Glow Effect -->
                    <div class="relative group">
                        <div
                            class="absolute inset-0 bg-white/30 rounded-full blur-xl group-hover:bg-white/40 transition-all">
                        </div>
                        <div
                            class="relative w-24 h-24 bg-white/20 backdrop-blur-lg rounded-full flex items-center justify-center text-4xl font-bold border-4 border-white/40 shadow-xl group-hover:scale-110 transition-transform">
                            {{ substr(Auth::user()->name ?? 'U', 0, 1) }}
                        </div>
                    </div>

                    <div class="text-center md:text-left flex-1">
                        <h2 class="text-3xl md:text-4xl font-bold mb-2 drop-shadow-lg">
                            ¡Hola, {{ Auth::user()->name ?? 'Viajero' }}! 👋
                        </h2>
                        <p class="text-blue-100 text-lg mb-6 font-medium">
                            Descubre las maravillas de Puno y crea recuerdos inolvidables
                        </p>
                        <div class="flex flex-wrap justify-center md:justify-start gap-3">
                            <a href="/"
                                class="group flex items-center gap-2 px-6 py-3 bg-white text-blue-600 font-bold rounded-xl shadow-lg hover:shadow-xl hover:-translate-y-1 transition-all">
                                <i data-lucide="compass" class="w-5 h-5 group-hover:rotate-12 transition-transform"></i>
                                <span>Explorar Destinos</span>
                            </a>
                            <a href="{{ url('/user/edit') }}"
                                class="group flex items-center gap-2 px-6 py-3 bg-blue-700/60 hover:bg-blue-700 backdrop-blur-md text-white font-bold rounded-xl border-2 border-white/30 hover:-translate-y-1 transition-all">
                                <i data-lucide="settings" class="w-5 h-5 group-hover:rotate-90 transition-transform"></i>
                                <span>Editar Perfil</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Premium Stats Grid -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Favorites Stat -->
            <a href="{{ url('/user/favorites') }}"
                class="group relative overflow-hidden bg-gradient-to-br from-red-50 to-pink-50 border-2 border-red-100 rounded-2xl p-6 hover:shadow-xl hover:-translate-y-1 transition-all duration-300">
                <div
                    class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-red-500/10 to-pink-500/10 rounded-full blur-2xl group-hover:scale-150 transition-transform">
                </div>
                <div class="relative flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold text-red-600 uppercase tracking-wider mb-2">Favoritos</p>
                        <h3 class="text-5xl font-black text-red-600 mb-1">12</h3>
                        <p class="text-sm text-red-500 font-medium">Lugares guardados</p>
                    </div>
                    <div
                        class="w-16 h-16 bg-gradient-to-br from-red-500 to-pink-500 text-white rounded-2xl flex items-center justify-center shadow-lg group-hover:scale-110 group-hover:rotate-6 transition-all">
                        <i data-lucide="heart" class="w-8 h-8"></i>
                    </div>
                </div>
                <div
                    class="absolute bottom-0 left-0 right-0 h-1 bg-gradient-to-r from-red-500 to-pink-500 transform scale-x-0 group-hover:scale-x-100 transition-transform">
                </div>
            </a>

            <!-- Reviews Stat -->
            <a href="{{ url('/user/reviews') }}"
                class="group relative overflow-hidden bg-gradient-to-br from-yellow-50 to-amber-50 border-2 border-yellow-100 rounded-2xl p-6 hover:shadow-xl hover:-translate-y-1 transition-all duration-300">
                <div
                    class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-yellow-500/10 to-amber-500/10 rounded-full blur-2xl group-hover:scale-150 transition-transform">
                </div>
                <div class="relative flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold text-yellow-600 uppercase tracking-wider mb-2">Reseñas</p>
                        <h3 class="text-5xl font-black text-yellow-600 mb-1">5</h3>
                        <p class="text-sm text-yellow-500 font-medium">Opiniones compartidas</p>
                    </div>
                    <div
                        class="w-16 h-16 bg-gradient-to-br from-yellow-500 to-amber-500 text-white rounded-2xl flex items-center justify-center shadow-lg group-hover:scale-110 group-hover:rotate-6 transition-all">
                        <i data-lucide="star" class="w-8 h-8"></i>
                    </div>
                </div>
                <div
                    class="absolute bottom-0 left-0 right-0 h-1 bg-gradient-to-r from-yellow-500 to-amber-500 transform scale-x-0 group-hover:scale-x-100 transition-transform">
                </div>
            </a>

            <!-- Level Stat -->
            <div
                class="group relative overflow-hidden bg-gradient-to-br from-emerald-50 to-teal-50 border-2 border-emerald-100 rounded-2xl p-6 hover:shadow-xl hover:-translate-y-1 transition-all duration-300">
                <div
                    class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-emerald-500/10 to-teal-500/10 rounded-full blur-2xl group-hover:scale-150 transition-transform">
                </div>
                <div class="relative flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold text-emerald-600 uppercase tracking-wider mb-2">Tu Nivel</p>
                        <h3 class="text-3xl font-black text-emerald-600 mb-1">Explorador</h3>
                        <p class="text-sm text-emerald-500 font-medium">¡Sigue descubriendo!</p>
                    </div>
                    <div
                        class="w-16 h-16 bg-gradient-to-br from-emerald-500 to-teal-500 text-white rounded-2xl flex items-center justify-center shadow-lg group-hover:scale-110 group-hover:rotate-6 transition-all">
                        <i data-lucide="award" class="w-8 h-8"></i>
                    </div>
                </div>
                <div
                    class="absolute bottom-0 left-0 right-0 h-1 bg-gradient-to-r from-emerald-500 to-teal-500 transform scale-x-0 group-hover:scale-x-100 transition-transform">
                </div>
            </div>
        </div>

        <!-- Recent Activity Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Recent Favorites -->
            <div
                class="bg-white rounded-2xl shadow-lg border border-slate-100 overflow-hidden hover:shadow-xl transition-shadow">
                <div class="bg-gradient-to-r from-red-500 to-pink-500 p-6">
                    <h3 class="font-bold text-xl text-white flex items-center gap-3">
                        <div class="w-10 h-10 bg-white/20 rounded-lg flex items-center justify-center backdrop-blur-sm">
                            <i data-lucide="heart" class="w-5 h-5"></i>
                        </div>
                        Favoritos Recientes
                    </h3>
                </div>
                <div class="p-6 space-y-4">
                    <!-- Item 1 -->
                    <div
                        class="group flex gap-4 items-center p-3 hover:bg-slate-50 rounded-xl transition-all border border-transparent hover:border-red-100 cursor-pointer">
                        <div class="w-16 h-16 rounded-xl bg-slate-200 bg-cover bg-center flex-shrink-0 shadow-sm"
                            style="background-image: url('https://via.placeholder.com/150')"></div>
                        <div class="flex-1 min-w-0">
                            <h4 class="font-bold text-slate-800 group-hover:text-red-600 transition-colors">Restaurante Los
                                Balcones</h4>
                            <p class="text-sm text-slate-500 flex items-center gap-1">
                                <i data-lucide="utensils" class="w-3 h-3"></i>
                                Gastronomía • Puno
                            </p>
                        </div>
                        <button class="text-red-500 hover:text-red-600 hover:scale-110 transition-all">
                            <i data-lucide="heart" class="w-5 h-5 fill-current"></i>
                        </button>
                    </div>
                    <!-- Item 2 -->
                    <div
                        class="group flex gap-4 items-center p-3 hover:bg-slate-50 rounded-xl transition-all border border-transparent hover:border-red-100 cursor-pointer">
                        <div class="w-16 h-16 rounded-xl bg-slate-200 bg-cover bg-center flex-shrink-0 shadow-sm"
                            style="background-image: url('https://via.placeholder.com/150')"></div>
                        <div class="flex-1 min-w-0">
                            <h4 class="font-bold text-slate-800 group-hover:text-red-600 transition-colors">Islas Uros</h4>
                            <p class="text-sm text-slate-500 flex items-center gap-1">
                                <i data-lucide="compass" class="w-3 h-3"></i>
                                Experiencia • Lago Titicaca
                            </p>
                        </div>
                        <button class="text-red-500 hover:text-red-600 hover:scale-110 transition-all">
                            <i data-lucide="heart" class="w-5 h-5 fill-current"></i>
                        </button>
                    </div>
                </div>
                <div class="px-6 pb-6">
                    <a href="{{ url('/user/favorites') }}"
                        class="flex items-center justify-center gap-2 w-full px-4 py-3 bg-gradient-to-r from-red-500 to-pink-500 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl hover:-translate-y-0.5 transition-all">
                        <span>Ver Todos los Favoritos</span>
                        <i data-lucide="arrow-right" class="w-4 h-4"></i>
                    </a>
                </div>
            </div>

            <!-- Recent Reviews -->
            <div
                class="bg-white rounded-2xl shadow-lg border border-slate-100 overflow-hidden hover:shadow-xl transition-shadow">
                <div class="bg-gradient-to-r from-yellow-500 to-amber-500 p-6">
                    <h3 class="font-bold text-xl text-white flex items-center gap-3">
                        <div class="w-10 h-10 bg-white/20 rounded-lg flex items-center justify-center backdrop-blur-sm">
                            <i data-lucide="message-square" class="w-5 h-5"></i>
                        </div>
                        Tus Reseñas Recientes
                    </h3>
                </div>
                <div class="p-6 space-y-4">
                    <div class="p-4 bg-gradient-to-br from-yellow-50 to-amber-50 rounded-xl border border-yellow-100">
                        <div class="flex justify-between items-start mb-3">
                            <h4 class="font-bold text-slate-800 text-sm">Festividad Candelaria</h4>
                            <div class="flex text-yellow-500 gap-0.5">
                                <i data-lucide="star" class="w-4 h-4 fill-current"></i>
                                <i data-lucide="star" class="w-4 h-4 fill-current"></i>
                                <i data-lucide="star" class="w-4 h-4 fill-current"></i>
                                <i data-lucide="star" class="w-4 h-4 fill-current"></i>
                                <i data-lucide="star" class="w-4 h-4 fill-current"></i>
                            </div>
                        </div>
                        <p class="text-sm text-slate-600 italic leading-relaxed">
                            "Una experiencia inolvidable, los trajes son impresionantes y la energía de la celebración es
                            contagiosa..."
                        </p>
                        <p class="text-xs text-slate-400 mt-2">Hace 2 días</p>
                    </div>
                </div>
                <div class="px-6 pb-6">
                    <a href="{{ url('/user/reviews') }}"
                        class="flex items-center justify-center gap-2 w-full px-4 py-3 bg-gradient-to-r from-yellow-500 to-amber-500 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl hover:-translate-y-0.5 transition-all">
                        <span>Ver Todas tus Reseñas</span>
                        <i data-lucide="arrow-right" class="w-4 h-4"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
