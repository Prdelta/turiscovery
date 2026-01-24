@extends('layouts.app')

@section('title', 'Mis Favoritos')

@section('content')
    <!-- Header Section -->
    <section class="text-white py-12" style="background: linear-gradient(135deg, var(--color-danger) 0%, #c0392b 100%);">
        <div class="container">
            <div class="flex items-center justify-between flex-wrap gap-4">
                <div>
                    <h1 class="text-4xl font-bold mb-2">Mis Favoritos</h1>
                    <p class="text-white/80">Lugares y eventos que has guardado</p>
                </div>
                <a href="/user" class="px-4 py-2 bg-white/20 hover:bg-white/30 backdrop-blur-md rounded-lg transition-colors border border-white/30 font-medium">
                    <i data-lucide="arrow-left" class="w-4 h-4 inline mr-2"></i>
                    Volver al Perfil
                </a>
            </div>
        </div>
    </section>

    <div class="container py-12">
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

        <!-- Grid of Favorites -->
        @if ($favorites->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach ($favorites as $favorite)
                    <article class="card group cursor-pointer hover:shadow-lg transition-all">
                        <div class="relative h-48 overflow-hidden">
                            <img src="{{ $favorite->favoritable->image_url ?? 'https://via.placeholder.com/400x300' }}"
                                alt="{{ $favorite->favoritable->name ?? $favorite->favoritable->title }}"
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
                                    {{ class_basename($favorite->favoritable_type) }}
                                </span>
                            </div>
                        </div>
                        <div class="p-4">
                            <h3
                                class="font-bold text-slate-800 line-clamp-1 group-hover:text-blue-600 transition-colors mb-2">
                                {{ $favorite->favoritable->name ?? ($favorite->favoritable->title ?? 'Sin título') }}
                            </h3>
                            <p class="text-sm text-slate-600 line-clamp-2 mb-3">
                                {{ Str::limit($favorite->favoritable->description ?? 'Sin descripción', 100) }}
                            </p>
                            <div class="flex items-center gap-2 text-xs text-slate-500 mb-3">
                                <i data-lucide="map-pin" class="w-3 h-3"></i>
                                <span>{{ $favorite->favoritable->address ?? 'Puno, Perú' }}</span>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-8">
                {{ $favorites->links() }}
            </div>
        @else
            <!-- Empty State -->
            <div class="card p-12 text-center">
                <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i data-lucide="heart" class="w-8 h-8 text-slate-400"></i>
                </div>
                <h3 class="text-lg font-medium text-slate-800 mb-2">Aún no tienes favoritos</h3>
                <p class="text-slate-500 mb-6">Explora Turiscovery y guarda lo que más te guste para verlo aquí.</p>
                <a href="/" class="btn btn-primary">Ir a Explorar</a>
            </div>
        @endif
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                lucide.createIcons();
            });
        </script>
    @endpush
@endsection
