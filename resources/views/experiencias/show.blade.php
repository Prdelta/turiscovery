@extends('layouts.app')

@section('title', 'Detalle de Experiencia')

@section('content')
    <!-- Hero Section -->
    <div class="relative w-full h-[50vh] min-h-[400px]">
        <!-- Skeleton Loader for Hero -->
        <div id="hero-loader" class="absolute inset-0 bg-slate-200 animate-pulse flex items-center justify-center z-20">
            <i data-lucide="loader" class="w-12 h-12 text-slate-400 animate-spin"></i>
        </div>

        <!-- Hero Content -->
        <div id="hero-content" class="relative w-full h-full hidden">
            <img id="detail-image" src="" alt="Experiencia" class="w-full h-full object-cover">
            <div class="absolute inset-0 bg-gradient-to-t from-slate-900/90 via-slate-900/40 to-transparent"></div>

            <div class="absolute bottom-0 left-0 w-full p-6 md:p-12">
                <div class="container mx-auto px-4">
                    <x-ui.badge variant="primary" class="mb-4">TURISMO</x-ui.badge>
                    <h1 id="detail-title"
                        class="text-3xl md:text-5xl font-bold text-white mb-4 leading-tight drop-shadow-sm"></h1>

                    <div class="flex flex-wrap items-center gap-6 text-white/90 text-sm md:text-base font-medium">
                        <div class="flex items-center gap-2">
                            <i data-lucide="map-pin" class="w-5 h-5 text-blue-400"></i>
                            <span id="detail-location"></span>
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="flex text-yellow-400">
                                <i data-lucide="star" class="w-5 h-5 fill-current"></i>
                            </div>
                            <span>4.9 (120 Reseñas)</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container mx-auto px-4 py-8 md:py-12">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 xl:gap-12">

            <!-- Left Column: Details -->
            <div class="lg:col-span-2 space-y-10">

                <!-- Quick Info Cards -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <x-ui.card class="text-center p-4 hover:shadow-md border-slate-100" padding="p-4">
                        <div
                            class="bg-blue-50 w-10 h-10 rounded-full flex items-center justify-center mx-auto mb-2 text-blue-600">
                            <i data-lucide="clock" class="w-5 h-5"></i>
                        </div>
                        <span class="block text-xs text-slate-500 uppercase tracking-wider mb-1">Duración</span>
                        <span id="detail-duration" class="font-bold text-slate-800"></span>
                    </x-ui.card>

                    <x-ui.card class="text-center p-4 hover:shadow-md border-slate-100" padding="p-4">
                        <div
                            class="bg-emerald-50 w-10 h-10 rounded-full flex items-center justify-center mx-auto mb-2 text-emerald-600">
                            <i data-lucide="users" class="w-5 h-5"></i>
                        </div>
                        <span class="block text-xs text-slate-500 uppercase tracking-wider mb-1">Grupo</span>
                        <span class="font-bold text-slate-800">Max 10</span>
                    </x-ui.card>

                    <x-ui.card class="text-center p-4 hover:shadow-md border-slate-100" padding="p-4">
                        <div
                            class="bg-violet-50 w-10 h-10 rounded-full flex items-center justify-center mx-auto mb-2 text-violet-600">
                            <i data-lucide="languages" class="w-5 h-5"></i>
                        </div>
                        <span class="block text-xs text-slate-500 uppercase tracking-wider mb-1">Idiomas</span>
                        <span class="font-bold text-slate-800">Esp/Ing</span>
                    </x-ui.card>

                    <x-ui.card class="text-center p-4 hover:shadow-md border-slate-100" padding="p-4">
                        <div
                            class="bg-orange-50 w-10 h-10 rounded-full flex items-center justify-center mx-auto mb-2 text-orange-600">
                            <i data-lucide="calendar" class="w-5 h-5"></i>
                        </div>
                        <span class="block text-xs text-slate-500 uppercase tracking-wider mb-1">Tipo</span>
                        <span class="font-bold text-slate-800">Diario</span>
                    </x-ui.card>
                </div>

                <!-- Description -->
                <div>
                    <h2 class="text-2xl font-bold text-slate-800 mb-4 flex items-center gap-2">
                        <i data-lucide="info" class="text-blue-600 w-6 h-6"></i>
                        Acerca de la experiencia
                    </h2>
                    <p id="detail-description"
                        class="text-slate-600 leading-relaxed text-base whitespace-pre-line text-justify"></p>
                </div>

                <!-- History -->
                <div>
                    <h2 class="text-2xl font-bold text-slate-800 mb-4 flex items-center gap-2">
                        <i data-lucide="book-open" class="text-emerald-600 w-6 h-6"></i>
                        Historia del Lugar
                    </h2>
                    <div class="prose prose-slate max-w-none text-slate-600">
                        <p class="mb-4">
                            Este destino tiene una rica historia que se remonta a épocas preincas.
                            Las comunidades locales han preservado sus tradiciones, vestimenta y lengua a lo largo de los
                            siglos.
                        </p>
                        <p>
                            Según la leyenda, este lugar fue sagrado para las antiguas civilizaciones del Altiplano,
                            quienes veían en sus aguas y montañas la conexión con los dioses. Hoy, los visitantes pueden
                            sentir esa misma energía mística mientras exploran los senderos y comparten con los pobladores.
                        </p>
                    </div>
                </div>

                <!-- Gallery -->
                <div>
                    <h2 class="text-2xl font-bold text-slate-800 mb-4">Galería</h2>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                        <div class="aspect-square rounded-xl bg-slate-100 overflow-hidden relative group">
                            <img src="https://images.unsplash.com/photo-1555396273-367ea4eb4db5?auto=format&fit=crop&w=400&q=80"
                                class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
                        </div>
                        <div class="aspect-square rounded-xl bg-slate-100 overflow-hidden relative group">
                            <img src="https://images.unsplash.com/photo-1526392060635-9d6019884377?auto=format&fit=crop&w=400&q=80"
                                class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
                        </div>
                        <div class="aspect-square rounded-xl bg-slate-100 overflow-hidden relative group">
                            <img src="https://images.unsplash.com/photo-1587595431973-160d0d94add1?auto=format&fit=crop&w=400&q=80"
                                class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
                        </div>
                    </div>
                </div>

                <!-- Reviews -->
                <div>
                    <h2 class="text-2xl font-bold text-slate-800 mb-6 flex items-center gap-2">
                        <i data-lucide="message-square" class="text-violet-600 w-6 h-6"></i>
                        Reseñas de viajeros
                    </h2>
                    <div class="space-y-6">
                        <x-ui.card class="border-slate-100 bg-slate-50/50">
                            <div class="flex items-center gap-4 mb-3">
                                <div
                                    class="w-12 h-12 rounded-full bg-blue-100 flex items-center justify-center font-bold text-blue-700 text-lg">
                                    MA
                                </div>
                                <div>
                                    <h4 class="font-bold text-slate-800">María Alonso</h4>
                                    <div class="flex text-yellow-400 gap-0.5 mt-0.5">
                                        <i data-lucide="star" class="w-4 h-4 fill-current"></i>
                                        <i data-lucide="star" class="w-4 h-4 fill-current"></i>
                                        <i data-lucide="star" class="w-4 h-4 fill-current"></i>
                                        <i data-lucide="star" class="w-4 h-4 fill-current"></i>
                                        <i data-lucide="star" class="w-4 h-4 fill-current"></i>
                                    </div>
                                </div>
                                <span class="ml-auto text-xs text-slate-400 font-medium">Hace 2 días</span>
                            </div>
                            <p class="text-slate-600">Increíble experiencia, la cultura y la gente son maravillosas. Muy
                                recomendado para familias.</p>
                        </x-ui.card>

                        <x-ui.card class="border-slate-100 bg-slate-50/50">
                            <div class="flex items-center gap-4 mb-3">
                                <div
                                    class="w-12 h-12 rounded-full bg-emerald-100 flex items-center justify-center font-bold text-emerald-700 text-lg">
                                    JP
                                </div>
                                <div>
                                    <h4 class="font-bold text-slate-800">Juan Pérez</h4>
                                    <div class="flex text-yellow-400 gap-0.5 mt-0.5">
                                        <i data-lucide="star" class="w-4 h-4 fill-current"></i>
                                        <i data-lucide="star" class="w-4 h-4 fill-current"></i>
                                        <i data-lucide="star" class="w-4 h-4 fill-current"></i>
                                        <i data-lucide="star" class="w-4 h-4 fill-current"></i>
                                        <i data-lucide="star" class="w-4 h-4 text-slate-300"></i>
                                    </div>
                                </div>
                                <span class="ml-auto text-xs text-slate-400 font-medium">Hace 1 semana</span>
                            </div>
                            <p class="text-slate-600">El paisaje es hermoso, aunque la caminata cansa un poco. Vale la pena
                                traer buen calzado.</p>
                        </x-ui.card>
                    </div>
                </div>
            </div>

            <!-- Right Column: Booking Box -->
            <div class="lg:col-span-1">
                <div class="sticky top-24">
                    <x-ui.card class="shadow-xl border-slate-100 !p-6 md:!p-8">
                        <div class="flex justify-between items-end mb-6">
                            <div>
                                <span class="text-slate-500 text-sm font-medium block mb-1">Precio por persona</span>
                                <div class="flex items-baseline gap-1">
                                    <span class="text-4xl font-extrabold text-slate-800" id="detail-price"></span>
                                    <span class="text-slate-500 font-bold">PEN</span>
                                </div>
                            </div>
                            <x-ui.badge variant="success" class="shadow-sm">DISPONIBLE</x-ui.badge>
                        </div>

                        <hr class="border-slate-100 my-6">

                        <form onsubmit="handleReservationSubmit(event)" class="space-y-4">
                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-1">Elige una fecha</label>
                                <input type="date" id="booking-date"
                                    class="w-full px-4 py-3 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all bg-slate-50 outline-none"
                                    required>
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-1">Viajeros</label>
                                <div class="relative">
                                    <select id="booking-people"
                                        class="w-full px-4 py-3 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all bg-slate-50 appearance-none outline-none">
                                        <option value="1">1 Persona</option>
                                        <option value="2">2 Personas</option>
                                        <option value="3">3 Personas</option>
                                        <option value="4+">4+ Personas</option>
                                    </select>
                                    <i data-lucide="chevron-down"
                                        class="absolute right-3 top-3.5 w-5 h-5 text-slate-400 pointer-events-none"></i>
                                </div>
                            </div>

                            <!-- Login Warning -->
                            <div id="auth-warning"
                                class="hidden p-3 bg-red-50 text-red-600 text-sm rounded-lg border border-red-100 mb-2 flex items-start gap-2">
                                <i data-lucide="alert-circle" class="w-4 h-4 mt-0.5 shrink-0"></i>
                                <span>Debes iniciar sesión para reservar.</span>
                            </div>

                            <x-ui.button type="submit" variant="primary"
                                class="w-full py-3.5 text-base font-bold shadow-lg shadow-blue-600/20">
                                Reservar Ahora
                            </x-ui.button>
                        </form>

                        <div class="mt-6 text-center space-y-3">
                            <span class="text-xs text-slate-400 block font-medium">No se te cobrará nada todavía</span>
                            <div class="flex justify-center gap-4 text-slate-400">
                                <div class="flex items-center gap-1.5 text-xs font-medium">
                                    <i data-lucide="shield-check" class="w-3.5 h-3.5"></i> Segura
                                </div>
                                <div class="flex items-center gap-1.5 text-xs font-medium">
                                    <i data-lucide="thumbs-up" class="w-3.5 h-3.5"></i> Verificada
                                </div>
                            </div>
                        </div>
                    </x-ui.card>
                </div>
            </div>

        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', async () => {
            lucide.createIcons();

            // Get ID from URL path
            const pathSegments = window.location.pathname.split('/');
            const id = pathSegments[pathSegments.length - 1];

            if (id) {
                await loadExperienceDetails(id);
            }
        });

        async function loadExperienceDetails(id) {
            try {
                const response = await axios.get(`/api/experiencias/${id}`);

                if (response.data.success) {
                    const item = response.data.data;

                    // Update DOM
                    document.title = `${item.title} | Turiscovery`;
                    // Image with fallback
                    const imgEl = document.getElementById('detail-image');
                    if (item.image_url) {
                        imgEl.src = item.image_url;
                    } else {
                        imgEl.src =
                            'https://images.unsplash.com/photo-1555396273-367ea4eb4db5?auto=format&fit=crop&w=1920&q=80';
                    }

                    document.getElementById('detail-title').textContent = item.title;
                    document.getElementById('detail-location').textContent = item.location || 'Puno, Perú';
                    document.getElementById('detail-duration').textContent = item.duration_hours ?
                        `${item.duration_hours} Horas` : 'Flexible';
                    document.getElementById('detail-price').textContent = `S/ ${Math.floor(Number(item.price_pen))}`;
                    document.getElementById('detail-description').textContent = item.description;

                    // Toggle visibility
                    document.getElementById('hero-loader').classList.add('hidden');
                    document.getElementById('hero-content').classList.remove('hidden');

                    lucide.createIcons();
                }
            } catch (e) {
                console.error(e);
                // Fallback for demo purposes if API fails or is not real
                document.getElementById('hero-loader').classList.add('hidden');
                document.getElementById('hero-content').classList.remove('hidden');
            }
        }

        function handleReservationSubmit(e) {
            e.preventDefault();

            // 1. Check Authentication using Blade directive (session-based)
            const warning = document.getElementById('auth-warning');

            @guest
            // User is not authenticated via session
            warning.classList.remove('hidden');
            warning.classList.add('animate-bounce');
            setTimeout(() => warning.classList.remove('animate-bounce'), 1000);

            setTimeout(() => {
                window.location.href = `/login?redirect=${encodeURIComponent(window.location.pathname)}`;
            }, 1500);
            return;
        @endguest

        warning.classList.add('hidden');

        // 2. Proceed with WhatsApp Reservation
        const title = document.getElementById('detail-title').textContent;
        const date = document.getElementById('booking-date').value;
        const people = document.getElementById('booking-people').value;

        if (!date) {
            alert('Por favor selecciona una fecha.');
            return;
        }

        const message =
            `Hola Turiscovery, deseo reservar: *${title}* para el día *${date}* (${people}). \n\nSoy un usuario registrado.`;
        const url = `https://wa.me/51950000000?text=${encodeURIComponent(message)}`;

        window.open(url, '_blank');
        }
    </script>
@endpush
