@extends('layouts.dashboard')

@section('title', 'Mi Perfil')
@section('page-title', 'Mi Perfil')
@section('page-subtitle', 'Bienvenido a tu espacio personal')

@section('content')
    <div class="fade-in space-y-6">

        <!-- Welcome Card -->
        <div class="card p-6 bg-gradient-to-r from-blue-600 to-blue-500 text-white border-none shadow-lg">
            <div class="flex flex-col md:flex-row items-center gap-6">
                <div
                    class="w-20 h-20 bg-white/20 backdrop-blur-sm rounded-full flex items-center justify-center text-3xl font-bold border-2 border-white/30">
                    {{ substr(Auth::user()->name ?? 'U', 0, 1) }}
                </div>
                <div class="text-center md:text-left flex-1">
                    <h2 class="text-2xl font-bold mb-1">¡Hola, {{ Auth::user()->name ?? 'Viajero' }}!</h2>
                    <p class="text-blue-100 mb-4">¿Listo para descubrir nuevas maravillas en Puno?</p>
                    <div class="flex flex-wrap justify-center md:justify-start gap-3">
                        <a href="/" class="btn bg-white text-blue-600 hover:bg-blue-50 border-none shadow-sm">
                            <i data-lucide="compass" class="w-4 h-4 mr-2"></i>
                            Explorar
                        </a>
                        <a href="{{ url('/user/edit') }}"
                            class="btn bg-blue-700/50 hover:bg-blue-700 text-white border-none backdrop-blur-sm">
                            <i data-lucide="settings" class="w-4 h-4 mr-2"></i>
                            Editar Perfil
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stats Grid -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Favorites Stat -->
            <a href="{{ url('/user/favorites') }}"
                class="card p-6 flex items-center justify-between hover:border-blue-300 transition-colors group">
                <div>
                    <p class="text-sm text-slate-500 font-medium uppercase tracking-wider">Favoritos</p>
                    <h3 class="text-3xl font-bold text-slate-800 mt-1Group">12</h3>
                </div>
                <div
                    class="w-12 h-12 bg-red-100 text-red-500 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform">
                    <i data-lucide="heart" class="w-6 h-6"></i>
                </div>
            </a>

            <!-- Reviews Stat -->
            <a href="{{ url('/user/reviews') }}"
                class="card p-6 flex items-center justify-between hover:border-blue-300 transition-colors group">
                <div>
                    <p class="text-sm text-slate-500 font-medium uppercase tracking-wider">Reseñas</p>
                    <h3 class="text-3xl font-bold text-slate-800 mt-1">5</h3>
                </div>
                <div
                    class="w-12 h-12 bg-yellow-100 text-yellow-600 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform">
                    <i data-lucide="star" class="w-6 h-6"></i>
                </div>
            </a>

            <!-- Local Level (Gamification Placeholder) -->
            <div class="card p-6 flex items-center justify-between border-gray-200">
                <div>
                    <p class="text-sm text-slate-500 font-medium uppercase tracking-wider">Nivel</p>
                    <h3 class="text-3xl font-bold text-slate-800 mt-1">Explorador</h3>
                </div>
                <div class="w-12 h-12 bg-green-100 text-green-600 rounded-xl flex items-center justify-center">
                    <i data-lucide="award" class="w-6 h-6"></i>
                </div>
            </div>
        </div>

        <!-- Recent Activity / Recommendations -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Recent Favorites -->
            <div class="card p-6">
                <h3 class="font-bold text-lg text-slate-800 mb-4 flex items-center gap-2">
                    <i data-lucide="heart" class="w-5 h-5 text-red-500"></i>
                    Favoritos Recientes
                </h3>
                <div class="space-y-4">
                    <!-- Item 1 -->
                    <div
                        class="flex gap-4 items-center p-3 hover:bg-slate-50 rounded-lg transition-colors border border-transparent hover:border-slate-100">
                        <div class="w-16 h-16 rounded-lg bg-slate-200 bg-cover bg-center flex-shrink-0"
                            style="background-image: url('https://via.placeholder.com/150')"></div>
                        <div class="flex-1">
                            <h4 class="font-semibold text-slate-800">Restaurante Los Balcones</h4>
                            <p class="text-sm text-slate-500">Gastronomía • Puno</p>
                        </div>
                        <button class="text-slate-400 hover:text-red-500"><i data-lucide="heart-off"
                                class="w-5 h-5"></i></button>
                    </div>
                    <!-- Item 2 -->
                    <div
                        class="flex gap-4 items-center p-3 hover:bg-slate-50 rounded-lg transition-colors border border-transparent hover:border-slate-100">
                        <div class="w-16 h-16 rounded-lg bg-slate-200 bg-cover bg-center flex-shrink-0"
                            style="background-image: url('https://via.placeholder.com/150')"></div>
                        <div class="flex-1">
                            <h4 class="font-semibold text-slate-800">Islas Uros</h4>
                            <p class="text-sm text-slate-500">Experiencia • Lago Titicaca</p>
                        </div>
                        <button class="text-slate-400 hover:text-red-500"><i data-lucide="heart-off"
                                class="w-5 h-5"></i></button>
                    </div>
                </div>
                <a href="{{ url('/user/favorites') }}"
                    class="block mt-4 text-center text-sm text-blue-600 font-medium hover:underline">Ver todos los
                    favoritos</a>
            </div>

            <!-- Recent Reviews -->
            <div class="card p-6">
                <h3 class="font-bold text-lg text-slate-800 mb-4 flex items-center gap-2">
                    <i data-lucide="message-square" class="w-5 h-5 text-blue-500"></i>
                    Tus Reseñas Recientes
                </h3>
                <div class="space-y-4">
                    <div class="p-3 bg-slate-50 rounded-lg">
                        <div class="flex justify-between items-start mb-2">
                            <h4 class="font-semibold text-slate-800 text-sm">Festividad Candelaria</h4>
                            <div class="flex text-yellow-400">
                                <i data-lucide="star" class="w-3 h-3 fill-current"></i>
                                <i data-lucide="star" class="w-3 h-3 fill-current"></i>
                                <i data-lucide="star" class="w-3 h-3 fill-current"></i>
                                <i data-lucide="star" class="w-3 h-3 fill-current"></i>
                                <i data-lucide="star" class="w-3 h-3 fill-current"></i>
                            </div>
                        </div>
                        <p class="text-sm text-slate-600 italic">"Una experiencia inolvidable, los trajes son
                            impresionantes..."</p>
                    </div>
                </div>
                <a href="{{ url('/user/reviews') }}"
                    class="block mt-4 text-center text-sm text-blue-600 font-medium hover:underline">Ver todas tus
                    reseñas</a>
            </div>
        </div>
    </div>
@endsection
