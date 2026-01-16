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
    </section>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', async () => {
            lucide.createIcons();
            await loadExperiencias();
        });

        async function loadExperiencias() {
            try {
                const response = await axios.get('/api/experiencias'); // Adjust endpoint if needed
                const container = document.getElementById('experiencias-grid');

                if (response.data.success && response.data.data.length > 0) {
                    container.innerHTML = response.data.data.map(item => `
                    <article class="card hover:shadow-lg transition-all duration-300">
                        <div style="position: relative; height: 200px; overflow: hidden; background: #eee;">
                            <img src="${item.image_url || 'https://via.placeholder.com/400x200'}" alt="${item.title}" style="width: 100%; height: 100%; object-fit: cover;">
                            <span class="badge badge-primary" style="position: absolute; top: 10px; right: 10px;">${item.price ? 'S/ ' + item.price : 'Consultar'}</span>
                        </div>
                        <div class="p-6">
                            <h3 style="font-size: 1.25rem; font-weight: 700; margin-bottom: 0.5rem;">${item.title}</h3>
                            <p class="text-secondary mb-4 line-clamp-2">${item.description || 'Sin descripción disponible.'}</p>
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-top: auto;">
                                <div style="display: flex; align-items: center; gap: 0.5rem; color: var(--color-text-light); font-size: 0.875rem;">
                                    <i data-lucide="clock" class="w-4 h-4"></i>
                                    <span>${item.duration || 'Flexible'}</span>
                                </div>
                                <a href="/experiencias/${item.id}" class="btn btn-outline btn-sm">Ver Más</a>
                            </div>
                        </div>
                    </article>
                `).join('');
                    lucide.createIcons();
                } else {
                    container.innerHTML = `
                    <div style="grid-column: 1/-1; text-align: center; padding: 3rem;">
                        <i data-lucide="compass" style="width: 48px; height: 48px; color: var(--color-gray); margin-bottom: 1rem;"></i>
                        <p class="text-secondary">Próximamente agregaremos nuevas experiencias.</p>
                    </div>
                `;
                    lucide.createIcons();
                }
            } catch (e) {
                console.error(e);
                document.getElementById('experiencias-grid').innerHTML =
                    '<p class="text-center text-danger">Error al cargar experiencias.</p>';
            }
        }
    </script>
@endpush
