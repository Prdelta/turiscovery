@extends('layouts.dashboard')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')
@section('page-subtitle', 'Bienvenido a tu panel de control')

@section('content')

    <!-- Welcome Section -->
    <div style="margin-bottom: var(--spacing-xl);">
        <div class="card" style="border-left: 5px solid var(--color-primary); padding: var(--spacing-lg);">
            <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem;">
                <div>
                    <h2 class="mb-1" style="font-size: 1.5rem;">¡Hola, <span id="welcome-user-name">Usuario</span>! <span
                            id="user-role-badge" class="badge badge-primary"
                            style="font-size: 0.8rem; vertical-align: middle; display: none;"></span> 👋</h2>
                    <p class="mb-0 text-secondary">Aquí tienes un resumen de tu actividad en Turiscovery.</p>
                </div>
                <div style="display: flex; gap: 10px;">
                    <a href="/search" class="btn btn-outline">
                        <i data-lucide="search"></i>
                        Explorar
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Section -->
    <div class="dashboard-stats fade-in">
        <!-- Locales -->
        <div class="stat-card">
            <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                <div>
                    <h3 id="stat-locales">0</h3>
                    <p>Locales Activos</p>
                </div>
                <div
                    style="background: rgba(94, 92, 232, 0.1); padding: 10px; border-radius: 10px; color: var(--color-primary);">
                    <i data-lucide="store"></i>
                </div>
            </div>
        </div>

        <!-- Eventos -->
        <div class="stat-card" style="border-left-color: var(--color-info-dark);">
            <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                <div>
                    <h3 id="stat-eventos" style="color: var(--color-info-dark);">0</h3>
                    <p>Eventos Próximos</p>
                </div>
                <div
                    style="background: rgba(143, 207, 222, 0.1); padding: 10px; border-radius: 10px; color: var(--color-info-dark);">
                    <i data-lucide="calendar"></i>
                </div>
            </div>
        </div>

        <!-- Promociones -->
        <div class="stat-card" style="border-left-color: var(--color-accent);">
            <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                <div>
                    <h3 id="stat-promociones" style="color: var(--color-accent);">0</h3>
                    <p>Promociones</p>
                </div>
                <div
                    style="background: rgba(252, 176, 50, 0.1); padding: 10px; border-radius: 10px; color: var(--color-accent);">
                    <i data-lucide="percent"></i>
                </div>
            </div>
        </div>

        <!-- Experiencias -->
        <div class="stat-card" style="border-left-color: var(--color-secondary);">
            <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                <div>
                    <h3 id="stat-experiencias" style="color: var(--color-secondary);">0</h3>
                    <p>Experiencias</p>
                </div>
                <div
                    style="background: rgba(120, 225, 129, 0.1); padding: 10px; border-radius: 10px; color: var(--color-secondary);">
                    <i data-lucide="compass"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Grid -->
    <div class="grid grid-3" style="margin-bottom: var(--spacing-xl);">

        <!-- Actions -->
        <div style="grid-column: span 2;">
            <div class="card p-4 h-100">
                <h3
                    style="font-size: 1.25rem; margin-bottom: 1rem; border-bottom: 1px solid var(--color-gray-light); padding-bottom: 0.5rem;">
                    Acciones Rápidas</h3>

                <!-- Business Actions -->
                <div id="quick-actions-content" class="grid grid-2">
                    <a href="/dashboard/locales/create" class="btn btn-outline text-left"
                        style="justify-content: flex-start; border-color: var(--color-gray-light); color: var(--color-text);">
                        <i data-lucide="plus-circle" style="color: var(--color-primary);"></i>
                        <div>
                            <span style="display: block; font-weight: 600;">Nuevo Local</span>
                            <small class="text-secondary" style="font-weight: 400;">Registrar negocio</small>
                        </div>
                    </a>
                    <a href="/dashboard/eventos/create" class="btn btn-outline text-left"
                        style="justify-content: flex-start; border-color: var(--color-gray-light); color: var(--color-text);">
                        <i data-lucide="calendar-plus" style="color: var(--color-info-dark);"></i>
                        <div>
                            <span style="display: block; font-weight: 600;">Nuevo Evento</span>
                            <small class="text-secondary" style="font-weight: 400;">Publicar evento</small>
                        </div>
                    </a>
                    <a href="/dashboard/promociones/create" class="btn btn-outline text-left"
                        style="justify-content: flex-start; border-color: var(--color-gray-light); color: var(--color-text);">
                        <i data-lucide="tag" style="color: var(--color-accent);"></i>
                        <div>
                            <span style="display: block; font-weight: 600;">Nueva Promo</span>
                            <small class="text-secondary" style="font-weight: 400;">Crear oferta</small>
                        </div>
                    </a>
                </div>

                <!-- Tourist Actions -->
                <div id="tourist-actions" class="grid grid-2" style="display: none;">
                    <a href="/user/favorites" class="btn btn-outline text-left"
                        style="justify-content: flex-start; border-color: var(--color-gray-light); color: var(--color-text);">
                        <i data-lucide="heart" style="color: var(--color-danger);"></i>
                        <div>
                            <span style="display: block; font-weight: 600;">Favoritos</span>
                            <small class="text-secondary" style="font-weight: 400;">Mis guardados</small>
                        </div>
                    </a>
                    <a href="/user/reviews" class="btn btn-outline text-left"
                        style="justify-content: flex-start; border-color: var(--color-gray-light); color: var(--color-text);">
                        <i data-lucide="star" style="color: var(--color-warning);"></i>
                        <div>
                            <span style="display: block; font-weight: 600;">Reseñas</span>
                            <small class="text-secondary" style="font-weight: 400;">Mis opiniones</small>
                        </div>
                    </a>
                </div>
            </div>
        </div>

        <!-- Recent Activity Panel -->
        <div class="card p-4">
            <h3
                style="font-size: 1.25rem; margin-bottom: 1rem; border-bottom: 1px solid var(--color-gray-light); padding-bottom: 0.5rem;">
                Estado</h3>
            <div style="display: flex; gap: 1rem; align-items: center; margin-bottom: 1rem;">
                <div
                    style="width: 40px; height: 40px; background: rgba(120, 225, 129, 0.2); color: var(--color-success); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                    <i data-lucide="check-circle"></i>
                </div>
                <div>
                    <span style="font-weight: 600; display: block;">Cuenta Activa</span>
                    <small class="text-secondary">Todo en orden</small>
                </div>
            </div>
            <div style="display: flex; gap: 1rem; align-items: center;">
                <div
                    style="width: 40px; height: 40px; background: rgba(94, 92, 232, 0.1); color: var(--color-primary); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                    <i data-lucide="bell"></i>
                </div>
                <div>
                    <span style="font-weight: 600; display: block;">Novedades</span>
                    <small class="text-secondary">Sin notificaciones nuevas</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Content Section -->
    <div style="margin-top: var(--spacing-xl);">
        <div class="dashboard-header">
            <h2 class="mb-0">Mi Contenido Reciente</h2>
            <select id="content-filter"
                style="padding: 0.5rem; border-radius: var(--radius-sm); border: 1px solid var(--color-gray-light);">
                <option value="all">Todo</option>
                <option value="locales">Locales</option>
                <option value="eventos">Eventos</option>
                <option value="promociones">Promociones</option>
                <option value="experiencias">Experiencias</option>
            </select>
        </div>

        <div id="recent-content" class="grid grid-3">
            <!-- JS will load content here -->
            <div class="card p-4 text-center">
                <i data-lucide="loader" class="animate-spin text-secondary"></i>
                <p>Cargando contenido...</p>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', async () => {
            // Load stats based on user role - use session instead of token
            try {
                // Use session-based endpoint
                const userResponse = await axios.get('/api/auth/user');

                if (userResponse.data.success && userResponse.data.authenticated) {
                    const user = userResponse.data.user;
                    document.getElementById('welcome-user-name').textContent = user.name;

                    // Show role badge
                    const roleBadge = document.getElementById('user-role-badge');
                    if (roleBadge) {
                        roleBadge.textContent = user.role.toUpperCase();
                        roleBadge.style.display = 'inline-block';

                        // Color coding
                        if (user.role === 'admin') roleBadge.style.backgroundColor = '#ef4444'; // Red for admin
                        else if (user.role === 'socio') roleBadge.style.backgroundColor =
                            '#4f46e5'; // Blue for socio
                        else roleBadge.style.backgroundColor = '#10b981'; // Green for tourist
                    }

                    // Show different actions based on role
                    if (user.role === 'tourist') {
                        document.getElementById('quick-actions-content').style.display = 'none';
                        document.getElementById('tourist-actions').style.display = 'grid';
                    }

                    // Load stats & content
                    loadStats();
                    loadRecentContent();

                    // Filter listener
                    document.getElementById('content-filter')?.addEventListener('change', (e) => {
                        loadRecentContent(e.target.value);
                    });
                }
            } catch (error) {
                console.error('Error loading user:', error);
                // Redirect to login if not authenticated
                window.location.href = '/login';
            }
        });

        async function loadStats() {
            try {
                // Load counts from API
                const [locales, eventos, promociones, experiencias] = await Promise.all([
                    axios.get('/api/locales?per_page=1'),
                    axios.get('/api/eventos?per_page=1'),
                    axios.get('/api/promociones?per_page=1'),
                    axios.get('/api/experiencias?per_page=1')
                ]);

                // Helper to get total from paginated response
                const getTotal = (res) => {
                    if (res.data.data && res.data.data.total) return res.data.data.total; // Paginator
                    if (Array.isArray(res.data.data)) return res.data.data.length; // Array
                    return 0;
                };

                animateCounter('stat-locales', getTotal(locales));
                animateCounter('stat-eventos', getTotal(eventos));
                animateCounter('stat-promociones', getTotal(promociones));
                animateCounter('stat-experiencias', getTotal(experiencias));
            } catch (error) {
                console.error('Error loading stats:', error);
            }
        }

        async function loadRecentContent(filter = 'all') {
            const container = document.getElementById('recent-content');
            if (!container) return;

            container.innerHTML =
                '<div class="card"><div style="padding: var(--spacing-lg); text-align: center;"><i data-lucide="loader" class="animate-spin" style="color: var(--color-primary);"></i><p class="text-secondary mt-2">Cargando...</p></div></div>';
            try {
                let endpoints = [];
                if (filter === 'all' || filter === 'locales') endpoints.push({
                    type: 'locales',
                    url: '/api/locales?per_page=2'
                });
                if (filter === 'all' || filter === 'eventos') endpoints.push({
                    type: 'eventos',
                    url: '/api/eventos?per_page=2'
                });
                if (filter === 'all' || filter === 'experiencias') endpoints.push({
                    type: 'experiencias',
                    url: '/api/experiencias?per_page=2'
                });

                const responses = await Promise.all(
                    endpoints.map(ep => axios.get(ep.url).then(res => ({
                        ...res,
                        contentType: ep.type
                    })))
                );

                let items = [];
                responses.forEach(res => {
                    if (res.data.success) {
                        // Handle pagination structure
                        const dataList = res.data.data.data ? res.data.data.data : res.data.data;

                        if (Array.isArray(dataList)) {
                            dataList.forEach(item => items.push({
                                ...item,
                                contentType: res.contentType
                            }));
                        }
                    }
                });

                if (items.length === 0) {
                    container.innerHTML =
                        '<div class="card p-4 text-center text-secondary">No hay contenido reciente.</div>';
                } else {
                    container.innerHTML = items.map(item => `
                    <article class="card fade-in">
                        <img src="${item.image_url || 'https://via.placeholder.com/400x200?text=' + item.name}" class="card-image">
                        <div style="padding: var(--spacing-md);">
                            <span class="badge badge-primary mb-2">${item.contentType}</span>
                            <h3>${item.name || item.title}</h3>
                            <p class="text-secondary" style="font-size: 0.9rem; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                ${item.description || ''}
                            </p>
                            <a href="/dashboard/${item.contentType}" class="btn btn-outline" style="width: 100%; margin-top: 1rem;">Ver Detalles</a>
                        </div>
                    </article>
                `).join('');
                }
                lucide.createIcons();
            } catch (e) {
                console.error(e);
                container.innerHTML = '<div class="card p-4 text-center text-danger">Error al cargar contenido.</div>';
            }
        }

        function animateCounter(elementId, target) {
            const element = document.getElementById(elementId);
            if (!element) return;
            let current = 0;
            const increment = Math.ceil(target / 20);
            const timer = setInterval(() => {
                current += increment;
                if (current >= target) {
                    current = target;
                    clearInterval(timer);
                }
                element.textContent = current;
            }, 50);
        }
    </script>
@endpush
