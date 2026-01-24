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
        const isAuthenticated = {{ auth()->check() ? 'true' : 'false' }};

        document.addEventListener('DOMContentLoaded', async () => {
            lucide.createIcons();
            await loadEventos();
        });

        async function loadEventos() {
            try {
                const response = await axios.get('/api/eventos');
                const container = document.getElementById('eventos-grid');
                const events = response.data.data.data || response.data.data;

                if (response.data.success && Array.isArray(events) && events.length > 0) {
                    container.innerHTML = events.map(ev => `
                    <article class="card card-hover group relative overflow-hidden">
                        <div class="relative h-56 overflow-hidden">
                            ${ev.image_url ?
                                `<img src="${ev.image_url}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500" alt="${ev.title}">` :
                                `<div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-blue-100 to-purple-100">
                                    <i data-lucide="calendar" class="w-16 h-16 text-blue-400"></i>
                                </div>`
                            }
                            <div class="card-image-overlay"></div>

                            <!-- Fecha Badge con gradiente -->
                            <div class="absolute top-4 left-4 bg-gradient-to-br from-white to-blue-50 px-4 py-3 rounded-xl shadow-2xl border-2 border-white/50 backdrop-blur-sm text-center transform group-hover:scale-110 transition-transform duration-300">
                                <span class="block font-black text-2xl bg-gradient-to-br from-blue-600 to-purple-600 bg-clip-text text-transparent">${new Date(ev.start_time).getDate()}</span>
                                <span class="block text-xs font-bold uppercase text-slate-600">${new Date(ev.start_time).toLocaleDateString('es-ES', { month: 'short' })}</span>
                            </div>

                            <!-- Badge de categoría -->
                            <div class="absolute top-4 right-4">
                                <span class="badge badge-primary shadow-lg backdrop-blur-sm bg-blue-600/90 px-4 py-2 text-xs font-bold">
                                    <i data-lucide="music" class="w-3 h-3 inline mr-1"></i>
                                    ${ev.category || 'Evento'}
                                </span>
                            </div>

                            <!-- Info overlay on hover -->
                            <div class="absolute inset-x-0 bottom-0 p-4 text-white transform translate-y-full group-hover:translate-y-0 transition-transform duration-300">
                                <p class="text-sm font-medium line-clamp-2 text-shadow-strong">${ev.description || 'Evento cultural en Puno'}</p>
                            </div>
                        </div>

                        <div class="p-5 bg-gradient-to-br from-white to-slate-50">
                            <h3 class="text-xl font-bold mb-3 leading-tight text-slate-800 group-hover:text-primary transition-colors line-clamp-2">${ev.title}</h3>

                            <div class="flex items-center gap-2 text-slate-500 text-sm mb-4 p-3 bg-white rounded-lg border border-slate-100">
                                <i data-lucide="map-pin" class="w-4 h-4 text-red-500 flex-shrink-0"></i>
                                <span class="truncate font-medium">${ev.address || 'Puno, Perú'}</span>
                            </div>

                            <button onclick="handleAttend('${ev.id}')" class="btn btn-primary w-full shadow-lg hover:shadow-xl transform hover:scale-105 transition-all">
                                <i data-lucide="calendar-check" class="w-4 h-4"></i>
                                Confirmar Asistencia
                            </button>
                        </div>

                        <!-- Efecto de brillo en hover -->
                        <div class="absolute inset-0 shimmer opacity-0 group-hover:opacity-100 pointer-events-none"></div>
                    </article>
                `).join('');
                    lucide.createIcons();
                } else {
                    container.innerHTML = `<x-empty-state icon="calendar-off" message="No hay eventos próximos registrados." />`;
                    lucide.createIcons();
                }
            } catch (e) {
                console.error(e);
                document.getElementById('eventos-grid').innerHTML =
                    '<p class="text-center text-danger">Error al cargar eventos.</p>';
            }
        }

        async function handleAttend(eventoId) {
            if (!isAuthenticated) {
                window.location.href = `/login?redirect=${encodeURIComponent(window.location.pathname)}`;
                return;
            }

            try {
                const response = await axios.post('/api/event-attendances', {
                    evento_id: eventoId,
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
                    alert('Error al confirmar asistencia. Por favor, intenta nuevamente.');
                }
            }
        }
    </script>
@endpush
