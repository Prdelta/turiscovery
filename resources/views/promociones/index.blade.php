@extends('layouts.app')

@section('content')
    <section class="hero text-center"
        style="background: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.6)), url('https://images.unsplash.com/photo-1552566626-52f8b828add9?auto=format&fit=crop&w=1920&q=80') center/cover no-repeat;">
        <div class="container">
            <span class="badge badge-accent mb-2 md:inline-flex hidden">OFERTAS Y DESCUENTOS</span>
            <h1 class="fade-in text-white mb-4">Promociones Exclusivas</h1>
            <p class="fade-in text-gray-200 mb-8" style="font-size: 1.25rem; max-width: 600px; margin: 0 auto;">
                Ahorra en restaurantes, hoteles y tours con nuestros cupones digitales.
            </p>
        </div>
    </section>

    <section class="section">
        <div class="container">
            <div id="promociones-grid" class="grid grid-3">
                <div class="col-span-3 text-center py-12">
                    <i data-lucide="loader" class="animate-spin mb-2 w-8 h-8 text-primary mx-auto"></i>
                    <p>Buscando mejores ofertas...</p>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', async () => {
            lucide.createIcons();
            await loadPromociones();
        });

        async function loadPromociones() {
            try {
                const response = await axios.get('/api/promociones');
                const container = document.getElementById('promociones-grid');

                // Handle pagination
                const items = response.data.data.data ? response.data.data.data : response.data.data;

                if (response.data.success && Array.isArray(items) && items.length > 0) {
                    container.innerHTML = items.map(promo => `
                    <article class="card overflow-hidden hover:translate-y-1 transition-transform border-t-0">
                        <div style="height: 150px; overflow: hidden; position: relative;">
                            <img src="${promo.image_url || 'https://via.placeholder.com/400x150'}" style="width: 100%; height: 100%; object-fit: cover;">
                            <div style="position: absolute; inset: 0; background: linear-gradient(to top, rgba(0,0,0,0.6), transparent);"></div>
                            <span class="text-white font-bold" style="position: absolute; bottom: 10px; left: 15px; text-shadow: 0 2px 4px rgba(0,0,0,0.5);">En ${promo.locale ? promo.locale.name : 'Seleccionado'}</span>
                        </div>
                        <div class="p-4 text-center border-b border-dashed border-gray-200" style="background: #fffbeb;">
                            <span class="text-3xl font-bold text-accent mb-1 block">${promo.discount_type === 'percentage' ? promo.discount_percentage + '%' : (promo.discount_type === 'fixed' ? 'S/' + promo.discount_amount : '2x1')} OFF</span>
                        <div class="p-6">
                            <h3 style="font-size: 1.1rem; margin-bottom: 0.5rem; text-align: center;">${promo.title}</h3>
                            <p class="text-secondary text-sm mb-4 text-center line-clamp-2">${promo.description || 'Aprovecha este descuento especial por tiempo limitado.'}</p>
                            
                            <div class="bg-slate-50 p-3 rounded-lg flex justify-between items-center text-sm">
                                <span class="text-gray-500">Expira:</span>
                                <span class="font-semibold text-gray-700">${new Date(promo.end_date).toLocaleDateString()}</span>
                            </div>
                            
                            <button onclick="handleRedeem('${promo.id}')" class="btn btn-primary w-full mt-4 justify-center">Obtener Cupón</button>
                        </div>
                    </article>
                `).join('');
                    lucide.createIcons();
                } else {
                    container.innerHTML = `
                    <div style="grid-column: 1/-1; text-align: center; padding: 3rem;">
                        <i data-lucide="tag" style="width: 48px; height: 48px; color: var(--color-gray); margin-bottom: 1rem;"></i>
                        <p class="text-secondary">No hay promociones activas en este momento.</p>
                    </div>
                `;
                    lucide.createIcons();
                }
            } catch (e) {
                console.error(e);
                document.getElementById('promociones-grid').innerHTML =
                    '<p class="text-center text-danger">Error al cargar promociones.</p>';
            }
        }

        function handleRedeem(id) {
            const token = localStorage.getItem('auth_token');
            if (!token) {
                window.location.href = `/login?redirect=${encodeURIComponent(window.location.pathname)}`;
            } else {
                alert('¡Cupón generado! Código: TURIS-' + Math.floor(1000 + Math.random() * 9000));
            }
        }
    </script>
@endpush
