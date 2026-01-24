@extends('layouts.app')

@section('content')
    <section class="hero text-center relative overflow-hidden bg-cover bg-center"
        style="background-image: url('https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?auto=format&fit=crop&w=1920&q=80');">
        <div class="absolute inset-0 bg-gradient-to-br from-slate-900/90 via-blue-900/80 to-purple-900/70"></div>

        <div class="container mx-auto px-4 relative z-10">
            <div class="inline-flex mb-4">
                <span class="badge bg-gradient-to-r from-blue-400 to-purple-500 text-white px-6 py-2 text-sm font-bold shadow-2xl backdrop-blur border-2 border-white/30">
                    <i data-lucide="store" class="w-4 h-4 inline mr-2"></i>
                    DIRECTORIO OFICIAL DE PUNO
                </span>
            </div>

            <h1 class="fade-in text-white mb-6 text-4xl md:text-6xl font-black text-shadow-strong leading-tight">
                Descubre los Mejores<br/>
                <span class="bg-gradient-to-r from-blue-300 to-purple-400 bg-clip-text text-transparent">Negocios de Puno</span>
            </h1>

            <p class="fade-in text-white/90 mb-10 text-lg md:text-2xl max-w-3xl mx-auto font-medium text-shadow">
                Restaurantes, hoteles, agencias de turismo y más. Todo verificado y calificado por viajeros.
            </p>

            <!-- Categorías rápidas -->
            <div class="flex gap-3 justify-center flex-wrap fade-in max-w-3xl mx-auto">
                <a href="#" class="bg-white/10 backdrop-blur-md hover:bg-white/20 text-white px-6 py-3 rounded-full font-bold text-sm border border-white/30 transition-all transform hover:scale-105">
                    <i data-lucide="utensils" class="w-4 h-4 inline mr-2"></i>
                    Restaurantes
                </a>
                <a href="#" class="bg-white/10 backdrop-blur-md hover:bg-white/20 text-white px-6 py-3 rounded-full font-bold text-sm border border-white/30 transition-all transform hover:scale-105">
                    <i data-lucide="hotel" class="w-4 h-4 inline mr-2"></i>
                    Hoteles
                </a>
                <a href="#" class="bg-white/10 backdrop-blur-md hover:bg-white/20 text-white px-6 py-3 rounded-full font-bold text-sm border border-white/30 transition-all transform hover:scale-105">
                    <i data-lucide="compass" class="w-4 h-4 inline mr-2"></i>
                    Agencias
                </a>
                <a href="#" class="bg-white/10 backdrop-blur-md hover:bg-white/20 text-white px-6 py-3 rounded-full font-bold text-sm border border-white/30 transition-all transform hover:scale-105">
                    <i data-lucide="shopping-bag" class="w-4 h-4 inline mr-2"></i>
                    Tiendas
                </a>
            </div>

            <!-- Stats -->
            <div class="grid grid-cols-3 gap-6 max-w-2xl mx-auto mt-12 fade-in">
                <div class="text-white">
                    <div class="text-3xl md:text-4xl font-black mb-1 text-blue-300">200+</div>
                    <div class="text-sm text-white/80 font-semibold">Negocios</div>
                </div>
                <div class="text-white">
                    <div class="text-3xl md:text-4xl font-black mb-1 text-purple-300">100%</div>
                    <div class="text-sm text-white/80 font-semibold">Verificados</div>
                </div>
                <div class="text-white">
                    <div class="text-3xl md:text-4xl font-black mb-1 text-pink-300">4.8★</div>
                    <div class="text-sm text-white/80 font-semibold">Calificación</div>
                </div>
            </div>
        </div>

        <div class="absolute bottom-0 left-0 right-0 h-32 bg-gradient-to-t from-slate-50 to-transparent"></div>
    </section>

    <section class="section bg-gradient-to-b from-slate-50 to-white">
        <div class="container mx-auto px-4">
            <div class="text-center mb-12">
                <h2 class="text-3xl md:text-4xl font-black mb-3 gradient-text">⭐ Negocios Destacados</h2>
                <p class="text-slate-600 max-w-2xl mx-auto">Los favoritos de nuestros viajeros</p>
            </div>

            <div id="locales-grid" class="grid grid-3">
                <x-loading-state message="Cargando directorio de negocios..." />
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        const isAuthenticated = {{ auth()->check() ? 'true' : 'false' }};

        document.addEventListener('DOMContentLoaded', async () => {
            lucide.createIcons();
            await loadLocales();
        });

        async function loadLocales() {
            try {
                const response = await axios.get('/api/locales');
                const container = document.getElementById('locales-grid');
                const items = response.data.data.data || response.data.data;

                if (response.data.success && Array.isArray(items) && items.length > 0) {
                    container.innerHTML = items.map(local => `
                    <article class="card card-hover group relative overflow-hidden">
                        <!-- Imagen con overlay -->
                        <div class="h-56 overflow-hidden relative">
                            <img src="${local.image_url || 'https://via.placeholder.com/400x200?text=Comercio'}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500" alt="${local.name}">
                            <div class="card-image-overlay"></div>

                            <!-- Badge de estado con animación -->
                            <div class="absolute top-4 right-4 z-10">
                                <span class="flex items-center gap-2 bg-green-500 text-white px-4 py-2 rounded-full text-xs font-bold shadow-lg animate-pulse-soft">
                                    <span class="w-2 h-2 bg-white rounded-full animate-ping"></span>
                                    Abierto Ahora
                                </span>
                            </div>

                            <!-- Botón de favorito mejorado -->
                            <button onclick="handleFavorite('locale', '${local.id}', this)" class="absolute top-4 left-4 z-10 bg-white/90 backdrop-blur-sm hover:bg-white rounded-full w-10 h-10 p-0 flex items-center justify-center shadow-lg transform hover:scale-110 transition-all group/fav">
                                <i data-lucide="heart" class="w-5 h-5 text-slate-400 group-hover/fav:text-red-500 transition-colors"></i>
                            </button>

                            <!-- Categoría del local -->
                            <div class="absolute bottom-4 left-4 z-10">
                                <span class="bg-gradient-to-r from-blue-600 to-purple-600 text-white px-4 py-2 rounded-lg text-xs font-bold shadow-lg backdrop-blur-sm">
                                    <i data-lucide="utensils" class="w-3 h-3 inline mr-1"></i>
                                    ${local.category || 'Restaurante'}
                                </span>
                            </div>
                        </div>

                        <!-- Contenido -->
                        <div class="p-6 bg-gradient-to-br from-white to-slate-50">
                            <!-- Título -->
                            <h3 class="text-xl font-bold mb-3 text-slate-800 group-hover:text-primary transition-colors line-clamp-2 min-h-[56px]">${local.name}</h3>

                            <!-- Rating destacado -->
                            <div class="flex items-center gap-3 mb-4 p-3 bg-gradient-to-r from-amber-50 to-yellow-50 rounded-xl border-l-4 border-amber-400">
                                <div class="flex items-center gap-1">
                                    ${[1,2,3,4,5].map(star => `
                                        <i data-lucide="star" class="w-4 h-4 ${star <= (local.average_rating || 4) ? 'fill-amber-400 text-amber-400' : 'text-slate-300'}"></i>
                                    `).join('')}
                                </div>
                                <div class="flex-1">
                                    <span class="text-lg font-black text-amber-600">${local.average_rating ? Number(local.average_rating).toFixed(1) : '4.5'}</span>
                                    <span class="text-xs text-slate-500 ml-1">(${local.reviews_count || 128} reviews)</span>
                                </div>
                            </div>

                            <!-- Descripción -->
                            <p class="text-slate-600 text-sm mb-4 line-clamp-3 leading-relaxed">${local.description || 'Disfruta de una experiencia única en este establecimiento de Puno.'}</p>

                            <!-- Información de contacto con íconos -->
                            <div class="space-y-2 mb-5 bg-white p-4 rounded-xl border border-slate-100">
                                <div class="flex items-center gap-3 text-sm text-slate-600">
                                    <div class="w-8 h-8 bg-red-100 rounded-full flex items-center justify-center flex-shrink-0">
                                        <i data-lucide="map-pin" class="w-4 h-4 text-red-600"></i>
                                    </div>
                                    <span class="truncate font-medium">${local.address || 'Jr. Lima 123, Puno Centro'}</span>
                                </div>
                                <div class="flex items-center gap-3 text-sm text-slate-600">
                                    <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0">
                                        <i data-lucide="phone" class="w-4 h-4 text-green-600"></i>
                                    </div>
                                    <span class="font-medium">${local.phone || '+51 951 234 567'}</span>
                                </div>
                            </div>

                            <!-- Botones de acción -->
                            <div class="grid grid-cols-2 gap-3">
                                <a href="#" class="btn btn-outline text-sm py-3 border-2 hover:bg-blue-50 hover:border-blue-600 hover:text-blue-600">
                                    <i data-lucide="eye" class="w-4 h-4"></i>
                                    Ver Más
                                </a>
                                <a href="tel:${local.phone || '+51951234567'}" class="btn btn-primary text-sm py-3 bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700">
                                    <i data-lucide="phone-call" class="w-4 h-4"></i>
                                    Llamar
                                </a>
                            </div>
                        </div>

                        <!-- Badge de verificado -->
                        ${local.verified ? `
                        <div class="absolute top-0 right-0 bg-blue-600 text-white px-3 py-1 text-xs font-bold shadow-lg" style="border-radius: 0 0 0 12px;">
                            <i data-lucide="check-circle" class="w-3 h-3 inline mr-1"></i>
                            Verificado
                        </div>
                        ` : ''}
                    </article>
                `).join('');
                    lucide.createIcons();
                } else {
                    container.innerHTML = `<x-empty-state icon="store" message="No hay locales registrados aún." />`;
                    lucide.createIcons();
                }
            } catch (e) {
                console.error(e);
                document.getElementById('locales-grid').innerHTML =
                    '<p class="text-center text-danger">Error al cargar locales.</p>';
            }
        }

        async function handleFavorite(favoriteableType, favoriteableId, btn) {
            if (!isAuthenticated) {
                window.location.href = `/login?redirect=${encodeURIComponent(window.location.pathname)}`;
                return;
            }

            try {
                const response = await axios.post('/api/favorites/toggle', {
                    favoriteable_type: favoriteableType,
                    favoriteable_id: favoriteableId
                });

                if (response.data.success) {
                    const icon = btn.querySelector('i');
                    if (response.data.action === 'added') {
                        icon.classList.add('fill-current', 'text-red-500');
                        icon.classList.remove('text-gray-400');
                        alert('¡Agregado a favoritos!');
                    } else {
                        icon.classList.remove('fill-current', 'text-red-500');
                        icon.classList.add('text-gray-400');
                        alert('Eliminado de favoritos');
                    }
                    lucide.createIcons();
                }
            } catch (error) {
                console.error('Error al alternar favorito:', error);
                if (error.response?.status === 401) {
                    window.location.href = `/login?redirect=${encodeURIComponent(window.location.pathname)}`;
                } else {
                    alert('Error al actualizar favoritos. Por favor, intenta nuevamente.');
                }
            }
        }
    </script>
@endpush
