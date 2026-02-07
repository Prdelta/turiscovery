@extends('layouts.dashboard')

@section('title', 'Buscador de Recursos')

@section('content')
    <div class="fade-in max-w-7xl mx-auto">
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-slate-800">Buscador de Recursos</h1>
            <p class="text-slate-500">Busca imágenes, historia e información de danzas y lugares de Puno</p>
        </div>

        <!-- Barra de búsqueda -->
        <div class="card p-6 mb-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                <div class="col-span-2">
                    <label for="searchQuery" class="form-label">¿Qué estás buscando?</label>
                    <input type="text" id="searchQuery"
                        class="form-input"
                        placeholder="Ej: Diablada, Morenada, Lago Titicaca, Sillustani..."
                        autocomplete="off">
                </div>
                <div>
                    <label for="searchType" class="form-label">Tipo de búsqueda</label>
                    <select id="searchType" class="form-select">
                        <option value="all">Todo</option>
                        <option value="images">Solo Imágenes</option>
                        <option value="info">Solo Información</option>
                    </select>
                </div>
            </div>

            <div class="flex gap-3">
                <button id="btnSearch" class="btn btn-primary">
                    <i data-lucide="search" class="w-4 h-4"></i>
                    Buscar
                </button>
                <button id="btnClear" class="btn btn-outline">
                    <i data-lucide="x" class="w-4 h-4"></i>
                    Limpiar
                </button>
            </div>
        </div>

        <!-- Loading -->
        <div id="loadingState" class="hidden">
            <div class="card p-8 text-center">
                <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-primary mb-4"></div>
                <p class="text-slate-600">Buscando recursos...</p>
            </div>
        </div>

        <!-- Resultados de Wikipedia -->
        <div id="wikiResults" class="hidden mb-6">
            <div class="card">
                <div class="p-4 bg-slate-50 border-b border-slate-200">
                    <h3 class="font-bold text-lg flex items-center gap-2">
                        <i data-lucide="book-open" class="w-5 h-5 text-blue-600"></i>
                        Información de Wikipedia
                    </h3>
                </div>
                <div id="wikiContent" class="p-6">
                    <!-- Se llenará con JavaScript -->
                </div>
            </div>
        </div>

        <!-- Resultados de Imágenes -->
        <div id="imageResults" class="hidden">
            <div class="card">
                <div class="p-4 bg-slate-50 border-b border-slate-200">
                    <h3 class="font-bold text-lg flex items-center gap-2">
                        <i data-lucide="images" class="w-5 h-5 text-green-600"></i>
                        Imágenes Encontradas
                    </h3>
                </div>
                <div id="imageGrid" class="p-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <!-- Se llenará con JavaScript -->
                </div>
            </div>
        </div>

        <!-- Estado vacío -->
        <div id="emptyState" class="card p-12 text-center">
            <div class="max-w-md mx-auto">
                <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i data-lucide="search" class="w-8 h-8 text-slate-400"></i>
                </div>
                <h3 class="text-lg font-bold text-slate-800 mb-2">Busca recursos para tu contenido</h3>
                <p class="text-slate-500 mb-6">
                    Encuentra imágenes de alta calidad e información histórica de Wikipedia sobre danzas,
                    lugares y tradiciones de Puno.
                </p>
                <div class="inline-flex flex-wrap gap-2 justify-center">
                    <button class="badge bg-slate-100 text-slate-700 hover:bg-slate-200 cursor-pointer"
                        onclick="quickSearch('Diablada Puneña')">Diablada</button>
                    <button class="badge bg-slate-100 text-slate-700 hover:bg-slate-200 cursor-pointer"
                        onclick="quickSearch('Morenada')">Morenada</button>
                    <button class="badge bg-slate-100 text-slate-700 hover:bg-slate-200 cursor-pointer"
                        onclick="quickSearch('Lago Titicaca')">Lago Titicaca</button>
                    <button class="badge bg-slate-100 text-slate-700 hover:bg-slate-200 cursor-pointer"
                        onclick="quickSearch('Sillustani')">Sillustani</button>
                    <button class="badge bg-slate-100 text-slate-700 hover:bg-slate-200 cursor-pointer"
                        onclick="quickSearch('Festividad Candelaria')">Candelaria</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para copiar URL de imagen -->
    <div id="imageModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
        <div class="card max-w-2xl w-full max-h-[90vh] overflow-y-auto">
            <div class="p-4 bg-slate-50 border-b border-slate-200 flex justify-between items-center">
                <h3 class="font-bold text-lg">URL de la Imagen</h3>
                <button onclick="closeImageModal()" class="text-slate-400 hover:text-slate-600">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>
            <div class="p-6">
                <img id="modalImage" src="" alt="" class="w-full rounded-lg mb-4">
                <div class="form-group mb-4">
                    <label class="form-label">URL de la imagen</label>
                    <div class="flex gap-2">
                        <input type="text" id="modalImageUrl" class="form-input" readonly>
                        <button onclick="copyImageUrl()" class="btn btn-primary">
                            <i data-lucide="copy" class="w-4 h-4"></i>
                            Copiar
                        </button>
                    </div>
                </div>
                <p class="text-sm text-slate-500">
                    Copia esta URL y pégala en el campo "URL de Imagen" al crear o editar una galería o danza.
                </p>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            lucide.createIcons();

            const searchQuery = document.getElementById('searchQuery');
            const searchType = document.getElementById('searchType');
            const btnSearch = document.getElementById('btnSearch');
            const btnClear = document.getElementById('btnClear');

            // Búsqueda al presionar Enter
            searchQuery.addEventListener('keypress', (e) => {
                if (e.key === 'Enter') {
                    performSearch();
                }
            });

            btnSearch.addEventListener('click', performSearch);
            btnClear.addEventListener('click', clearSearch);
        });

        function quickSearch(query) {
            document.getElementById('searchQuery').value = query;
            performSearch();
        }

        async function performSearch() {
            const query = document.getElementById('searchQuery').value.trim();
            const type = document.getElementById('searchType').value;

            if (!query) {
                alert('Por favor ingresa un término de búsqueda');
                return;
            }

            // Mostrar loading
            document.getElementById('emptyState').classList.add('hidden');
            document.getElementById('wikiResults').classList.add('hidden');
            document.getElementById('imageResults').classList.add('hidden');
            document.getElementById('loadingState').classList.remove('hidden');

            try {
                // Buscar en Wikipedia si no es solo imágenes
                if (type === 'all' || type === 'info') {
                    await searchWikipedia(query);
                }

                // Buscar imágenes si no es solo info
                if (type === 'all' || type === 'images') {
                    await searchImages(query);
                }

                document.getElementById('loadingState').classList.add('hidden');

            } catch (error) {
                console.error('Error en búsqueda:', error);
                document.getElementById('loadingState').classList.add('hidden');
                alert('Error al realizar la búsqueda');
            }
        }

        async function searchWikipedia(query) {
            try {
                const response = await axios.get('/admin/candelaria/resources/search-wikipedia', {
                    params: { query }
                });

                if (response.data.success) {
                    const data = response.data.data;
                    const wikiContent = document.getElementById('wikiContent');

                    wikiContent.innerHTML = `
                        <div class="mb-4">
                            <h4 class="text-xl font-bold text-slate-800 mb-2">${data.title}</h4>
                            ${data.description ? `<p class="text-sm text-slate-500 mb-3">${data.description}</p>` : ''}
                        </div>
                        ${data.thumbnail ? `
                            <img src="${data.thumbnail}" alt="${data.title}"
                                class="w-full max-w-md rounded-lg mb-4 shadow-md">
                        ` : ''}
                        <div class="prose max-w-none mb-4">
                            <p class="text-slate-700 leading-relaxed">${data.extract}</p>
                        </div>
                        <div class="flex gap-3">
                            <button onclick="copyWikiText('${data.extract.replace(/'/g, "\\'")}')"
                                class="btn btn-outline btn-sm">
                                <i data-lucide="copy" class="w-4 h-4"></i>
                                Copiar Texto
                            </button>
                            <a href="${data.url}" target="_blank" class="btn btn-outline btn-sm">
                                <i data-lucide="external-link" class="w-4 h-4"></i>
                                Ver en Wikipedia
                            </a>
                        </div>
                    `;

                    document.getElementById('wikiResults').classList.remove('hidden');
                    lucide.createIcons();
                }
            } catch (error) {
                console.log('No se encontró información en Wikipedia');
            }
        }

        async function searchImages(query) {
            try {
                const response = await axios.get('/admin/candelaria/resources/search-images', {
                    params: { query }
                });

                if (response.data.success) {
                    const unsplashResults = response.data.data.unsplash || [];
                    const wikimediaResults = response.data.data.wikimedia || [];
                    const allResults = [...unsplashResults, ...wikimediaResults];

                    if (allResults.length > 0) {
                        const imageGrid = document.getElementById('imageGrid');

                        imageGrid.innerHTML = allResults.map(img => `
                            <div class="border border-slate-200 rounded-lg overflow-hidden hover:shadow-lg transition-all">
                                ${img.url ? `
                                    <img src="${img.url}" alt="${img.title}"
                                        class="w-full h-48 object-cover cursor-pointer"
                                        onclick="showImageModal('${img.url}', '${img.title}')">
                                ` : `
                                    <div class="w-full h-48 bg-slate-100 flex items-center justify-center">
                                        <span class="text-slate-400">Vista previa no disponible</span>
                                    </div>
                                `}
                                <div class="p-3 bg-white">
                                    <p class="text-sm font-semibold text-slate-800 mb-1">${img.title}</p>
                                    <div class="flex items-center justify-between">
                                        <span class="badge badge-info text-xs">${img.source}</span>
                                        ${img.url ? `
                                            <button onclick="showImageModal('${img.url}', '${img.title}')"
                                                class="text-xs text-primary hover:underline">
                                                Usar imagen
                                            </button>
                                        ` : `
                                            <a href="${img.page_url}" target="_blank"
                                                class="text-xs text-primary hover:underline">
                                                Ver en ${img.source}
                                            </a>
                                        `}
                                    </div>
                                </div>
                            </div>
                        `).join('');

                        document.getElementById('imageResults').classList.remove('hidden');
                    }
                }
            } catch (error) {
                console.log('Error al buscar imágenes:', error);
            }
        }

        function showImageModal(url, title) {
            document.getElementById('modalImage').src = url;
            document.getElementById('modalImageUrl').value = url;
            document.getElementById('imageModal').classList.remove('hidden');
            lucide.createIcons();
        }

        function closeImageModal() {
            document.getElementById('imageModal').classList.add('hidden');
        }

        function copyImageUrl() {
            const input = document.getElementById('modalImageUrl');
            input.select();
            document.execCommand('copy');

            alert('✅ URL copiada al portapapeles');
        }

        function copyWikiText(text) {
            const textarea = document.createElement('textarea');
            textarea.value = text;
            document.body.appendChild(textarea);
            textarea.select();
            document.execCommand('copy');
            document.body.removeChild(textarea);

            alert('✅ Texto copiado al portapapeles');
        }

        function clearSearch() {
            document.getElementById('searchQuery').value = '';
            document.getElementById('searchType').value = 'all';
            document.getElementById('wikiResults').classList.add('hidden');
            document.getElementById('imageResults').classList.add('hidden');
            document.getElementById('loadingState').classList.add('hidden');
            document.getElementById('emptyState').classList.remove('hidden');
        }
    </script>
@endpush
