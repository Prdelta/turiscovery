@extends('layouts.app')

@section('content')
    <section class="hero text-center"
        style="background: linear-gradient(rgba(0,0,0,0.7), rgba(0,0,0,0.7)), url('https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?auto=format&fit=crop&w=1920&q=80') center/cover no-repeat;">
        <div class="container">
            <h1 class="fade-in text-white mb-4">Directorio de Negocios</h1>
            <p class="fade-in text-gray-300 mb-8" style="font-size: 1.25rem; max-width: 600px; margin: 0 auto;">
                Encuentra los mejores restaurantes, hoteles, agencias y tiendas de Puno.
            </p>
        </div>
    </section>

    <section class="section">
        <div class="container">
            <div id="locales-grid" class="grid grid-3">
                <div class="col-span-3 text-center py-12">
                    <i data-lucide="loader" class="animate-spin mb-2 w-8 h-8 text-primary mx-auto"></i>
                    <p>Cargando directorio...</p>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', async () => {
            lucide.createIcons();
            await loadLocales();
        });

        async function loadLocales() {
            try {
                const response = await axios.get('/api/locales');
                const container = document.getElementById('locales-grid');

                if (response.data.success && response.data.data.length > 0) {
                    container.innerHTML = response.data.data.map(local => `
                    <article class="card hover:shadow-lg transition-shadow">
                        <div style="height: 180px; overflow: hidden; position: relative;">
                            <img src="${local.image_url || 'https://via.placeholder.com/400x200?text=Comercio'}" style="width: 100%; height: 100%; object-fit: cover;">
                            <span class="badge badge-success" style="position: absolute; top: 10px; right: 10px;">Abierto</span>
                        </div>
                        <div class="p-6">
                            <h3 style="font-size: 1.2rem; margin-bottom: 0.5rem;">${local.name}</h3>
                            <div class="flex items-center gap-1 mb-3 text-warning">
                                <i data-lucide="star" class="w-4 h-4 fill-current"></i>
                                <span class="text-sm font-semibold text-gray-700">${local.rating || '4.5'}</span>
                                <span class="text-xs text-gray-400">(${local.reviews_count || 12} reseñas)</span>
                            </div>
                            <p class="text-secondary text-sm mb-4 line-clamp-2">${local.description || 'Descripción del negocio...'}</p>
                            
                            <hr class="border-gray-100 mb-4">
                            
                            <div class="flex items-center gap-2 text-sm text-gray-500 mb-2">
                                <i data-lucide="map-pin" class="w-4 h-4"></i>
                                <span class="truncate">${local.address || 'Puno Centro'}</span>
                            </div>
                            <div class="flex items-center gap-2 text-sm text-gray-500 mb-4">
                                <i data-lucide="phone" class="w-4 h-4"></i>
                                <span>${local.phone || '+51 900 000 000'}</span>
                            </div>
                            
                            <a href="#" class="btn btn-outline w-full justify-center">Ver Perfil</a>
                        </div>
                    </article>
                `).join('');
                    lucide.createIcons();
                } else {
                    container.innerHTML = `
                    <div style="grid-column: 1/-1; text-align: center; padding: 3rem;">
                        <i data-lucide="store" style="width: 48px; height: 48px; color: var(--color-gray); margin-bottom: 1rem;"></i>
                        <p class="text-secondary">No hay locales registrados aún.</p>
                    </div>
                `;
                    lucide.createIcons();
                }
            } catch (e) {
                console.error(e);
                document.getElementById('locales-grid').innerHTML =
                    '<p class="text-center text-danger">Error al cargar locales.</p>';
            }
        }
    </script>
@endpush
