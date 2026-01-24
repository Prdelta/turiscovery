@extends('layouts.app')

@section('content')
    <section class="hero text-center relative overflow-hidden bg-cover bg-center"
        style="background-image: url('https://images.unsplash.com/photo-1552566626-52f8b828add9?auto=format&fit=crop&w=1920&q=80');">
        <div class="absolute inset-0 bg-gradient-to-br from-orange-900/85 via-red-900/75 to-pink-900/70"></div>

        <div class="container mx-auto px-4 relative z-10">
            <!-- Badge animado con precio -->
            <div class="inline-flex mb-4">
                <span class="badge bg-gradient-to-r from-yellow-400 via-orange-500 to-red-500 text-white px-8 py-3 text-base font-black shadow-2xl backdrop-blur border-2 border-white/40 animate-pulse-soft">
                    <i data-lucide="percent" class="w-5 h-5 inline mr-2 animate-spin" style="animation-duration: 3s;"></i>
                    HASTA 70% DE DESCUENTO
                </span>
            </div>

            <h1 class="fade-in text-white mb-6 text-4xl md:text-6xl font-black text-shadow-strong leading-tight">
                🎁 Promociones<br/>
                <span class="bg-gradient-to-r from-yellow-300 to-orange-400 bg-clip-text text-transparent">Irresistibles</span>
            </h1>

            <p class="fade-in text-white/95 mb-10 text-xl md:text-2xl max-w-3xl mx-auto font-bold text-shadow">
                ¡Ahorra en grande! Cupones digitales para restaurantes, hoteles y tours.
            </p>

            <!-- CTA destacado -->
            <div class="flex gap-4 justify-center flex-wrap fade-in">
                <a href="#promociones" class="btn bg-gradient-to-r from-yellow-400 to-orange-500 hover:from-yellow-500 hover:to-orange-600 text-slate-900 font-black px-10 py-5 text-lg shadow-2xl hover:shadow-yellow-500/50 transform hover:scale-110 transition-all border-4 border-white/30">
                    <i data-lucide="gift" class="w-6 h-6 mr-2"></i>
                    Ver Todas las Ofertas
                </a>
            </div>

            <!-- Indicadores de beneficios -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 max-w-4xl mx-auto mt-12 fade-in">
                <div class="bg-white/10 backdrop-blur-md rounded-xl p-4 border border-white/20">
                    <div class="text-3xl font-black text-yellow-300 mb-1">50+</div>
                    <div class="text-xs text-white/90 font-bold uppercase">Ofertas Activas</div>
                </div>
                <div class="bg-white/10 backdrop-blur-md rounded-xl p-4 border border-white/20">
                    <div class="text-3xl font-black text-yellow-300 mb-1">70%</div>
                    <div class="text-xs text-white/90 font-bold uppercase">Descuento Máx</div>
                </div>
                <div class="bg-white/10 backdrop-blur-md rounded-xl p-4 border border-white/20">
                    <div class="text-3xl font-black text-yellow-300 mb-1">24/7</div>
                    <div class="text-xs text-white/90 font-bold uppercase">Disponible</div>
                </div>
                <div class="bg-white/10 backdrop-blur-md rounded-xl p-4 border border-white/20">
                    <div class="text-3xl font-black text-yellow-300 mb-1">100%</div>
                    <div class="text-xs text-white/90 font-bold uppercase">Gratis</div>
                </div>
            </div>
        </div>

        <!-- Wave decoration -->
        <div class="absolute bottom-0 left-0 right-0 h-32 bg-gradient-to-t from-slate-50 to-transparent"></div>
    </section>

    <section id="promociones" class="section bg-gradient-to-b from-slate-50 to-white">
        <div class="container mx-auto px-4">
            <!-- Header de sección -->
            <div class="text-center mb-12">
                <h2 class="text-3xl md:text-4xl font-black mb-3 gradient-text">🔥 Ofertas del Momento</h2>
                <p class="text-slate-600 max-w-2xl mx-auto">Cupones exclusivos para tu viaje a Puno</p>
            </div>

            <div id="promociones-grid" class="grid grid-3">
                <x-loading-state message="Buscando las mejores ofertas para ti..." />
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        const isAuthenticated = {{ auth()->check() ? 'true' : 'false' }};

        document.addEventListener('DOMContentLoaded', async () => {
            lucide.createIcons();
            await loadPromociones();
        });

        async function loadPromociones() {
            try {
                const response = await axios.get('/api/promociones');
                const container = document.getElementById('promociones-grid');
                const items = response.data.data.data || response.data.data;

                if (response.data.success && Array.isArray(items) && items.length > 0) {
                    container.innerHTML = items.map(promo => `
                    <article class="card card-hover group relative overflow-hidden">
                        <!-- Imagen con efecto -->
                        <div class="h-48 overflow-hidden relative">
                            <img src="${promo.image_url || 'https://via.placeholder.com/400x150'}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500" alt="${promo.title}">
                            <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent"></div>

                            <!-- Badge del local -->
                            <div class="absolute bottom-3 left-3">
                                <span class="bg-white/95 backdrop-blur-sm px-3 py-1.5 rounded-full text-xs font-bold text-slate-800 shadow-lg flex items-center gap-1">
                                    <i data-lucide="store" class="w-3 h-3 text-blue-600"></i>
                                    ${promo.locale ? promo.locale.name : 'Seleccionado'}
                                </span>
                            </div>

                            <!-- Estrella de oferta -->
                            <div class="absolute top-3 right-3 animate-pulse-soft">
                                <div class="bg-red-500 text-white px-3 py-1 rounded-full text-xs font-black shadow-lg transform rotate-12">
                                    ¡OFERTA!
                                </div>
                            </div>
                        </div>

                        <!-- Sección de descuento llamativa -->
                        <div class="relative p-6 text-center bg-gradient-to-br from-amber-50 via-orange-50 to-yellow-50 border-b-4 border-dashed border-amber-300">
                            <!-- Círculos decorativos en los bordes -->
                            <div class="absolute -left-3 top-1/2 transform -translate-y-1/2 w-6 h-6 bg-slate-50 rounded-full"></div>
                            <div class="absolute -right-3 top-1/2 transform -translate-y-1/2 w-6 h-6 bg-slate-50 rounded-full"></div>

                            <div class="inline-block bg-gradient-to-br from-orange-500 to-red-600 text-white px-8 py-4 rounded-2xl shadow-2xl transform group-hover:scale-110 transition-transform duration-300">
                                <div class="text-5xl font-black mb-1">
                                    ${promo.discount_type === 'percentage' ? promo.discount_percentage + '%' : (promo.discount_type === 'fixed' ? 'S/' + promo.discount_amount : '2x1')}
                                </div>
                                <div class="text-sm font-bold uppercase tracking-wider">Descuento</div>
                            </div>
                        </div>

                        <!-- Contenido -->
                        <div class="p-6 bg-white">
                            <h3 class="text-xl font-bold mb-3 text-center text-slate-800 line-clamp-2 min-h-[56px]">${promo.title}</h3>
                            <p class="text-slate-600 text-sm mb-4 text-center line-clamp-2">${promo.description || 'Aprovecha este descuento especial por tiempo limitado.'}</p>

                            <!-- Fecha de expiración -->
                            <div class="bg-gradient-to-r from-red-50 to-orange-50 p-4 rounded-xl mb-4 border-l-4 border-red-500">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-2 text-slate-600">
                                        <i data-lucide="clock" class="w-4 h-4 text-red-500"></i>
                                        <span class="text-xs font-semibold">Válido hasta:</span>
                                    </div>
                                    <span class="font-bold text-slate-800">${new Date(promo.end_date).toLocaleDateString('es-ES', { day: 'numeric', month: 'short', year: 'numeric' })}</span>
                                </div>
                            </div>

                            <!-- Botón llamativo -->
                            <button onclick="handleRedeem('${promo.id}')" class="btn btn-primary w-full bg-gradient-to-r from-green-600 to-emerald-600 hover:from-green-700 hover:to-emerald-700 shadow-lg hover:shadow-xl transform hover:scale-105 transition-all text-white font-bold py-4">
                                <i data-lucide="gift" class="w-5 h-5"></i>
                                ¡Obtener Cupón Gratis!
                            </button>
                        </div>

                        <!-- Badge de "Limitado" -->
                        <div class="absolute top-0 left-0 right-0 text-center">
                            <span class="inline-block bg-gradient-to-r from-purple-600 to-pink-600 text-white px-4 py-1 text-xs font-bold shadow-lg">
                                ⚡ CUPONES LIMITADOS
                            </span>
                        </div>
                    </article>
                `).join('');
                    lucide.createIcons();
                } else {
                    container.innerHTML = `<x-empty-state icon="tag" message="No hay promociones activas en este momento." />`;
                    lucide.createIcons();
                }
            } catch (e) {
                console.error(e);
                document.getElementById('promociones-grid').innerHTML =
                    '<p class="text-center text-danger">Error al cargar promociones.</p>';
            }
        }

        async function handleRedeem(promocionId) {
            if (!isAuthenticated) {
                window.location.href = `/login?redirect=${encodeURIComponent(window.location.pathname)}`;
                return;
            }

            try {
                const response = await axios.post('/api/user-coupons', {
                    promocion_id: promocionId
                });

                if (response.data.success) {
                    alert('¡Cupón obtenido! Código: ' + response.data.data.coupon_code + '\n' + response.data.message);
                }
            } catch (error) {
                console.error('Error al obtener cupón:', error);
                if (error.response?.status === 401) {
                    window.location.href = `/login?redirect=${encodeURIComponent(window.location.pathname)}`;
                } else {
                    alert('Error al obtener cupón. Por favor, intenta nuevamente.');
                }
            }
        }
    </script>
@endpush
