@extends('layouts.app')

@section('title', 'Experiencia - Turiscovery')

@section('content')
    <div class="container" style="margin-top: 2rem; margin-bottom: 4rem;">
        <a href="/experiencias" class="btn btn-outline" style="margin-bottom: 2rem;">← Volver a Experiencias</a>

        <div id="experiencia-detail">
            <div class="card" style="text-align: center; padding: 3rem;">
                <p class="text-secondary">Cargando experiencia...</p>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
@endpush

@push('scripts')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        const experienciaId = window.location.pathname.split('/').pop();

        async function loadExperiencia() {
            try {
                const response = await axios.get(`/api/experiencias/${experienciaId}`);
                const exp = response.data.data;

                const container = document.getElementById('experiencia-detail');

                container.innerHTML = `
            <div class="grid grid-3" style="gap: 2rem;">
                <div style="grid-column: span 2;">
                    ${exp.images && exp.images[0] ? `
                            <img src="${exp.images[0]}" alt="${exp.title}" style="width: 100%; height: 400px; object-fit: cover; border-radius: 1rem; margin-bottom: 2rem;">
                        ` : ''}
                    
                    <div style="margin-bottom: 2rem;">
                        <div style="display: flex; gap: 0.5rem; align-items: center; margin-bottom: 1rem;">
                            ${getDifficultyBadge(exp.difficulty)}
                            ${exp.is_active ? '<span class="badge badge-success">✓ Disponible</span>' : '<span class="badge" style="background: #6b7280; color: white;">No Disponible</span>'}
                        </div>
                        <h1 style="margin: 0 0 1rem 0;">${exp.title}</h1>
                        <p style="font-size: 1.125rem; color: var(--text-secondary);">${exp.description}</p>
                    </div>
                    
                    ${exp.tags && exp.tags.length > 0 ? `
                            <div style="display: flex; gap: 0.5rem; flex-wrap: wrap; margin-bottom: 2rem;">
                                ${exp.tags.map(tag => `<span class="badge badge-primary">${tag}</span>`).join('')}
                            </div>
                        ` : ''}
                    
                    ${exp.content ? `
                            <div style="background: white; padding: 2rem; border-radius: 1rem; box-shadow: 0 2px 4px rgba(0,0,0,0.1); margin-bottom: 2rem;">
                                <h2 style="margin: 0 0 1rem 0;">📖 Descripción Completa</h2>
                                <p style="white-space: pre-line;">${exp.content}</p>
                            </div>
                        ` : ''}
                    
                    ${exp.latitude && exp.longitude ? `
                            <div style="background: white; padding: 2rem; border-radius: 1rem; box-shadow: 0 2px 4px rgba(0,0,0,0.1); margin-bottom: 2rem;">
                                <h2 style="margin: 0 0 1rem 0;">📍 Punto de Encuentro</h2>
                                ${exp.address ? `<p style="margin-bottom: 1rem;">${exp.address}</p>` : ''}
                                <div id="map" style="height: 300px; border-radius: 0.5rem;"></div>
                            </div>
                        ` : ''}
                    
                    <div id="reviews-section" style="background: white; padding: 2rem; border-radius: 1rem; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                        <h2 style="margin: 0 0 1.5rem 0;">⭐ Reseñas</h2>
                        <div id="reviews-list"></div>
                    </div>
                </div>
                
                <div>
                    <div style="background: white; padding: 2rem; border-radius: 1rem; box-shadow: 0 2px 4px rgba(0,0,0,0.1); position: sticky; top: 2rem;">
                        ${exp.price_pen ? `
                                <div style="text-align: center; padding: 1.5rem; background: linear-gradient(135deg, #10b981, #059669); border-radius: 0.5rem; color: white; margin-bottom: 1.5rem;">
                                    <h2 style="font-size: 2.5rem; margin: 0; font-weight: 800;">${formatPrice(exp.price_pen)}</h2>
                                    <p style="margin: 0.5rem 0 0 0; opacity: 0.9;">Por persona</p>
                                </div>
                            ` : `
                                <div style="text-align: center; padding: 1.5rem; background: linear-gradient(135deg, #6366f1, #4f46e5); border-radius: 0.5rem; color: white; margin-bottom: 1.5rem;">
                                    <h2 style="font-size: 1.5rem; margin: 0; font-weight: 800;">Precio a Consultar</h2>
                                    <p style="margin: 0.5rem 0 0 0; opacity: 0.9; font-size: 0.875rem;">Contacta al organizador</p>
                                </div>
                            `}
                        
                        <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1rem; margin-bottom: 1.5rem;">
                            ${exp.duration_hours ? `
                                    <div style="background: var(--bg-light); padding: 1rem; border-radius: 0.5rem; text-align: center;">
                                        <p style="margin: 0; font-size: 0.875rem; color: var(--text-secondary);">⏱️ Duración</p>
                                        <p style="margin: 0.25rem 0 0 0; font-weight: 600; font-size: 1.25rem;">${exp.duration_hours}h</p>
                                    </div>
                                ` : ''}
                            ${exp.max_participants ? `
                                    <div style="background: var(--bg-light); padding: 1rem; border-radius: 0.5rem; text-align: center;">
                                        <p style="margin: 0; font-size: 0.875rem; color: var(--text-secondary);">👥 Max</p>
                                        <p style="margin: 0.25rem 0 0 0; font-weight: 600; font-size: 1.25rem;">${exp.max_participants}</p>
                                    </div>
                                ` : ''}
                        </div>
                        
                        <div style="margin-bottom: 1.5rem;">
                            <h3 style="margin: 0 0 0.5rem 0; font-size: 1rem;">🔥 Dificultad</h3>
                            <p style="margin: 0; font-size: 0.875rem;">${getDifficultyDescription(exp.difficulty)}</p>
                        </div>
                        
                        <button onclick="toggleFavorite()" id="favorite-btn" class="btn btn-outline" style="width: 100%; margin-bottom: 0.5rem;">
                            <span id="favorite-icon">♡</span> Guardar
                        </button>
                        
                        <button onclick="openReviewModal()" class="btn btn-primary" style="width: 100%;">
                            ⭐ Escribir Reseña
                        </button>
                    </div>
                </div>
            </div>
        `;

                if (exp.latitude && exp.longitude) {
                    setTimeout(() => {
                        const map = L.map('map').setView([exp.latitude, exp.longitude], 15);
                        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);
                        L.marker([exp.latitude, exp.longitude]).addTo(map);
                    }, 100);
                }

                loadReviews();
                checkFavoriteStatus();

            } catch (error) {
                console.error('Error loading experiencia:', error);
                document.getElementById('experiencia-detail').innerHTML = `
            <div class="card" style="text-align: center; padding: 3rem;">
                <h2>Experiencia no encontrada</h2>
                <p class="text-secondary">La experiencia que buscas no existe o ha sido eliminada.</p>
                <a href="/experiencias" class="btn btn-primary" style="margin-top: 1rem;">Ver Todas las Experiencias</a>
            </div>
        `;
            }
        }

        function getDifficultyBadge(difficulty) {
            const badges = {
                easy: '<span class="badge badge-success" style="font-size: 1rem;">✓ Fácil</span>',
                medium: '<span class="badge badge-warning" style="font-size: 1rem;">⚡ Media</span>',
                hard: '<span class="badge badge-secondary" style="font-size: 1rem;">🔥 Difícil</span>'
            };
            return badges[difficulty] || '';
        }

        function getDifficultyDescription(difficulty) {
            const descriptions = {
                easy: 'Apta para todos. No requiere experiencia previa ni condición física especial.',
                medium: 'Requiere condición física básica y algo de experiencia. Apta para la mayoría.',
                hard: 'Solo para personas con buena condición física y experiencia. Desafiante.'
            };
            return descriptions[difficulty] || '';
        }

        async function loadReviews() {
            try {
                const response = await axios.get(`/api/reviews`, {
                    params: {
                        reviewable_type: 'App\\\\Models\\\\Experiencia',
                        reviewable_id: experienciaId
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
                        favoritable_type: 'App\\\\Models\\\\Experiencia',
                        favoritable_id: experienciaId
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
                await api.toggleFavorite('App\\\\Models\\\\Experiencia', experienciaId);

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
                    reviewable_type: 'App\\\\Models\\\\Experiencia',
                    reviewable_id: experienciaId,
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
            loadExperiencia();
        });
    </script>
@endpush
