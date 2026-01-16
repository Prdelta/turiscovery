@extends('layouts.dashboard')

@section('title', 'Mis Favoritos')
@section('page-title', 'Mis Favoritos')
@section('page-subtitle', 'Lugares y eventos que has guardado')

@section('content')
    <div class="fade-in">
        <!-- Filter Tabs (Visual Only for now) -->
        <div class="flex gap-2 mb-6 overflow-x-auto pb-2">
            <button class="px-4 py-2 bg-blue-600 text-white rounded-full text-sm font-medium shadow-sm">Todos</button>
            <button
                class="px-4 py-2 bg-white text-slate-600 hover:bg-slate-50 border border-slate-200 rounded-full text-sm font-medium">Locales</button>
            <button
                class="px-4 py-2 bg-white text-slate-600 hover:bg-slate-50 border border-slate-200 rounded-full text-sm font-medium">Eventos</button>
            <button
                class="px-4 py-2 bg-white text-slate-600 hover:bg-slate-50 border border-slate-200 rounded-full text-sm font-medium">Experiencias</button>
        </div>

        <!-- Empty State -->
        <!--
        <div class="card p-12 text-center">
            <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i data-lucide="heart" class="w-8 h-8 text-slate-400"></i>
            </div>
            <h3 class="text-lg font-medium text-slate-800 mb-2">Aún no tienes favoritos</h3>
            <p class="text-slate-500 mb-6">Explora Turiscovery y guarda lo que más te guste para verlo aquí.</p>
            <a href="/" class="btn btn-primary">Ir a Explorar</a>
        </div>
        -->

        <!-- Grid of Favorites -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

            <!-- Favorito Demo 1 -->
            <article class="card group cursor-pointer hover:shadow-lg transition-all">
                <div class="relative h-48 overflow-hidden">
                    <img src="https://via.placeholder.com/400x300" alt="Lugar"
                        class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                    <div class="absolute top-3 right-3">
                        <button
                            class="w-8 h-8 bg-white/90 backdrop-blur rounded-full flex items-center justify-center text-red-500 hover:scale-110 transition-transform shadow-sm">
                            <i data-lucide="heart" class="w-4 h-4 fill-current"></i>
                        </button>
                    </div>
                    <div class="absolute bottom-3 left-3">
                        <span
                            class="badge bg-white/90 text-blue-700 backdrop-blur text-xs font-bold px-2 py-1 rounded-md shadow-sm">
                            Restaurante
                        </span>
                    </div>
                </div>
                <div class="p-4">
                    <div class="flex justify-between items-start mb-2">
                        <h3 class="font-bold text-slate-800 line-clamp-1 group-hover:text-blue-600 transition-colors">La
                            Casona del Lago</h3>
                        <div class="flex items-center gap-1 text-sm font-semibold text-slate-700">
                            <i data-lucide="star" class="w-3 h-3 fill-yellow-400 text-yellow-400"></i>
                            4.8
                        </div>
                    </div>
                    <p class="text-sm text-slate-500 flex items-center gap-1 mb-4">
                        <i data-lucide="map-pin" class="w-3 h-3"></i>
                        Puno, Centro
                    </p>
                    <a href="#" class="btn btn-outline w-full justify-center py-2 text-sm">Ver Detalles</a>
                </div>
            </article>

            <!-- Favorito Demo 2 -->
            <article class="card group cursor-pointer hover:shadow-lg transition-all">
                <div class="relative h-48 overflow-hidden">
                    <img src="https://via.placeholder.com/400x300" alt="Evento"
                        class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                    <div class="absolute top-3 right-3">
                        <button
                            class="w-8 h-8 bg-white/90 backdrop-blur rounded-full flex items-center justify-center text-red-500 hover:scale-110 transition-transform shadow-sm">
                            <i data-lucide="heart" class="w-4 h-4 fill-current"></i>
                        </button>
                    </div>
                    <div class="absolute bottom-3 left-3">
                        <span
                            class="badge bg-white/90 text-purple-700 backdrop-blur text-xs font-bold px-2 py-1 rounded-md shadow-sm">
                            Evento
                        </span>
                    </div>
                </div>
                <div class="p-4">
                    <div class="flex justify-between items-start mb-2">
                        <h3 class="font-bold text-slate-800 line-clamp-1 group-hover:text-purple-600 transition-colors">
                            Festival de Danzas</h3>
                        <div class="text-sm text-slate-500 font-medium">Feb 12, 2024</div>
                    </div>
                    <p class="text-sm text-slate-500 flex items-center gap-1 mb-4">
                        <i data-lucide="map-pin" class="w-3 h-3"></i>
                        Estadio Torres Belón
                    </p>
                    <a href="#" class="btn btn-outline w-full justify-center py-2 text-sm">Ver Detalles</a>
                </div>
            </article>

        </div>
    </div>
@endsection
