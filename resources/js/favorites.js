// Favorite Toggle Functionality
document.addEventListener('DOMContentLoaded', () => {
    // Handle favorite button clicks
    document.addEventListener('click', async (e) => {
        const favoriteBtn = e.target.closest('[data-favorite-btn]');
        if (!favoriteBtn) return;

        e.preventDefault();
        e.stopPropagation();

        const type = favoriteBtn.dataset.type;
        const id = favoriteBtn.dataset.id;
        const icon = favoriteBtn.querySelector('i[data-lucide="heart"]');

        if (!type || !id) {
            console.error('Missing type or id in favorite button');
            return;
        }

        try {
            const response = await fetch('/user/favorites/toggle', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ type, id: parseInt(id) })
            });

            const data = await response.json();

            if (data.success) {
                // Update UI
                if (data.favorited) {
                    icon?.classList.add('fill-current');
                    favoriteBtn.classList.add('text-red-500');
                    favoriteBtn.classList.remove('text-slate-400');
                } else {
                    icon?.classList.remove('fill-current');
                    favoriteBtn.classList.remove('text-red-500');
                    favoriteBtn.classList.add('text-slate-400');
                }

                // Show toast notification (optional)
                showToast(data.message);
            }
        } catch (error) {
            console.error('Error toggling favorite:', error);
            showToast('Error al actualizar favoritos');
        }
    });
});

// Simple toast notification
function showToast(message) {
    const toast = document.createElement('div');
    toast.className = 'fixed bottom-4 right-4 bg-slate-800 text-white px-4 py-3 rounded-lg shadow-lg z-50 animate-fade-in';
    toast.textContent = message;
    document.body.appendChild(toast);

    setTimeout(() => {
        toast.classList.add('opacity-0', 'transition-opacity');
        setTimeout(() => toast.remove(), 300);
    }, 2000);
}
