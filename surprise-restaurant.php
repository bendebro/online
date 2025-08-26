<?php
$page_title = 'Surprise-moi ! - Restaurant au hasard';
require_once 'includes/header.php';
?>

<main class="main-content">
    <!-- Section Hero -->
    <section class="hero-section">
        <div class="container">
            <div class="hero-content">
                <h1>🎲 Surprise-moi !</h1>
                <p class="hero-subtitle">Laissez le hasard choisir votre prochain restaurant à Agen</p>
                <p class="hero-description">Vous ne savez pas où manger ? Notre système de sélection aléatoire vous propose une découverte culinaire surprise !</p>
            </div>
        </div>
    </section>

    <!-- Section Filtres -->
    <section class="filters-section">
        <div class="container">
            <div class="filters-card">
                <h3>🎯 Personnalisez votre surprise</h3>
                <div class="filters-grid">
                    <div class="filter-group">
                        <label for="budget-filter">💰 Budget</label>
                        <select id="budget-filter" class="filter-select">
                            <option value="">Peu importe</option>
                            <option value="1">Budget (€)</option>
                            <option value="2">Modéré (€€)</option>
                            <option value="3">Élevé (€€€)</option>
                            <option value="4">Très élevé (€€€€)</option>
                        </select>
                    </div>
                    
                    <div class="filter-group">
                        <label for="cuisine-filter">🍽️ Type de cuisine</label>
                        <select id="cuisine-filter" class="filter-select">
                            <option value="">Toutes les cuisines</option>
                            <option value="Française">Française</option>
                            <option value="Italienne">Italienne</option>
                            <option value="Japonaise">Japonaise</option>
                            <option value="Indienne">Indienne</option>
                            <option value="Chinoise">Chinoise</option>
                            <option value="Américaine">Américaine</option>
                            <option value="Asiatique">Asiatique</option>
                        </select>
                    </div>
                    
                    <div class="filter-group">
                        <label for="category-filter">🏷️ Catégorie</label>
                        <select id="category-filter" class="filter-select">
                            <option value="">Toutes les catégories</option>
                            <option value="gastronomique">Gastronomique</option>
                            <option value="brasserie">Brasserie</option>
                            <option value="italien">Italien</option>
                            <option value="fast_food">Fast Food</option>
                            <option value="pub">Pub</option>
                            <option value="café">Café</option>
                        </select>
                    </div>
                    
                    <div class="filter-group">
                        <label for="rating-filter">⭐ Note minimum</label>
                        <select id="rating-filter" class="filter-select">
                            <option value="">Toutes les notes</option>
                            <option value="4.5">4.5+ étoiles</option>
                            <option value="4.0">4.0+ étoiles</option>
                            <option value="3.5">3.5+ étoiles</option>
                            <option value="3.0">3.0+ étoiles</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Section Roulette -->
    <section class="roulette-section">
        <div class="container">
            <div class="roulette-container">
                <div class="roulette-wheel" id="roulette-wheel">
                    <div class="wheel-center">
                        <div class="wheel-button" id="spin-button">
                            <span>🎲</span>
                            <div>Surprise !</div>
                        </div>
                    </div>
                    <div class="wheel-loading" id="wheel-loading">
                        <div class="loading-spinner"></div>
                        <div>Sélection en cours...</div>
                    </div>
                </div>
                
                <div class="roulette-pointer"></div>
            </div>
        </div>
    </section>

    <!-- Section Résultat -->
    <section class="result-section" id="result-section" style="display: none;">
        <div class="container">
            <div class="result-card" id="result-card">
                <div class="result-header">
                    <h2>🎉 Votre restaurant surprise !</h2>
                    <button class="btn-retry" id="retry-button">🔄 Nouveau tirage</button>
                </div>
                
                <div class="restaurant-result" id="restaurant-result">
                    <!-- Le restaurant sélectionné sera affiché ici par JavaScript -->
                </div>
                
                <div class="result-actions">
                    <button class="btn-details" id="view-details-btn">📍 Voir les détails</button>
                    <button class="btn-map" id="view-map-btn">🗺️ Voir sur la carte</button>
                </div>
            </div>
        </div>
    </section>

    <!-- Section Statistiques -->
    <section class="stats-section">
        <div class="container">
            <div class="stats-grid">
                <div class="stat-item">
                    <div class="stat-number" id="total-restaurants">33</div>
                    <div class="stat-label">Restaurants disponibles</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number" id="selections-today">0</div>
                    <div class="stat-label">Sélections aujourd'hui</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">4.2</div>
                    <div class="stat-label">Note moyenne</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">18</div>
                    <div class="stat-label">Types de cuisine</div>
                </div>
            </div>
        </div>
    </section>
</main>

<style>
/* Styles pour la page Surprise Restaurant - Compatible thème jour/nuit */
.hero-section {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 80px 0;
    text-align: center;
}

.hero-content h1 {
    font-size: 3.5rem;
    margin-bottom: 20px;
    font-weight: bold;
}

.hero-subtitle {
    font-size: 1.5rem;
    margin-bottom: 15px;
    opacity: 0.9;
}

.hero-description {
    font-size: 1.1rem;
    opacity: 0.8;
    max-width: 600px;
    margin: 0 auto;
}

.filters-section {
    padding: 50px 0;
    background: var(--bg-secondary);
}

.filters-card {
    background: var(--bg-primary);
    border-radius: 20px;
    padding: 30px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    max-width: 800px;
    margin: 0 auto;
    border: 1px solid var(--border-color);
}

.filters-card h3 {
    text-align: center;
    margin-bottom: 25px;
    color: var(--text-primary);
    font-size: 1.4rem;
}

.filters-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
}

.filter-group label {
    display: block;
    margin-bottom: 8px;
    font-weight: 600;
    color: var(--text-secondary);
}

.filter-select {
    width: 100%;
    padding: 12px;
    border: 2px solid var(--border-color);
    border-radius: 10px;
    font-size: 1rem;
    transition: all 0.3s ease;
    background: var(--bg-primary);
    color: var(--text-primary);
}

.filter-select:focus {
    border-color: #667eea;
    outline: none;
}

.roulette-section {
    padding: 80px 0;
    background: var(--bg-primary);
}

.roulette-container {
    display: flex;
    justify-content: center;
    align-items: center;
    position: relative;
}

.roulette-wheel {
    width: 300px;
    height: 300px;
    border: 8px solid #667eea;
    border-radius: 50%;
    position: relative;
    background: linear-gradient(45deg, 
        #ff6b6b 0%, #ff8e53 12.5%, 
        #ff6b6b 12.5%, #4ecdc4 25%, 
        #45b7d1 25%, #96ceb4 37.5%, 
        #feca57 37.5%, #ff9ff3 50%, 
        #54a0ff 50%, #5f27cd 62.5%, 
        #00d2d3 62.5%, #ff9ff3 75%, 
        #54a0ff 75%, #5f27cd 87.5%, 
        #ff6b6b 87.5%, #ff6b6b 100%);
    transition: transform 3s cubic-bezier(0.23, 1, 0.32, 1);
}

.wheel-center {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    background: var(--bg-primary);
    border-radius: 50%;
    width: 120px;
    height: 120px;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 4px 20px rgba(0,0,0,0.2);
}

.wheel-button {
    cursor: pointer;
    text-align: center;
    font-weight: bold;
    color: #667eea;
    transition: all 0.3s ease;
    user-select: none;
}

.wheel-button:hover {
    transform: scale(1.1);
}

.wheel-button span {
    font-size: 2rem;
    display: block;
    margin-bottom: 5px;
}

.wheel-loading {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    background: var(--bg-primary);
    border-radius: 50%;
    width: 120px;
    height: 120px;
    display: none;
    align-items: center;
    justify-content: center;
    flex-direction: column;
    font-size: 0.9rem;
    color: #667eea;
    font-weight: 600;
    box-shadow: 0 4px 20px rgba(0,0,0,0.2);
}

.loading-spinner {
    width: 30px;
    height: 30px;
    border: 3px solid var(--border-color);
    border-top: 3px solid #667eea;
    border-radius: 50%;
    animation: spin 1s linear infinite;
    margin-bottom: 8px;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

.roulette-pointer {
    position: absolute;
    top: -10px;
    left: 50%;
    transform: translateX(-50%);
    width: 0;
    height: 0;
    border-left: 15px solid transparent;
    border-right: 15px solid transparent;
    border-bottom: 25px solid #ff6b6b;
    z-index: 10;
}

.result-section {
    padding: 50px 0;
    background: var(--bg-secondary);
}

.result-card {
    background: var(--bg-primary);
    border-radius: 20px;
    padding: 40px;
    box-shadow: 0 15px 40px rgba(0,0,0,0.1);
    max-width: 800px;
    margin: 0 auto;
    animation: slideUp 0.6s ease-out;
    border: 1px solid var(--border-color);
}

@keyframes slideUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.result-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 30px;
    flex-wrap: wrap;
}

.result-header h2 {
    color: var(--text-primary);
    font-size: 1.8rem;
}

.btn-retry {
    background: #ff6b6b;
    color: white;
    border: none;
    padding: 12px 20px;
    border-radius: 10px;
    cursor: pointer;
    font-weight: 600;
    transition: all 0.3s ease;
}

.btn-retry:hover {
    background: #ff5252;
    transform: translateY(-2px);
}

.restaurant-result {
    margin-bottom: 30px;
}

.result-actions {
    display: flex;
    gap: 15px;
    justify-content: center;
    flex-wrap: wrap;
}

.btn-details, .btn-map {
    padding: 15px 25px;
    border: none;
    border-radius: 10px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    font-size: 1rem;
}

.btn-details {
    background: #667eea;
    color: white;
}

.btn-details:hover {
    background: #5a67d8;
    transform: translateY(-2px);
}

.btn-map {
    background: #4ecdc4;
    color: white;
}

.btn-map:hover {
    background: #26d0ce;
    transform: translateY(-2px);
}

.stats-section {
    padding: 50px 0;
    background: var(--bg-primary);
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 30px;
    max-width: 800px;
    margin: 0 auto;
}

.stat-item {
    text-align: center;
    padding: 20px;
    background: linear-gradient(135deg, #667eea, #764ba2);
    color: white;
    border-radius: 15px;
    box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
}

.stat-number {
    font-size: 2.5rem;
    font-weight: bold;
    margin-bottom: 10px;
}

.stat-label {
    font-size: 1rem;
    opacity: 0.9;
}

/* Responsive */
@media (max-width: 768px) {
    .hero-content h1 {
        font-size: 2.5rem;
    }
    
    .roulette-wheel {
        width: 250px;
        height: 250px;
    }
    
    .filters-grid {
        grid-template-columns: 1fr;
    }
    
    .result-header {
        flex-direction: column;
        gap: 15px;
        text-align: center;
    }
    
    .result-actions {
        flex-direction: column;
        align-items: center;
    }
    
    .btn-details, .btn-map {
        width: 100%;
        max-width: 250px;
    }
}
</style>

<script>
// Système de sélection aléatoire de restaurant
class RestaurantSurprise {
    constructor() {
        this.restaurants = [];
        this.selectedRestaurant = null;
        this.isSpinning = false;
        this.selectionsToday = this.getSelectionsToday();
        
        this.init();
    }
    
    async init() {
        try {
            await this.loadRestaurants();
            this.bindEvents();
            this.updateStats();
        } catch (error) {
            console.error('Erreur lors de l\'initialisation:', error);
            this.showError('Erreur lors du chargement des données');
        }
    }
    
    async loadRestaurants() {
        try {
            const response = await fetch('api/restaurants.php');
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            const data = await response.json();
            
            if (data.success && data.items) {
                this.restaurants = data.items;
                document.getElementById('total-restaurants').textContent = this.restaurants.length;
            } else {
                // Fallback : utiliser des données de test si l'API ne fonctionne pas
                console.warn('API ne fonctionne pas, utilisation des données de test');
                this.restaurants = this.getTestRestaurants();
                document.getElementById('total-restaurants').textContent = this.restaurants.length;
            }
        } catch (error) {
            console.error('Erreur lors du chargement des restaurants:', error);
            // Utiliser des données de test en cas d'erreur
            this.restaurants = this.getTestRestaurants();
            document.getElementById('total-restaurants').textContent = this.restaurants.length;
            this.showError('Impossible de charger les restaurants, utilisation des données de test');
        }
    }
    
    getTestRestaurants() {
        return [
            {
                id: 1,
                name: 'Le Nostradamus',
                description: 'Restaurant de charme dans une ferme rustique proposant une cuisine audacieuse et maîtrisée par le chef.',
                image: 'https://images.unsplash.com/photo-1414235077428-338989a2e8c0?w=800',
                address: '40 Rue des Nitiobriges, 47000 Agen',
                phone: '05 53 68 26 55',
                rating: 4.7,
                price_range: 4,
                category: 'gastronomique',
                cuisine: 'Française créative',
                opening_hours: 'Mar-Sam: 12h-13h30, 19h30-21h30',
                latitude: 44.2034,
                longitude: 0.6185,
                specialties: '["Cuisine créative", "Terroir revisité", "Menu dégustation"]',
                features: '["Cadre rustique", "Terrasse", "Parking"]',
                tags: '["gastronomique", "créatif", "terroir"]'
            },
            {
                id: 2,
                name: 'Serra Boutique Hôtel Restaurant',
                description: 'Restaurant gastronomique proposant petit-déjeuner, déjeuner, dîner et brunch.',
                image: 'https://images.unsplash.com/photo-1552566558-b04e6e1c1c8d?w=800',
                address: '2-4 Avenue du Général de Gaulle, 47000 Agen',
                phone: '05 53 77 88 99',
                rating: 4.8,
                price_range: 4,
                category: 'gastronomique',
                cuisine: 'Française haute cuisine',
                opening_hours: 'Lun-Dim: 7h-10h, 12h-14h, 19h-22h',
                latitude: 44.2061,
                longitude: 0.6184,
                specialties: '["Haute cuisine", "Petit-déjeuner", "Brunch"]',
                features: '["Hôtel boutique", "Service premium", "Parking"]',
                tags: '["gastronomique", "hôtel", "luxe"]'
            },
            {
                id: 3,
                name: 'Al Dente',
                description: 'Restaurant italien situé près de la cathédrale, proposant des pizzas et des pâtes fraîches maison.',
                image: 'https://images.unsplash.com/photo-1513104890138-7c749659a591?w=800',
                address: '11 Rue Molinier, 47000 Agen',
                phone: '05 53 66 44 55',
                rating: 4.4,
                price_range: 2,
                category: 'italien',
                cuisine: 'Italienne',
                opening_hours: 'Mar-Dim: 12h-14h, 19h-22h30',
                latitude: 44.2026,
                longitude: 0.6171,
                specialties: '["Pizza maison", "Pâtes fraîches", "Tiramisu"]',
                features: '["Près cathédrale", "Terrasse", "Emporter"]',
                tags: '["italien", "pizza", "pâtes"]'
            },
            {
                id: 4,
                name: 'Le Temple de la Bière',
                description: 'Pub convivial proposant une large sélection de bières et une cuisine bistrot.',
                image: 'https://images.unsplash.com/photo-1518176258769-f227c798150e?w=800',
                address: '6 Rue Garonne, 47000 Agen',
                phone: '05 53 66 78 90',
                rating: 4.4,
                price_range: 2,
                category: 'pub',
                cuisine: 'Bistrot',
                opening_hours: 'Mar-Sam: 15h-2h',
                latitude: 44.2065,
                longitude: 0.6195,
                specialties: '["Bières artisanales", "Planches charcuterie", "Burgers"]',
                features: '["Grande sélection bières", "Ambiance pub", "Soirées"]',
                tags: '["pub", "bières", "convivial"]'
            },
            {
                id: 5,
                name: 'Sushi Zen',
                description: 'Restaurant japonais proposant sushis frais, sashimis et spécialités japonaises.',
                image: 'https://images.unsplash.com/photo-1579584425555-c3ce17fd4351?w=800',
                address: '42 Rue des Cornières, 47000 Agen',
                phone: '05 53 77 56 78',
                rating: 4.1,
                price_range: 2,
                category: 'japonais',
                cuisine: 'Japonaise',
                opening_hours: 'Mar-Sam: 12h-14h, 19h-22h',
                latitude: 44.2044,
                longitude: 0.6162,
                specialties: '["Sushi", "Sashimi", "Ramen", "Maki"]',
                features: '["Emporter", "Livraison", "Menu midi"]',
                tags: '["sushi", "japonais", "zen"]'
            },
            {
                id: 6,
                name: 'McDonald\'s Agen Centre',
                description: 'Restaurant McDonald\'s du centre-ville d\'Agen, proposant burgers, frites et menus.',
                image: 'https://images.unsplash.com/photo-1552570297-a38a3886a0df?w=800',
                address: '89 Boulevard de la République, 47000 Agen',
                phone: '05 53 77 12 34',
                rating: 3.5,
                price_range: 1,
                category: 'fast_food',
                cuisine: 'Américaine',
                opening_hours: 'Lun-Dim: 8h-1h',
                latitude: 44.2055,
                longitude: 0.6175,
                specialties: '["Big Mac", "Chicken McNuggets", "Royal Cheese"]',
                features: '["Drive", "Livraison", "Wifi"]',
                tags: '["fast-food", "burgers", "livraison"]'
            },
            {
                id: 7,
                name: 'La Grande Brasserie',
                description: 'Brasserie située à la gare d\'Agen, offrant une cuisine traditionnelle de brasserie.',
                image: 'https://images.unsplash.com/photo-1414235077428-338989a2e8c0?w=800',
                address: '1 Place Rabelais, 47000 Agen',
                phone: '05 53 47 10 10',
                rating: 3.8,
                price_range: 2,
                category: 'brasserie',
                cuisine: 'Française',
                opening_hours: 'Lun-Dim: 6h-23h',
                latitude: 44.2052,
                longitude: 0.6202,
                specialties: '["Plats brasserie", "Petit-déjeuner", "Bières pression"]',
                features: '["Gare SNCF", "Ouvert tôt", "Terrasse"]',
                tags: '["brasserie", "gare", "traditionnel"]'
            },
            {
                id: 8,
                name: 'Villa Toscana',
                description: 'Restaurant italien familial proposant des pizzas au feu de bois et des spécialités toscanes.',
                image: 'https://images.unsplash.com/photo-1513104890138-7c749659a591?w=800',
                address: '22 Boulevard Carnot, 47000 Agen',
                phone: '05 53 45 67 89',
                rating: 4.3,
                price_range: 2,
                category: 'italien',
                cuisine: 'Italienne',
                opening_hours: 'Mar-Dim: 12h-14h, 19h-22h30',
                latitude: 44.2029,
                longitude: 0.6159,
                specialties: '["Pizza feu de bois", "Spécialités toscanes", "Risotto"]',
                features: '["Feu de bois", "Familial", "Terrasse"]',
                tags: '["italien", "pizza", "familial"]'
            },
            {
                id: 9,
                name: 'Le Maharaja',
                description: 'Restaurant indien authentique proposant currys, tandoori et spécialités indiennes.',
                image: 'https://images.unsplash.com/photo-1565557623262-b51c2513a641?w=800',
                address: '78 Rue Montesquieu, 47000 Agen',
                phone: '05 53 88 56 78',
                rating: 4.2,
                price_range: 2,
                category: 'indien',
                cuisine: 'Indienne',
                opening_hours: 'Mar-Dim: 12h-14h, 19h-23h',
                latitude: 44.2031,
                longitude: 0.6197,
                specialties: '["Chicken tikka masala", "Agneau biryani", "Naan cheese"]',
                features: '["Emporter", "Livraison", "Végétarien"]',
                tags: '["indien", "curry", "tandoori"]'
            },
            {
                id: 10,
                name: 'Café Central',
                description: 'Café traditionnel au cœur d\'Agen proposant petit-déjeuner, déjeuner et pâtisseries.',
                image: 'https://images.unsplash.com/photo-1554118811-1e0d58224f24?w=800',
                address: '12 Place Wilson, 47000 Agen',
                phone: '05 53 66 55 44',
                rating: 4.1,
                price_range: 1,
                category: 'café',
                cuisine: 'Café',
                opening_hours: 'Lun-Sam: 6h30-19h',
                latitude: 44.2064,
                longitude: 0.6209,
                specialties: '["Café", "Croissants", "Salades"]',
                features: '["Terrasse", "Wifi", "Journaux"]',
                tags: '["café", "central", "traditionnel"]'
            }
        ];
    }
    
    showError(message) {
        const errorDiv = document.createElement('div');
        errorDiv.className = 'error-message';
        errorDiv.innerHTML = `
            <div style="background: #fee; border: 1px solid #fcc; color: #c00; padding: 10px; border-radius: 5px; margin: 10px 0;">
                ⚠️ ${message}
            </div>
        `;
        document.querySelector('.roulette-section .container').prepend(errorDiv);
        
        setTimeout(() => {
            errorDiv.remove();
        }, 5000);
    }
    
    bindEvents() {
        const spinButton = document.getElementById('spin-button');
        const retryButton = document.getElementById('retry-button');
        
        spinButton.addEventListener('click', () => this.spinWheel());
        retryButton.addEventListener('click', () => this.resetAndSpin());
        
        // Filtres
        const filters = ['budget-filter', 'cuisine-filter', 'category-filter', 'rating-filter'];
        filters.forEach(filterId => {
            const filterElement = document.getElementById(filterId);
            if (filterElement) {
                filterElement.addEventListener('change', () => this.updateAvailableRestaurants());
            }
        });
    }
    
    getFilteredRestaurants() {
        const budgetFilter = document.getElementById('budget-filter').value;
        const cuisineFilter = document.getElementById('cuisine-filter').value;
        const categoryFilter = document.getElementById('category-filter').value;
        const ratingFilter = document.getElementById('rating-filter').value;
        
        return this.restaurants.filter(restaurant => {
            if (budgetFilter && restaurant.price_range != budgetFilter) return false;
            if (cuisineFilter && restaurant.cuisine !== cuisineFilter) return false;
            if (categoryFilter && restaurant.category !== categoryFilter) return false;
            if (ratingFilter && parseFloat(restaurant.rating) < parseFloat(ratingFilter)) return false;
            return true;
        });
    }
    
    updateAvailableRestaurants() {
        const filtered = this.getFilteredRestaurants();
        document.getElementById('total-restaurants').textContent = filtered.length;
    }
    
    async spinWheel() {
        if (this.isSpinning) return;
        
        const filteredRestaurants = this.getFilteredRestaurants();
        if (filteredRestaurants.length === 0) {
            alert('Aucun restaurant ne correspond à vos critères. Veuillez ajuster vos filtres.');
            return;
        }
        
        this.isSpinning = true;
        const wheel = document.getElementById('roulette-wheel');
        const spinButton = document.getElementById('spin-button');
        const wheelLoading = document.getElementById('wheel-loading');
        const resultSection = document.getElementById('result-section');
        
        // Masquer le bouton et afficher le loading
        spinButton.style.display = 'none';
        wheelLoading.style.display = 'flex';
        
        // Animation de rotation
        const spins = 5 + Math.random() * 5; // 5-10 tours
        const finalRotation = spins * 360;
        wheel.style.transform = `rotate(${finalRotation}deg)`;
        
        // Sélectionner un restaurant aléatoire immédiatement
        const randomIndex = Math.floor(Math.random() * filteredRestaurants.length);
        this.selectedRestaurant = filteredRestaurants[randomIndex];
        
        // Attendre la fin de l'animation
        setTimeout(() => {
            // Afficher le résultat
            this.displayResult();
            
            // Masquer le loading et réinitialiser
            wheelLoading.style.display = 'none';
            spinButton.style.display = 'block';
            resultSection.style.display = 'block';
            
            // Scroller vers le résultat
            resultSection.scrollIntoView({ behavior: 'smooth' });
            
            this.isSpinning = false;
            this.incrementSelectionsToday();
            
        }, 3000);
    }
    
    displayResult() {
        const restaurant = this.selectedRestaurant;
        const resultDiv = document.getElementById('restaurant-result');
        
        // Parser les données JSON si elles sont des chaînes
        let specialties = [];
        let features = [];
        
        try {
            specialties = typeof restaurant.specialties === 'string' ? 
                JSON.parse(restaurant.specialties) : restaurant.specialties || [];
            features = typeof restaurant.features === 'string' ? 
                JSON.parse(restaurant.features) : restaurant.features || [];
        } catch (e) {
            console.warn('Erreur lors du parsing des données JSON:', e);
            specialties = [];
            features = [];
        }
        
        // Affichage du restaurant sélectionné
        resultDiv.innerHTML = `
            <div class="restaurant-card-surprise">
                <div class="restaurant-image">
                    <img src="${restaurant.image}" alt="${restaurant.name}" onerror="this.src='https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?w=800'">
                    <div class="restaurant-category">${restaurant.category}</div>
                </div>
                <div class="restaurant-info">
                    <h3 class="restaurant-name">${restaurant.name}</h3>
                    <div class="restaurant-details">
                        <div class="detail-item">
                            <span class="detail-icon">🍽️</span>
                            <span>${restaurant.cuisine}</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-icon">⭐</span>
                            <span>${restaurant.rating}/5</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-icon">💰</span>
                            <span>${'€'.repeat(parseInt(restaurant.price_range))}</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-icon">📍</span>
                            <span>${restaurant.address}</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-icon">🕒</span>
                            <span>${restaurant.opening_hours}</span>
                        </div>
                        ${restaurant.phone ? `
                        <div class="detail-item">
                            <span class="detail-icon">📞</span>
                            <span>${restaurant.phone}</span>
                        </div>
                        ` : ''}
                    </div>
                    <p class="restaurant-description">${restaurant.description}</p>
                    
                    ${specialties.length > 0 ? `
                    <div class="restaurant-specialties">
                        <h4>🌟 Spécialités :</h4>
                        <div class="specialties-tags">
                            ${specialties.map(spec => `<span class="specialty-tag">${spec}</span>`).join('')}
                        </div>
                    </div>
                    ` : ''}
                    
                    ${features.length > 0 ? `
                    <div class="restaurant-features">
                        <h4>✨ Équipements :</h4>
                        <div class="features-tags">
                            ${features.map(feat => `<span class="feature-tag">${feat}</span>`).join('')}
                        </div>
                    </div>
                    ` : ''}
                </div>
            </div>
        `;
        
        // Mettre à jour les boutons d'action
        const viewDetailsBtn = document.getElementById('view-details-btn');
        const viewMapBtn = document.getElementById('view-map-btn');
        
        if (viewDetailsBtn) {
            viewDetailsBtn.onclick = () => {
                window.location.href = `detail.php?type=restaurant&id=${restaurant.id}`;
            };
        }
        
        if (viewMapBtn) {
            viewMapBtn.onclick = () => {
                window.location.href = `map.php?restaurant=${restaurant.id}`;
            };
        }
    }
    
    resetAndSpin() {
        document.getElementById('result-section').style.display = 'none';
        const wheel = document.getElementById('roulette-wheel');
        wheel.style.transform = 'rotate(0deg)';
        
        setTimeout(() => {
            this.spinWheel();
        }, 100);
    }
    
    getSelectionsToday() {
        const today = new Date().toDateString();
        const stored = localStorage.getItem('restaurant-selections-' + today);
        return stored ? parseInt(stored) : 0;
    }
    
    incrementSelectionsToday() {
        const today = new Date().toDateString();
        this.selectionsToday++;
        localStorage.setItem('restaurant-selections-' + today, this.selectionsToday.toString());
        this.updateStats();
    }
    
    updateStats() {
        const selectionsElement = document.getElementById('selections-today');
        if (selectionsElement) {
            selectionsElement.textContent = this.selectionsToday;
        }
    }
}

// Styles CSS pour le restaurant card - Compatible thème jour/nuit
const restaurantCardStyles = `
<style>
.restaurant-card-surprise {
    display: flex;
    gap: 20px;
    background: var(--bg-secondary);
    border-radius: 15px;
    padding: 20px;
    box-shadow: 0 5px 20px rgba(0,0,0,0.08);
    border: 1px solid var(--border-color);
}

.restaurant-image {
    position: relative;
    flex-shrink: 0;
    width: 200px;
    height: 200px;
}

.restaurant-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    border-radius: 10px;
}

.restaurant-category {
    position: absolute;
    top: 10px;
    right: 10px;
    background: rgba(102, 126, 234, 0.9);
    color: white;
    padding: 5px 10px;
    border-radius: 15px;
    font-size: 0.8rem;
    font-weight: 600;
    text-transform: capitalize;
}

.restaurant-info {
    flex: 1;
}

.restaurant-name {
    font-size: 1.8rem;
    margin-bottom: 15px;
    color: var(--text-primary);
    font-weight: bold;
}

.restaurant-details {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 10px;
    margin-bottom: 15px;
}

.detail-item {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 0.95rem;
    color: var(--text-secondary);
}

.detail-icon {
    font-size: 1.1rem;
}

.restaurant-description {
    font-size: 1rem;
    line-height: 1.6;
    color: var(--text-secondary);
    margin-bottom: 20px;
}

.restaurant-specialties h4,
.restaurant-features h4 {
    margin-bottom: 10px;
    color: var(--text-primary);
    font-size: 1.1rem;
}

.specialties-tags,
.features-tags {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
}

.specialty-tag {
    background: #667eea;
    color: white;
    padding: 5px 12px;
    border-radius: 20px;
    font-size: 0.85rem;
    font-weight: 500;
}

.restaurant-features {
    margin-top: 15px;
}

.feature-tag {
    background: #4ecdc4;
    color: white;
    padding: 5px 12px;
    border-radius: 20px;
    font-size: 0.85rem;
    font-weight: 500;
}

.error-message {
    margin-bottom: 20px;
}

@media (max-width: 768px) {
    .restaurant-card-surprise {
        flex-direction: column;
    }
    
    .restaurant-image {
        width: 100%;
        height: 200px;
    }
    
    .restaurant-details {
        grid-template-columns: 1fr;
    }
}
</style>
`;

// Injecter les styles
document.head.insertAdjacentHTML('beforeend', restaurantCardStyles);

// Initialiser l'application
document.addEventListener('DOMContentLoaded', () => {
    new RestaurantSurprise();
});
</script>

<?php require_once 'includes/footer.php'; ?>