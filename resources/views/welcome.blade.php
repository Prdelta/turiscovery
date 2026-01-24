@extends('layouts.app')

@section('title', 'Descubre Puno')

@section('content')

    <!-- Success Message -->
    @if (session('success'))
        <div class="fixed top-20 right-4 z-50 animate-[slideIn_0.3s_ease-out] max-w-md">
            <div class="bg-white border-l-4 rounded-lg shadow-xl p-4 flex items-start gap-3" style="border-left-color: var(--color-success);">
                <div class="w-10 h-10 rounded-full flex items-center justify-center flex-shrink-0" style="background-color: var(--color-success);">
                    <i data-lucide="check-circle" class="w-6 h-6 text-white"></i>
                </div>
                <div class="flex-1">
                    <p class="font-semibold" style="color: var(--color-text);">¡Éxito!</p>
                    <p class="text-sm" style="color: var(--color-text-light);">{{ session('success') }}</p>
                </div>
                <button onclick="this.parentElement.parentElement.remove()" class="text-slate-400 hover:text-slate-600">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>
        </div>
        <script>
            // Initialize icons in the alert
            setTimeout(() => lucide.createIcons(), 10);

            // Auto-dismiss after 5 seconds
            setTimeout(() => {
                const alert = document.querySelector('.fixed.top-20');
                if (alert) {
                    alert.style.animation = 'slideIn 0.3s ease-out reverse';
                    setTimeout(() => alert.remove(), 300);
                }
            }, 5000);
        </script>
    @endif

    <section class="hero relative flex items-center justify-center min-h-[600px] overflow-hidden"
        style="background-color: #000 !important;">

        <div id="hero-carousel" class="absolute inset-0 w-full h-full z-0">
            <img src="{{ asset('images/lake_titicaca.png') }}" alt="Lago Titicaca"
                class="carousel-slide absolute inset-0 w-full h-full object-cover transition-opacity duration-1000 ease-in-out opacity-100">

            <img src="{{ asset('images/puno_cathedral.png') }}" alt="Catedral"
                class="carousel-slide absolute inset-0 w-full h-full object-cover transition-opacity duration-1000 ease-in-out opacity-0">

            <img src="{{ asset('images/puno_dancers.png') }}" alt="Danzantes"
                class="carousel-slide absolute inset-0 w-full h-full object-cover transition-opacity duration-1000 ease-in-out opacity-0">

            <img src="{{ asset('images/traditional_textiles.png') }}" alt="Textiles"
                class="carousel-slide absolute inset-0 w-full h-full object-cover transition-opacity duration-1000 ease-in-out opacity-0">

            <div class="absolute inset-0 bg-black/60 z-10"></div>
        </div>

        <div class="container relative z-20 text-center text-white px-4">
            <span class="badge bg-white/20 backdrop-blur-md border-2 border-white/30 text-white font-bold px-6 py-2 mb-6 inline-flex items-center gap-2">
                <i data-lucide="award" class="w-4 h-4"></i>
                Patrimonio Cultural de la Humanidad UNESCO
            </span>

            <h1 class="fade-in text-white text-4xl md:text-6xl lg:text-7xl font-black mb-6 leading-tight text-shadow-strong">
                Descubre la Magia de Puno
            </h1>

            <p class="fade-in text-white/95 text-base md:text-lg lg:text-xl max-w-3xl mx-auto mb-10 leading-relaxed text-shadow font-medium" style="animation-delay: 0.2s;">
                Vive la Festividad de la Candelaria, explora experiencias únicas y descubre los mejores eventos y
                promociones del altiplano peruano.
            </p>

            <div class="fade-in"
                style="animation-delay: 0.4s; display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap;">
                <a href="/experiencias" class="btn btn-secondary">
                    <i data-lucide="compass"></i>
                    Explorar Experiencias
                </a>
                <a href="/candelaria" class="btn btn-outline hover:bg-white hover:text-black transition-colors"
                    style="color: white; border-color: white;">
                    <i data-lucide="sparkles"></i>
                    Conocer la Candelaria
                </a>
            </div>
        </div>
    </section>

    <div class="container mx-auto px-4 relative z-10" style="margin-top: -4rem;">
        <div class="bg-white rounded-2xl shadow-2xl border-2 border-white/50 overflow-hidden fade-in">
            <div class="grid grid-cols-2 lg:grid-cols-4 divide-x divide-slate-100">
                <div class="p-6 md:p-8 text-center bg-gradient-to-br from-white to-blue-50 hover:from-blue-50 hover:to-blue-100 transition-all group">
                    <div class="w-12 h-12 mx-auto mb-3 bg-gradient-to-br from-blue-500 to-purple-600 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform">
                        <i data-lucide="users" class="w-6 h-6 text-white"></i>
                    </div>
                    <div class="text-3xl md:text-4xl font-black bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent mb-2">50K+</div>
                    <div class="text-xs md:text-sm text-slate-600 font-semibold uppercase tracking-wide">Visitantes Anuales</div>
                </div>
                <div class="p-6 md:p-8 text-center bg-gradient-to-br from-white to-green-50 hover:from-green-50 hover:to-green-100 transition-all group">
                    <div class="w-12 h-12 mx-auto mb-3 bg-gradient-to-br from-green-500 to-emerald-600 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform">
                        <i data-lucide="compass" class="w-6 h-6 text-white"></i>
                    </div>
                    <div class="text-3xl md:text-4xl font-black bg-gradient-to-r from-green-600 to-emerald-600 bg-clip-text text-transparent mb-2">200+</div>
                    <div class="text-xs md:text-sm text-slate-600 font-semibold uppercase tracking-wide">Experiencias</div>
                </div>
                <div class="p-6 md:p-8 text-center bg-gradient-to-br from-white to-orange-50 hover:from-orange-50 hover:to-orange-100 transition-all group">
                    <div class="w-12 h-12 mx-auto mb-3 bg-gradient-to-br from-orange-500 to-red-600 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform">
                        <i data-lucide="store" class="w-6 h-6 text-white"></i>
                    </div>
                    <div class="text-3xl md:text-4xl font-black bg-gradient-to-r from-orange-600 to-red-600 bg-clip-text text-transparent mb-2">150+</div>
                    <div class="text-xs md:text-sm text-slate-600 font-semibold uppercase tracking-wide">Negocios</div>
                </div>
                <div class="p-6 md:p-8 text-center bg-gradient-to-br from-white to-yellow-50 hover:from-yellow-50 hover:to-yellow-100 transition-all group">
                    <div class="w-12 h-12 mx-auto mb-3 bg-gradient-to-br from-yellow-500 to-orange-600 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform">
                        <i data-lucide="star" class="w-6 h-6 text-white"></i>
                    </div>
                    <div class="text-3xl md:text-4xl font-black bg-gradient-to-r from-yellow-600 to-orange-600 bg-clip-text text-transparent mb-2">4.9★</div>
                    <div class="text-xs md:text-sm text-slate-600 font-semibold uppercase tracking-wide">Rating Promedio</div>
                </div>
            </div>
        </div>
    </div>

    <section class="section" style="background: var(--color-bg-light);">
        <div class="container mx-auto px-4">
            <div class="text-center mb-16">
                <span class="badge badge-info mb-4 inline-flex items-center gap-2 px-6 py-2 text-sm font-bold">
                    <i data-lucide="sparkles" class="w-4 h-4"></i>
                    EXPLORA TURISCOVERY
                </span>
                <h2 class="text-3xl md:text-4xl font-bold mb-4" style="color: var(--color-text);">Los 4 Pilares del Turismo</h2>
                <p class="max-w-4xl mx-auto text-lg leading-relaxed" style="color: var(--color-text-light);">
                    Todo lo que necesitas para vivir la mejor experiencia turística en Puno organizado para ti.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-12">
                <a href="/candelaria" class="group card card-hover p-8 text-center border-2 hover:shadow-xl transition-all" style="border-color: var(--color-primary-light); background: white;">
                    <div class="w-20 h-20 mx-auto mb-6 rounded-2xl flex items-center justify-center transform group-hover:scale-110 transition-transform shadow-lg" style="background: var(--color-primary);">
                        <i data-lucide="sparkles" class="w-10 h-10 text-white"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-3 group-hover:opacity-80 transition-opacity" style="color: var(--color-text);">La Candelaria</h3>
                    <p class="text-sm leading-relaxed" style="color: var(--color-text-light);">La festividad más grande del Perú, patrimonio cultural inmaterial.</p>
                </a>

                <a href="/experiencias" class="group card card-hover p-8 text-center border-2 hover:shadow-xl transition-all" style="border-color: var(--color-secondary); background: white;">
                    <div class="w-20 h-20 mx-auto mb-6 rounded-2xl flex items-center justify-center transform group-hover:scale-110 transition-transform shadow-lg" style="background: var(--color-secondary);">
                        <i data-lucide="compass" class="w-10 h-10 text-white"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-3 group-hover:opacity-80 transition-opacity" style="color: var(--color-text);">Experiencias</h3>
                    <p class="text-sm leading-relaxed" style="color: var(--color-text-light);">Tours únicos al Lago Titicaca, Sillustani y aventuras inolvidables.</p>
                </a>

                <a href="/eventos" class="group card card-hover p-8 text-center border-2 hover:shadow-xl transition-all" style="border-color: var(--color-info-dark); background: white;">
                    <div class="w-20 h-20 mx-auto mb-6 rounded-2xl flex items-center justify-center transform group-hover:scale-110 transition-transform shadow-lg" style="background: var(--color-info-dark);">
                        <i data-lucide="calendar" class="w-10 h-10 text-white"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-3 group-hover:opacity-80 transition-opacity" style="color: var(--color-text);">Eventos</h3>
                    <p class="text-sm leading-relaxed" style="color: var(--color-text-light);">Festivales, conciertos y eventos culturales que celebran la tradición.</p>
                </a>

                <a href="/promociones" class="group card card-hover p-8 text-center border-2 hover:shadow-xl transition-all" style="border-color: var(--color-accent); background: white;">
                    <div class="w-20 h-20 mx-auto mb-6 rounded-2xl flex items-center justify-center transform group-hover:scale-110 transition-transform shadow-lg" style="background: var(--color-accent);">
                        <i data-lucide="percent" class="w-10 h-10 text-white"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-3 group-hover:opacity-80 transition-opacity" style="color: var(--color-text);">Promociones</h3>
                    <p class="text-sm leading-relaxed" style="color: var(--color-text-light);">Las mejores ofertas y descuentos en restaurantes y hoteles.</p>
                </a>
            </div>

            <div class="card mt-8 border-0" style="background: linear-gradient(135deg, var(--color-primary-dark) 0%, var(--color-primary) 100%);">
                <div class="flex flex-col md:flex-row items-center justify-between gap-6 p-8 md:p-12">
                    <div class="flex-1 text-center md:text-left">
                        <span class="inline-flex items-center gap-2 bg-white/20 backdrop-blur-sm text-white px-4 py-2 rounded-full font-bold text-sm mb-4 border border-white/30">
                            <i data-lucide="sparkles" class="w-4 h-4"></i>
                            DESCUBRE LOCALES
                        </span>
                        <h3 class="text-2xl md:text-3xl font-bold text-white mb-4">
                            +150 Negocios Asociados
                        </h3>
                        <p class="text-white/95 leading-relaxed text-base md:text-lg" style="max-width: 600px;">
                            Encuentra los mejores restaurantes, hoteles, artesanías y servicios turísticos verificados y seguros en Puno.
                        </p>
                    </div>
                    <div>
                        <a href="/locales" class="btn btn-secondary font-bold px-8 py-4 shadow-xl hover:shadow-2xl transform hover:scale-105 transition-all">
                            <i data-lucide="store" class="w-5 h-5"></i>
                            Ver Directorio
                            <i data-lucide="arrow-right" class="w-5 h-5"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="section" style="background: var(--color-bg-light);">
        <div class="container mx-auto px-4">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-end gap-4 mb-12">
                <div>
                    <span class="inline-flex items-center gap-2 badge badge-warning mb-3 px-5 py-2 text-sm font-bold">
                        <i data-lucide="calendar-heart" class="w-4 h-4"></i>
                        PRÓXIMOS EVENTOS
                    </span>
                    <h2 class="text-3xl md:text-4xl font-bold mb-2" style="color: var(--color-text);">No te los pierdas</h2>
                    <p style="color: var(--color-text-light);">Los eventos más esperados de Puno</p>
                </div>
                <a href="/eventos" class="btn btn-outline gap-2 items-center hover:bg-blue-600 hover:text-white hover:border-blue-600 transition-all">
                    Ver calendario completo
                    <i data-lucide="arrow-right" class="w-4 h-4"></i>
                </a>
            </div>

            <div id="featured-events" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <x-loading-state message="Cargando eventos destacados..." />
            </div>
        </div>
    </section>

    <section class="section text-center" style="background: linear-gradient(135deg, var(--color-secondary-dark) 0%, var(--color-secondary) 100%);">
        <div class="container mx-auto px-4">
            <div class="max-w-5xl mx-auto text-white">
                <span class="inline-flex items-center gap-2 bg-white/20 backdrop-blur-md border-2 border-white/30 text-white px-6 py-2 rounded-full font-bold text-sm mb-6">
                    <i data-lucide="briefcase" class="w-4 h-4"></i>
                    PARA NEGOCIOS
                </span>

                <h2 class="text-3xl md:text-5xl font-bold mb-6 text-white leading-tight">
                    ¿Tienes un negocio en Puno?
                </h2>

                <p class="text-lg md:text-xl mb-10 text-white/95 max-w-3xl mx-auto leading-relaxed">
                    Únete a Turiscovery y conecta con miles de turistas. Publica tus promociones, eventos y experiencias de forma totalmente gratuita.
                </p>

                <!-- Features rápidos -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10 max-w-4xl mx-auto">
                    <div class="bg-white/10 backdrop-blur-md rounded-xl p-6 border border-white/20">
                        <i data-lucide="trending-up" class="w-10 h-10 mx-auto mb-3 text-white"></i>
                        <div class="font-bold text-base">Aumenta Ventas</div>
                    </div>
                    <div class="bg-white/10 backdrop-blur-md rounded-xl p-6 border border-white/20">
                        <i data-lucide="users" class="w-10 h-10 mx-auto mb-3 text-white"></i>
                        <div class="font-bold text-base">Más Clientes</div>
                    </div>
                    <div class="bg-white/10 backdrop-blur-md rounded-xl p-6 border border-white/20">
                        <i data-lucide="shield-check" class="w-10 h-10 mx-auto mb-3 text-white"></i>
                        <div class="font-bold text-base">100% Gratis</div>
                    </div>
                </div>

                <div class="flex gap-4 justify-center flex-wrap">
                    <a href="/register" class="btn bg-white hover:bg-slate-100 font-bold px-10 py-5 text-lg shadow-2xl transform hover:scale-105 transition-all" style="color: var(--color-secondary-dark);">
                        <i data-lucide="rocket" class="w-6 h-6"></i>
                        Registrar mi Negocio
                    </a>
                    <a href="#" class="btn bg-white/10 backdrop-blur-md border-2 border-white/40 text-white hover:bg-white/20 font-bold px-10 py-5 text-lg shadow-xl transform hover:scale-105 transition-all">
                        <i data-lucide="info" class="w-6 h-6"></i>
                        Más Información
                    </a>
                </div>
            </div>
        </div>
    </section>

@endsection

@push('scripts')
    <script>
        // Script del Carrusel
        document.addEventListener('DOMContentLoaded', () => {
            const slides = document.querySelectorAll('.carousel-slide');

            // Si no hay slides, no hacemos nada para evitar errores
            if (slides.length === 0) return;

            let currentSlide = 0;
            const slideInterval = 5000; // Cambia cada 5 segundos

            setInterval(() => {
                // Ocultar slide actual
                slides[currentSlide].classList.remove('opacity-100');
                slides[currentSlide].classList.add('opacity-0');

                // Calcular siguiente slide
                currentSlide = (currentSlide + 1) % slides.length;

                // Mostrar siguiente slide
                slides[currentSlide].classList.remove('opacity-0');
                slides[currentSlide].classList.add('opacity-100');
            }, slideInterval);
        });

        // Script de Carga de Eventos (Axios)
        document.addEventListener('DOMContentLoaded', async () => {
            try {
                const container = document.getElementById('featured-events');
                if (!container) return;

                // Use per_page instead of limit to match Laravel pagination
                const response = await axios.get('/api/eventos?per_page=3');

                // Handle pagination structure (response.data.data.data) or simple array (response.data.data)
                const events = response.data.data.data ? response.data.data.data : response.data.data;

                if (response.data.success && Array.isArray(events) && events.length > 0) {
                    // Remove duplicates based on ID
                    const uniqueEvents = events.filter((evento, index, self) =>
                        index === self.findIndex((e) => e.id === evento.id)
                    );

                    container.innerHTML = uniqueEvents.slice(0, 3).map(evento => {
                        // Parse date correctly - try start_time first, then start_date
                        const eventDate = evento.start_time || evento.start_date;
                        let dateDisplay = 'Fecha por confirmar';

                        if (eventDate) {
                            try {
                                const date = new Date(eventDate);
                                if (!isNaN(date.getTime())) {
                                    dateDisplay = date.toLocaleDateString('es-ES', {
                                        day: 'numeric',
                                        month: 'short',
                                        year: 'numeric'
                                    });
                                }
                            } catch (e) {
                                console.error('Error parsing date:', e);
                            }
                        }

                        return `
                        <article class="card card-hover group overflow-hidden">
                            <div class="h-48 overflow-hidden relative">
                                <img src="${evento.image_url || 'https://via.placeholder.com/400x240?text=Evento'}"
                                     alt="${evento.title}"
                                     class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                                <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>

                                <!-- Badge de fecha -->
                                <div class="absolute top-4 left-4">
                                    <div class="bg-white/95 backdrop-blur-sm px-4 py-2 rounded-lg shadow-lg">
                                        <i data-lucide="calendar" class="w-4 h-4 inline text-blue-600 mr-1"></i>
                                        <span class="font-bold text-slate-800 text-sm">${dateDisplay}</span>
                                    </div>
                                </div>

                                ${evento.category ? `
                                <div class="absolute top-4 right-4">
                                    <span class="badge badge-primary text-xs">${evento.category}</span>
                                </div>
                                ` : ''}
                            </div>

                            <div class="p-6 bg-white">
                                <h3 class="text-lg font-bold mb-3 text-slate-800 group-hover:text-primary transition-colors line-clamp-2 min-h-[56px]">${evento.title}</h3>
                                <p class="text-slate-600 text-sm mb-4 line-clamp-2 leading-relaxed">${evento.description || 'Evento cultural en Puno'}</p>

                                <div class="flex items-start gap-2 text-slate-500 text-sm p-3 bg-slate-50 rounded-lg">
                                    <i data-lucide="map-pin" class="w-4 h-4 text-red-500 flex-shrink-0 mt-0.5"></i>
                                    <span class="line-clamp-2 font-medium">${evento.address || 'Puno, Perú'}</span>
                                </div>

                                <a href="/eventos" class="btn btn-outline w-full mt-4 text-sm">
                                    <i data-lucide="info" class="w-4 h-4"></i>
                                    Ver Detalles
                                </a>
                            </div>
                        </article>
                    `;
                    }).join('');
                    lucide.createIcons();
                } else {
                    container.innerHTML = `
                    <div class="card p-5 text-center" style="grid-column: 1 / -1;">
                        <i data-lucide="calendar" style="width: 48px; height: 48px; margin-bottom: 1rem; color: var(--color-gray);"></i>
                        <h3>No hay eventos próximos</h3>
                    </div>
                `;
                    lucide.createIcons();
                }
            } catch (error) {
                console.error('Error loading events:', error);
                const container = document.getElementById('featured-events');
                if (container) {
                    container.innerHTML = `
                        <div class="card p-5 text-center" style="grid-column: 1 / -1; color: #ef4444;">
                            <i data-lucide="alert-circle" style="width: 48px; height: 48px; margin: 0 auto 1rem;"></i>
                            <h3 class="font-bold">Error al cargar eventos</h3>
                            <p class="text-sm">Hubo un problema de conexión. Por favor, intenta recargar la página.</p>
                        </div>
                    `;
                    lucide.createIcons();
                }
            }
        });
    </script>
@endpush
