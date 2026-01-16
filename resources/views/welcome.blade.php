@extends('layouts.app')

@section('title', 'Descubre Puno')

@section('content')

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
            <span class="badge badge-primary md:inline-flex hidden"
                style="background: rgba(255,255,255,0.2); backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.3); margin-bottom: var(--spacing-md);">
                Patrimonio Cultural de la Humanidad UNESCO
            </span>

            <h1 class="fade-in text-5xl md:text-7xl font-bold mb-6">
                Descubre la Magia de Puno
            </h1>

            <p class="fade-in text-lg md:text-xl max-w-2xl mx-auto mb-8" style="animation-delay: 0.2s;">
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

    <div class="container" style="margin-top: -3rem; position: relative; z-index: 10;">
        <div class="card grid grid-4 fade-in"
            style="padding: var(--spacing-lg); text-align: center; border: none; box-shadow: var(--shadow-xl);">
            <div>
                <strong style="font-size: 2rem; color: var(--color-primary); display: block;">50K+</strong>
                <span class="text-secondary text-sm">Visitantes anuales</span>
            </div>
            <div>
                <strong style="font-size: 2rem; color: var(--color-primary); display: block;">200+</strong>
                <span class="text-secondary text-sm">Experiencias</span>
            </div>
            <div>
                <strong style="font-size: 2rem; color: var(--color-primary); display: block;">150+</strong>
                <span class="text-secondary text-sm">Negocios</span>
            </div>
            <div>
                <strong style="font-size: 2rem; color: var(--color-primary); display: block;">4.9</strong>
                <span class="text-secondary text-sm">Rating promedio</span>
            </div>
        </div>
    </div>

    <section class="section">
        <div class="container">
            <div class="text-center">
                <span class="badge badge-info mb-2">EXPLORA TURISCOVERY</span>
                <h2>Los 4 Pilares del Turismo</h2>
                <p style="max-width: 600px; margin: 0 auto;">Todo lo que necesitas para vivir la mejor experiencia turística
                    en Puno organizado para ti.</p>
            </div>

            <div class="pillars">
                <a href="/candelaria" class="pillar-card">
                    <i data-lucide="sparkles" class="pillar-icon"></i>
                    <h3>La Candelaria</h3>
                    <p>La festividad más grande del Perú, patrimonio cultural inmaterial.</p>
                </a>

                <a href="/experiencias" class="pillar-card">
                    <i data-lucide="compass" class="pillar-icon" style="color: var(--color-secondary);"></i>
                    <h3>Experiencias</h3>
                    <p>Tours únicos al Lago Titicaca, Sillustani y aventuras inolvidables.</p>
                </a>

                <a href="/eventos" class="pillar-card">
                    <i data-lucide="calendar" class="pillar-icon" style="color: var(--color-info-dark);"></i>
                    <h3>Eventos</h3>
                    <p>Festivales, conciertos y eventos culturales que celebran la tradición.</p>
                </a>

                <a href="/promociones" class="pillar-card">
                    <i data-lucide="percent" class="pillar-icon" style="color: var(--color-accent);"></i>
                    <h3>Promociones</h3>
                    <p>Las mejores ofertas y descuentos en restaurantes y hoteles.</p>
                </a>
            </div>

            <div class="card mt-4"
                style="background: linear-gradient(135deg, #2c3e50 0%, #1a252f 100%); color: white; border: none;">
                <div
                    style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 2rem; padding: var(--spacing-xl);">
                    <div style="flex: 1; min-width: 300px;">
                        <span class="badge badge-primary mb-2">DESCUBRE LOCALES</span>
                        <h3 style="color: white; margin-bottom: 1rem;">+150 Negocios Asociados</h3>
                        <p style="color: rgba(255,255,255,0.7); margin-bottom: 0;">Encuentra los mejores restaurantes,
                            hoteles, artesanías y servicios turísticos verificados y seguros.</p>
                    </div>
                    <div>
                        <a href="/locales" class="btn btn-primary">
                            Ver Directorio
                            <i data-lucide="arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="section" style="background: var(--color-bg-light);">
        <div class="container">
            <div
                style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: var(--spacing-xl); flex-wrap: wrap; gap: 1rem;">
                <div>
                    <span class="badge badge-warning mb-2">PRÓXIMOS EVENTOS</span>
                    <h2 class="mb-0">No te los pierdas</h2>
                </div>
                <a href="/eventos" class="btn btn-outline">
                    Ver calendario completo
                    <i data-lucide="arrow-right"></i>
                </a>
            </div>

            <div id="featured-events" class="grid grid-3">
                <div class="card p-4 text-center">
                    <i data-lucide="loader" class="animate-spin text-secondary"></i>
                    <p>Cargando eventos...</p>
                </div>
            </div>
        </div>
    </section>

    <section class="section" style="text-align: center;">
        <div class="container">
            <div style="max-width: 800px; margin: 0 auto;">
                <span class="badge badge-info mb-2">PARA NEGOCIOS</span>
                <h2>¿Tienes un negocio en Puno?</h2>
                <p class="mb-4">Únete a Turiscovery y conecta con miles de turistas. Publica tus promociones, eventos y
                    experiencias.</p>

                <div style="display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap;">
                    <a href="/register" class="btn btn-primary">
                        <i data-lucide="store"></i>
                        Registrar mi Negocio
                    </a>
                    <a href="#" class="btn btn-outline">
                        <i data-lucide="info"></i>
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

                const response = await axios.get('/api/eventos?limit=3');

                if (response.data.success && response.data.data.length > 0) {
                    container.innerHTML = response.data.data.map(evento => `
                    <article class="card">
                        <img src="${evento.image_url || 'https://via.placeholder.com/400x240'}" alt="${evento.title}" class="card-image">
                        <div style="padding: var(--spacing-md);">
                            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: var(--spacing-sm);">
                                <span class="badge badge-info">${new Date(evento.start_date).toLocaleDateString()}</span>
                            </div>
                            <h3>${evento.title}</h3>
                            <p style="margin-bottom: var(--spacing-md); display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">${evento.description || ''}</p>
                            <div style="display: flex; align-items: center; gap: 0.5rem; color: var(--color-text-light);">
                                <i data-lucide="map-pin" style="width: 16px;"></i>
                                <small>${evento.location || 'Puno'}</small>
                            </div>
                        </div>
                    </article>
                `).join('');
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
