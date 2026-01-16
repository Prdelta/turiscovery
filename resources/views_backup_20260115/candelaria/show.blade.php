@extends('layouts.app')

@section('title', 'Candelaria - Turiscovery')

@section('content')
    <div class="container" style="margin-top: 2rem; margin-bottom: 4rem;">
        <a href="/candelaria" class="btn btn-outline" style="margin-bottom: 2rem;">← Volver a Candelaria</a>

        <div id="candelaria-detail">
            <div class="card" style="text-align: center; padding: 3rem;">
                <p class="text-secondary">Cargando contenido...</p>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        const candelariaId = window.location.pathname.split('/').pop();

        async function loadCandelaria() {
            try {
                const response = await axios.get(`/api/candelaria/${candelariaId}`);
                const item = response.data.data;

                const container = document.getElementById('candelaria-detail');

                container.innerHTML = `
            <div class="grid grid-3" style="gap: 2rem;">
                <div style="grid-column: span 2;">
                    ${item.images && item.images[0] ? `
                            <div style="position: relative; margin-bottom: 2rem;">
                                <img src="${item.images[0]}" alt="${item.title}" style="width: 100%; height: 500px; object-fit: cover; border-radius: 1rem;">
                                ${item.is_featured ? '<span class="badge" style="position: absolute; top: 1rem; right: 1rem; background: #f59e0b; color: white; font-size: 1rem;">⭐ Destacado</span>' : ''}
                            </div>
                        ` : ''}
                    
                    <div style="margin-bottom: 2rem;">
                        <div style="display: flex; gap: 0.5rem; margin-bottom: 1rem;">
                            ${getCategoryBadge(item.category)}
                            ${item.is_active ? '<span class="badge badge-success">✓ Publicado</span>' : ''}
                        </div>
                        <h1 style="margin: 0 0 1rem 0; font-size: 2.5rem;">${item.title}</h1>
                        <p style="font-size: 1.25rem; color: var(--text-secondary); line-height: 1.8;">${item.description}</p>
                    </div>
                    
                    ${item.content ? `
                            <div style="background: white; padding: 2.5rem; border-radius: 1rem; box-shadow: 0 4px 6px rgba(0,0,0,0.1); margin-bottom: 2rem; border-left: 4px solid var(--primary);">
                                <h2 style="margin: 0 0 1.5rem 0; color: var(--primary);">📜 Historia y Significado</h2>
                                <div style="font-size: 1.125rem; line-height: 1.8; white-space: pre-line;">${item.content}</div>
                            </div>
                        ` : ''}
                    
                    ${item.event_date ? `
                            <div style="background: linear-gradient(135deg, #ec4899, #be185d); color: white; padding: 2rem; border-radius: 1rem; margin-bottom: 2rem; text-align: center;">
                                <h3 style="margin: 0 0 0.5rem 0; font-size: 1.25rem; opacity: 0.9;">📅 Fecha del Evento</h3>
                                <p style="margin: 0; font-size: 2rem; font-weight: 800;">${formatDate(item.event_date)}</p>
                            </div>
                        ` : ''}
                    
                    ${item.locale ? `
                            <div style="background: white; padding: 2rem; border-radius: 1rem; box-shadow: 0 2px 4px rgba(0,0,0,0.1); margin-bottom: 2rem;">
                                <h2 style="margin: 0 0 1rem 0;">🏢 Organizado por</h2>
                                <h3 style="margin: 0 0 0.5rem 0;">${item.locale.name}</h3>
                                <p style="margin: 0; color: var(--text-secondary);">${item.locale.description}</p>
                                ${item.locale.address ? `<p style="margin-top: 0.5rem;">📍 ${item.locale.address}</p>` : ''}
                            </div>
                        ` : ''}
                    
                    <div id="reviews-section" style="background: white; padding: 2rem; border-radius: 1rem; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                        <h2 style="margin: 0 0 1.5rem 0;">⭐ Reseñas</h2>
                        <div id="reviews-list"></div>
                    </div>
                </div>
                
                <div>
                    <div style="background: linear-gradient(135deg, #8b5cf6, #6d28d9); color: white; padding: 2rem; border-radius: 1rem; margin-bottom: 1.5rem;">
                        <h3 style="margin: 0 0 1rem 0; font-size: 1.5rem;">🎭 Patrimonio Cultural</h3>
                        <p style="margin: 0; opacity: 0.95; line-height: 1.6;">La Festividad de la Virgen de la Candelaria es Patrimonio Cultural Inmaterial de la Humanidad por UNESCO.</p>
                    </div>
                    
                    <div style="background: white; padding: 2rem; border-radius: 1rem; box-shadow: 0 2px 4px rgba(0,0,0,0.1); margin-bottom: 1.5rem;">
                        <h3 style="margin: 0 0 1rem 0;">📌 Categoría</h3>
                        <p style="margin: 0; font-size: 1.125rem;">${getCategoryName(item.category)}</p>
                    </div>
                    
                    <button onclick="toggleFavorite()" id="favorite-btn" class="btn btn-outline" style="width: 100%; margin-bottom: 0.5rem;">
                        <span id="favorite-icon">♡</span> Guardar
                    </button>
                    
                    <button onclick="openReviewModal()" class="btn btn-primary" style="width: 100%;">
                        ⭐ Escribir Reseña
                    </button>
                    
                    <div style="margin-top: 1.5rem; padding: 1.5rem; background: var(--bg-light); border-radius: 0.5rem;">
                        <p style="margin: 0; font-size: 0.875rem; color: var(--text-secondary); line-height: 1.6;">
                            <strong>💡 Sabías que...</strong><br>
                            La Fiesta de la Candelaria atrae más de 200,000 visitantes cada año a Puno.
                        </p>
                    </div>
                </div>
            </div>
        `;

                loadReviews();
                checkFavoriteStatus();

            } catch (error) {
                console.error('Error loading candelaria:', error);
                document.getElementById('candelaria-detail').innerHTML = `
            <div class="card" style="text-align: center; padding: 3rem;">
                <h2>Contenido no encontrado</h2>
                <p class="text-secondary">El contenido que buscas no existe o ha sido eliminado.</p>
                <a href="/candelaria" class="btn btn-primary" style="margin-top: 1rem;">Ver Todo Candelaria</a>
            </div>
        `;
            }
        }

        function getCategoryBadge(category) {
            const badges = {
                dance: '<span class="badge" style="background: #ec4899; color: white; font-size: 1rem;">💃 Danzas</span>',
                history: '<span class="badge" style="background: #8b5cf6; color: white; font-size: 1rem;">📜 Historia</span>',
                costume: '<span class="badge" style="background: #f59e0b; color: white; font-size: 1rem;">👗 Trajes</span>',
                music: '<span class="badge" style="background: #10b981; color: white; font-size: 1rem;">🎵 Música</span>',
                tradition: '<span class="badge" style="background: #0ea5e9; color: white; font-size: 1rem;">🎭 Tradiciones</span>'
            };
            return badges[category] || '';
        }

        function getCategoryName(category) {
            const names = {
                dance: '💃 Danzas Folklóricas',
                history: '📜 Historia y Origen',
                costume: '👗 Trajes Típicos',
                music: '🎵 Música Tradicional',
                tradition: '🎭 Tradiciones y Costumbres'
            };
            return names[category] || category;
        }

        async function loadReviews() {
            try {
                const response = await axios.get(`/api/reviews`, {
                    params: {
                        reviewable_type: 'App\\\\Models\\\\Candelaria',
                        reviewable_id: candelariaId
                    }
                });

                const reviews = response.data.data.data;
                const container = document.getElementById('reviews-list');

                if (reviews.length === 0) {
                    container.innerHTML = '<p class="text-secondary">Aún no hay reseñas. ¡Sé el primero!</p>';
                    return;
                }

                container.innerHTML = reviews.map(review => `
            <div style="padding: 1.5rem; border: 2px solid var(--bg-light); border-radius: 0.5rem; margin-bottom: 1rem;">
                <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 0.5rem;">
                    <div>
                        <strong>${review.user?.name || 'Usuario'}</strong>
                        <div>${renderStars(review.rating)}</div>
                    </div>
                    <span style="font-size: 0.875rem; color: var(--text-secondary);">${formatDate(review.created_at)}</span>
                </div>
                ${review.title ? `<h4 style="margin: 0.5rem 0;">${review.title}</h4>` : ''}
                ${review.comment ? `<p style="margin: 0.5rem 0; color: var(--text-secondary);">${review.comment}</p>` : ''}
            </div>
        `).join('');

            } catch (error) {
                console.error('Error loading reviews:', error);
            }
        }

        async function checkFavoriteStatus() {
            const token = api.getToken();
            if (!token) return;

            try {
                const response = await axios.get(`/api/favorites/check`, {
                    params: {
                        favoritable_type: 'App\\\\Models\\\\Candelaria',
                        favoritable_id: candelariaId
                    }
                });

                if (response.data.data.is_favorite) {
                    document.getElementById('favorite-icon').textContent = '♥';
                    document.getElementById('favorite-btn').classList.add('btn-primary');
                    document.getElementById('favorite-btn').classList.remove('btn-outline');
                }
            } catch (error) {}
        }

        async function toggleFavorite() {
            const token = api.getToken();
            if (!token) {
                showNotification('Debes iniciar sesión para guardar favoritos', 'error');
                window.location.href = '/login';
                return;
            }

            try {
                await api.toggleFavorite('App\\\\Models\\\\Candelaria', candelariaId);

                const icon = document.getElementById('favorite-icon');
                const btn = document.getElementById('favorite-btn');

                if (icon.textContent === '♡') {
                    icon.textContent = '♥';
                    btn.classList.add('btn-primary');
                    btn.classList.remove('btn-outline');
                    showNotification('Agregado a favoritos', 'success');
                } else {
                    icon.textContent = '♡';
                    btn.classList.remove('btn-primary');
                    btn.classList.add('btn-outline');
                    showNotification('Eliminado de favoritos', 'success');
                }
            } catch (error) {
                showNotification('Error al actualizar favoritos', 'error');
            }
        }

        function openReviewModal() {
            const token = api.getToken();
            if (!token) {
                showNotification('Debes iniciar sesión para escribir reseñas', 'error');
                window.location.href = '/login';
                return;
            }

            const rating = prompt('Calificación (1-5 estrellas):');
            if (!rating || rating < 1 || rating > 5) return;

            const title = prompt('Título de tu reseña:');
            const comment = prompt('Tu comentario:');

            submitReview(parseInt(rating), title, comment);
        }

        async function submitReview(rating, title, comment) {
            try {
                await api.addReview({
                    reviewable_type: 'App\\\\Models\\\\Candelaria',
                    reviewable_id: candelariaId,
                    rating,
                    title,
                    comment
                });

                showNotification('¡Reseña publicada!', 'success');
                loadReviews();
            } catch (error) {
                showNotification('Error al publicar reseña', 'error');
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            loadCandelaria();
        });
    </script>
@endpush
