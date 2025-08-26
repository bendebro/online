<?php
$page_title = 'Carte Interactive';
require_once 'config/database.php';
require_once 'includes/header.php';

// Récupération de tous les lieux
try {
    $database = new Database();
    $db = $database->getConnection();
    
    // Récupération des restaurants
    $restaurants_stmt = $db->prepare("SELECT id, name, description, image, address, phone, rating, price_range, category, 'restaurant' as type FROM restaurants");
    $restaurants_stmt->execute();
    $restaurants = $restaurants_stmt->fetchAll();
    
    // Récupération des activités
    $activities_stmt = $db->prepare("SELECT id, name, description, image, address, phone, rating, price_range, category, 'activity' as type FROM activities");
    $activities_stmt->execute();
    $activities = $activities_stmt->fetchAll();
    
    // Récupération des événements
    $events_stmt = $db->prepare("SELECT id, name, description, image, address, phone, rating, price_range, category, 'event' as type FROM events");
    $events_stmt->execute();
    $events = $events_stmt->fetchAll();
    
    // Combinaison de tous les lieux
    $all_places = array_merge($restaurants, $activities, $events);
    
} catch (Exception $e) {
    $error_message = "Erreur lors du chargement des données : " . $e->getMessage();
    $all_places = [];
}
?>

<!-- Leaflet CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
    .map-container {
        background: var(--bg-card);
        backdrop-filter: blur(10px);
        border-radius: 1rem;
        overflow: hidden;
        box-shadow: 0 8px 32px var(--shadow-color);
        border: 1px solid var(--border-color);
        margin-bottom: 2rem;
    }
    
    .map-header {
        padding: 1.5rem;
        border-bottom: 1px solid var(--border-color);
    }
    
    .map-title {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--text-primary);
        margin-bottom: 0.5rem;
    }
    
    .map-subtitle {
        color: var(--text-secondary);
        font-size: 0.9rem;
    }
    
    #map {
        height: 600px;
        width: 100%;
    }
    
    .map-filters {
        padding: 1rem 1.5rem;
        background: var(--input-bg);
        border-bottom: 1px solid var(--border-color);
        display: flex;
        gap: 1rem;
        flex-wrap: wrap;
        align-items: center;
    }
    
    .filter-label {
        font-weight: 500;
        color: var(--text-primary);
        font-size: 0.9rem;
    }
    
    .filter-checkbox {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        cursor: pointer;
        padding: 0.5rem;
        border-radius: 0.5rem;
        transition: all 0.2s;
    }
    
    .filter-checkbox:hover {
        background: var(--bg-card);
    }
    
    .filter-checkbox input[type="checkbox"] {
        margin: 0;
    }
    
    .filter-checkbox label {
        cursor: pointer;
        font-size: 0.875rem;
        color: var(--text-primary);
    }
    
    .restaurant-marker { color: #059669; }
    .activity-marker { color: #7c3aed; }
    .event-marker { color: #ea580c; }
    
    .leaflet-popup-content {
        margin: 0.5rem;
        min-width: 250px;
    }
    
    .popup-title {
        font-weight: 700;
        font-size: 1.1rem;
        margin-bottom: 0.5rem;
        color: #1f2937;
    }
    
    .popup-category {
        color: #6b7280;
        font-size: 0.875rem;
        margin-bottom: 0.5rem;
    }
    
    .popup-rating {
        display: flex;
        align-items: center;
        gap: 0.25rem;
        margin-bottom: 0.5rem;
        color: #fbbf24;
    }
    
    .popup-description {
        color: #4b5563;
        font-size: 0.875rem;
        line-height: 1.4;
        margin-bottom: 0.75rem;
    }
    
    .popup-actions {
        display: flex;
        gap: 0.5rem;
    }
    
    .popup-btn {
        padding: 0.5rem 1rem;
        border: none;
        border-radius: 0.375rem;
        font-size: 0.875rem;
        font-weight: 500;
        cursor: pointer;
        text-decoration: none;
        text-align: center;
        transition: all 0.2s;
        flex: 1;
    }
    
    .popup-btn-primary {
        background: #2563eb;
        color: white;
    }
    
    .popup-btn-primary:hover {
        background: #1d4ed8;
    }
    
    .stats-row {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
        margin-bottom: 2rem;
    }
    
    .stat-card {
        background: var(--bg-card);
        backdrop-filter: blur(10px);
        border-radius: 1rem;
        padding: 1.5rem;
        text-align: center;
        box-shadow: 0 8px 32px var(--shadow-color);
        border: 1px solid var(--border-color);
    }
    
    .stat-icon {
        font-size: 2rem;
        margin-bottom: 0.5rem;
    }
    
    .stat-number {
        font-size: 1.5rem;
        font-weight: 800;
        color: var(--text-primary);
        margin-bottom: 0.25rem;
    }
    
    .stat-label {
        color: var(--text-secondary);
        font-size: 0.875rem;
    }
</style>

<div class="container">
    <!-- Header -->
    <div class="page-header">
        <h1 class="page-title" style="background: linear-gradient(135deg, #10b981, #059669); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;">
            <span style="-webkit-text-fill-color: initial; background: none;">🗺️</span> Carte Interactive
        </h1>
        <p class="page-subtitle">
            Explorez tous les lieux d'Agen sur une carte interactive. Découvrez restaurants, activités et événements à proximité.
        </p>
    </div>
    
    <!-- Statistiques -->
    <div class="stats-row">
        <div class="stat-card">
            <div class="stat-icon">🍽️</div>
            <div class="stat-number" id="restaurants-count">0</div>
            <div class="stat-label">Restaurants</div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon">🎯</div>
            <div class="stat-number" id="activities-count">0</div>
            <div class="stat-label">Activités</div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon">🎭</div>
            <div class="stat-number" id="events-count">0</div>
            <div class="stat-label">Événements</div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon">📍</div>
            <div class="stat-number" id="total-count">0</div>
            <div class="stat-label">Total des lieux</div>
        </div>
    </div>

    <!-- Carte -->
    <div class="map-container">
        <div class="map-header">
            <h2 class="map-title">Tous les lieux à Agen</h2>
            <p class="map-subtitle">Cliquez sur les marqueurs pour plus d'informations</p>
        </div>
        
        <div class="map-filters">
            <span class="filter-label">Afficher :</span>
            
            <div class="filter-checkbox">
                <input type="checkbox" id="show-restaurants" checked>
                <label for="show-restaurants">🍽️ Restaurants</label>
            </div>
            
            <div class="filter-checkbox">
                <input type="checkbox" id="show-activities" checked>
                <label for="show-activities">🎯 Activités</label>
            </div>
            
            <div class="filter-checkbox">
                <input type="checkbox" id="show-events" checked>
                <label for="show-events">🎭 Événements</label>
            </div>
        </div>
        
        <div id="map"></div>
    </div>
    
    <?php if (isset($error_message)): ?>
        <div style="background: #fef2f2; border: 1px solid #fecaca; color: #b91c1c; padding: 1rem; border-radius: 0.5rem; text-align: center;">
            <?php echo h($error_message); ?>
        </div>
    <?php endif; ?>
</div>

<!-- Leaflet JavaScript -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
// Données des lieux depuis PHP
const places = <?php echo json_encode($all_places, JSON_UNESCAPED_UNICODE); ?>;

// Configuration de la carte
let map;
let markers = {
    restaurants: [],
    activities: [],
    events: []
};

// Coordonnées approximatives d'Agen
const AGEN_COORDS = [44.2028, 0.6167];

// Fonction pour obtenir les coordonnées approximatives basées sur l'adresse
function getApproxCoords(address, type, index) {
    // Génération de coordonnées approximatives autour d'Agen
    const baseLatitude = 44.2028;
    const baseLongitude = 0.6167;
    
    // Variation aléatoire mais déterministe basée sur l'index
    const latVariation = (Math.sin(index * 2.1) * 0.02);
    const lngVariation = (Math.cos(index * 1.7) * 0.03);
    
    return [
        baseLatitude + latVariation,
        baseLongitude + lngVariation
    ];
}

// Fonction pour créer le contenu du popup
function createPopupContent(place) {
    const typeEmoji = place.type === 'restaurant' ? '🍽️' : (place.type === 'activity' ? '🎯' : '🎭');
    const stars = '★'.repeat(Math.floor(place.rating)) + '☆'.repeat(5 - Math.floor(place.rating));
    
    return `
        <div class="popup-content">
            <div class="popup-title">${typeEmoji} ${place.name}</div>
            <div class="popup-category">${place.category} • ${place.price_range}</div>
            <div class="popup-rating">
                <span style="color: #fbbf24;">${stars}</span>
                <span style="color: #6b7280; margin-left: 0.5rem;">${place.rating}</span>
            </div>
            <div class="popup-description">${place.description.substring(0, 120)}...</div>
            <div class="popup-actions">
                <a href="detail.php?type=${place.type}&id=${place.id}" class="popup-btn popup-btn-primary">
                    Voir détails
                </a>
            </div>
        </div>
    `;
}

// Fonction pour créer les marqueurs
function createMarkers() {
    places.forEach((place, index) => {
        const coords = getApproxCoords(place.address, place.type, index);
        
        // Icône différente selon le type
        let iconColor = '#2563eb';
        if (place.type === 'restaurant') iconColor = '#059669';
        else if (place.type === 'activity') iconColor = '#7c3aed';
        else if (place.type === 'event') iconColor = '#ea580c';
        
        // Création du marqueur personnalisé
        const marker = L.marker(coords, {
            icon: L.divIcon({
                html: `<div style="background: ${iconColor}; width: 30px; height: 30px; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; border: 3px solid white; box-shadow: 0 2px 4px rgba(0,0,0,0.2);">${place.type === 'restaurant' ? '🍽️' : (place.type === 'activity' ? '🎯' : '🎭')}</div>`,
                className: 'custom-marker',
                iconSize: [30, 30],
                iconAnchor: [15, 15]
            })
        });
        
        // Ajout du popup
        marker.bindPopup(createPopupContent(place), {
            maxWidth: 300,
            className: 'custom-popup'
        });
        
        // Stockage du marqueur selon le type
        let markerKey;
        if (place.type === 'restaurant') markerKey = 'restaurants';
        else if (place.type === 'activity') markerKey = 'activities';
        else if (place.type === 'event') markerKey = 'events';
        
        if (markerKey && markers[markerKey]) {
            markers[markerKey].push(marker);
        }
        
        // Ajout à la carte
        marker.addTo(map);
    });
}

// Fonction pour filtrer les marqueurs
function filterMarkers() {
    const showRestaurants = document.getElementById('show-restaurants').checked;
    const showActivities = document.getElementById('show-activities').checked;
    const showEvents = document.getElementById('show-events').checked;
    
    // Restaurants
    markers.restaurants.forEach(marker => {
        if (showRestaurants) {
            marker.addTo(map);
        } else {
            map.removeLayer(marker);
        }
    });
    
    // Activités
    markers.activities.forEach(marker => {
        if (showActivities) {
            marker.addTo(map);
        } else {
            map.removeLayer(marker);
        }
    });
    
    // Événements
    markers.events.forEach(marker => {
        if (showEvents) {
            marker.addTo(map);
        } else {
            map.removeLayer(marker);
        }
    });
}

// Fonction pour mettre à jour les statistiques
function updateStats() {
    console.log('Updating stats with', places.length, 'places');
    
    const restaurantsCount = places.filter(p => p.type === 'restaurant').length;
    const activitiesCount = places.filter(p => p.type === 'activity').length;
    const eventsCount = places.filter(p => p.type === 'event').length;
    
    console.log('Counts:', { restaurants: restaurantsCount, activities: activitiesCount, events: eventsCount });
    
    const restaurantsElement = document.getElementById('restaurants-count');
    const activitiesElement = document.getElementById('activities-count');
    const eventsElement = document.getElementById('events-count');
    const totalElement = document.getElementById('total-count');
    
    if (restaurantsElement) restaurantsElement.textContent = restaurantsCount;
    if (activitiesElement) activitiesElement.textContent = activitiesCount;
    if (eventsElement) eventsElement.textContent = eventsCount;
    if (totalElement) totalElement.textContent = places.length;
    
    console.log('Stats updated successfully');
}

// Initialisation de la carte
function initMap() {
    console.log('Starting initMap() with', places.length, 'places');
    
    try {
        // Création de la carte
        console.log('Creating map...');
        map = L.map('map').setView(AGEN_COORDS, 13);
        console.log('Map created successfully');
        
        // Ajout de la couche de tuiles
        console.log('Adding tile layer...');
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);
        console.log('Tile layer added successfully');
        
        // Création des marqueurs
        console.log('Creating markers...');
        createMarkers();
        console.log('Markers created successfully');
        
        // Ajout des événements pour les filtres
        console.log('Adding filter events...');
        document.getElementById('show-restaurants').addEventListener('change', filterMarkers);
        document.getElementById('show-activities').addEventListener('change', filterMarkers);
        document.getElementById('show-events').addEventListener('change', filterMarkers);
        console.log('Filter events added successfully');
        
        // Mise à jour des statistiques
        console.log('Updating stats...');
        updateStats();
        console.log('Stats updated successfully');
        
        console.log('Map initialization complete');
    } catch (error) {
        console.error('Error in initMap():', error);
    }
}

// Initialisation au chargement de la page - avec diagnostic complet
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded, initializing map...');
    console.log('Places data:', places);
    console.log('Places count:', places.length);
    
    // Vérifier que Leaflet est chargé
    if (typeof L === 'undefined') {
        console.error('Leaflet is not loaded!');
        return;
    }
    
    // Vérifier que l'élément map existe
    const mapElement = document.getElementById('map');
    if (!mapElement) {
        console.error('Map element not found!');
        return;
    }
    
    console.log('Leaflet loaded, map element found, initializing...');
    
    // Vérifier que initMap est définie
    if (typeof initMap !== 'function') {
        console.error('initMap function is not defined!');
        return;
    }
    
    console.log('initMap function found, calling it now...');
    
    try {
        initMap();
        console.log('initMap called successfully');
    } catch (error) {
        console.error('Error calling initMap:', error);
        console.error('Error stack:', error.stack);
    }
});
</script>

<?php require_once 'includes/footer.php'; ?>