@extends('layouts.app')

@section('title', 'Mi Perfil')

@section('content')
    <!-- Hero Section con Gradient Primary -->
    <section class="relative overflow-hidden" style="background: linear-gradient(135deg, var(--color-primary) 0%, var(--color-primary-dark) 100%);">
        <div class="absolute inset-0 opacity-10">
            <div class="absolute top-0 right-0 w-96 h-96 bg-white rounded-full blur-3xl transform translate-x-32 -translate-y-32"></div>
            <div class="absolute bottom-0 left-0 w-80 h-80 bg-white rounded-full blur-3xl transform -translate-x-24 translate-y-24"></div>
        </div>

        <div class="container relative py-16 text-white">
            <div class="flex flex-col md:flex-row items-center gap-8">
                <!-- Avatar Grande -->
                <div class="relative group">
                    <div class="absolute inset-0 bg-white/30 rounded-full blur-xl group-hover:bg-white/40 transition-all"></div>
                    <div class="relative w-32 h-32 bg-white/20 backdrop-blur-lg rounded-full flex items-center justify-center text-6xl font-bold border-4 border-white/40 shadow-2xl">
                        {{ substr(Auth::user()->name ?? 'U', 0, 1) }}
                    </div>
                </div>

                <!-- Info del Usuario -->
                <div class="text-center md:text-left flex-1">
                    <h1 class="text-4xl md:text-5xl font-bold mb-3 drop-shadow-lg">
                        {{ Auth::user()->name ?? 'Viajero' }}
                    </h1>
                    <p class="text-white/80 text-lg mb-4 font-medium">
                        {{ Auth::user()->email }}
                    </p>
                    <div class="flex flex-wrap gap-3 justify-center md:justify-start">
                        <span class="px-4 py-2 bg-white/20 backdrop-blur-md rounded-full text-sm font-medium border border-white/30">
                            <i data-lucide="calendar" class="w-4 h-4 inline mr-1"></i>
                            Miembro desde {{ Auth::user()->created_at->format('M Y') }}
                        </span>
                        <span class="px-4 py-2 bg-white/20 backdrop-blur-md rounded-full text-sm font-medium border border-white/30">
                            <i data-lucide="award" class="w-4 h-4 inline mr-1"></i>
                            Explorador Nivel 1
                        </span>
                    </div>
                </div>

                <!-- Botón de Editar -->
                <div class="flex gap-3">
                    <a href="{{ url('/user/edit') }}"
                        class="flex items-center gap-2 px-6 py-3 bg-white font-bold rounded-lg shadow-lg hover:shadow-xl hover:-translate-y-1 transition-all"
                        style="color: var(--color-primary);">
                        <i data-lucide="settings" class="w-5 h-5"></i>
                        <span>Editar Perfil</span>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Estadísticas Grid -->
    <section class="container -mt-8 mb-12 relative z-10">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Favoritos Stat -->
            <a href="{{ url('/user/favorites') }}"
                class="card group p-6 border-l-4 hover:-translate-y-2 transition-all duration-300"
                style="border-left-color: var(--color-danger);">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-wider mb-2" style="color: var(--color-text-muted);">Favoritos</p>
                        <h3 class="text-5xl font-black mb-1" style="color: var(--color-danger);">{{ $favoritesCount }}</h3>
                        <p class="text-sm font-medium" style="color: var(--color-text-light);">Lugares guardados</p>
                    </div>
                    <div class="w-16 h-16 rounded-xl flex items-center justify-center shadow-md group-hover:scale-110 transition-all"
                        style="background: linear-gradient(135deg, var(--color-danger) 0%, #c0392b 100%);">
                        <i data-lucide="heart" class="w-8 h-8 text-white"></i>
                    </div>
                </div>
            </a>

            <!-- Reviews Stat -->
            <a href="{{ url('/user/reviews') }}"
                class="card group p-6 border-l-4 hover:-translate-y-2 transition-all duration-300"
                style="border-left-color: var(--color-accent);">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-wider mb-2" style="color: var(--color-text-muted);">Reseñas</p>
                        <h3 class="text-5xl font-black mb-1" style="color: var(--color-accent);">{{ $reviewsCount }}</h3>
                        <p class="text-sm font-medium" style="color: var(--color-text-light);">Opiniones compartidas</p>
                    </div>
                    <div class="w-16 h-16 rounded-xl flex items-center justify-center shadow-md group-hover:scale-110 transition-all"
                        style="background: linear-gradient(135deg, var(--color-accent) 0%, var(--color-accent-dark) 100%);">
                        <i data-lucide="star" class="w-8 h-8 text-white"></i>
                    </div>
                </div>
            </a>

            <!-- Explorador Badge -->
            <div class="card group p-6 border-l-4 hover:-translate-y-2 transition-all duration-300"
                style="border-left-color: var(--color-secondary);">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-wider mb-2" style="color: var(--color-text-muted);">Tu Nivel</p>
                        <h3 class="text-3xl font-black mb-1" style="color: var(--color-secondary);">Explorador</h3>
                        <p class="text-sm font-medium" style="color: var(--color-text-light);">¡Sigue descubriendo!</p>
                    </div>
                    <div class="w-16 h-16 rounded-xl flex items-center justify-center shadow-md group-hover:scale-110 transition-all"
                        style="background: linear-gradient(135deg, var(--color-secondary) 0%, var(--color-secondary-dark) 100%);">
                        <i data-lucide="award" class="w-8 h-8 text-white"></i>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Actividad Reciente -->
    <section class="container mb-16">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">

            <!-- Favoritos Recientes -->
            <div class="card overflow-hidden">
                <div class="p-6" style="background: linear-gradient(135deg, var(--color-danger) 0%, #c0392b 100%);">
                    <h3 class="font-bold text-2xl text-white flex items-center gap-3">
                        <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur-sm">
                            <i data-lucide="heart" class="w-6 h-6"></i>
                        </div>
                        Favoritos Recientes
                    </h3>
                </div>
                <div class="p-6 space-y-4">
                    @forelse($recentFavorites as $favorite)
                        <div class="group flex gap-4 items-center p-4 hover:bg-gray-50 rounded-xl transition-all border-2 border-transparent hover:border-gray-100 cursor-pointer">
                            <div class="w-20 h-20 rounded-xl bg-gray-200 bg-cover bg-center flex-shrink-0 shadow-md"
                                style="background-image: url('{{ $favorite->favoritable->image_url ?? 'https://via.placeholder.com/150' }}')">
                            </div>
                            <div class="flex-1 min-w-0">
                                <h4 class="font-bold group-hover:text-primary transition-colors mb-1" style="color: var(--color-text);">
                                    {{ $favorite->favoritable->name ?? ($favorite->favoritable->title ?? 'Sin título') }}
                                </h4>
                                <p class="text-sm flex items-center gap-1" style="color: var(--color-text-muted);">
                                    <i data-lucide="{{ $favorite->favoritable_type === 'App\\Models\\Locale' ? 'store' : 'compass' }}" class="w-3 h-3"></i>
                                    {{ class_basename($favorite->favoritable_type) }}
                                </p>
                            </div>
                            <button class="hover:scale-125 transition-all" style="color: var(--color-danger);">
                                <i data-lucide="heart" class="w-6 h-6 fill-current"></i>
                            </button>
                        </div>
                    @empty
                        <div class="text-center py-12">
                            <div class="w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-4" style="background-color: var(--color-bg-gray);">
                                <i data-lucide="heart" class="w-10 h-10" style="color: var(--color-gray);"></i>
                            </div>
                            <p class="mb-3" style="color: var(--color-text-muted);">Aún no tienes favoritos</p>
                            <a href="/" class="font-medium" style="color: var(--color-primary);">Explorar ahora</a>
                        </div>
                    @endforelse
                </div>
                @if($favoritesCount > 0)
                    <div class="px-6 pb-6">
                        <a href="{{ url('/user/favorites') }}"
                            class="btn btn-primary w-full justify-center">
                            <span>Ver Todos los Favoritos</span>
                            <i data-lucide="arrow-right" class="w-5 h-5"></i>
                        </a>
                    </div>
                @endif
            </div>

            <!-- Reseñas Recientes -->
            <div class="card overflow-hidden">
                <div class="p-6" style="background: linear-gradient(135deg, var(--color-accent) 0%, var(--color-accent-dark) 100%);">
                    <h3 class="font-bold text-2xl text-white flex items-center gap-3">
                        <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur-sm">
                            <i data-lucide="message-square" class="w-6 h-6"></i>
                        </div>
                        Tus Reseñas Recientes
                    </h3>
                </div>
                <div class="p-6 space-y-4">
                    @forelse($recentReviews as $review)
                        <div class="p-5 rounded-xl border-2" style="background-color: #FEF5E7; border-color: var(--color-accent);">
                            <div class="flex justify-between items-start mb-3">
                                <h4 class="font-bold text-base" style="color: var(--color-text);">
                                    {{ $review->reviewable->name ?? ($review->reviewable->title ?? 'Sin título') }}
                                </h4>
                                <div class="flex gap-0.5" style="color: var(--color-accent);">
                                    @for ($i = 1; $i <= 5; $i++)
                                        <i data-lucide="star" class="w-4 h-4 {{ $i <= $review->rating ? 'fill-current' : '' }}"></i>
                                    @endfor
                                </div>
                            </div>
                            <p class="text-sm italic leading-relaxed mb-2" style="color: var(--color-text-light);">
                                "{{ Str::limit($review->comment, 150) }}"
                            </p>
                            <p class="text-xs" style="color: var(--color-text-muted);">{{ $review->created_at->diffForHumans() }}</p>
                        </div>
                    @empty
                        <div class="text-center py-12">
                            <div class="w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-4" style="background-color: var(--color-bg-gray);">
                                <i data-lucide="message-square" class="w-10 h-10" style="color: var(--color-gray);"></i>
                            </div>
                            <p class="mb-3" style="color: var(--color-text-muted);">Aún no has escrito reseñas</p>
                            <a href="/" class="font-medium" style="color: var(--color-primary);">Explorar y compartir tu opinión</a>
                        </div>
                    @endforelse
                </div>
                @if($reviewsCount > 0)
                    <div class="px-6 pb-6">
                        <a href="{{ url('/user/reviews') }}"
                            class="btn w-full justify-center"
                            style="background: linear-gradient(135deg, var(--color-accent) 0%, var(--color-accent-dark) 100%); color: white;">
                            <span>Ver Todas tus Reseñas</span>
                            <i data-lucide="arrow-right" class="w-5 h-5"></i>
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </section>

    <!-- Call to Action -->
    <section class="container mb-16">
        <div class="rounded-3xl p-8 md:p-12 text-white text-center shadow-2xl"
            style="background: linear-gradient(135deg, var(--color-text) 0%, #1a252f 100%);">
            <h2 class="text-3xl md:text-4xl font-bold mb-4">¿Listo para tu próxima aventura?</h2>
            <p class="text-white/70 text-lg mb-8 max-w-2xl mx-auto">
                Descubre experiencias únicas, eventos emocionantes y las mejores promociones en Puno
            </p>
            <div class="flex flex-wrap gap-4 justify-center">
                <a href="/experiencias" class="btn btn-secondary">
                    <i data-lucide="compass" class="w-5 h-5"></i>
                    Explorar Experiencias
                </a>
                <a href="/eventos" class="btn btn-primary">
                    <i data-lucide="calendar" class="w-5 h-5"></i>
                    Ver Eventos
                </a>
            </div>
        </div>
    </section>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                lucide.createIcons();
            });
        </script>
    @endpush
@endsection
