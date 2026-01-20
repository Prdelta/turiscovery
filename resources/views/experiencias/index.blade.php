@extends('layouts.app')

@section('content')
    <section class="hero text-center"
        style="background: linear-gradient(rgba(0,0,0,0.5), rgba(0,0,0,0.5)), url('https://images.unsplash.com/photo-1555396273-367ea4eb4db5?auto=format&fit=crop&w=1920&q=80') center/cover no-repeat;">
        <div class="container">
            <span class="badge badge-info mb-2 md:inline-flex hidden"
                style="background: rgba(255,255,255,0.2); backdrop-filter: blur(5px); color: white; border: 1px solid rgba(255,255,255,0.4);">
                Turismo Vivencial y Aventura
            </span>
            <h1 class="fade-in text-white mb-4">Experiencias Inolvidables</h1>
            <p class="fade-in text-gray-200 mb-8"
                style="font-size: 1.25rem; max-width: 700px; margin: 0 auto; animation-delay: 0.2s;">
                Navega por el Titicaca, convive con comunidades locales y explora ruinas ancestrales.
            </p>

            <div class="card p-2 fade-in"
                style="max-width: 600px; margin: 0 auto; background: white; animation-delay: 0.4s; display: flex; gap: 0.5rem; align-items: center;">
                <i data-lucide="search" class="text-gray-400 ml-2"></i>
                <input type="text" placeholder="¿Qué te gustaría vivir hoy?"
                    style="border: none; outline: none; flex: 1; padding: 0.5rem;">
                <button class="btn btn-primary">Buscar</button>
            </div>
        </div>
    </section>

    <section class="section">
        <div class="container">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
                <h2 class="mb-0">Explorar por Categoría</h2>
            </div>

            <div class="grid grid-4 mb-12">
                <a href="#" class="card hover:shadow-md transition-shadow text-center p-6 bg-slate-50 border-0">
                    <div
                        style="width: 60px; height: 60px; background: #e0f2fe; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem;">
                        <i data-lucide="ship" style="color: #0284c7; width: 32px; height: 32px;"></i>
                    </div>
                    <h3 style="font-size: 1.1rem; margin-bottom: 0;">Lago Titicaca</h3>
                </a>
                <a href="#" class="card hover:shadow-md transition-shadow text-center p-6 bg-slate-50 border-0">
                    <div
                        style="width: 60px; height: 60px; background: #fce7f3; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem;">
                        <i data-lucide="users" style="color: #db2777; width: 32px; height: 32px;"></i>
                    </div>
                    <h3 style="font-size: 1.1rem; margin-bottom: 0;">Vivencial</h3>
                </a>
                <a href="#" class="card hover:shadow-md transition-shadow text-center p-6 bg-slate-50 border-0">
                    <div
                        style="width: 60px; height: 60px; background: #dcfce7; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem;">
                        <i data-lucide="mountain" style="color: #16a34a; width: 32px; height: 32px;"></i>
                    </div>
                    <h3 style="font-size: 1.1rem; margin-bottom: 0;">Aventura</h3>
                </a>
                <a href="#" class="card hover:shadow-md transition-shadow text-center p-6 bg-slate-50 border-0">
                    <div
                        style="width: 60px; height: 60px; background: #fef3c7; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem;">
                        <i data-lucide="camera" style="color: #d97706; width: 32px; height: 32px;"></i>
                    </div>
                    <h3 style="font-size: 1.1rem; margin-bottom: 0;">Cultural</h3>
                </a>
            </div>

            <h2 class="mb-6">Experiencias Destacadas</h2>
            <div id="experiencias-grid" class="grid grid-3">
                <div class="col-span-3 text-center py-12">
                    <i data-lucide="loader" class="animate-spin mb-2 w-8 h-8 text-primary mx-auto"></i>
                    <p>Cargando experiencias...</p>
                </div>
            </div>
        </div>
        <!-- Reservation Modal -->
        <div id="booking-modal" class="fixed inset-0 z-[100] hidden" aria-labelledby="modal-title" role="dialog"
            aria-modal="true">
            <!-- Backdrop -->
            <div class="absolute inset-0 bg-black/60 backdrop-blur-sm transition-opacity opacity-0" id="modal-backdrop">
            </div>

            <div class="fixed inset-0 z-10 overflow-y-auto">
                <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                    <div class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                        id="modal-panel">

                        <!-- Header -->
                        <div class="bg-primary px-4 py-6 sm:px-6 relative overflow-hidden">
                            <div class="absolute inset-0 bg-gradient-to-br from-white/10 to-transparent"></div>
                            <div class="flex items-center justify-between relative z-10">
                                <h3 class="text-xl font-bold text-white flex items-center gap-2" id="modal-title">
                                    <i data-lucide="calendar-check" class="w-6 h-6"></i>
                                    Reservar Experiencia
                                </h3>
                                <button onclick="closeModal()"
                                    class="text-white/80 hover:text-white transition-colors p-1 rounded-full hover:bg-white/10">
                                    <i data-lucide="x" class="w-6 h-6"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Body -->
                        <div class="px-4 py-6 sm:p-6 bg-white">
                            <div class="mb-4 bg-slate-50 p-4 rounded-lg border border-slate-100 flex items-start gap-3">
                                <div class="bg-blue-100 p-2 rounded-md">
                                    <i data-lucide="compass" class="w-5 h-5 text-primary"></i>
                                </div>
                                <div>
                                    <p class="text-xs text-slate-500 uppercase font-bold tracking-wider mb-1">Experiencia
                                        Seleccionada</p>
                                    <p id="modal-exp-title" class="font-bold text-slate-800 text-lg leading-tight"></p>
                                    <p id="modal-exp-price" class="text-primary font-bold mt-1 text-sm"></p>
                                </div>
                            </div>

                            <form id="booking-form" onsubmit="confirmBooking(event)" class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 mb-1">Nombre Completo</label>
                                    <div class="relative">
                                        <i data-lucide="user" class="absolute left-3 top-2.5 w-5 h-5 text-slate-400"></i>
                                        <input type="text" id="booking-name"
                                            class="pl-10 w-full rounded-lg border-slate-300 border py-2 focus:ring-2 focus:ring-primary focus:border-primary transition-all"
                                            placeholder="Tu nombre" required>
                                    </div>
                                </div>

                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-slate-700 mb-1">Fecha</label>
                                        <div class="relative">
                                            <i data-lucide="calendar"
                                                class="absolute left-3 top-2.5 w-5 h-5 text-slate-400"></i>
                                            <input type="date" id="booking-date"
                                                class="pl-10 w-full rounded-lg border-slate-300 border py-2 focus:ring-2 focus:ring-primary focus:border-primary transition-all"
                                                required>
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-slate-700 mb-1">Personas</label>
                                        <div class="relative">
                                            <i data-lucide="users"
                                                class="absolute left-3 top-2.5 w-5 h-5 text-slate-400"></i>
                                            <select id="booking-people"
                                                class="pl-10 w-full rounded-lg border-slate-300 border py-2 focus:ring-2 focus:ring-primary focus:border-primary transition-all">
                                                <option value="1">1 Persona</option>
                                                <option value="2">2 Personas</option>
                                                <option value="3">3 Personas</option>
                                                <option value="4">4 Personas</option>
                                                <option value="5+">5+ Personas</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>

                        <!-- Footer -->
                        <div class="bg-gray-50 px-4 py-4 sm:flex sm:flex-row-reverse sm:px-6 border-t border-gray-100">
                            <button type="submit" form="booking-form"
                                class="btn btn-primary w-full sm:w-auto sm:ml-3 shadow-lg shadow-green-500/20 hover:shadow-green-500/40 bg-green-600 border-green-600 hover:bg-green-700 flex justify-center items-center gap-2">
                                <i data-lucide="message-circle" class="w-4 h-4"></i>
                                Confirmar por WhatsApp
                            </button>
                            <button type="button" onclick="closeModal()"
                                class="mt-3 inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto border-0">Cancelar</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endsection

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', async () => {
                lucide.createIcons();
                await loadExperiencias();
            });

            async function loadExperiencias() {
                try {
                    const response = await axios.get('/api/experiencias');
                    const container = document.getElementById('experiencias-grid');

                    const items = response.data.data.data ? response.data.data.data : response.data.data;

                    if (response.data.success && Array.isArray(items) && items.length > 0) {
                        container.innerHTML = items.map(item => `
                    <article class="card hover:shadow-lg transition-all duration-300 group">
                        <div style="position: relative; height: 220px; overflow: hidden;" class="rounded-t-xl bg-slate-200">
                            <img src="${item.image_url || 'https://via.placeholder.com/400x200'}" alt="${item.title}" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110">
                            <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                            <span class="badge badge-primary shadow-lg" style="position: absolute; top: 12px; right: 12px; font-size: 0.9rem;">${item.price_pen ? 'S/ ' + parseFloat(item.price_pen).toFixed(0) : 'Consultar'}</span>
                        </div>
                        <div class="p-6">
                            <h3 class="text-xl font-bold text-slate-800 mb-2 leading-tight group-hover:text-primary transition-colors">${item.title}</h3>
                            <p class="text-slate-500 mb-4 line-clamp-2 text-sm">${item.description || 'Sin descripción disponible.'}</p>
                            
                            <div class="flex items-center gap-4 text-xs text-slate-400 mb-6 border-t border-slate-100 pt-4">
                                <div class="flex items-center gap-1">
                                    <i data-lucide="clock" class="w-4 h-4"></i>
                                    <span>${item.duration_hours ? item.duration_hours + ' h' : 'Flexible'}</span>
                                </div>
                                <div class="flex items-center gap-1">
                                    <i data-lucide="map-pin" class="w-4 h-4"></i>
                                    <span>${item.location || 'Puno'}</span>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-3">
                                <button onclick="openModal('${item.title}', '${item.price_pen}')" class="btn btn-primary justify-center text-sm font-bold shadow-md hover:shadow-lg transition-all transform hover:-translate-y-0.5">
                                    Reservar
                                </button>
                                <a href="/experiencias/${item.id}" class="btn btn-outline justify-center text-sm font-bold border-2 hover:bg-slate-50">
                                    Ver Más
                                </a>
                            </div>
                        </div>
                    </article>
                `).join('');
                        lucide.createIcons();
                    } else {
                        container.innerHTML = `
                    <div style="grid-column: 1/-1; text-align: center; padding: 4rem;">
                        <i data-lucide="compass" class="w-16 h-16 text-slate-300 mx-auto mb-4"></i>
                        <h3 class="text-xl font-medium text-slate-600">No hay experiencias disponibles</h3>
                        <p class="text-slate-400">Próximamente agregaremos nuevas aventuras.</p>
                    </div>
                `;
                        lucide.createIcons();
                    }
                } catch (e) {
                    console.error(e);
                    document.getElementById('experiencias-grid').innerHTML =
                        '<p class="col-span-3 text-center text-red-500 py-8">Error al cargar experiencias. Por favor intenta más tarde.</p>';
                }
            }

            // --- Modal Logic ---
            const modal = document.getElementById('booking-modal');
            const backdrop = document.getElementById('modal-backdrop');
            const panel = document.getElementById('modal-panel');

            function openModal(title, price) {
                document.getElementById('modal-exp-title').textContent = title;
                document.getElementById('modal-exp-price').textContent = price ? `S/ ${price} por persona` :
                    'Precio a consultar';

                // Show modal
                modal.classList.remove('hidden');
                // Animate in
                setTimeout(() => {
                    backdrop.classList.remove('opacity-0');
                    panel.classList.remove('opacity-0', 'translate-y-4', 'sm:translate-y-0', 'sm:scale-95');
                    panel.classList.add('opacity-100', 'translate-y-0', 'sm:scale-100');
                }, 10);
            }

            function closeModal() {
                // Animate out
                backdrop.classList.add('opacity-0');
                panel.classList.remove('opacity-100', 'translate-y-0', 'sm:scale-100');
                panel.classList.add('opacity-0', 'translate-y-4', 'sm:translate-y-0', 'sm:scale-95');

                setTimeout(() => {
                    modal.classList.add('hidden');
                }, 300);
            }

            function confirmBooking(e) {
                e.preventDefault();

                const title = document.getElementById('modal-exp-title').textContent;
                const name = document.getElementById('booking-name').value;
                const date = document.getElementById('booking-date').value;
                const people = document.getElementById('booking-people').value;

                const message =
                    `Hola Turiscovery, estoy interesado en reservar: *${title}*. \n\nMis datos son: \n- Nombre: ${name} \n- Fecha: ${date} \n- Cantidad: ${people} \n\nQuedo a la espera de su confirmación.`;

                const phoneNumber = '51950000000'; // Replace with real number
                const url = `https://wa.me/${phoneNumber}?text=${encodeURIComponent(message)}`;

                // Close modal and open WhatsApp
                closeModal();
                window.open(url, '_blank');
            }

            // Close on click outside
            modal.addEventListener('click', (e) => {
                if (e.target === backdrop) {
                    closeModal();
                }
            });
        </script>
    @endpush
