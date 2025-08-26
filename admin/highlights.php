<?php
require_once '../config/database.php';

// Vérification de l'authentification (simplifié)
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit;
}

$page_title = 'Gestion des Coups de Cœur';

// Traitement des actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $database = new Database();
        $db = $database->getConnection();
        
        if (isset($_POST['action'])) {
            switch ($_POST['action']) {
                case 'update_restaurant_rating':
                    $stmt = $db->prepare("UPDATE restaurants SET rating = ? WHERE id = ?");
                    $stmt->execute([
                        floatval($_POST['rating']),
                        intval($_POST['id'])
                    ]);
                    $success_message = "Note du restaurant mise à jour avec succès !";
                    break;
                    
                case 'update_activity_rating':
                    $stmt = $db->prepare("UPDATE activities SET rating = ? WHERE id = ?");
                    $stmt->execute([
                        floatval($_POST['rating']),
                        intval($_POST['id'])
                    ]);
                    $success_message = "Note de l'activité mise à jour avec succès !";
                    break;
                    
                case 'update_event_rating':
                    $stmt = $db->prepare("UPDATE events SET rating = ? WHERE id = ?");
                    $stmt->execute([
                        floatval($_POST['rating']),
                        intval($_POST['id'])
                    ]);
                    $success_message = "Note de l'événement mise à jour avec succès !";
                    break;
            }
        }
    } catch (Exception $e) {
        $error_message = "Erreur : " . $e->getMessage();
    }
}

// Récupération des données
try {
    $database = new Database();
    $db = $database->getConnection();
    
    // Restaurants
    $restaurants_stmt = $db->prepare("SELECT * FROM restaurants ORDER BY rating DESC");
    $restaurants_stmt->execute();
    $restaurants = $restaurants_stmt->fetchAll();
    
    // Activités
    $activities_stmt = $db->prepare("SELECT * FROM activities ORDER BY rating DESC");
    $activities_stmt->execute();
    $activities = $activities_stmt->fetchAll();
    
    // Événements
    $events_stmt = $db->prepare("SELECT * FROM events ORDER BY rating DESC");
    $events_stmt->execute();
    $events = $events_stmt->fetchAll();
    
} catch (Exception $e) {
    $error_message = "Erreur lors du chargement : " . $e->getMessage();
    $restaurants = $activities = $events = [];
}

include '../includes/admin_header.php';
?>

<div class="admin-container">
    <div class="admin-header">
        <h1>🌟 Gestion des Coups de Cœur</h1>
        <p>Sélectionnez les éléments à mettre en avant sur la page d'accueil</p>
    </div>

    <?php if (isset($success_message)): ?>
        <div class="alert alert-success"><?php echo $success_message; ?></div>
    <?php endif; ?>

    <?php if (isset($error_message)): ?>
        <div class="alert alert-error"><?php echo $error_message; ?></div>
    <?php endif; ?>

    <!-- Explication du système actuel -->
    <div class="info-box">
        <h3>💡 Comment ça fonctionne</h3>
        <p><strong>Système actuel :</strong> Les coups de cœur sont sélectionnés automatiquement par note (rating) :</p>
        <ul>
            <li>🍽️ <strong>Restaurants :</strong> Top 3 par rating</li>
            <li>🎯 <strong>Activités :</strong> Top 3 par rating</li>
            <li>📅 <strong>Événements :</strong> Top 2 par rating</li>
        </ul>
        <p><strong>Pour modifier :</strong> Changez les notes ci-dessous ou modifiez le code dans <code>/app/index.php</code> lignes 25, 31, 37.</p>
    </div>

    <!-- Restaurants -->
    <div class="section">
        <h2>🍽️ Restaurants (Top 3 actuels)</h2>
        <div class="items-grid">
            <?php foreach ($restaurants as $index => $restaurant): ?>
                <div class="item-card <?php echo $index < 3 ? 'highlighted' : ''; ?>">
                    <div class="item-info">
                        <img src="<?php echo htmlspecialchars($restaurant['image']); ?>" alt="<?php echo htmlspecialchars($restaurant['name']); ?>" class="item-image">
                        <div class="item-details">
                            <h4><?php echo htmlspecialchars($restaurant['name']); ?></h4>
                            <p class="item-category"><?php echo htmlspecialchars($restaurant['category']); ?></p>
                            <div class="item-rating">
                                ⭐ <?php echo $restaurant['rating']; ?>/5
                            </div>
                        </div>
                    </div>
                    <div class="item-actions">
                        <form method="POST" style="display: inline;">
                            <input type="hidden" name="action" value="update_restaurant_rating">
                            <input type="hidden" name="id" value="<?php echo $restaurant['id']; ?>">
                            <label>Note : </label>
                            <input type="number" name="rating" value="<?php echo $restaurant['rating']; ?>" min="1" max="5" step="0.1" style="width: 80px;">
                            <button type="submit" class="btn-small">Mettre à jour</button>
                        </form>
                        <?php if ($index < 3): ?>
                            <span class="badge-highlight">Coup de cœur actuel</span>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Activités -->
    <div class="section">
        <h2>🎯 Activités (Top 3 actuels)</h2>
        <div class="items-grid">
            <?php foreach ($activities as $index => $activity): ?>
                <div class="item-card <?php echo $index < 3 ? 'highlighted' : ''; ?>">
                    <div class="item-info">
                        <img src="<?php echo htmlspecialchars($activity['image']); ?>" alt="<?php echo htmlspecialchars($activity['name']); ?>" class="item-image">
                        <div class="item-details">
                            <h4><?php echo htmlspecialchars($activity['name']); ?></h4>
                            <p class="item-category"><?php echo htmlspecialchars($activity['category']); ?></p>
                            <div class="item-rating">
                                ⭐ <?php echo $activity['rating']; ?>/5
                            </div>
                        </div>
                    </div>
                    <div class="item-actions">
                        <form method="POST" style="display: inline;">
                            <input type="hidden" name="action" value="update_activity_rating">
                            <input type="hidden" name="id" value="<?php echo $activity['id']; ?>">
                            <label>Note : </label>
                            <input type="number" name="rating" value="<?php echo $activity['rating']; ?>" min="1" max="5" step="0.1" style="width: 80px;">
                            <button type="submit" class="btn-small">Mettre à jour</button>
                        </form>
                        <?php if ($index < 3): ?>
                            <span class="badge-highlight">Coup de cœur actuel</span>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Événements -->
    <div class="section">
        <h2>📅 Événements (Top 2 actuels)</h2>
        <div class="items-grid">
            <?php foreach ($events as $index => $event): ?>
                <div class="item-card <?php echo $index < 2 ? 'highlighted' : ''; ?>">
                    <div class="item-info">
                        <img src="<?php echo htmlspecialchars($event['image']); ?>" alt="<?php echo htmlspecialchars($event['name']); ?>" class="item-image">
                        <div class="item-details">
                            <h4><?php echo htmlspecialchars($event['name']); ?></h4>
                            <p class="item-category"><?php echo htmlspecialchars($event['category']); ?></p>
                            <div class="item-rating">
                                ⭐ <?php echo $event['rating']; ?>/5
                            </div>
                        </div>
                    </div>
                    <div class="item-actions">
                        <form method="POST" style="display: inline;">
                            <input type="hidden" name="action" value="update_event_rating">
                            <input type="hidden" name="id" value="<?php echo $event['id']; ?>">
                            <label>Note : </label>
                            <input type="number" name="rating" value="<?php echo $event['rating']; ?>" min="1" max="5" step="0.1" style="width: 80px;">
                            <button type="submit" class="btn-small">Mettre à jour</button>
                        </form>
                        <?php if ($index < 2): ?>
                            <span class="badge-highlight">Coup de cœur actuel</span>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<style>
.info-box {
    background: linear-gradient(135deg, #e0f2fe, #f3e5f5);
    border-radius: 1rem;
    padding: 1.5rem;
    margin-bottom: 2rem;
    border-left: 4px solid #2196f3;
}

.info-box h3 {
    color: #1565c0;
    margin-bottom: 1rem;
}

.info-box ul {
    margin: 1rem 0;
}

.info-box code {
    background: rgba(0,0,0,0.1);
    padding: 2px 6px;
    border-radius: 4px;
    font-family: monospace;
}

.section {
    margin-bottom: 3rem;
}

.section h2 {
    border-bottom: 2px solid #e5e7eb;
    padding-bottom: 0.5rem;
    margin-bottom: 1.5rem;
}

.items-grid {
    display: grid;
    gap: 1rem;
    grid-template-columns: 1fr;
}

.item-card {
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: white;
    border: 1px solid #e5e7eb;
    border-radius: 0.5rem;
    padding: 1rem;
    transition: all 0.3s;
}

.item-card.highlighted {
    border-color: #f59e0b;
    background: linear-gradient(135deg, #fef3c7, #fef7cd);
    box-shadow: 0 4px 12px rgba(245, 158, 11, 0.1);
}

.item-info {
    display: flex;
    align-items: center;
    flex: 1;
}

.item-image {
    width: 60px;
    height: 60px;
    object-fit: cover;
    border-radius: 0.5rem;
    margin-right: 1rem;
}

.item-details h4 {
    margin: 0 0 0.25rem 0;
    font-size: 1.1rem;
}

.item-category {
    color: #6b7280;
    font-size: 0.9rem;
    margin: 0 0 0.25rem 0;
}

.item-rating {
    color: #f59e0b;
    font-weight: 600;
}

.item-actions {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.btn-small {
    background: #3b82f6;
    color: white;
    border: none;
    padding: 0.25rem 0.75rem;
    border-radius: 0.25rem;
    cursor: pointer;
    font-size: 0.875rem;
}

.btn-small:hover {
    background: #2563eb;
}

.badge-highlight {
    background: #f59e0b;
    color: white;
    padding: 0.25rem 0.75rem;
    border-radius: 9999px;
    font-size: 0.75rem;
    font-weight: 600;
}
</style>

<?php include '../includes/admin_footer.php'; ?>