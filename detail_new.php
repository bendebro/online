<?php
/**
 * Page de détail avec réservation externe
 * Guide d'Agen - Version complète
 */

$page_title = 'Détail';
require_once 'config/database.php';
require_once 'includes/header.php';

// Récupération et validation des paramètres
$type = isset($_GET['type']) && in_array($_GET['type'], ['restaurant', 'activity', 'event']) ? $_GET['type'] : null;
$id = isset($_GET['id']) && is_numeric($_GET['id']) && $_GET['id'] > 0 ? intval($_GET['id']) : null;

if (!$type || !$id) {
    header('Location: index.php'); 
    exit;
}

// Mapping des types vers les tables
$tables = [
    'restaurant' => 'restaurants',
    'activity' => 'activities', 
    'event' => 'events'
];

$table = $tables[$type];
$item = null;

try {
    $database = new Database();
    $db = $database->getConnection();
    
    $stmt = $db->prepare("SELECT * FROM {$table} WHERE id = ?");
    $stmt->execute([$id]);
    $item = $stmt->fetch();
    
    if (!$item) {
        header('Location: index.php');
        exit;
    }
    
    // Traitement des champs JSON
    if (isset($item['specialties']) && $item['specialties']) {
        $item['specialties'] = json_decode($item['specialties'], true) ?? [];
    } else {
        $item['specialties'] = [];
    }
    
    if (isset($item['tags']) && $item['tags']) {
        $item['tags'] = json_decode($item['tags'], true) ?? [];
    } else {
        $item['tags'] = [];
    }
    
} catch (Exception $e) {
    header('Location: index.php');
    exit;
}

// Configuration selon le type
$typeConfig = [
    'restaurant' => [
        'icon' => '🍽️',
        'title' => 'Restaurant',
        'action_text' => 'Réserver une table',
        'action_icon' => '📅',
        'phone_text' => 'Appeler le restaurant',
        'back_url' => 'restaurants.php'
    ],
    'activity' => [
        'icon' => '🎯',
        'title' => 'Activité',
        'action_text' => 'Réserver l\'activité',
        'action_icon' => '🎫',
        'phone_text' => 'Renseignements',
        'back_url' => 'activities.php'
    ],
    'event' => [
        'icon' => '🎭',
        'title' => 'Événement',
        'action_text' => 'Acheter des billets',
        'action_icon' => '🎟️',
        'phone_text' => 'Informations',
        'back_url' => 'events.php'
    ]
];

$config = $typeConfig[$type];
?>

<style>
    .detail-hero {
        position: relative;
        height: 400px;
        border-radius: 1rem;
        overflow: hidden;
        margin-bottom: 2rem;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
    }
    
    .detail-hero img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    
    .detail-hero-overlay {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        background: linear-gradient(transparent, rgba(0, 0, 0, 0.8));
        padding: 2rem;
        color: white;
    }
    
    .detail-title {
        font-size: 2.5rem;
        font-weight: 800;
        margin-bottom: 0.5rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }
    
    .detail-category {
        background: rgba(255, 255, 255, 0.2);
        backdrop-filter: blur(10px);
        padding: 0.5rem 1rem;
        border-radius: 2rem;
        font-size: 0.875rem;
        display: inline-block;
        margin-bottom: 1rem;
    }
    
    .detail-rating {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 1.1rem;
    }
    
    .detail-content {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 3rem;
        margin-bottom: 3rem;
    }
    
    .detail-main {
        background: var(--bg-card);
        backdrop-filter: blur(10px);
        border-radius: 1rem;
        padding: 2rem;
        box-shadow: 0 8px 32px var(--shadow-color);
        border: 1px solid var(--border-color);
    }
    
    .detail-sidebar {
        display: flex;
        flex-direction: column;
        gap: 1.5rem;
    }
    
    .sidebar-card {
        background: var(--bg-card);
        backdrop-filter: blur(10px);
        border-radius: 1rem;
        padding: 1.5rem;
        box-shadow: 0 8px 32px var(--shadow-color);
        border: 1px solid var(--border-color);
    }
    
    .sidebar-title {
        font-size: 1.25rem;
        font-weight: 700;
        color: var(--text-primary);
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .info-item {
        display: flex;
        align-items: flex-start;
        gap: 0.75rem;
        margin-bottom: 1rem;
        padding: 0.75rem;
        background: var(--input-bg);
        border-radius: 0.5rem;
        border: 1px solid var(--border-color);
    }
    
    .info-icon {
        font-size: 1.25rem;
        width: 1.5rem;
        text-align: center;
    }
    
    .info-content {
        flex: 1;
    }
    
    .info-label {
        font-weight: 600;
        color: var(--text-primary);
        font-size: 0.875rem;
        margin-bottom: 0.25rem;
    }
    
    .info-value {
        color: var(--text-secondary);
        font-size: 0.875rem;
        line-height: 1.4;
    }
    
    .action-buttons {
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }
    
    .btn-action {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.75rem;
        padding: 1rem 1.5rem;
        border: none;
        border-radius: 0.75rem;
        font-weight: 600;
        font-size: 1rem;
        cursor: pointer;
        text-decoration: none;
        transition: all 0.3s ease;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }
    
    .btn-primary {
        background: linear-gradient(135deg, #2563eb, #3b82f6);
        color: white;
    }
    
    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(37, 99, 235, 0.3);
    }
    
    .btn-secondary {
        background: linear-gradient(135deg, #10b981, #059669);
        color: white;
    }
    
    .btn-secondary:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(16, 185, 129, 0.3);
    }
    
    .btn-outline {
        background: transparent;
        border: 2px solid var(--border-color);
        color: var(--text-primary);
    }
    
    .btn-outline:hover {
        border-color: var(--text-accent);
        color: var(--text-accent);
        transform: translateY(-2px);
    }
    
    .description-section {
        margin-bottom: 2rem;
    }
    
    .section-title {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--text-primary);
        margin-bottom: 1rem;
    }
    
    .description-text {
        color: var(--text-secondary);
        line-height: 1.6;
        font-size: 1rem;
    }
    
    .specialties-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
        margin-top: 1rem;
    }
    
    .specialty-item {
        background: var(--input-bg);
        padding: 1rem;
        border-radius: 0.5rem;
        border: 1px solid var(--border-color);
        text-align: center;
        font-weight: 500;
        color: var(--text-primary);
    }
    
    .tags-container {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
        margin-top: 1rem;
    }
    
    .tag {
        background: linear-gradient(135deg, #f3f4f6, #e5e7eb);
        color: var(--text-primary);
        padding: 0.375rem 0.75rem;
        border-radius: 2rem;
        font-size: 0.875rem;
        font-weight: 500;
        border: 1px solid var(--border-color);
    }
    
    .back-button {
        margin-bottom: 2rem;
    }
    
    .platform-badge {
        background: #f0f9ff;
        color: #0369a1;
        padding: 0.25rem 0.75rem;
        border-radius: 1rem;
        font-size: 0.75rem;
        font-weight: 500;
        margin-top: 0.5rem;
        display: inline-block;
    }
    
    .favorite-toggle {
        position: absolute;
        top: 1rem;
        right: 1rem;
        width: 3rem;
        height: 3rem;
        background: rgba(255, 255, 255, 0.9);
        backdrop-filter: blur(10px);
        border: none;
        border-radius: 50%;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        transition: all 0.3s ease;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }
    
    .favorite-toggle:hover {
        transform: scale(1.1);
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
    }
    
    .favorite-toggle.active {
        background: #ef4444;
        color: white;
    }
    
    @media (max-width: 768px) {
        .detail-content {
            grid-template-columns: 1fr;
            gap: 2rem;
        }
        
        .detail-hero {
            height: 250px;
            margin-bottom: 1rem;
        }
        
        .detail-title {
            font-size: 1.75rem;
        }
        
        .detail-hero-overlay {
            padding: 1rem;
        }
        
        .detail-main, .sidebar-card {
            padding: 1.5rem;
        }
        
        .action-buttons {
            order: -1;
        }
        
        .btn-action {
            font-size: 0.9rem;
            padding: 0.875rem 1.25rem;
        }
    }
</style>

<div class="container">
    <!-- Bouton retour -->
    <div class="back-button">
        <a href="<?php echo $config['back_url']; ?>" class="btn btn-outline">
            ← Retour aux <?php echo strtolower($config['title']); ?>s
        </a>
    </div>
    
    <!-- Hero section avec image -->
    <div class="detail-hero">
        <img src="<?php echo h($item['image']); ?>" alt="<?php echo h($item['name']); ?>" loading="lazy">
        
        <button class="favorite-toggle" onclick="toggleFavorite('<?php echo $type; ?>', <?php echo $id; ?>)" aria-label="Ajouter aux favoris">
            <span class="favorite-icon">🤍</span>
        </button>
        
        <div class="detail-hero-overlay">
            <div class="detail-category">
                <?php echo $config['icon']; ?> <?php echo h($item['category'] ?? $config['title']); ?>
            </div>
            
            <h1 class="detail-title">
                <?php echo h($item['name']); ?>
            </h1>
            
            <div class="detail-rating">
                <?php 
                $rating = floatval($item['rating'] ?? 4.0);
                $stars = str_repeat('★', floor($rating)) . str_repeat('☆', 5 - floor($rating));
                echo $stars . ' ' . number_format($rating, 1);
                ?>
                <span style="margin-left: 1rem; opacity: 0.8;">
                    <?php echo h($item['price_range'] ?? '€€'); ?>
                </span>
            </div>
        </div>
    </div>
    
    <!-- Contenu principal -->
    <div class="detail-content">
        <div class="detail-main">
            <!-- Description -->
            <div class="description-section">
                <h2 class="section-title">📋 Description</h2>
                <p class="description-text">
                    <?php echo nl2br(h($item['description'])); ?>
                </p>
            </div>
            
            <!-- Spécialités -->
            <?php if (!empty($item['specialties'])): ?>
            <div class="description-section">
                <h2 class="section-title">⭐ Spécialités</h2>
                <div class="specialties-grid">
                    <?php foreach ($item['specialties'] as $specialty): ?>
                        <div class="specialty-item">
                            <?php echo h($specialty); ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>
            
            <!-- Tags -->
            <?php if (!empty($item['tags'])): ?>
            <div class="description-section">
                <h2 class="section-title">🏷️ Tags</h2>
                <div class="tags-container">
                    <?php foreach ($item['tags'] as $tag): ?>
                        <span class="tag"><?php echo h($tag); ?></span>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>
        </div>
        
        <!-- Sidebar avec infos et actions -->
        <div class="detail-sidebar">
            <!-- Actions de réservation -->
            <div class="sidebar-card">
                <h3 class="sidebar-title">
                    <?php echo $config['action_icon']; ?> Réservation
                </h3>
                
                <div class="action-buttons">
                    <?php if (!empty($item['booking_url'])): ?>
                        <a href="<?php echo h($item['booking_url']); ?>" 
                           target="_blank" 
                           class="btn-action btn-primary"
                           onclick="Analytics.track('booking_click', 'reservation', '<?php echo $type; ?>_<?php echo $id; ?>')">
                            <?php echo $config['action_icon']; ?>
                            <?php echo $config['action_text']; ?>
                        </a>
                        
                        <?php if (!empty($item['booking_platform'])): ?>
                            <div class="platform-badge">
                                via <?php echo h($item['booking_platform']); ?>
                            </div>
                        <?php endif; ?>
                        
                        <?php if (!empty($item['ticket_price'])): ?>
                            <div class="info-item">
                                <div class="info-icon">💰</div>
                                <div class="info-content">
                                    <div class="info-label">Tarifs</div>
                                    <div class="info-value"><?php echo h($item['ticket_price']); ?></div>
                                </div>
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>
                    
                    <?php if (!empty($item['booking_phone']) || !empty($item['phone'])): ?>
                        <a href="tel:<?php echo h($item['booking_phone'] ?? $item['phone']); ?>" 
                           class="btn-action btn-secondary"
                           onclick="Analytics.track('phone_click', 'contact', '<?php echo $type; ?>_<?php echo $id; ?>')">
                            📞 <?php echo $config['phone_text']; ?>
                        </a>
                    <?php endif; ?>
                    
                    <?php if (!empty($item['website'])): ?>
                        <a href="<?php echo h($item['website']); ?>" 
                           target="_blank" 
                           class="btn-action btn-outline"
                           onclick="Analytics.track('website_click', 'external', '<?php echo $type; ?>_<?php echo $id; ?>')">
                            🌐 Site web
                        </a>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Informations pratiques -->
            <div class="sidebar-card">
                <h3 class="sidebar-title">ℹ️ Informations</h3>
                
                <?php if (!empty($item['address'])): ?>
                <div class="info-item">
                    <div class="info-icon">📍</div>
                    <div class="info-content">
                        <div class="info-label">Adresse</div>
                        <div class="info-value"><?php echo h($item['address']); ?></div>
                    </div>
                </div>
                <?php endif; ?>
                
                <?php if (!empty($item['phone'])): ?>
                <div class="info-item">
                    <div class="info-icon">📞</div>
                    <div class="info-content">
                        <div class="info-label">Téléphone</div>
                        <div class="info-value">
                            <a href="tel:<?php echo h($item['phone']); ?>" style="color: var(--text-accent);">
                                <?php echo h($item['phone']); ?>
                            </a>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
                
                <?php if (!empty($item['opening_hours'])): ?>
                <div class="info-item">
                    <div class="info-icon">🕒</div>
                    <div class="info-content">
                        <div class="info-label">Horaires</div>
                        <div class="info-value"><?php echo h($item['opening_hours']); ?></div>
                    </div>
                </div>
                <?php endif; ?>
                
                <?php if (!empty($item['event_date']) && $type === 'event'): ?>
                <div class="info-item">
                    <div class="info-icon">📅</div>
                    <div class="info-content">
                        <div class="info-label">Date</div>
                        <div class="info-value"><?php echo h($item['event_date']); ?></div>
                    </div>
                </div>
                <?php endif; ?>
                
                <?php if (!empty($item['duration']) && in_array($type, ['activity', 'event'])): ?>
                <div class="info-item">
                    <div class="info-icon">⏱️</div>
                    <div class="info-content">
                        <div class="info-label">Durée</div>
                        <div class="info-value"><?php echo h($item['duration']); ?></div>
                    </div>
                </div>
                <?php endif; ?>
            </div>
            
            <!-- Actions secondaires -->
            <div class="sidebar-card">
                <h3 class="sidebar-title">🚀 Actions</h3>
                
                <div class="action-buttons">
                    <a href="map.php" class="btn-action btn-outline">
                        🗺️ Voir sur la carte
                    </a>
                    
                    <button onclick="shareItem()" class="btn-action btn-outline">
                        📤 Partager
                    </button>
                    
                    <button onclick="addToFavorites()" class="btn-action btn-outline">
                        ❤️ Ajouter aux favoris
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Gestion des favoris
function toggleFavorite(type, id) {
    const favorites = JSON.parse(localStorage.getItem('agen_favorites') || '[]');
    const itemKey = `${type}_${id}`;
    const favoriteIcon = document.querySelector('.favorite-icon');
    const favoriteBtn = document.querySelector('.favorite-toggle');
    
    if (favorites.includes(itemKey)) {
        // Retirer des favoris
        const index = favorites.indexOf(itemKey);
        favorites.splice(index, 1);
        favoriteIcon.textContent = '🤍';
        favoriteBtn.classList.remove('active');
        GuideAgen.showNotification('Retiré des favoris', 'info');
    } else {
        // Ajouter aux favoris
        favorites.push(itemKey);
        favoriteIcon.textContent = '❤️';
        favoriteBtn.classList.add('active');
        GuideAgen.showNotification('Ajouté aux favoris !', 'success');
    }
    
    localStorage.setItem('agen_favorites', JSON.stringify(favorites));
    GuideAgen.updateFavoritesCount();
    
    // Analytics
    Analytics.track('favorite_toggle', 'interaction', `${type}_${id}`);
}

// Vérifier si l'item est déjà en favoris au chargement
document.addEventListener('DOMContentLoaded', function() {
    const favorites = JSON.parse(localStorage.getItem('agen_favorites') || '[]');
    const itemKey = '<?php echo $type; ?>_<?php echo $id; ?>';
    
    if (favorites.includes(itemKey)) {
        document.querySelector('.favorite-icon').textContent = '❤️';
        document.querySelector('.favorite-toggle').classList.add('active');
    }
});

// Partage
function shareItem() {
    if (navigator.share) {
        navigator.share({
            title: '<?php echo h($item['name']); ?>',
            text: '<?php echo h(substr($item['description'], 0, 100)); ?>...',
            url: window.location.href
        });
    } else {
        // Fallback: copier l'URL
        navigator.clipboard.writeText(window.location.href);
        GuideAgen.showNotification('Lien copié !', 'success');
    }
    
    Analytics.track('share', 'interaction', '<?php echo $type; ?>_<?php echo $id; ?>');
}

// Ajouter aux favoris (fonction alternative)
function addToFavorites() {
    toggleFavorite('<?php echo $type; ?>', <?php echo $id; ?>);
}

// Analytics - Vue de la page de détail
Analytics.track('detail_view', 'page', '<?php echo $type; ?>_<?php echo $id; ?>');
</script>

<?php require_once 'includes/footer.php'; ?>