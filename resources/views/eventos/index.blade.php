@extends('layouts.app')

@section('content')
    <section class="hero text-center"
        style="background: linear-gradient(rgba(0,0,0,0.7), rgba(0,0,0,0.7)), url('https://images.unsplash.com/photo-1514525253440-b393452e23da?auto=format&fit=crop&w=1920&q=80') center/cover no-repeat;">
        <div class="container">
            <span class="badge badge-primary mb-2 md:inline-flex hidden">AGENDA CULTURAL</span>
            <h1 class="fade-in text-white mb-4">Eventos en Puno</h1>
            <p class="fade-in text-gray-300 mb-8" style="font-size: 1.25rem; max-width: 600px; margin: 0 auto;">
                Música, danza, teatro y festivales. Descubre qué está pasando en la capital del folklore.
            </p>
        </div>
    </section>

    <section class="section">
        <div class="container">
            <div class="grid grid-4 gap-6 mb-8">
                <div class="card p-4" style="grid-column: span 1;">
                    <h3 style="font-size: 1.1rem; margin-bottom: 1rem;">Filtrar Eventos</h3>
                    <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                        <label style="display: flex; gap: 0.5rem; align-items: center;">
                            <input type="checkbox" checked> Todos
                        </label>
                        <label style="display: flex; gap: 0.5rem; align-items: center;">
                            <input type="checkbox"> Festivales
                        </label>
                        <label style="display: flex; gap: 0.5rem; align-items: center;">
                            <input type="checkbox"> Conciertos
                        </label>
                        <label style="display: flex; gap: 0.5rem; align-items: center;">
                            <input type="checkbox"> Teatro
                        </label>
                    </div>
                </div>

                <div class="col-span-3" style="grid-column: span 3;">
                    <div id="eventos-grid" class="grid grid-2">
                        <div class="col-span-2 text-center py-12">
                            <i data-lucide="loader" class="animate-spin mb-2 w-8 h-8 text-primary mx-auto"></i>
                            <p>Cargando agenda...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', async () => {
            lucide.createIcons();
            await loadEventos();
        });

        async function loadEventos() {
            try {
                const response = await axios.get('/api/eventos');
                const container = document.getElementById('eventos-grid');

                if (response.data.success && response.data.data.length > 0) {
                    container.innerHTML = response.data.data.map(ev => `
                    <article class="card flex-row overflow-hidden hover:shadow-md transition-shadow" style="display: flex; flex-direction: row;">
                        <div style="width: 140px; background: #f0f0f0; position: relative;">
                            ${ev.image_url ? 
                                `<img src="${ev.image_url}" style="width: 100%; height: 100%; object-fit: cover;">` : 
                                `<div style="width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; background: var(--color-gray-light); color: var(--color-gray);"><i data-lucide="calendar"></i></div>`
                            }
                            <div style="position: absolute; top: 10px; left: 10px; background: white; padding: 4px 8px; border-radius: 4px; text-align: center; line-height: 1.2; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                                <span style="display: block; font-weight: 700; font-size: 1.1rem; color: var(--color-primary);">${new Date(ev.start_date).getDate()}</span>
                                <span style="display: block; font-size: 0.7rem; text-transform: uppercase;">${new Date(ev.start_date).toLocaleDateString('es-ES', { month: 'short' })}</span>
                            </div>
                        </div>
                        <div class="p-4 flex-1">
                            <span class="badge badge-sm badge-info mb-1" style="font-size: 0.7rem;">${ev.category || 'Evento'}</span>
                            <h3 style="font-size: 1.1rem; margin-bottom: 0.5rem;">${ev.title}</h3>
                            <p class="text-secondary text-sm mb-3 line-clamp-2">${ev.description || ''}</p>
                            <div style="display: flex; items-center; gap: 0.5rem; color: var(--color-text-light); font-size: 0.8rem;">
                                <i data-lucide="map-pin" class="w-3 h-3"></i>
                                <span>${ev.location || 'Puno'}</span>
                            </div>
                        </div>
                    </article>
                `).join('');
                    lucide.createIcons();
                } else {
                    container.innerHTML = `
                    <div style="grid-column: 1/-1; text-align: center; padding: 3rem;">
                        <i data-lucide="calendar-off" style="width: 48px; height: 48px; color: var(--color-gray); margin-bottom: 1rem;"></i>
                        <p class="text-secondary">No hay eventos próximos registrados.</p>
                    </div>
                `;
                    lucide.createIcons();
                }
            } catch (e) {
                console.error(e);
                document.getElementById('eventos-grid').innerHTML =
                    '<p class="text-center text-danger">Error al cargar eventos.</p>';
            }
        }
    </script>
@endpush
