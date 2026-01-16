@extends('layouts.app')

@section('title', 'Turiscovery - Descubre Puno')

@section('content')
    <!-- Hero Section with Image Carousel -->
    <div style="position: relative; height: 600px; overflow: hidden; background: #000;">
        <!-- Carousel Container -->
        <div id="carousel" style="position: relative; width: 100%; height: 100%;">
            <!-- Slide 1 -->
            <div class="carousel-slide active" style="background-image: url('/images/lake_titicaca.png');">
                <div class="carousel-caption">
                    <h1>Descubre el Lago Titicaca</h1>
                    <p>El lago navegable más alto del mundo</p>
                </div>
            </div>

            <!-- Slide 2 -->
            <div class="carousel-slide" style="background-image: url('/images/puno_dancers.png');">
                <div class="carousel-caption">
                    <h1>Festividad de la Candelaria</h1>
                    <p>Patrimonio Cultural Inmaterial de la Humanidad</p>
                </div>
            </div>

            <!-- Slide 3 -->
            <div class="carousel-slide" style="background-image: url('/images/puno_cathedral.png');">
                <div class="carousel-caption">
                    <h1>Historia y Arquitectura</h1>
                    <p>Descubre el patrimonio colonial de Puno</p>
                </div>
            </div>

            <!-- Slide 4 -->
            <div class="carousel-slide" style="background-image: url('/images/traditional_textiles.png');">
                <div class="carousel-caption">
                    <h1>Artesanías Tradicionales</h1>
                    <p>Textiles andinos hechos a mano</p>
                </div>
            </div>
        </div>

        <!-- Navigation Arrows -->
        <button onclick="prevSlide()"
            style="position: absolute; left: 2rem; top: 50%; transform: translateY(-50%); background: rgba(255,255,255,0.3); border: none; color: white; font-size: 2rem; width: 50px; height: 50px; border-radius: 50%; cursor: pointer; backdrop-filter: blur(10px); transition: all 0.3s; z-index: 10;"
            onmouseover="this.style.background='rgba(255,255,255,0.5)'"
            onmouseout="this.style.background='rgba(255,255,255,0.3)'">‹</button>
        <button onclick="nextSlide()"
            style="position: absolute; right: 2rem; top: 50%; transform: translateY(-50%); background: rgba(255,255,255,0.3); border: none; color: white; font-size: 2rem; width: 50px; height: 50px; border-radius: 50%; cursor: pointer; backdrop-filter: blur(10px); transition: all 0.3s; z-index: 10;"
            onmouseover="this.style.background='rgba(255,255,255,0.5)'"
            onmouseout="this.style.background='rgba(255,255,255,0.3)'">›</button>

        <!-- Dots Navigation -->
        <div
            style="position: absolute; bottom: 2rem; left: 50%; transform: translateX(-50%); display: flex; gap: 0.75rem; z-index: 10;">
            <button onclick="goToSlide(0)" class="carousel-dot active"></button>
            <button onclick="goToSlide(1)" class="carousel-dot"></button>
            <button onclick="goToSlide(2)" class="carousel-dot"></button>
            <button onclick="goToSlide(3)" class="carousel-dot"></button>
        </div>
    </div>

    <!-- Los 4 Pilares -->
    <div class="container" style="margin-top: 4rem;">
        <h2 class="text-center mb-4">Los 4 Pilares de Turiscovery</h2>

        <div class="pillars">
            <a href="/candelaria" class="pillar-card" style="text-decoration: none; color: inherit;">
                <div class="pillar-icon">🎭</div>
                <h3 style="color: var(--primary);">Candelaria</h3>
                <p class="text-secondary">Patrimonio Cultural de la Humanidad. Descubre las danzas, trajes y tradiciones de
                    la festividad más importante de Puno.</p>
            </a>

            <a href="/experiencias" class="pillar-card" style="text-decoration: none; color: inherit;">
                <div class="pillar-icon">🚣</div>
                <h3 style="color: var(--primary);">Experiencias</h3>
                <p class="text-secondary">Turismo vivencial en las islas flotantes, trekking, kayak y aventuras únicas en el
                    lago navegable más alto del mundo.</p>
            </a>

            <a href="/eventos" class="pillar-card" style="text-decoration: none; color: inherit;">
                <div class="pillar-icon">🎪</div>
                <h3 style="color: var(--primary);">Eventos</h3>
                <p class="text-secondary">Conciertos, festivales culturales, vida nocturna y agenda completa de eventos en
                    Puno.</p>
            </a>

            <a href="/promociones" class="pillar-card" style="text-decoration: none; color: inherit;">
                <div class="pillar-icon">💰</div>
                <h3 style="color: var(--primary);">Promociones</h3>
                <p class="text-secondary">Ofertas exclusivas, descuentos 2x1 y promociones especiales de nuestros socios
                    locales.</p>
            </a>
        </div>
    </div>

    <!-- Featured Content -->
    <div class="container" style="margin-top: 4rem;">
        <h2 class="text-center mb-4">Contenido Destacado</h2>

        <div class="grid grid-3" id="featured-content">
            <!-- Content will be loaded dynamically -->
            <div class="card" style="text-align: center; padding: 3rem;">
                <p class="text-secondary">Cargando contenido destacado...</p>
            </div>
        </div>
    </div>

    <!-- Call to Action -->
    <div class="container"
        style="margin: 4rem auto; text-align: center; background: linear-gradient(135deg, var(--primary), var(--primary-dark)); color: white; padding: 3rem; border-radius: 1rem;">
        <h2 style="color: white;">¿Eres un Empresario Local?</h2>
        <p style="font-size: 1.125rem; opacity: 0.9; margin-bottom: 2rem;">Únete a Turiscovery y promociona tu negocio</p>
        <a href="/register" class="btn" style="background: white; color: var(--primary);">Registrarse como Socio</a>
    </div>
@endsection

@push('styles')
    <style>
        .carousel-slide {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-size: cover;
            background-position: center;
            opacity: 0;
            transition: opacity 1s ease-in-out;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .carousel-slide.active {
            opacity: 1;
            z-index: 1;
        }

        .carousel-caption {
            text-align: center;
            color: white;
            text-shadow: 2px 2px 8px rgba(0, 0, 0, 0.8);
            background: rgba(0, 0, 0, 0.3);
            padding: 3rem;
            border-radius: 1rem;
            backdrop-filter: blur(10px);
            animation: fadeInUp 0.8s ease-out;
        }

        .carousel-caption h1 {
            font-size: 3.5rem;
            margin: 0 0 1rem 0;
            font-weight: 800;
        }

        .carousel-caption p {
            font-size: 1.5rem;
            margin: 0;
            opacity: 0.95;
        }

        .carousel-dot {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            border: 2px solid white;
            background: transparent;
            cursor: pointer;
            transition: all 0.3s;
            padding: 0;
        }

        .carousel-dot.active {
            background: white;
            transform: scale(1.2);
        }

        .carousel-dot:hover {
            transform: scale(1.3);
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
@endpush

@push('scripts')
    <script>
        let currentSlide = 0;
        const slides = document.querySelectorAll('.carousel-slide');
        const dots = document.querySelectorAll('.carousel-dot');
        let autoplayInterval;

        function showSlide(index) {
            // Remove active class from all slides and dots
            slides.forEach(slide => slide.classList.remove('active'));
            dots.forEach(dot => dot.classList.remove('active'));

            // Add active class to current slide and dot
            slides[index].classList.add('active');
            dots[index].classList.add('active');

            currentSlide = index;
        }

        function nextSlide() {
            let next = (currentSlide + 1) % slides.length;
            showSlide(next);
            resetAutoplay();
        }

        function prevSlide() {
            let prev = (currentSlide - 1 + slides.length) % slides.length;
            showSlide(prev);
            resetAutoplay();
        }

        function goToSlide(index) {
            showSlide(index);
            resetAutoplay();
        }

        function startAutoplay() {
            autoplayInterval = setInterval(nextSlide, 5000); // Change slide every 5 seconds
        }

        function resetAutoplay() {
            clearInterval(autoplayInterval);
            startAutoplay();
        }

        // Start autoplay when page loads
        document.addEventListener('DOMContentLoaded', () => {
            startAutoplay();
        });

        // Pause autoplay on hover
        document.getElementById('carousel').addEventListener('mouseenter', () => {
            clearInterval(autoplayInterval);
        });

        document.getElementById('carousel').addEventListener('mouseleave', () => {
            startAutoplay();
        });
    </script>
@endpush

@push('scripts')
    <script>
        // Load featured content
        document.addEventListener('DOMContentLoaded', async () => {
            try {
                // Fetch some featured Candelaria content
                const response = await api.getCandelaria({
                    featured: true,
                    per_page: 3
                });

                if (response.success && response.data.data.length > 0) {
                    const container = document.getElementById('featured-content');
                    container.innerHTML = '';

                    response.data.data.forEach(item => {
                        const card = document.createElement('div');
                        card.className = 'card';
                        card.innerHTML = `
                    ${item.images && item.images[0] ? `<img src="${item.images[0]}" alt="${item.title}" class="card-image">` : ''}
                    <span class="badge badge-primary">Candelaria</span>
                    <h3 style="margin-top: 1rem;">${item.title}</h3>
                    <p class="text-secondary">${item.description.substring(0, 100)}...</p>
                    <a href="/candelaria/${item.id}" class="btn btn-outline" style="margin-top: 1rem;">Ver Más</a>
                `;
                        container.appendChild(card);
                    });
                }
            } catch (error) {
                console.error('Error loading featured content:', error);
            }
        });
    </script>
@endpush
