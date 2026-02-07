@extends('layouts.app')

@section('content')
    <x-hero
        title="Eventos en Puno"
        subtitle="Música, danza, teatro y festivales. Descubre qué está pasando en la capital del folklore."
        image="https://images.unsplash.com/photo-1514525253440-b393452e23da?auto=format&fit=crop&w=1920&q=80"
        :badge="['text' => 'AGENDA CULTURAL', 'class' => 'badge-primary']"
    />

    <section class="section bg-gradient-to-br from-slate-50 via-blue-50 to-purple-50">
        <div class="container mx-auto px-4">
            <!-- Encabezado de sección -->
            <div class="text-center mb-12">
                <h2 class="text-3xl md:text-4xl font-black mb-3 gradient-text">Próximos Eventos</h2>
                <p class="text-slate-600 max-w-2xl mx-auto">Vive la cultura de Puno en su máximo esplendor</p>
            </div>

            <div class="grid grid-4 gap-6 mb-8">
                <!-- Filtros mejorados -->
                <div class="lg:col-span-1 col-span-full">
                    <div class="card p-6 sticky top-24 bg-gradient-to-br from-white to-blue-50 border-2 border-blue-100">
                        <div class="flex items-center gap-2 mb-6">
                            <div class="w-10 h-10 bg-gradient-to-br from-blue-600 to-purple-600 rounded-lg flex items-center justify-center">
                                <i data-lucide="filter" class="w-5 h-5 text-white"></i>
                            </div>
                            <h3 class="text-lg font-bold text-slate-800">Filtrar</h3>
                        </div>

                        <div class="space-y-3">
                            <label class="flex gap-3 items-center cursor-pointer p-3 rounded-lg hover:bg-blue-100 transition-colors group">
                                <input type="checkbox" checked class="w-5 h-5 rounded text-blue-600 border-2 border-slate-300 focus:ring-2 focus:ring-blue-500">
                                <div class="flex items-center gap-2 flex-1">
                                    <i data-lucide="calendar" class="w-4 h-4 text-blue-600 group-hover:scale-110 transition-transform"></i>
                                    <span class="font-semibold text-slate-700 group-hover:text-blue-600">Todos</span>
                                </div>
                            </label>
                            <label class="flex gap-3 items-center cursor-pointer p-3 rounded-lg hover:bg-purple-100 transition-colors group">
                                <input type="checkbox" class="w-5 h-5 rounded text-purple-600 border-2 border-slate-300 focus:ring-2 focus:ring-purple-500">
                                <div class="flex items-center gap-2 flex-1">
                                    <i data-lucide="sparkles" class="w-4 h-4 text-purple-600 group-hover:scale-110 transition-transform"></i>
                                    <span class="font-semibold text-slate-700 group-hover:text-purple-600">Festivales</span>
                                </div>
                            </label>
                            <label class="flex gap-3 items-center cursor-pointer p-3 rounded-lg hover:bg-pink-100 transition-colors group">
                                <input type="checkbox" class="w-5 h-5 rounded text-pink-600 border-2 border-slate-300 focus:ring-2 focus:ring-pink-500">
                                <div class="flex items-center gap-2 flex-1">
                                    <i data-lucide="music" class="w-4 h-4 text-pink-600 group-hover:scale-110 transition-transform"></i>
                                    <span class="font-semibold text-slate-700 group-hover:text-pink-600">Conciertos</span>
                                </div>
                            </label>
                            <label class="flex gap-3 items-center cursor-pointer p-3 rounded-lg hover:bg-indigo-100 transition-colors group">
                                <input type="checkbox" class="w-5 h-5 rounded text-indigo-600 border-2 border-slate-300 focus:ring-2 focus:ring-indigo-500">
                                <div class="flex items-center gap-2 flex-1">
                                    <i data-lucide="drama" class="w-4 h-4 text-indigo-600 group-hover:scale-110 transition-transform"></i>
                                    <span class="font-semibold text-slate-700 group-hover:text-indigo-600">Teatro</span>
                                </div>
                            </label>
                        </div>

                        <!-- Contador de eventos -->
                        <div class="mt-6 p-4 bg-gradient-to-r from-blue-600 to-purple-600 rounded-xl text-white text-center">
                            <div class="text-3xl font-black mb-1">12+</div>
                            <div class="text-xs font-semibold uppercase tracking-wider">Eventos Este Mes</div>
                        </div>
                    </div>
                </div>

                <div class="lg:col-span-3 col-span-full">
                    <div id="eventos-grid" class="grid grid-2 gap-6">
                        <x-loading-state message="Cargando agenda..." />
                    </div>
                </div>
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
            await loadEventos();
        });

        async function loadEventos() {
            const container = document.getElementById('eventos-grid');

            try {
                const response = await axios.get('/api/eventos');
                const { data: events, meta } = extractPaginatedData(response);

                // Limpiar contenedor de forma segura
                clearContainer(container);

                if (events.length > 0) {
                    // Renderizar eventos usando DOM API (sin innerHTML)
                    events.forEach(evento => {
                        const card = createEventCard(evento);
                        container.appendChild(card);
                    });

                    // Event delegation para manejar clicks en botones
                    container.addEventListener('click', handleEventClick);

                    // Reinicializar iconos de Lucide
                    lucide.createIcons();
                } else {
                    // Mostrar estado vacío
                    const emptyState = createEmptyState('No hay eventos próximos registrados.', 'calendar-off');
                    container.appendChild(emptyState);
                    lucide.createIcons();
                }
            } catch (error) {
                const errorMessage = handleApiError(error);
                const errorDiv = createElementWithText('p', errorMessage, 'text-center text-red-600 py-8');
                clearContainer(container);
                container.appendChild(errorDiv);
            }
        }

        /**
         * Crea tarjeta de evento de forma segura (sin XSS)
         */
        function createEventCard(evento) {
            const article = document.createElement('article');
            article.className = 'card card-hover group relative overflow-hidden';

            // Imagen del evento
            const imageDiv = document.createElement('div');
            imageDiv.className = 'relative h-56 overflow-hidden';

            if (evento.image_url) {
                const img = document.createElement('img');
                safeSetAttribute(img, 'src', evento.image_url);
                safeSetAttribute(img, 'alt', evento.title || 'Evento');
                img.className = 'w-full h-full object-cover group-hover:scale-110 transition-transform duration-500';
                imageDiv.appendChild(img);
            } else {
                const placeholder = document.createElement('div');
                placeholder.className = 'w-full h-full flex items-center justify-center bg-gradient-to-br from-blue-100 to-purple-100';
                const icon = document.createElement('i');
                icon.setAttribute('data-lucide', 'calendar');
                icon.className = 'w-16 h-16 text-blue-400';
                placeholder.appendChild(icon);
                imageDiv.appendChild(placeholder);
            }

            // Overlay de imagen
            const overlay = document.createElement('div');
            overlay.className = 'card-image-overlay';
            imageDiv.appendChild(overlay);

            // Badge de fecha
            const dateBadge = document.createElement('div');
            dateBadge.className = 'absolute top-4 left-4 bg-gradient-to-br from-white to-blue-50 px-4 py-3 rounded-xl shadow-2xl border-2 border-white/50 backdrop-blur-sm text-center transform group-hover:scale-110 transition-transform duration-300';

            const eventDate = new Date(evento.start_time);
            const daySpan = createElementWithText('span', eventDate.getDate().toString(), 'block font-black text-2xl bg-gradient-to-br from-blue-600 to-purple-600 bg-clip-text text-transparent');
            const monthSpan = createElementWithText('span', eventDate.toLocaleDateString('es-ES', { month: 'short' }), 'block text-xs font-bold uppercase text-slate-600');

            dateBadge.appendChild(daySpan);
            dateBadge.appendChild(monthSpan);
            imageDiv.appendChild(dateBadge);

            // Badge de categoría
            const categoryBadgeWrapper = document.createElement('div');
            categoryBadgeWrapper.className = 'absolute top-4 right-4';

            const categoryBadge = document.createElement('span');
            categoryBadge.className = 'badge badge-primary shadow-lg backdrop-blur-sm bg-blue-600/90 px-4 py-2 text-xs font-bold';

            const musicIcon = document.createElement('i');
            musicIcon.setAttribute('data-lucide', 'music');
            musicIcon.className = 'w-3 h-3 inline mr-1';

            const categoryText = document.createTextNode(evento.category || 'Evento');

            categoryBadge.appendChild(musicIcon);
            categoryBadge.appendChild(categoryText);
            categoryBadgeWrapper.appendChild(categoryBadge);
            imageDiv.appendChild(categoryBadgeWrapper);

            // Info overlay on hover
            const infoOverlay = document.createElement('div');
            infoOverlay.className = 'absolute inset-x-0 bottom-0 p-4 text-white transform translate-y-full group-hover:translate-y-0 transition-transform duration-300';

            const descriptionP = createElementWithText('p', evento.description || 'Evento cultural en Puno', 'text-sm font-medium line-clamp-2 text-shadow-strong');
            infoOverlay.appendChild(descriptionP);
            imageDiv.appendChild(infoOverlay);

            // Contenido de la tarjeta
            const contentDiv = document.createElement('div');
            contentDiv.className = 'p-5 bg-gradient-to-br from-white to-slate-50';

            // Título
            const title = createElementWithText('h3', evento.title || 'Sin título', 'text-xl font-bold mb-3 leading-tight text-slate-800 group-hover:text-primary transition-colors line-clamp-2');
            contentDiv.appendChild(title);

            // Ubicación
            const locationDiv = document.createElement('div');
            locationDiv.className = 'flex items-center gap-2 text-slate-500 text-sm mb-4 p-3 bg-white rounded-lg border border-slate-100';

            const mapIcon = document.createElement('i');
            mapIcon.setAttribute('data-lucide', 'map-pin');
            mapIcon.className = 'w-4 h-4 text-red-500 flex-shrink-0';

            const locationSpan = createElementWithText('span', evento.address || 'Puno, Perú', 'truncate font-medium');

            locationDiv.appendChild(mapIcon);
            locationDiv.appendChild(locationSpan);
            contentDiv.appendChild(locationDiv);

            // Botón (sin onclick inline - usamos event delegation)
            const button = document.createElement('button');
            button.className = 'btn btn-primary w-full shadow-lg hover:shadow-xl transform hover:scale-105 transition-all';
            button.dataset.eventoId = evento.id;
            button.dataset.action = 'attend';

            const buttonIcon = document.createElement('i');
            buttonIcon.setAttribute('data-lucide', 'calendar-check');
            buttonIcon.className = 'w-4 h-4';

            const buttonText = document.createTextNode(' Confirmar Asistencia');

            button.appendChild(buttonIcon);
            button.appendChild(buttonText);
            contentDiv.appendChild(button);

            // Efecto shimmer
            const shimmer = document.createElement('div');
            shimmer.className = 'absolute inset-0 shimmer opacity-0 group-hover:opacity-100 pointer-events-none';

            // Ensamblar tarjeta
            article.appendChild(imageDiv);
            article.appendChild(contentDiv);
            article.appendChild(shimmer);

            return article;
        }

        /**
         * Event delegation para manejar clicks (evita onclick inline)
         */
        function handleEventClick(e) {
            const button = e.target.closest('[data-action="attend"]');
            if (button) {
                const eventoId = button.dataset.eventoId;
                handleAttend(eventoId);
            }
        }

        async function handleAttend(eventoId) {
            if (!isAuthenticated) {
                window.location.href = `/login?redirect=${encodeURIComponent(window.location.pathname)}`;
                return;
            }

            try {
                const response = await axios.post('/api/event-attendances', {
                    evento_id: parseInt(eventoId, 10),
                    status: 'confirmed',
                    guests: 1
                });

                if (response.data.success) {
                    alert('¡Asistencia confirmada! ' + response.data.message);
                }
            } catch (error) {
                console.error('Error al confirmar asistencia:', error);
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
