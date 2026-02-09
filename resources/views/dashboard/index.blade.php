@extends('layouts.dashboard')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')
@section('page-subtitle', 'Bienvenido a tu panel de control')

@section('content')

    <!-- Welcome Section -->
    <div style="margin-bottom: var(--spacing-xl);">
        <div class="card" style="background: var(--color-primary); border: none; padding: var(--spacing-xl); color: white;">
            <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1.5rem;">
                <div style="flex: 1; min-width: 250px;">
                    <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 0.75rem;">
                        <div style="width: 60px; height: 60px; background: rgba(255,255,255,0.2); backdrop-filter: blur(10px); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.75rem; font-weight: bold; border: 3px solid rgba(255,255,255,0.3);">
                            <span id="welcome-user-initial">{{ substr(auth()->user()->name ?? 'S', 0, 1) }}</span>
                        </div>
                        <div>
                            <h2 class="mb-1" style="font-size: 1.75rem; font-weight: 800; text-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                                ¡Hola, <span id="welcome-user-name">{{ auth()->user()->name }}</span>!
                                <span id="user-role-badge" class="badge" style="font-size: 0.7rem; vertical-align: middle; background: rgba(255,255,255,0.25); border: 1px solid rgba(255,255,255,0.3);">
                                    {{ strtoupper(auth()->user()->role) }}
                                </span>
                            </h2>
                            <p class="mb-0" style="color: rgba(255,255,255,0.9); font-size: 1rem;">
                                @if(auth()->user()->role === 'admin')
                                    Panel de administración global de Turiscovery
                                @elseif(auth()->user()->role === 'socio')
                                    Gestiona tu negocio y atrae más clientes en Turiscovery
                                @else
                                    Explora y disfruta de las mejores experiencias turísticas
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
                <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                    <a href="/locales" class="btn" style="background: rgba(255,255,255,0.2); color: white; border: 1px solid rgba(255,255,255,0.3); backdrop-filter: blur(10px);">
                        <i data-lucide="search"></i>
                        Explorar Plataforma
                    </a>
                    @if(auth()->user()->role === 'socio')
                    <a href="/dashboard/locales/create" class="btn" style="background: white; color: var(--color-primary); font-weight: bold;">
                        <i data-lucide="plus-circle"></i>
                        Crear Contenido
                    </a>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Section -->
    <div style="margin-bottom: var(--spacing-xl);">
        <div class="card p-4">
            <h3 class="flex items-center gap-2"
                style="font-size: 1.25rem; margin-bottom: 1.5rem; border-bottom: 1px solid var(--color-gray-light); padding-bottom: 0.75rem;">
                <div style="width: 32px; height: 32px; background: var(--color-primary); border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                    <i data-lucide="bar-chart-3" style="width: 18px; height: 18px; color: white;"></i>
                </div>
                @if(auth()->user()->role === 'admin')
                    Estadísticas Globales
                @else
                    Mis Estadísticas
                @endif
            </h3>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 1rem;">
                <!-- Locales Stat -->
                <div style="background: rgba(89, 48, 232, 0.08); padding: 1rem; border-radius: 12px; display: flex; align-items: center; gap: 1rem; border-left: 4px solid var(--color-primary);">
                    <div style="width: 48px; height: 48px; background: var(--color-primary); border-radius: 12px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                        <i data-lucide="store" style="color: white; width: 24px; height: 24px;"></i>
                    </div>
                    <div>
                        <span style="display: block; font-size: 2rem; font-weight: 800; color: var(--color-primary); line-height: 1;" id="stat-locales">0</span>
                        <span style="display: block; font-size: 0.875rem; color: var(--color-text-muted); margin-top: 0.25rem;">Locales</span>
                    </div>
                </div>

                <!-- Eventos Stat -->
                <div style="background: rgba(89, 48, 232, 0.08); padding: 1rem; border-radius: 12px; display: flex; align-items: center; gap: 1rem; border-left: 4px solid var(--color-primary);">
                    <div style="width: 48px; height: 48px; background: var(--color-primary); border-radius: 12px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                        <i data-lucide="calendar" style="color: white; width: 24px; height: 24px;"></i>
                    </div>
                    <div>
                        <span style="display: block; font-size: 2rem; font-weight: 800; color: var(--color-primary); line-height: 1;" id="stat-eventos">0</span>
                        <span style="display: block; font-size: 0.875rem; color: var(--color-text-muted); margin-top: 0.25rem;">Eventos</span>
                    </div>
                </div>

                <!-- Experiencias Stat -->
                <div style="background: rgba(89, 48, 232, 0.08); padding: 1rem; border-radius: 12px; display: flex; align-items: center; gap: 1rem; border-left: 4px solid var(--color-primary);">
                    <div style="width: 48px; height: 48px; background: var(--color-primary); border-radius: 12px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                        <i data-lucide="compass" style="color: white; width: 24px; height: 24px;"></i>
                    </div>
                    <div>
                        <span style="display: block; font-size: 2rem; font-weight: 800; color: var(--color-primary); line-height: 1;" id="stat-experiencias">0</span>
                        <span style="display: block; font-size: 0.875rem; color: var(--color-text-muted); margin-top: 0.25rem;">Experiencias</span>
                    </div>
                </div>

                <!-- Promociones Stat -->
                <div style="background: rgba(89, 48, 232, 0.08); padding: 1rem; border-radius: 12px; display: flex; align-items: center; gap: 1rem; border-left: 4px solid var(--color-primary);">
                    <div style="width: 48px; height: 48px; background: var(--color-primary); border-radius: 12px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                        <i data-lucide="percent" style="color: white; width: 24px; height: 24px;"></i>
                    </div>
                    <div>
                        <span style="display: block; font-size: 2rem; font-weight: 800; color: var(--color-primary); line-height: 1;" id="stat-promociones">0</span>
                        <span style="display: block; font-size: 0.875rem; color: var(--color-text-muted); margin-top: 0.25rem;">Promociones</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Grid -->
    <div class="grid grid-3" style="margin-bottom: var(--spacing-xl);">

        <!-- Actions -->
        <div style="grid-column: span 2;">
            <div class="card p-4 h-100">
                <h3 class="flex items-center gap-2"
                    style="font-size: 1.25rem; margin-bottom: 1.5rem; border-bottom: 1px solid var(--color-gray-light); padding-bottom: 0.75rem;">
                    <div style="width: 32px; height: 32px; background: var(--color-primary); border-radius: 8px; display: flex; align-items: center; justify-center;">
                        <i data-lucide="zap" style="width: 18px; height: 18px; color: white;"></i>
                    </div>
                    Acciones Rápidas
                </h3>

                <!-- Business Actions -->
                <div id="quick-actions-content" class="grid grid-2 gap-4">
                    <a href="/dashboard/locales/create" class="btn btn-outline text-left group hover:border-primary transition-all"
                        style="justify-content: flex-start; padding: 1rem; height: auto;">
                        <div style="width: 48px; height: 48px; background: var(--color-primary); border-radius: 12px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                            <i data-lucide="store" style="color: white; width: 24px; height: 24px;"></i>
                        </div>
                        <div>
                            <span style="display: block; font-weight: 700; font-size: 1rem; margin-bottom: 0.25rem;">Nuevo Local</span>
                            <small class="text-secondary" style="font-weight: 400; font-size: 0.875rem;">Registra tu negocio turístico</small>
                        </div>
                    </a>
                    <a href="/dashboard/eventos/create" class="btn btn-outline text-left group hover:border-primary transition-all"
                        style="justify-content: flex-start; padding: 1rem; height: auto;">
                        <div style="width: 48px; height: 48px; background: var(--color-primary); border-radius: 12px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                            <i data-lucide="calendar-plus" style="color: white; width: 24px; height: 24px;"></i>
                        </div>
                        <div>
                            <span style="display: block; font-weight: 700; font-size: 1rem; margin-bottom: 0.25rem;">Nuevo Evento</span>
                            <small class="text-secondary" style="font-weight: 400; font-size: 0.875rem;">Publica eventos especiales</small>
                        </div>
                    </a>
                    <a href="/dashboard/experiencias/create" class="btn btn-outline text-left group hover:border-primary transition-all"
                        style="justify-content: flex-start; padding: 1rem; height: auto;">
                        <div style="width: 48px; height: 48px; background: var(--color-primary); border-radius: 12px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                            <i data-lucide="compass" style="color: white; width: 24px; height: 24px;"></i>
                        </div>
                        <div>
                            <span style="display: block; font-weight: 700; font-size: 1rem; margin-bottom: 0.25rem;">Nueva Experiencia</span>
                            <small class="text-secondary" style="font-weight: 400; font-size: 0.875rem;">Crea tours y actividades</small>
                        </div>
                    </a>
                    <a href="/dashboard/promociones/create" class="btn btn-outline text-left group hover:border-primary transition-all"
                        style="justify-content: flex-start; padding: 1rem; height: auto;">
                        <div style="width: 48px; height: 48px; background: var(--color-primary); border-radius: 12px; display: flex; align-items: center; justify-center; flex-shrink: 0;">
                            <i data-lucide="percent" style="color: white; width: 24px; height: 24px;"></i>
                        </div>
                        <div>
                            <span style="display: block; font-weight: 700; font-size: 1rem; margin-bottom: 0.25rem;">Nueva Promoción</span>
                            <small class="text-secondary" style="font-weight: 400; font-size: 0.875rem;">Atrae clientes con ofertas</small>
                        </div>
                    </a>
                </div>
            </div>
        </div>

        <!-- Tips & Status Panel -->
        <div class="card p-4">
            <h3 class="flex items-center gap-2"
                style="font-size: 1.25rem; margin-bottom: 1.5rem; border-bottom: 1px solid var(--color-gray-light); padding-bottom: 0.75rem;">
                <div style="width: 32px; height: 32px; background: var(--color-success); border-radius: 8px; display: flex; align-items: center; justify-center;">
                    <i data-lucide="lightbulb" style="width: 18px; height: 18px; color: white;"></i>
                </div>
                Estado & Consejos
            </h3>

            <!-- Account Status -->
            <div style="background: rgba(116, 250, 138, 0.08); padding: 1rem; border-radius: 12px; margin-bottom: 1rem; border-left: 4px solid var(--color-success);">
                <div style="display: flex; gap: 1rem; align-items: center;">
                    <div style="width: 48px; height: 48px; background: var(--color-success); border-radius: 12px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                        <i data-lucide="check-circle" style="width: 24px; height: 24px; color: white;"></i>
                    </div>
                    <div>
                        <span style="font-weight: 700; display: block; font-size: 1rem; margin-bottom: 0.25rem;">Cuenta Activa</span>
                        <small style="color: var(--color-text-muted); font-size: 0.875rem;">Todo funcionando correctamente ✨</small>
                    </div>
                </div>
            </div>

            <!-- Quick Tip -->
            <div style="background: rgba(89, 48, 232, 0.08); padding: 1rem; border-radius: 12px; border-left: 4px solid var(--color-primary);">
                <div style="display: flex; gap: 1rem; align-items: flex-start;">
                    <div style="width: 48px; height: 48px; background: var(--color-primary); border-radius: 12px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                        <i data-lucide="sparkles" style="width: 24px; height: 24px; color: white;"></i>
                    </div>
                    <div>
                        <span style="font-weight: 700; display: block; font-size: 1rem; margin-bottom: 0.5rem;">💡 Consejo del día</span>
                        <p style="font-size: 0.875rem; color: var(--color-text-light); line-height: 1.5; margin: 0;">
                            Agrega fotos de alta calidad a tus locales para atraer más visitantes. Los lugares con imágenes reciben 3x más clics.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Quick Links -->
            <div style="margin-top: 1.5rem; padding-top: 1rem; border-top: 1px solid var(--color-gray-light);">
                <a href="/dashboard/locales" style="display: flex; align-items: center; gap: 0.75rem; padding: 0.75rem; border-radius: 8px; transition: all 0.2s; text-decoration: none; color: var(--color-text); margin-bottom: 0.5rem;" onmouseover="this.style.background='var(--color-bg-gray)'" onmouseout="this.style.background='transparent'">
                    <i data-lucide="list" style="width: 18px; height: 18px; color: var(--color-primary);"></i>
                    <span style="font-weight: 600; font-size: 0.875rem;">Ver todos mis contenidos</span>
                </a>
                <a href="/user/edit" style="display: flex; align-items: center; gap: 0.75rem; padding: 0.75rem; border-radius: 8px; transition: all 0.2s; text-decoration: none; color: var(--color-text);" onmouseover="this.style.background='var(--color-bg-gray)'" onmouseout="this.style.background='transparent'">
                    <i data-lucide="settings" style="width: 18px; height: 18px; color: var(--color-primary);"></i>
                    <span style="font-weight: 600; font-size: 0.875rem;">Configurar mi perfil</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Recent Content Section -->
    <div style="margin-top: var(--spacing-xl);">
        <div class="dashboard-header" style="align-items: center;">
            <div>
                <h2 class="mb-1" style="font-size: 1.75rem; font-weight: 800;">Tu Contenido Reciente</h2>
                <p style="color: var(--color-text-muted); font-size: 0.875rem; margin: 0;">Gestiona y revisa lo que has publicado</p>
            </div>
            <select id="content-filter"
                style="padding: 0.75rem 1rem; border-radius: 10px; border: 2px solid var(--color-gray-light); font-weight: 600; background: white; cursor: pointer; transition: all 0.2s;">
                <option value="all">📋 Todo</option>
                <option value="locales">🏪 Locales</option>
                <option value="eventos">📅 Eventos</option>
                <option value="experiencias">🧭 Experiencias</option>
                <option value="promociones">🎟️ Promociones</option>
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
            // Load stats based on user role - use server-side session data
            @if (auth()->check())
                const user = {
                    name: "{{ auth()->user()->name }}",
                    role: "{{ auth()->user()->role }}"
                };

                document.getElementById('welcome-user-name').textContent = user.name;

                // Show role badge
                const roleBadge = document.getElementById('user-role-badge');
                if (roleBadge) {
                    roleBadge.textContent = user.role.toUpperCase();
                    roleBadge.style.display = 'inline-block';

                    // Color coding
                    if (user.role === 'admin') roleBadge.style.backgroundColor = '#ef4444';
                    else if (user.role === 'socio') roleBadge.style.backgroundColor = '#4f46e5';
                    else roleBadge.style.backgroundColor = '#10b981';
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
            @else
                // No authenticated - redirect to login
                window.location.href = '/login';
            @endif
        });

        async function loadStats() {
            try {
                // ✨ OPTIMIZADO: Solo carga conteos, no registros completos
                const response = await axios.get('/api/stats');

                if (response.data.success) {
                    const stats = response.data.data;

                    animateCounter('stat-locales', stats.locales || 0);
                    animateCounter('stat-eventos', stats.eventos || 0);
                    animateCounter('stat-experiencias', stats.experiencias || 0);
                    animateCounter('stat-promociones', stats.promociones || 0);
                }
            } catch (error) {
                console.error('Error loading stats:', error);
                ['stat-locales', 'stat-eventos', 'stat-promociones', 'stat-experiencias'].forEach(id => {
                    const el = document.getElementById(id);
                    if (el) el.textContent = '0';
                });
            }
        }

        async function loadRecentContent(filter = 'all') {
            const container = document.getElementById('recent-content');
            if (!container) return;

            const userRole = "{{ auth()->user()->role }}";

            container.innerHTML =
                '<div class="card"><div style="padding: var(--spacing-lg); text-align: center;"><i data-lucide="loader" class="animate-spin" style="color: var(--color-primary);"></i><p class="text-secondary mt-2">Cargando contenido...</p></div></div>';
            try {
                // ✨ OPTIMIZADO: Usa endpoint específico que ya filtra y limita
                const response = await axios.get('/api/recent-content', {
                    params: {
                        filter: filter,
                        limit: 6
                    }
                });

                let items = [];
                if (response.data.success) {
                    items = response.data.data;
                }

                if (items.length === 0) {
                    const emptyMessage = userRole === 'admin'
                        ? 'No hay contenido en la plataforma aún'
                        : 'No tienes contenido aún';
                    const emptyAction = userRole === 'admin'
                        ? 'Los usuarios comenzarán a crear contenido pronto'
                        : 'Comienza creando tu primer local, evento o experiencia.';

                    container.innerHTML = `
                        <div class="card p-8 text-center col-span-full">
                            <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i data-lucide="inbox" class="w-8 h-8 text-slate-400"></i>
                            </div>
                            <h3 class="text-lg font-medium text-slate-800 mb-2">${emptyMessage}</h3>
                            <p class="text-secondary mb-4">${emptyAction}</p>
                            ${userRole !== 'admin' ? '<a href="/dashboard/locales/create" class="btn btn-primary">Crear Ahora</a>' : ''}
                        </div>`;
                    lucide.createIcons();
                } else {
                    container.innerHTML = items.map(item => `
                    <article class="card fade-in group hover:shadow-lg transition-all">
                        <img src="${item.image_url || 'https://via.placeholder.com/400x200?text=' + (item.name || item.title)}"
                             class="card-image group-hover:scale-105 transition-transform duration-300"
                             onerror="this.src='https://via.placeholder.com/400x200?text=${encodeURIComponent(item.name || item.title)}'">
                        <div style="padding: var(--spacing-md);">
                            <div class="flex items-center justify-between mb-2">
                                <span class="badge badge-primary text-xs">${item.contentType}</span>
                                ${item.is_active ? '<span class="badge badge-success text-xs">Activo</span>' : '<span class="badge badge-danger text-xs">Inactivo</span>'}
                            </div>
                            <h3 class="font-bold text-lg mb-2 line-clamp-1">${item.name || item.title}</h3>
                            <p class="text-secondary text-sm mb-3 line-clamp-2">
                                ${item.description || 'Sin descripción'}
                            </p>
                            <div class="flex items-center gap-2 text-xs text-slate-500 mb-4">
                                <i data-lucide="map-pin" class="w-3 h-3"></i>
                                <span class="truncate">${item.short_address || item.formatted_address || item.city || item.address || 'Puno'}</span>
                            </div>
                            <div class="flex gap-2">
                                <a href="/dashboard/${item.contentType}/${item.id}/edit" class="btn btn-outline text-sm flex-1">
                                    <i data-lucide="edit-2" class="w-3 h-3"></i> Editar
                                </a>
                                <a href="/dashboard/${item.contentType}" class="btn btn-primary text-sm flex-1">
                                    Ver Todos
                                </a>
                            </div>
                        </div>
                    </article>
                `).join('');
                    lucide.createIcons();
                }
            } catch (e) {
                console.error('Error al cargar contenido:', e);
                container.innerHTML = `
                    <div class="card p-8 text-center col-span-full">
                        <i data-lucide="alert-circle" class="w-12 h-12 text-red-500 mx-auto mb-3"></i>
                        <h3 class="text-lg font-medium text-slate-800 mb-2">Error al cargar contenido</h3>
                        <p class="text-secondary">Por favor, intenta recargar la página.</p>
                    </div>`;
                lucide.createIcons();
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
