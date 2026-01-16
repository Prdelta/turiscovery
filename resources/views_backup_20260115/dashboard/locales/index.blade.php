@extends('layouts.dashboard')

@section('title', 'Mis Locales - Dashboard')

@section('content')
    <div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Mis Locales</h1>
            <p class="text-gray-500 mt-1">Gestiona tus ubicaciones comerciales</p>
        </div>
        <div>
            <a href="/dashboard/locales/create"
                class="inline-flex items-center gap-2 bg-primary text-white px-5 py-2.5 rounded-lg font-medium hover:bg-primary-dark transition-all shadow-lg shadow-primary/30">
                <i class="fa-solid fa-plus"></i>
                Nuevo Local
            </a>
        </div>
    </div>

    <div id="locales-list" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <!-- Loading State -->
        <div
            class="col-span-full flex justify-center items-center py-12 bg-white rounded-2xl shadow-sm border border-gray-100">
            <div class="text-center">
                <div class="w-10 h-10 border-4 border-primary/30 border-t-primary rounded-full animate-spin mx-auto mb-4">
                </div>
                <p class="text-gray-500">Cargando tus locales...</p>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        async function loadMyLocales() {
            try {
                const userResponse = await axios.get('/api/me');
                const user = userResponse.data.data;

                const response = await axios.get('/api/locales');
                const allLocales = response.data.data.data;
                const myLocales = allLocales.filter(l => l.user_id === user.id);

                const container = document.getElementById('locales-list');

                if (myLocales.length === 0) {
                    container.className =
                        'flex flex-col items-center justify-center bg-white rounded-2xl shadow-sm border border-gray-100 p-12 text-center';
                    container.innerHTML = `
                        <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mb-4">
                            <span class="text-4xl">🏢</span>
                        </div>
                        <h3 class="text-xl font-bold text-gray-800 mb-2">No tienes locales registrados</h3>
                        <p class="text-gray-500 mb-6 max-w-md">Crea tu primer local para empezar a promocionar tu negocio y llegar a más clientes.</p>
                        <a href="/dashboard/locales/create" class="bg-primary text-white px-6 py-2.5 rounded-lg font-medium hover:bg-primary-dark transition-all shadow-lg shadow-primary/30">
                            Crear mi primer Local
                        </a>
                    `;
                    return;
                }

                container.className = 'grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6';
                container.innerHTML = myLocales.map(locale => `
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition-shadow duration-300">
                        <div class="p-6">
                            <div class="flex justify-between items-start mb-4">
                                <div>
                                    <h3 class="text-lg font-bold text-gray-800 mb-1 line-clamp-1">${locale.name}</h3>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-primary/10 text-primary">
                                        ${getCategoryName(locale.category)}
                                    </span>
                                </div>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${locale.is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600'}">
                                    ${locale.is_active ? 'Activo' : 'Inactivo'}
                                </span>
                            </div>
                            
                            <p class="text-gray-500 text-sm mb-4 line-clamp-2 min-h-[2.5rem]">${locale.description}</p>
                            
                            <div class="space-y-2 mb-6">
                                <div class="flex items-center gap-2 text-sm text-gray-600">
                                    <i class="fa-solid fa-location-dot w-5 text-center text-gray-400"></i>
                                    <span class="line-clamp-1">${locale.address}</span>
                                </div>
                                ${locale.phone ? `
                                    <div class="flex items-center gap-2 text-sm text-gray-600">
                                        <i class="fa-solid fa-phone w-5 text-center text-gray-400"></i>
                                        <span>${locale.phone}</span>
                                    </div>` : ''}
                            </div>
                            
                            <div class="grid grid-cols-2 gap-3 pt-4 border-t border-gray-50">
                                <a href="/dashboard/locales/${locale.id}/edit" class="flex items-center justify-center gap-2 px-4 py-2 rounded-lg border border-gray-200 text-gray-600 hover:bg-gray-50 hover:text-primary hover:border-primary/30 transition-all text-sm font-medium">
                                    <i class="fa-solid fa-pen-to-square"></i> Editar
                                </a>
                                <button onclick="deleteLocale(${locale.id})" class="flex items-center justify-center gap-2 px-4 py-2 rounded-lg border border-red-100 text-red-600 hover:bg-red-50 transition-all text-sm font-medium">
                                    <i class="fa-solid fa-trash"></i> Eliminar
                                </button>
                            </div>
                        </div>
                    </div>
                `).join('');

            } catch (error) {
                console.error('Error loading locales:', error);
                const container = document.getElementById('locales-list');
                container.className =
                    'col-span-full bg-red-50 border border-red-100 rounded-xl p-6 text-center text-red-600';
                container.innerHTML = '<p>Error al cargar los locales. Por favor, intenta de nuevo.</p>';
            }
        }

        function getCategoryName(category) {
            const categories = {
                restaurant: '🍽️ Restaurante',
                hotel: '🏨 Hotel',
                tour_agency: '🗺️ Agencia',
                craft_shop: '🎨 Artesanía',
                museum: '🏛️ Museo',
                cultural_center: '🎭 Centro Cultural',
                other: '📍 Otro'
            };
            return categories[category] || category;
        }

        async function deleteLocale(id) {
            if (!confirm('¿Estás seguro de eliminar este local?')) return;

            try {
                await axios.delete(`/api/locales/${id}`);
                showNotification('Local eliminado correctamente', 'success');
                loadMyLocales();
            } catch (error) {
                showNotification('Error al eliminar local', 'error');
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            loadMyLocales();
        });
    </script>
@endpush
