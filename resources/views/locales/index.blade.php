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
        // Las utilidades ya están disponibles globalmente vía app.js compilado por Vite
        const isAuthenticated = {{ auth()->check() ? 'true' : 'false' }};

        document.addEventListener('DOMContentLoaded', async () => {
            lucide.createIcons();
            await loadLocales();
        });

        async function loadLocales() {
            const container = document.getElementById('locales-grid');

            try {
                const response = await axios.get('/api/locales');
                const { data: items, meta } = extractPaginatedData(response);

                clearContainer(container);

                if (items.length > 0) {
                    items.forEach(local => {
                        const card = createLocalCard(local);
                        container.appendChild(card);
                    });

                    // Event delegation
                    container.addEventListener('click', handleCardClick);

                    lucide.createIcons();
                } else {
                    const emptyDiv = document.createElement('div');
                    emptyDiv.className = 'col-span-3 text-center py-12';
                    const icon = document.createElement('i');
                    icon.setAttribute('data-lucide', 'store');
                    icon.className = 'w-16 h-16 mx-auto mb-4 text-slate-300';
                    const text = createElementWithText('p', 'No hay locales registrados aún.', 'text-slate-500');
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
         * Crea tarjeta de local (sin XSS)
         */
        function createLocalCard(local) {
            const article = document.createElement('article');
            article.className = 'card card-hover group relative overflow-hidden';

            // Badge de verificado (si aplica)
            if (local.verified) {
                const verifiedBadge = document.createElement('div');
                verifiedBadge.className = 'absolute top-0 right-0 bg-blue-600 text-white px-3 py-1 text-xs font-bold shadow-lg z-20';
                verifiedBadge.style.borderRadius = '0 0 0 12px';
                const checkIcon = document.createElement('i');
                checkIcon.setAttribute('data-lucide', 'check-circle');
                checkIcon.className = 'w-3 h-3 inline mr-1';
                const verifiedText = document.createTextNode(' Verificado');
                verifiedBadge.appendChild(checkIcon);
                verifiedBadge.appendChild(verifiedText);
                article.appendChild(verifiedBadge);
            }

            // Imagen con overlay
            const imageDiv = document.createElement('div');
            imageDiv.className = 'h-56 overflow-hidden relative';

            const img = document.createElement('img');
            safeSetAttribute(img, 'src', local.image_url || 'https://via.placeholder.com/400x200?text=Comercio');
            safeSetAttribute(img, 'alt', local.name || 'Local');
            img.className = 'w-full h-full object-cover group-hover:scale-110 transition-transform duration-500';
            imageDiv.appendChild(img);

            const overlay = document.createElement('div');
            overlay.className = 'card-image-overlay';
            imageDiv.appendChild(overlay);

            // Badge de estado
            const statusBadge = document.createElement('div');
            statusBadge.className = 'absolute top-4 right-4 z-10';
            const statusSpan = document.createElement('span');
            statusSpan.className = 'flex items-center gap-2 bg-green-500 text-white px-4 py-2 rounded-full text-xs font-bold shadow-lg animate-pulse-soft';
            const pingSpan = document.createElement('span');
            pingSpan.className = 'w-2 h-2 bg-white rounded-full animate-ping';
            const statusText = document.createTextNode(' Abierto Ahora');
            statusSpan.appendChild(pingSpan);
            statusSpan.appendChild(statusText);
            statusBadge.appendChild(statusSpan);
            imageDiv.appendChild(statusBadge);

            // Botón de favorito (sin onclick inline)
            const favButton = document.createElement('button');
            favButton.className = 'absolute top-4 left-4 z-10 bg-white/90 backdrop-blur-sm hover:bg-white rounded-full w-10 h-10 p-0 flex items-center justify-center shadow-lg transform hover:scale-110 transition-all group/fav';
            favButton.dataset.action = 'favorite';
            favButton.dataset.type = 'locale';
            favButton.dataset.id = local.id;
            const heartIcon = document.createElement('i');
            heartIcon.setAttribute('data-lucide', 'heart');
            heartIcon.className = 'w-5 h-5 text-slate-400 group-hover/fav:text-red-500 transition-colors';
            favButton.appendChild(heartIcon);
            imageDiv.appendChild(favButton);

            // Categoría del local
            const categoryBadge = document.createElement('div');
            categoryBadge.className = 'absolute bottom-4 left-4 z-10';
            const categorySpan = document.createElement('span');
            categorySpan.className = 'bg-gradient-to-r from-blue-600 to-purple-600 text-white px-4 py-2 rounded-lg text-xs font-bold shadow-lg backdrop-blur-sm';
            const utensilsIcon = document.createElement('i');
            utensilsIcon.setAttribute('data-lucide', 'utensils');
            utensilsIcon.className = 'w-3 h-3 inline mr-1';
            const categoryText = document.createTextNode(local.category || 'Restaurante');
            categorySpan.appendChild(utensilsIcon);
            categorySpan.appendChild(categoryText);
            categoryBadge.appendChild(categorySpan);
            imageDiv.appendChild(categoryBadge);

            article.appendChild(imageDiv);

            // Contenido
            const contentDiv = document.createElement('div');
            contentDiv.className = 'p-6 bg-gradient-to-br from-white to-slate-50';

            // Título
            const title = createElementWithText('h3', local.name || 'Sin nombre', 'text-xl font-bold mb-3 text-slate-800 group-hover:text-primary transition-colors line-clamp-2 min-h-[56px]');
            contentDiv.appendChild(title);

            // Rating destacado
            const ratingDiv = document.createElement('div');
            ratingDiv.className = 'flex items-center gap-3 mb-4 p-3 bg-gradient-to-r from-amber-50 to-yellow-50 rounded-xl border-l-4 border-amber-400';

            const starsDiv = document.createElement('div');
            starsDiv.className = 'flex items-center gap-1';
            const rating = local.average_rating || 4;
            for (let i = 1; i <= 5; i++) {
                const star = document.createElement('i');
                star.setAttribute('data-lucide', 'star');
                star.className = i <= rating ? 'w-4 h-4 fill-amber-400 text-amber-400' : 'w-4 h-4 text-slate-300';
                starsDiv.appendChild(star);
            }
            ratingDiv.appendChild(starsDiv);

            const ratingTextDiv = document.createElement('div');
            ratingTextDiv.className = 'flex-1';
            const ratingNumber = createElementWithText('span', (local.average_rating ? Number(local.average_rating).toFixed(1) : '4.5'), 'text-lg font-black text-amber-600');
            const reviewsCount = createElementWithText('span', `(${local.reviews_count || 128} reviews)`, 'text-xs text-slate-500 ml-1');
            ratingTextDiv.appendChild(ratingNumber);
            ratingTextDiv.appendChild(reviewsCount);
            ratingDiv.appendChild(ratingTextDiv);

            contentDiv.appendChild(ratingDiv);

            // Descripción
            const description = createElementWithText('p', local.description || 'Disfruta de una experiencia única en este establecimiento de Puno.', 'text-slate-600 text-sm mb-4 line-clamp-3 leading-relaxed');
            contentDiv.appendChild(description);

            // Información de contacto
            const contactDiv = document.createElement('div');
            contactDiv.className = 'space-y-2 mb-5 bg-white p-4 rounded-xl border border-slate-100';

            // Dirección
            const addressRow = document.createElement('div');
            addressRow.className = 'flex items-center gap-3 text-sm text-slate-600';
            const addressIconDiv = document.createElement('div');
            addressIconDiv.className = 'w-8 h-8 bg-red-100 rounded-full flex items-center justify-center flex-shrink-0';
            const mapPinIcon = document.createElement('i');
            mapPinIcon.setAttribute('data-lucide', 'map-pin');
            mapPinIcon.className = 'w-4 h-4 text-red-600';
            addressIconDiv.appendChild(mapPinIcon);
            const addressSpan = createElementWithText('span', local.address || 'Jr. Lima 123, Puno Centro', 'truncate font-medium');
            addressRow.appendChild(addressIconDiv);
            addressRow.appendChild(addressSpan);
            contactDiv.appendChild(addressRow);

            // Teléfono
            const phoneRow = document.createElement('div');
            phoneRow.className = 'flex items-center gap-3 text-sm text-slate-600';
            const phoneIconDiv = document.createElement('div');
            phoneIconDiv.className = 'w-8 h-8 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0';
            const phoneIcon = document.createElement('i');
            phoneIcon.setAttribute('data-lucide', 'phone');
            phoneIcon.className = 'w-4 h-4 text-green-600';
            phoneIconDiv.appendChild(phoneIcon);
            const phoneSpan = createElementWithText('span', local.phone || '+51 951 234 567', 'font-medium');
            phoneRow.appendChild(phoneIconDiv);
            phoneRow.appendChild(phoneSpan);
            contactDiv.appendChild(phoneRow);

            contentDiv.appendChild(contactDiv);

            // Botones de acción
            const buttonsDiv = document.createElement('div');
            buttonsDiv.className = 'grid grid-cols-2 gap-3';

            const verMasLink = document.createElement('a');
            safeSetAttribute(verMasLink, 'href', '#');
            verMasLink.className = 'btn btn-outline text-sm py-3 border-2 hover:bg-blue-50 hover:border-blue-600 hover:text-blue-600';
            const eyeIcon = document.createElement('i');
            eyeIcon.setAttribute('data-lucide', 'eye');
            eyeIcon.className = 'w-4 h-4';
            const verMasText = document.createTextNode(' Ver Más');
            verMasLink.appendChild(eyeIcon);
            verMasLink.appendChild(verMasText);

            const llamarLink = document.createElement('a');
            const phoneNumber = local.phone || '+51951234567';
            safeSetAttribute(llamarLink, 'href', `tel:${phoneNumber}`);
            llamarLink.className = 'btn btn-primary text-sm py-3 bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700';
            const phoneCallIcon = document.createElement('i');
            phoneCallIcon.setAttribute('data-lucide', 'phone-call');
            phoneCallIcon.className = 'w-4 h-4';
            const llamarText = document.createTextNode(' Llamar');
            llamarLink.appendChild(phoneCallIcon);
            llamarLink.appendChild(llamarText);

            buttonsDiv.appendChild(verMasLink);
            buttonsDiv.appendChild(llamarLink);
            contentDiv.appendChild(buttonsDiv);

            article.appendChild(contentDiv);

            return article;
        }

        /**
         * Event delegation para clicks
         */
        function handleCardClick(e) {
            const button = e.target.closest('[data-action="favorite"]');
            if (button) {
                const type = button.dataset.type;
                const id = button.dataset.id;
                handleFavorite(type, id, button);
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
                    favoriteable_id: parseInt(favoriteableId, 10)
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
                    const errorMessage = handleApiError(error);
                    alert(errorMessage);
                }
            }
        }
    </script>
@endpush
