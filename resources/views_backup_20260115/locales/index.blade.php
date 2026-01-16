@extends('layouts.app')

@section('title', 'Locales - Turiscovery')

@section('content')
    <!-- Hero Section -->
    <div class="relative bg-primary-dark overflow-hidden">
        <div class="absolute inset-0 bg-cover bg-center"
            style="background-image: url('/images/locales-hero.jpg'); opacity: 0.2"></div>
        <div class="absolute inset-0 bg-gradient-to-r from-primary-dark/90 to-primary/80"></div>

        <div class="container relative z-10 py-16 md:py-24 text-center">
            <h1 class="text-4xl md:text-5xl font-bold text-white mb-6 animate-fade-in-up">Descubre Puno</h1>
            <p class="text-xl text-blue-100 max-w-2xl mx-auto mb-8 animate-fade-in-up delay-100">
                Explora los mejores restaurantes, hoteles y sitios de interés en la capital del folklore peruano.
            </p>

            <!-- Search Bar -->
            <div class="max-w-xl mx-auto bg-white rounded-full p-2 shadow-2xl flex animate-fade-in-up delay-200">
                <input type="text" placeholder="¿Qué estás buscando?"
                    class="flex-1 px-6 py-3 rounded-full border-none focus:ring-0 text-gray-700 placeholder-gray-400">
                <button
                    class="bg-primary text-white w-12 h-12 rounded-full flex items-center justify-center hover:bg-primary-dark transition-colors">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- Filters Section -->
    <div class="bg-gray-50 border-b border-gray-200">
        <div class="container py-8">
            <div class="flex flex-wrap justify-center gap-4">
                <button
                    class="px-6 py-2 rounded-full bg-white border border-gray-200 text-gray-600 hover:border-primary hover:text-primary transition-all shadow-sm">
                    🍽️ Restaurantes
                </button>
                <button
                    class="px-6 py-2 rounded-full bg-white border border-gray-200 text-gray-600 hover:border-primary hover:text-primary transition-all shadow-sm">
                    🏨 Hoteles
                </button>
                <button
                    class="px-6 py-2 rounded-full bg-white border border-gray-200 text-gray-600 hover:border-primary hover:text-primary transition-all shadow-sm">
                    🗺️ Agencias
                </button>
                <button
                    class="px-6 py-2 rounded-full bg-white border border-gray-200 text-gray-600 hover:border-primary hover:text-primary transition-all shadow-sm">
                    🎨 Artesanías
                </button>
                <button
                    class="px-6 py-2 rounded-full bg-white border border-gray-200 text-gray-600 hover:border-primary hover:text-primary transition-all shadow-sm">
                    🏛️ Museos
                </button>
            </div>
        </div>
    </div>

    <!-- Locales Grid -->
    <div class="container py-16">
        <div id="locales-grid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <!-- Loading State -->
            <div class="col-span-full text-center py-20">
                <div
                    class="inline-block w-12 h-12 border-4 border-primary/20 border-t-primary rounded-full animate-spin mb-4">
                </div>
                <p class="text-gray-500">Buscando los mejores lugares...</p>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', async () => {
            try {
                const response = await axios.get('/api/locales');
                const locales = response.data.data.data.filter(l => l.is_active);
                const container = document.getElementById('locales-grid');

                if (locales.length === 0) {
                    container.innerHTML = `
                    <div class="col-span-full text-center py-12">
                        <div class="text-6xl mb-4">🏙️</div>
                        <h3 class="text-xl font-bold text-gray-800">No hay locales disponibles</h3>
                        <p class="text-gray-500">Vuelve pronto para descubrir nuevos lugares.</p>
                    </div>
                `;
                    return;
                }

                container.innerHTML = locales.map(locale => `
                <div class="group bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-xl hover:-translate-y-1 transition-all duration-300">
                    <div class="relative h-48 overflow-hidden bg-gray-100">
                        <div class="absolute top-4 left-4 z-10">
                            <span class="px-3 py-1 rounded-full text-xs font-bold bg-white/90 backdrop-blur-sm text-primary shadow-sm">
                                ${getCategoryName(locale.category)}
                            </span>
                        </div>
                        <img src="${locale.image_url || '/images/placeholder-locale.jpg'}" 
                             alt="${locale.name}" 
                             class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700"
                             onerror="this.src='https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?w=800&q=80'">
                    </div>
                    
                    <div class="p-6">
                        <h3 class="text-xl font-bold text-gray-800 mb-2 group-hover:text-primary transition-colors">${locale.name}</h3>
                        <p class="text-gray-500 text-sm mb-4 line-clamp-2">${locale.description}</p>
                        
                        <div class="flex items-center gap-2 text-sm text-gray-600 mb-4">
                            <i class="fa-solid fa-location-dot text-gray-400"></i>
                            <span class="truncate">${locale.address}</span>
                        </div>
                        
                        <div class="pt-4 border-t border-gray-50 flex justify-between items-center">
                            <span class="text-sm font-medium text-primary cursor-pointer hover:underline">Ver detalles</span>
                            <button class="w-8 h-8 rounded-full bg-gray-50 flex items-center justify-center text-gray-400 hover:bg-primary hover:text-white transition-colors">
                                <i class="fa-regular fa-heart"></i>
                            </button>
                        </div>
                    </div>
                </div>
            `).join('');

            } catch (error) {
                console.error('Error loading locales:', error);
            }
        });

        function getCategoryName(category) {
            const categories = {
                restaurant: 'Restaurante',
                hotel: 'Hotel',
                tour_agency: 'Agencia',
                craft_shop: 'Artesanía',
                museum: 'Museo',
                cultural_center: 'Cultural',
                other: 'Otro'
            };
            return categories[category] || category;
        }
    </script>
@endpush
