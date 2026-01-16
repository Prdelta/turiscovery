@extends('layouts.app')

@section('title', 'Evento - Turiscovery')

@section('content')
    <div class="container" style="margin-top: 2rem; margin-bottom: 4rem;">
        <a href="/eventos" class="btn btn-outline" style="margin-bottom: 2rem;">← Volver a Eventos</a>

        <div id="evento-detail">
            <div class="card" style="text-align: center; padding: 3rem;">
                <p class="text-secondary">Cargando evento...</p>
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
        const eventoId = window.location.pathname.split('/').pop();

        async function loadEvento() {
            try {
                const response = await axios.get(`/api/eventos/${eventoId}`);
                const evento = response.data.data;

                const container = document.getElementById('evento-detail');

                const now = new Date();
                const startTime = new Date(evento.start_time);
                const endTime = new Date(evento.end_time);
                let statusBadge = '';

                if (now < startTime) {
                    statusBadge =
                        '<span class="badge" style="background: #10b981; color: white; font-size: 1rem;">📅 Próximo</span>';
                } else if (now >= startTime && now <= endTime) {
                    statusBadge =
                        '<span class="badge" style="background: #f59e0b; color: white; font-size: 1rem;">🔴 EN VIVO AHORA</span>';
                } else {
                    statusBadge =
                        '<span class="badge" style="background: #6b7280; color: white; font-size: 1rem;">Finalizado</span>';
                }

                const isFree = !evento.ticket_price || evento.ticket_price == 0;

                container.innerHTML = `
            <div class="grid grid-3" style="gap: 2rem;">
                <div style="grid-column: span 2;">
                    ${evento.images && evento.images[0] ? `
                            <img src="${evento.images[0]}" alt="${evento.title}" style="width: 100%; height: 400px; object-fit: cover; border-radius: 1rem; margin-bottom: 2rem;">
                        ` : ''}
                    
                    <div style="margin-bottom: 2rem;">
                        <div style="display: flex; gap: 0.5rem; margin-bottom: 1rem;">
                            ${getCategoryBadge(evento.category)}
                            ${statusBadge}
                        </div>
                        <h1 style="margin: 0 0 1rem 0;">${evento.title}</h1>
                        <p style="font-size: 1.125rem; color: var(--text-secondary);">${evento.description}</p>
                    </div>
                    
                    ${evento.content ? `
                            <div style="background: white; padding: 2rem; border-radius: 1rem; box-shadow: 0 2px 4px rgba(0,0,0,0.1); margin-bottom: 2rem;">
                                <h2 style="margin: 0 0 1rem 0;">Detalles del Evento</h2>
                                <p style="white-space: pre-line;">${evento.content}</p>
                            </div>
                        ` : ''}
                    
                    ${evento.latitude && evento.longitude ? `
                            <div style="background: white; padding: 2rem; border-radius: 1rem; box-shadow: 0 2px 4px rgba(0,0,0,0.1); margin-bottom: 2rem;">
                                <h2 style="margin: 0 0 1rem 0;">📍 Ubicación</h2>
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
                        <div style="text-align: center; padding: 1.5rem; background: linear-gradient(135deg, #8b5cf6, #7c3aed); border-radius: 0.5rem; color: white; margin-bottom: 1.5rem;">
                            ${isFree ? 
                                '<h2 style="font-size: 2rem; margin: 0; font-weight: 800;">GRATIS 🎉</h2>' :
                                `<h2 style="font-size: 2.5rem; margin: 0; font-weight: 800;">${formatPrice(evento.ticket_price)}</h2><p style="margin: 0.5rem 0 0 0; opacity: 0.9;">Entrada</p>`
                            }
                        </div>
                        
                        <div style="margin-bottom: 1.5rem;">
                            <h3 style="margin: 0 0 1rem 0; font-size: 1rem;">📅 Fecha y Hora</h3>
                            <p style="margin: 0.5rem 0; font-size: 0.875rem;"><strong>Inicio:</strong><br>${formatDate(evento.start_time)}</p>
                            <p style="margin: 0.5rem 0; font-size: 0.875rem;"><strong>Fin:</strong><br>${formatDate(evento.end_time)}</p>
                        </div>
                        
                        ${evento.address ? `
                                <div style="margin-bottom: 1.5rem;">
                                    <h3 style="margin: 0 0 0.5rem 0; font-size: 1rem;">📍 Dirección</h3>
                                    <p style="margin: 0; font-size: 0.875rem;">${evento.address}</p>
                                </div>
                            ` : ''}
                        
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

                // Initialize map if location exists
                if (evento.latitude && evento.longitude) {
                    setTimeout(() => {
                        const map = L.map('map').setView([evento.latitude, evento.longitude], 15);
                        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);
                        L.marker([evento.latitude, evento.longitude]).addTo(map);
                    }, 100);
                }

                // Load reviews and favorite status
                loadReviews();
                checkFavoriteStatus();

            } catch (error) {
                console.error('Error loading evento:', error);
                document.getElementById('evento-detail').innerHTML = `
            <div class="card" style="text-align: center; padding: 3rem;">
                <h2>Evento no encontrado</h2>
                <p class="text-secondary">El evento que buscas no existe o ha sido eliminado.</p>
                <a href="/eventos" class="btn btn-primary" style="margin-top: 1rem;">Ver Todos los Eventos</a>
            </div>
        `;
            }
        }

        function getCategoryBadge(category) {
            const badges = {
                concert: '<span class="badge" style="background: #ec4899; color: white;">🎵 Concierto</span>',
                festival: '<span class="badge" style="background: #f59e0b; color: white;">🎉 Festival</span>',
                cultural: '<span class="badge" style="background: #0ea5e9; color: white;">🎭 Cultural</span>',
                nightlife: '<span class="badge" style="background: #8b5cf6; color: white;">🌙 Nocturno</span>',
                sports: '<span class="badge" style="background: #10b981; color: white;">⚽ Deportivo</span>',
                exhibition: '<span class="badge" style="background: #6366f1; color: white;">🖼️ Exposición</span>',
            };
            return badges[category] || '';
        }

        async function loadReviews() {
            try {
                const response = await axios.get(`/api/reviews`, {
                    params: {
                        reviewable_type: 'App\\\\Models\\\\Evento',
                        reviewable_id: eventoId
                    }
                });

                const reviews = response.data.data.data;
                const container = document.getElementById('reviews-list');

                if (reviews.length === 0) {
                    container.innerHTML =
                        '<p class="text-secondary">Aún no hay reseñas. ¡Sé el primero en compartir tu experiencia!</p>';
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
                        favoritable_type: 'App\\\\Models\\\\Evento',
                        favoritable_id: eventoId
                    }
                });

                if (response.data.data.is_favorite) {
                    document.getElementById('favorite-icon').textContent = '♥';
                    document.getElementById('favorite-btn').classList.add('btn-primary');
                    document.getElementById('favorite-btn').classList.remove('btn-outline');
                }
            } catch (error) {
                // Not logged in or error
            }
        }

        async function toggleFavorite() {
            const token = api.getToken();
            if (!token) {
                showNotification('Debes iniciar sesión para guardar favoritos', 'error');
                window.location.href = '/login';
                return;
            }

            try {
                await api.toggleFavorite('App\\\\Models\\\\Evento', eventoId);

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

            // Simple prompt for now (can be improved with a modal)
            const rating = prompt('Calificación (1-5 estrellas):');
            if (!rating || rating < 1 || rating > 5) return;

            const title = prompt('Título de tu reseña:');
            const comment = prompt('Tu comentario:');

            submitReview(parseInt(rating), title, comment);
        }

        async function submitReview(rating, title, comment) {
            try {
                await api.addReview({
                    reviewable_type: 'App\\\\Models\\\\Evento',
                    reviewable_id: eventoId,
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
            loadEvento();
        });
    </script>
@endpush
