<?php
/**
 * Page de détail pour restaurants, activités et événements
 * Le petit agenais - Affichage détaillé avec toutes les fonctionnalités
 */

// Récupération et validation des paramètres
$type = isset($_GET['type']) && in_array($_GET['type'], ['restaurant', 'activity', 'event']) ? $_GET['type'] : null;
$id = isset($_GET['id']) && is_numeric($_GET['id']) && $_GET['id'] > 0 ? intval($_GET['id']) : null;

if (!$type || !$id) {
    header('Location: ./'); 
    exit;
}

// Structure de données par défaut
$item = [
    'id' => $id,
    'name' => '',
    'description' => '',
    'image' => 'https://images.unsplash.com/photo-1551218808-94e220e084d2?w=800',
    'address' => 'Centre-ville d\'Agen',
    'phone' => '05 53 XX XX XX',
    'email' => 'contact@guide-agen.fr',
    'website' => 'https://www.agen.fr',
    'rating' => 4.0,
    'price_range' => '€€',
    'category' => '',
    'opening_hours' => 'Lundi-Dimanche: 9h-18h',
    'specialties' => [],
    'tags' => [],
    'lat' => 44.2028,
    'lng' => 0.6169,
    'created_at' => date('Y-m-d')
];

// Champs spécifiques par type
if ($type === 'restaurant') {
    $item['cuisine'] = 'Française';
    $item['specialties'] = ['Pruneaux d\'Agen', 'Cassoulet', 'Foie gras'];
    $item['tags'] = ['Terroir', 'Local', 'Tradition'];
} elseif ($type === 'activity') {
    $item['duration'] = '2h';
    $item['difficulty'] = 'Facile';
    $item['type'] = 'Culturel';
    $item['specialties'] = ['Visite guidée', 'Audio-guide', 'Groupe'];
    $item['tags'] = ['Culture', 'Histoire', 'Patrimoine'];
} elseif ($type === 'event') {
    $item['event_date'] = date('Y-m-d', strtotime('+7 days'));
    $item['duration'] = '3h';
    $item['price'] = 'Gratuit';
    $item['type'] = 'Festival';
    $item['specialties'] = ['Animation', 'Dégustations', 'Spectacles'];
    $item['tags'] = ['Festival', 'Gratuit', 'Famille'];
}

// Tentative de récupération des données réelles
try {
    require_once 'config/database.php';
    
    $database = new Database();
    $db = $database->getConnection();
    
    if ($db) {
        $table = $type . 's';
        $stmt = $db->prepare("SELECT * FROM `$table` WHERE id = ? LIMIT 1");
        $stmt->execute([$id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($result) {
            // Fusionner les données trouvées avec les données par défaut
            foreach ($result as $key => $value) {
                if (!empty($value)) {
                    $item[$key] = $value;
                }
            }
            
            // Traiter les JSON
            if (isset($result['specialties']) && !empty($result['specialties'])) {
                $decoded = json_decode($result['specialties'], true);
                if (is_array($decoded)) $item['specialties'] = $decoded;
            }
            
            if (isset($result['tags']) && !empty($result['tags'])) {
                $decoded = json_decode($result['tags'], true);
                if (is_array($decoded)) $item['tags'] = $decoded;
            }
        }
    }
} catch (Exception $e) {
    // En cas d'erreur, on utilise les données par défaut
    error_log("Erreur database: " . $e->getMessage());
}

// Si pas de nom, générer un nom par défaut
if (empty($item['name'])) {
    $names = [
        'restaurant' => ['Le Petit Agenais', 'Brasserie du Canal', 'Chez Marcel'],
        'activity' => ['Musée des Beaux-Arts', 'Croisière Garonne', 'Visite Guidée'],
        'event' => ['Festival des Pruneaux', 'Marché de Noël', 'Concert Jazz']
    ];
    $item['name'] = $names[$type][($id - 1) % 3];
    $item['description'] = 'Découvrez ce magnifique ' . $type . ' situé au cœur d\'Agen.';
}

// Copier aussi vers $lieu pour compatibilité
$lieu = $item;

require_once 'includes/header.php';
?>

<main class="detail-page">
    <div class="container">
        <!-- Navigation -->
        <div class="detail-nav">
            <a href="javascript:history.back()" class="back-btn">
                ← Retour
            </a>
            <div class="detail-actions">

                <button class="action-btn favorite-btn" 
                        data-id="<?php echo $id; ?>" 
                        data-type="<?php echo $type; ?>">
                    ❤️ Favoris
                </button>
            </div>
        </div>

        <!-- Header principal -->
        <div class="detail-header">
            <div class="detail-image">
                <img src="<?php echo htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>" loading="lazy">
                <div class="image-overlay">
                    <div class="detail-rating">
                        ⭐ <?php echo number_format($item['rating'], 1); ?>
                    </div>
                    <div class="detail-price">
                        <?php echo htmlspecialchars($item['price_range']); ?>
                    </div>
                </div>
            </div>
            <div class="detail-info">
                <h1><?php echo htmlspecialchars($item['name']); ?></h1>
                <p class="detail-description"><?php echo htmlspecialchars($item['description']); ?></p>
                <div class="detail-meta">
                    <?php if (!empty($item['category'])): ?>
                        <span class="meta-tag">🏷️ <?php echo htmlspecialchars($item['category']); ?></span>
                    <?php endif; ?>
                    <?php if ($type === 'restaurant' && !empty($item['cuisine'])): ?>
                        <span class="meta-tag">🍽️ <?php echo htmlspecialchars($item['cuisine']); ?></span>
                    <?php endif; ?>
                    <?php if ($type === 'activity' && !empty($item['duration'])): ?>
                        <span class="meta-tag">⏱️ <?php echo htmlspecialchars($item['duration']); ?></span>
                    <?php endif; ?>
                    <?php if ($type === 'event' && !empty($item['event_date'])): ?>
                        <span class="meta-tag">📅 <?php echo date('d/m/Y', strtotime($item['event_date'])); ?></span>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Contenu principal -->
        <div class="detail-content">
            <div class="detail-main">
                <!-- Informations pratiques -->
                <div class="info-card">
                    <h2>📍 Informations pratiques</h2>
                    <div class="info-list">
                        <?php if (!empty($item['address'])): ?>
                            <div class="info-item">
                                <span class="info-icon">📍</span>
                                <div class="info-text">
                                    <strong>Adresse</strong>
                                    <p><?php echo htmlspecialchars($item['address']); ?></p>
                                </div>
                            </div>
                        <?php endif; ?>
                        
                        <?php if (!empty($item['phone'])): ?>
                            <div class="info-item">
                                <span class="info-icon">📞</span>
                                <div class="info-text">
                                    <strong>Téléphone</strong>
                                    <p><a href="tel:<?php echo htmlspecialchars($item['phone']); ?>"><?php echo htmlspecialchars($item['phone']); ?></a></p>
                                </div>
                            </div>
                        <?php endif; ?>
                        
                        <?php if (!empty($item['website'])): ?>
                            <div class="info-item">
                                <span class="info-icon">🌐</span>
                                <div class="info-text">
                                    <strong>Site web</strong>
                                    <p><a href="<?php echo htmlspecialchars($item['website']); ?>" target="_blank">Visiter le site</a></p>
                                </div>
                            </div>
                        <?php endif; ?>
                        
                        <?php if (!empty($item['opening_hours'])): ?>
                            <div class="info-item">
                                <span class="info-icon">🕒</span>
                                <div class="info-text">
                                    <strong>Horaires</strong>
                                    <p><?php echo htmlspecialchars($item['opening_hours']); ?></p>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Spécialités/Détails -->
                <?php if (!empty($item['specialties']) && is_array($item['specialties'])): ?>
                    <div class="specialties-card">
                        <h2>✨ Spécialités</h2>
                        <div class="specialties-grid">
                            <?php foreach ($item['specialties'] as $specialty): ?>
                                <div class="specialty-item">
                                    <?php echo htmlspecialchars($specialty); ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Tags -->
                <?php if (!empty($item['tags']) && is_array($item['tags'])): ?>
                    <div class="tags-card">
                        <h2>🏷️ Tags</h2>
                        <div class="tags-list">
                            <?php foreach ($item['tags'] as $tag): ?>
                                <span class="tag"><?php echo htmlspecialchars($tag); ?></span>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <div class="detail-sidebar">
                <!-- Carte -->
                <?php if (!empty($item['lat']) && !empty($item['lng'])): ?>
                    <div class="map-card">
                        <h3>📍 Localisation</h3>
                        <div class="map-container" id="detail-map" 
                             data-lat="<?php echo htmlspecialchars($item['lat']); ?>" 
                             data-lng="<?php echo htmlspecialchars($item['lng']); ?>"
                             data-name="<?php echo htmlspecialchars($item['name']); ?>">
                            <div class="map-placeholder">
                                <p>Carte interactive</p>
                                <button onclick="initDetailMap()" class="btn-primary">Afficher la carte</button>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Actions rapides -->
                <div class="actions-card">
                    <h3>⚡ Actions rapides</h3>
                    <div class="quick-actions">
                        <button class="quick-action-btn" onclick="getDirections()">
                            🧭 Itinéraire
                        </button>
                        <button class="quick-action-btn" onclick="callPlace()">
                            📞 Appeler
                        </button>
                        <button class="quick-action-btn" onclick="sharePlace()">
                            📤 Partager
                        </button>
                        <button class="quick-action-btn" onclick="addToCalendar()">
                            📅 Agenda
                        </button>
                    </div>
                </div>

                <!-- Recommandations similaires -->
                <div class="recommendations-card">
                    <h3>🎯 Suggestions similaires</h3>
                    <div class="similar-items" id="similar-recommendations">
                        <div class="loading-placeholder">
                            Chargement des suggestions...
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<!-- Scripts spécifiques à la page de détail -->
<script>
// Données de l'élément actuel
const currentItem = <?php echo json_encode($item); ?>;
const itemType = '<?php echo $type; ?>';
const itemId = <?php echo $id; ?>;

// Fonctions d'interaction
function toggleFavorite(type, id) {
    const btn = document.getElementById(`favorite-btn-${id}`);
    const isFavorited = btn.classList.contains('favorited');
    
    if (isFavorited) {
        btn.classList.remove('favorited');
        btn.innerHTML = '🤍 Favoris';
        removeFavorite(type, id);
    } else {
        btn.classList.add('favorited');
        btn.innerHTML = '❤️ Favoris';
        addFavorite(type, id);
    }
}

function addFavorite(type, id) {
    const favorites = JSON.parse(localStorage.getItem('userFavorites') || '[]');
    const favorite = {
        id: id,
        type: type,
        name: currentItem.name,
        image: currentItem.image,
        rating: currentItem.rating,
        added_at: new Date().toISOString()
    };
    
    favorites.push(favorite);
    localStorage.setItem('userFavorites', JSON.stringify(favorites));
    
    if (window.notificationManager) {
        window.notificationManager.showFallbackNotification('❤️ Ajouté aux favoris!');
    }
}

function removeFavorite(type, id) {
    const favorites = JSON.parse(localStorage.getItem('userFavorites') || '[]');
    const filtered = favorites.filter(fav => !(fav.type === type && fav.id == id));
    localStorage.setItem('userFavorites', JSON.stringify(filtered));
    
    if (window.notificationManager) {
        window.notificationManager.showFallbackNotification('💔 Retiré des favoris');
    }
}

function getDirections() {
    if (currentItem.lat && currentItem.lng) {
        const url = `https://www.google.com/maps/dir/?api=1&destination=${currentItem.lat},${currentItem.lng}`;
        window.open(url, '_blank');
    }
}

function callPlace() {
    if (currentItem.phone) {
        window.location.href = `tel:${currentItem.phone}`;
    } else {
        alert('Numéro de téléphone non disponible');
    }
}

function sharePlace() {
    if (window.socialManager) {
        window.socialManager.shareContent(itemType, currentItem);
    } else {
        // Fallback simple
        const url = window.location.href;
        navigator.clipboard.writeText(url).then(() => {
            alert('Lien copié dans le presse-papiers!');
        });
    }
}

function addToCalendar() {
    if (itemType === 'event' && currentItem.event_date) {
        const startDate = new Date(currentItem.event_date).toISOString().replace(/-|:|\.\d\d\d/g, '');
        const endDate = new Date(new Date(currentItem.event_date).getTime() + 3*60*60*1000).toISOString().replace(/-|:|\.\d\d\d/g, '');
        
        const calendarUrl = `https://calendar.google.com/calendar/render?action=TEMPLATE&text=${encodeURIComponent(currentItem.name)}&dates=${startDate}/${endDate}&details=${encodeURIComponent(currentItem.description)}&location=${encodeURIComponent(currentItem.address)}`;
        window.open(calendarUrl, '_blank');
    } else {
        alert('Fonction disponible uniquement pour les événements');
    }
}

function initDetailMap() {
    if (typeof L !== 'undefined' && currentItem.lat && currentItem.lng) {
        const mapContainer = document.getElementById('detail-map');
        mapContainer.innerHTML = '<div style="height: 200px;" id="leaflet-map"></div>';
        
        const map = L.map('leaflet-map').setView([currentItem.lat, currentItem.lng], 15);
        
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);
        
        L.marker([currentItem.lat, currentItem.lng])
            .addTo(map)
            .bindPopup(`<b>${currentItem.name}</b><br>${currentItem.address}`)
            .openPopup();
    } else {
        alert('Carte non disponible');
    }
}

// Charger les recommandations similaires
async function loadSimilarRecommendations() {
    try {
        const response = await fetch(`/api/personalized-recommendations.php?type=${itemType}&limit=3&exclude_id=${itemId}`);
        const data = await response.json();
        
        if (data.success && data.recommendations.length > 0) {
            const container = document.getElementById('similar-recommendations');
            container.innerHTML = data.recommendations.map(item => `
                <div class="similar-item" onclick="window.location.href='detail.php?type=${item.type}&id=${item.id}'">
                    <img src="${item.image}" alt="${item.name}" loading="lazy">
                    <div class="similar-info">
                        <h4>${item.name}</h4>
                        <div class="similar-rating">⭐ ${item.rating}</div>
                    </div>
                </div>
            `).join('');
        } else {
            document.getElementById('similar-recommendations').innerHTML = '<p>Aucune suggestion disponible</p>';
        }
    } catch (error) {
        console.error('Erreur lors du chargement des suggestions:', error);
        document.getElementById('similar-recommendations').innerHTML = '<p>Erreur de chargement</p>';
    }
}

// Vérifier si l'élément est en favoris au chargement
document.addEventListener('DOMContentLoaded', function() {
    const favorites = JSON.parse(localStorage.getItem('userFavorites') || '[]');
    const isFavorited = favorites.some(fav => fav.type === itemType && fav.id == itemId);
    
    const favoriteBtn = document.getElementById(`favorite-btn-${itemId}`);
    if (isFavorited && favoriteBtn) {
        favoriteBtn.classList.add('favorited');
        favoriteBtn.innerHTML = '❤️ Favoris';
    }
    
    // Charger les recommandations
    loadSimilarRecommendations();
});
</script>

<?php require_once 'includes/footer.php'; ?>