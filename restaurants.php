<?php
$page_title = 'Restaurants à Agen';
require_once 'includes/header.php';
?>

<div class="container">
    <!-- Header -->
    <div class="page-header">
        <h1 class="page-title" style="background: linear-gradient(135deg, #059669, #10b981); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;">
            Restaurants à Agen
        </h1>
        <p class="page-subtitle">
            Découvrez la richesse gastronomique d'Agen, des bistrots traditionnels aux restaurants gastronomiques.
        </p>
        <div style="display: flex; justify-content: center; align-items: center; margin-top: 1rem; gap: 1rem;">
            <span id="results-count" style="background: #f3f4f6; color: #374151; padding: 0.5rem 0.75rem; border-radius: 9999px;">
                Chargement...
            </span>
            <span style="background: #f3f4f6; color: #374151; padding: 0.5rem 0.75rem; border-radius: 9999px;">
                🍽️ Cuisine locale
            </span>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="filters">
        <div class="filters-grid">
            <div class="filter-controls">
                <div class="form-group" style="margin-bottom: 0;">
                    <input type="text" 
                           class="form-input search-input" 
                           placeholder="Rechercher un restaurant..." 
                           style="padding-left: 2.5rem;">
                    <svg style="position: absolute; left: 0.75rem; top: 50%; transform: translateY(-50%); width: 1.25rem; height: 1.25rem; color: #9ca3af; pointer-events: none;" fill="currentColor" viewBox="0 0 16 16">
                        <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z"/>
                    </svg>
                </div>

                <div class="form-group" style="margin-bottom: 0;">
                    <select class="form-select filter-select" data-filter="category">
                        <option value="">Toutes catégories</option>
                        <option value="Gastronomique">Gastronomique</option>
                        <option value="Bistrot">Bistrot</option>
                        <option value="Italien">Italien</option>
                        <option value="Asiatique">Asiatique</option>
                        <option value="Végétarien">Végétarien</option>
                        <option value="Brasserie">Brasserie</option>
                    </select>
                </div>

                <div class="form-group" style="margin-bottom: 0;">
                    <select class="form-select filter-select" data-filter="priceRange">
                        <option value="">Tous prix</option>
                        <option value="Gratuit">Gratuit</option>
                        <option value="€">€</option>
                        <option value="€€">€€</option>
                        <option value="€€€">€€€</option>
                    </select>
                </div>

                <div class="form-group" style="margin-bottom: 0;">
                    <select class="form-select filter-select" data-filter="minRating">
                        <option value="0">Toutes notes</option>
                        <option value="3">3+ ⭐</option>
                        <option value="4">4+ ⭐</option>
                        <option value="4.5">4.5+ ⭐</option>
                    </select>
                </div>
            </div>

            <button class="btn btn-outline clear-filters" style="display: none;">
                <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                    <path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8 2.146 2.854Z"/>
                </svg>
                Effacer
                <span class="filter-count" style="background: #3b82f6; color: white; font-size: 0.75rem; padding: 0.125rem 0.375rem; border-radius: 9999px; margin-left: 0.5rem;">0</span>
            </button>
        </div>
    </div>

    <!-- Results Container -->
    <div id="results-container" class="results-container" data-type="restaurants">
        <div class="loading">
            <div class="loading-spinner"></div>
            <p>Chargement des restaurants...</p>
        </div>
    </div>
</div>

<style>
.form-group {
    position: relative;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    loadRestaurants();
    
    // Gestionnaires d'événements pour les filtres
    const searchInput = document.querySelector('.search-input');
    const filterSelects = document.querySelectorAll('.filter-select');
    const clearButton = document.querySelector('.clear-filters');
    
    let searchTimeout;
    
    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            loadRestaurants();
            updateFilterDisplay();
        }, 300);
    });
    
    filterSelects.forEach(select => {
        select.addEventListener('change', function() {
            loadRestaurants();
            updateFilterDisplay();
        });
    });
    
    clearButton.addEventListener('click', function() {
        searchInput.value = '';
        filterSelects.forEach(select => select.selectedIndex = 0);
        loadRestaurants();
        updateFilterDisplay();
    });
    
    function updateFilterDisplay() {
        const searchValue = searchInput.value.trim();
        const activeFilters = Array.from(filterSelects).filter(select => select.value !== '').length;
        const totalFilters = (searchValue ? 1 : 0) + activeFilters;
        
        if (totalFilters > 0) {
            clearButton.style.display = 'flex';
            clearButton.querySelector('.filter-count').textContent = totalFilters;
        } else {
            clearButton.style.display = 'none';
        }
    }
});

async function loadRestaurants() {
    const container = document.getElementById('results-container');
    const countSpan = document.getElementById('results-count');
    
    // Affichage du loader
    container.innerHTML = `
        <div class="loading">
            <div class="loading-spinner"></div>
            <p>Chargement des restaurants...</p>
        </div>
    `;
    
    try {
        // Construction des paramètres
        const params = new URLSearchParams();
        
        const searchValue = document.querySelector('.search-input').value.trim();
        if (searchValue) params.append('search', searchValue);
        
        const categoryValue = document.querySelector('[data-filter="category"]').value;
        if (categoryValue) params.append('category', categoryValue);
        
        const priceValue = document.querySelector('[data-filter="priceRange"]').value;
        if (priceValue) params.append('priceRange', priceValue);
        
        const ratingValue = document.querySelector('[data-filter="minRating"]').value;
        if (ratingValue && ratingValue !== '0') params.append('minRating', ratingValue);
        
        // Requête API
        const response = await fetch(`api/restaurants.php?${params}`);
        const data = await response.json();
        
        if (data.success) {
            countSpan.textContent = `${data.count} restaurant${data.count > 1 ? 's' : ''} trouvé${data.count > 1 ? 's' : ''}`;
            
            if (data.items.length === 0) {
                container.innerHTML = `
                    <div class="empty-state">
                        <div class="empty-icon">🔍</div>
                        <h3>Aucun restaurant trouvé</h3>
                        <p>Essayez d'ajuster vos critères de recherche.</p>
                    </div>
                `;
            } else {
                const itemsHtml = data.items.map(createRestaurantCard).join('');
                container.innerHTML = `<div class="grid grid-3">${itemsHtml}</div>`;
                
                // Réattacher les événements favoris
                attachFavoriteEvents();
            }
        } else {
            throw new Error(data.message);
        }
        
    } catch (error) {
        console.error('Erreur:', error);
        container.innerHTML = `
            <div class="empty-state">
                <div class="empty-icon">❌</div>
                <h3>Erreur de chargement</h3>
                <p>Impossible de charger les restaurants.</p>
            </div>
        `;
        countSpan.textContent = '0 restaurant trouvé';
    }
}

function createRestaurantCard(restaurant) {
    const tags = restaurant.tags.slice(0, 3);
    const tagsHtml = tags.map(tag => `<span class="tag">${tag}</span>`).join('');
    
    return `
        <div class="card hover-scale" data-id="${restaurant.id}" data-type="restaurant">
            <div class="card-image">
                <img src="${restaurant.image}" alt="${restaurant.name}" loading="lazy">
                <div class="card-badge">
                    <svg width="16" height="16" fill="#fbbf24" viewBox="0 0 16 16">
                        <path d="M3.612 15.443c-.386.198-.824-.149-.746-.592l.83-4.73L.173 6.765c-.329-.314-.158-.888.283-.95l4.898-.696L7.538.792c.197-.39.73-.39.927 0l2.184 4.327 4.898.696c.441.062.612.636.282.95l-3.522 3.356.83 4.73c.078.443-.36.79-.746.592L8 13.187l-4.389 2.256z"/>
                    </svg>
                    ${restaurant.rating}
                </div>
                <button class="favorite-btn" data-id="${restaurant.id}" data-type="restaurant">
                    <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                        <path d="m8 2.748-.717-.737C5.6.281 2.514.878 1.4 3.053c-.523 1.023-.641 2.5.314 4.385.92 1.815 2.834 3.989 6.286 6.357 3.452-2.368 5.365-4.542 6.286-6.357.955-1.886.838-3.362.314-4.385C13.486.878 10.4.28 8.717 2.01L8 2.748z"/>
                    </svg>
                </button>
            </div>
            <div class="card-content">
                <h3 class="card-title">${restaurant.name}</h3>
                <div class="card-category">${restaurant.category} • ${restaurant.cuisine}</div>
                <p class="card-description">${restaurant.description}</p>
                
                <div class="card-details">
                    <div class="card-detail">
                        <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                            <path d="M8 16s6-5.686 6-10A6 6 0 0 0 2 6c0 4.314 6 10 6 10z"/>
                            <path d="M8 8a2 2 0 1 1 0-4 2 2 0 0 1 0 4z"/>
                        </svg>
                        ${restaurant.address}
                    </div>
                    <div class="card-detail">
                        <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                            <path d="M3.654 1.328a.678.678 0 0 0-1.015-.063L1.605 2.3c-.483.484-.661 1.169-.45 1.77a17.568 17.568 0 0 0 4.168 6.608 17.569 17.569 0 0 0 6.608 4.168c.601.211 1.286.033 1.77-.45l1.034-1.034a.678.678 0 0 0-.063-1.015l-2.307-1.794a.678.678 0 0 0-.58-.122L9.98 10.19c-.197.099-.42.097-.615-.01L5.478 7.942a.678.678 0 0 1-.122-.58L6.69 5.058a.678.678 0 0 0 .063-1.015L4.959 1.736z"/>
                        </svg>
                        ${restaurant.phone}
                    </div>
                    <div class="card-detail">
                        <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                            <path d="M8 3.5a.5.5 0 0 0-1 0V9a.5.5 0 0 0 .252.434l3.5 2a.5.5 0 0 0 .496-.868L8 8.71V3.5z"/>
                            <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm7-8A7 7 0 1 1 1 8a7 7 0 0 1 14 0z"/>
                        </svg>
                        ${restaurant.opening_hours}
                    </div>
                </div>
                
                ${tagsHtml ? `<div class="card-tags">${tagsHtml}</div>` : ''}
                
                <a href="detail.php?type=restaurant&id=${restaurant.id}" class="btn btn-success">
                    Voir les détails
                </a>
            </div>
        </div>
    `;
}

function attachFavoriteEvents() {
    document.querySelectorAll('.favorite-btn').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            const itemId = this.dataset.id;
            const itemType = this.dataset.type;
            const favoriteKey = `${itemType}_${itemId}`;
            
            let favorites = JSON.parse(localStorage.getItem('agen_favorites') || '[]');
            const isFavorite = favorites.includes(favoriteKey);
            
            if (isFavorite) {
                favorites = favorites.filter(fav => fav !== favoriteKey);
                this.classList.remove('active');
                GuideAgen.showNotification('Retiré des favoris', 'success');
            } else {
                favorites.push(favoriteKey);
                this.classList.add('active');
                GuideAgen.showNotification('Ajouté aux favoris', 'success');
            }
            
            localStorage.setItem('agen_favorites', JSON.stringify(favorites));
            GuideAgen.updateFavoritesCount();
            
            // Animation
            this.style.transform = 'scale(1.2)';
            setTimeout(() => {
                this.style.transform = '';
            }, 200);
        });
        
        // Vérifier si déjà en favoris
        const favoriteKey = `${button.dataset.type}_${button.dataset.id}`;
        const favorites = JSON.parse(localStorage.getItem('agen_favorites') || '[]');
        if (favorites.includes(favoriteKey)) {
            button.classList.add('active');
        }
    });
}
</script>

<?php require_once 'includes/footer.php'; ?>