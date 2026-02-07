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
        // Las utilidades ya están disponibles globalmente vía app.js compilado por Vite
        const isAuthenticated = {{ auth()->check() ? 'true' : 'false' }};

        document.addEventListener('DOMContentLoaded', async () => {
            lucide.createIcons();
            await loadPromociones();
        });

        async function loadPromociones() {
            const container = document.getElementById('promociones-grid');

            try {
                const response = await axios.get('/api/promociones');
                const { data: items, meta } = extractPaginatedData(response);

                clearContainer(container);

                if (items.length > 0) {
                    items.forEach(promo => {
                        const card = createPromocionCard(promo);
                        container.appendChild(card);
                    });

                    // Event delegation
                    container.addEventListener('click', handleCardClick);

                    lucide.createIcons();
                } else {
                    const emptyDiv = document.createElement('div');
                    emptyDiv.className = 'col-span-3 text-center py-12';
                    const icon = document.createElement('i');
                    icon.setAttribute('data-lucide', 'tag');
                    icon.className = 'w-16 h-16 mx-auto mb-4 text-slate-300';
                    const text = createElementWithText('p', 'No hay promociones activas en este momento.', 'text-slate-500');
                    emptyDiv.appendChild(icon);
                    emptyDiv.appendChild(text);
                    container.appendChild(emptyDiv);
                    lucide.createIcons();
                }
            } catch (error) {
                const errorMessage = handleApiError(error);
                const errorP = createElementWithText('p', errorMessage, 'text-center text-danger');
                clearContainer(container);
                container.appendChild(errorP);
            }
        }

        /**
         * Crea tarjeta de promoción (sin XSS)
         */
        function createPromocionCard(promo) {
            const article = document.createElement('article');
            article.className = 'card card-hover group relative overflow-hidden';

            // Badge de "Limitado" en la parte superior
            const topBadge = document.createElement('div');
            topBadge.className = 'absolute top-0 left-0 right-0 text-center z-10';
            const limitedSpan = createElementWithText('span', '⚡ CUPONES LIMITADOS', 'inline-block bg-gradient-to-r from-purple-600 to-pink-600 text-white px-4 py-1 text-xs font-bold shadow-lg');
            topBadge.appendChild(limitedSpan);
            article.appendChild(topBadge);

            // Imagen con efecto
            const imageDiv = document.createElement('div');
            imageDiv.className = 'h-48 overflow-hidden relative';

            const img = document.createElement('img');
            safeSetAttribute(img, 'src', promo.image_url || 'https://via.placeholder.com/400x150');
            safeSetAttribute(img, 'alt', promo.title || 'Promoción');
            img.className = 'w-full h-full object-cover group-hover:scale-110 transition-transform duration-500';
            imageDiv.appendChild(img);

            const overlay = document.createElement('div');
            overlay.className = 'absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent';
            imageDiv.appendChild(overlay);

            // Badge del local
            const localBadgeWrapper = document.createElement('div');
            localBadgeWrapper.className = 'absolute bottom-3 left-3';
            const localBadge = document.createElement('span');
            localBadge.className = 'bg-white/95 backdrop-blur-sm px-3 py-1.5 rounded-full text-xs font-bold text-slate-800 shadow-lg flex items-center gap-1';
            const storeIcon = document.createElement('i');
            storeIcon.setAttribute('data-lucide', 'store');
            storeIcon.className = 'w-3 h-3 text-blue-600';
            const localeName = document.createTextNode(promo.locale ? promo.locale.name : 'Seleccionado');
            localBadge.appendChild(storeIcon);
            localBadge.appendChild(localeName);
            localBadgeWrapper.appendChild(localBadge);
            imageDiv.appendChild(localBadgeWrapper);

            // Estrella de oferta
            const offerBadgeWrapper = document.createElement('div');
            offerBadgeWrapper.className = 'absolute top-3 right-3 animate-pulse-soft';
            const offerBadge = createElementWithText('div', '¡OFERTA!', 'bg-red-500 text-white px-3 py-1 rounded-full text-xs font-black shadow-lg transform rotate-12');
            offerBadgeWrapper.appendChild(offerBadge);
            imageDiv.appendChild(offerBadgeWrapper);

            article.appendChild(imageDiv);

            // Sección de descuento llamativa
            const discountSection = document.createElement('div');
            discountSection.className = 'relative p-6 text-center bg-gradient-to-br from-amber-50 via-orange-50 to-yellow-50 border-b-4 border-dashed border-amber-300';

            // Círculos decorativos
            const leftCircle = document.createElement('div');
            leftCircle.className = 'absolute -left-3 top-1/2 transform -translate-y-1/2 w-6 h-6 bg-slate-50 rounded-full';
            const rightCircle = document.createElement('div');
            rightCircle.className = 'absolute -right-3 top-1/2 transform -translate-y-1/2 w-6 h-6 bg-slate-50 rounded-full';
            discountSection.appendChild(leftCircle);
            discountSection.appendChild(rightCircle);

            // Badge de descuento
            const discountBadge = document.createElement('div');
            discountBadge.className = 'inline-block bg-gradient-to-br from-orange-500 to-red-600 text-white px-8 py-4 rounded-2xl shadow-2xl transform group-hover:scale-110 transition-transform duration-300';

            let discountValue = '2x1'; // Default
            if (promo.discount_type === 'percentage') {
                discountValue = `${promo.discount_percentage}%`;
            } else if (promo.discount_type === 'fixed') {
                discountValue = `S/${promo.discount_amount}`;
            }

            const discountNumber = createElementWithText('div', discountValue, 'text-5xl font-black mb-1');
            const discountLabel = createElementWithText('div', 'Descuento', 'text-sm font-bold uppercase tracking-wider');
            discountBadge.appendChild(discountNumber);
            discountBadge.appendChild(discountLabel);
            discountSection.appendChild(discountBadge);

            article.appendChild(discountSection);

            // Contenido
            const contentDiv = document.createElement('div');
            contentDiv.className = 'p-6 bg-white';

            const title = createElementWithText('h3', promo.title || 'Sin título', 'text-xl font-bold mb-3 text-center text-slate-800 line-clamp-2 min-h-[56px]');
            const description = createElementWithText('p', promo.description || 'Aprovecha este descuento especial por tiempo limitado.', 'text-slate-600 text-sm mb-4 text-center line-clamp-2');

            contentDiv.appendChild(title);
            contentDiv.appendChild(description);

            // Fecha de expiración
            const expiracionDiv = document.createElement('div');
            expiracionDiv.className = 'bg-gradient-to-r from-red-50 to-orange-50 p-4 rounded-xl mb-4 border-l-4 border-red-500';
            const flexDiv = document.createElement('div');
            flexDiv.className = 'flex items-center justify-between';

            const labelDiv = document.createElement('div');
            labelDiv.className = 'flex items-center gap-2 text-slate-600';
            const clockIcon = document.createElement('i');
            clockIcon.setAttribute('data-lucide', 'clock');
            clockIcon.className = 'w-4 h-4 text-red-500';
            const labelSpan = createElementWithText('span', 'Válido hasta:', 'text-xs font-semibold');
            labelDiv.appendChild(clockIcon);
            labelDiv.appendChild(labelSpan);

            const dateSpan = createElementWithText('span', new Date(promo.end_date).toLocaleDateString('es-ES', { day: 'numeric', month: 'short', year: 'numeric' }), 'font-bold text-slate-800');

            flexDiv.appendChild(labelDiv);
            flexDiv.appendChild(dateSpan);
            expiracionDiv.appendChild(flexDiv);
            contentDiv.appendChild(expiracionDiv);

            // Botón (sin onclick inline)
            const button = document.createElement('button');
            button.className = 'btn btn-primary w-full bg-gradient-to-r from-green-600 to-emerald-600 hover:from-green-700 hover:to-emerald-700 shadow-lg hover:shadow-xl transform hover:scale-105 transition-all text-white font-bold py-4';
            button.dataset.promocionId = promo.id;
            button.dataset.action = 'redeem';

            const giftIcon = document.createElement('i');
            giftIcon.setAttribute('data-lucide', 'gift');
            giftIcon.className = 'w-5 h-5';
            const buttonText = document.createTextNode(' ¡Obtener Cupón Gratis!');

            button.appendChild(giftIcon);
            button.appendChild(buttonText);
            contentDiv.appendChild(button);

            article.appendChild(contentDiv);

            return article;
        }

        /**
         * Event delegation para clicks
         */
        function handleCardClick(e) {
            const button = e.target.closest('[data-action="redeem"]');
            if (button) {
                const promocionId = button.dataset.promocionId;
                handleRedeem(promocionId);
            }
        }

        async function handleRedeem(promocionId) {
            if (!isAuthenticated) {
                window.location.href = `/login?redirect=${encodeURIComponent(window.location.pathname)}`;
                return;
            }

            try {
                const response = await axios.post('/api/user-coupons', {
                    promocion_id: parseInt(promocionId, 10)
                });

                if (response.data.success) {
                    alert('¡Cupón obtenido! Código: ' + response.data.data.coupon_code + '\n' + response.data.message);
                }
            } catch (error) {
                console.error('Error al obtener cupón:', error);
                if (error.response?.status === 401) {
                    window.location.href = `/login?redirect=${encodeURIComponent(window.location.pathname)}`;
                } else {
                    const errorMessage = handleApiError(error);
                    alert(errorMessage);
                }
            }
        }
    </script>
@endpush
