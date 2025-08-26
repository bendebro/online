<?php
/**
 * Gestion des activités
 * Le petit agenais - Admin
 */

session_start();

// Vérification de la connexion admin
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

require_once __DIR__ . '/../config/database.php';

$database = new Database();
$db = $database->getConnection();

// Gestion des actions
$action = $_GET['action'] ?? '';
$id = $_GET['id'] ?? '';
$message = '';

// Suppression d'une activité
if ($action === 'delete' && $id) {
    try {
        $stmt = $db->prepare("DELETE FROM activities WHERE id = ?");
        $stmt->execute([$id]);
        $message = '<div class="success-message">Activité supprimée avec succès!</div>';
    } catch (Exception $e) {
        $message = '<div class="error-message">Erreur lors de la suppression: ' . $e->getMessage() . '</div>';
    }
}

// Récupération des activités
$activities = [];
try {
    $stmt = $db->prepare("SELECT * FROM activities ORDER BY name ASC");
    $stmt->execute();
    $activities = $stmt->fetchAll();
} catch (Exception $e) {
    $message = '<div class="error-message">Erreur de base de données: ' . $e->getMessage() . '</div>';
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des activités - Le petit agenais</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
        }
        
        .header {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 1rem;
            padding: 1.5rem 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .header-title {
            font-size: 1.8rem;
            font-weight: 800;
            color: #1f2937;
        }
        
        .header-actions {
            display: flex;
            gap: 1rem;
            align-items: center;
        }
        
        .btn {
            padding: 0.5rem 1rem;
            border: none;
            border-radius: 0.5rem;
            text-decoration: none;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s;
            font-size: 0.875rem;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #2563eb, #3b82f6);
            color: white;
        }
        
        .btn-success {
            background: linear-gradient(135deg, #10b981, #059669);
            color: white;
        }
        
        .btn-danger {
            background: linear-gradient(135deg, #ef4444, #dc2626);
            color: white;
        }
        
        .btn-outline {
            background: transparent;
            border: 1px solid #d1d5db;
            color: #374151;
        }
        
        .btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }
        
        .content-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 1rem;
            padding: 2rem;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        }
        
        .activities-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
        }
        
        .activities-table th,
        .activities-table td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid #e5e7eb;
        }
        
        .activities-table th {
            background: #f9fafb;
            font-weight: 600;
            color: #374151;
        }
        
        .activities-table tr:hover {
            background: #f9fafb;
        }
        
        .activity-image {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 0.5rem;
        }
        
        .activity-rating {
            color: #fbbf24;
            font-weight: 600;
        }
        
        .activity-price {
            color: #10b981;
            font-weight: 600;
        }
        
        .actions-cell {
            display: flex;
            gap: 0.5rem;
        }
        
        .success-message {
            background: #dcfce7;
            border: 1px solid #bbf7d0;
            color: #166534;
            padding: 1rem;
            border-radius: 0.5rem;
            margin-bottom: 2rem;
        }
        
        .error-message {
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #b91c1c;
            padding: 1rem;
            border-radius: 0.5rem;
            margin-bottom: 2rem;
        }
        
        .empty-state {
            text-align: center;
            padding: 3rem;
            color: #6b7280;
        }
        
        .empty-state-icon {
            font-size: 4rem;
            margin-bottom: 1rem;
        }
        
        .stats-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            padding: 1rem;
            background: #f8fafc;
            border-radius: 0.5rem;
        }
        
        .activity-status {
            padding: 0.25rem 0.5rem;
            border-radius: 0.375rem;
            font-size: 0.75rem;
            font-weight: 500;
        }
        
        .status-active {
            background: #dcfce7;
            color: #166534;
        }
        
        .status-inactive {
            background: #fef3c7;
            color: #92400e;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1 class="header-title">🎯 Gestion des activités</h1>
            <div class="header-actions">
                <a href="add_activity.php" class="btn btn-success">➕ Ajouter une activité</a>
                <a href="dashboard.php" class="btn btn-outline">← Retour au dashboard</a>
            </div>
        </div>
        
        <?php echo $message; ?>
        
        <div class="content-card">
            <div class="stats-bar">
                <div>
                    <strong><?php echo count($activities); ?></strong> activités au total
                </div>
            </div>
            
            <?php if (empty($activities)): ?>
                <div class="empty-state">
                    <div class="empty-state-icon">🎯</div>
                    <h3>Aucune activité trouvée</h3>
                    <p>Commencez par ajouter votre première activité</p>
                    <a href="add_activity.php" class="btn btn-primary" style="margin-top: 1rem;">Ajouter une activité</a>
                </div>
            <?php else: ?>
                <table class="activities-table">
                    <thead>
                        <tr>
                            <th>Image</th>
                            <th>Nom</th>
                            <th>Adresse</th>
                            <th>Type</th>
                            <th>Note</th>
                            <th>Prix</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($activities as $activity): ?>
                            <tr>
                                <td>
                                    <?php if ($activity['image']): ?>
                                        <img src="<?php echo htmlspecialchars($activity['image']); ?>" 
                                             alt="<?php echo htmlspecialchars($activity['name']); ?>" 
                                             class="activity-image"
                                             onerror="this.src='../assets/images/placeholder.jpg'">
                                    <?php else: ?>
                                        <div class="activity-image" style="background: #f3f4f6; display: flex; align-items: center; justify-content: center;">
                                            🎯
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div>
                                        <strong><?php echo htmlspecialchars($activity['name']); ?></strong>
                                        <?php if ($activity['phone']): ?>
                                            <br><small style="color: #6b7280;"><?php echo htmlspecialchars($activity['phone']); ?></small>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td>
                                    <div>
                                        <?php echo htmlspecialchars($activity['address']); ?>
                                        <?php if ($activity['latitude'] && $activity['longitude']): ?>
                                            <br><small style="color: #6b7280;">📍 <?php echo $activity['latitude']; ?>, <?php echo $activity['longitude']; ?></small>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td>
                                    <span class="activity-type">
                                        <?php echo htmlspecialchars($activity['category'] ?? 'Activité'); ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="activity-rating">
                                        <?php if ($activity['rating']): ?>
                                            ⭐ <?php echo $activity['rating']; ?>/5
                                        <?php else: ?>
                                            - - -
                                        <?php endif; ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="activity-price">
                                        <?php 
                                        $price_range = $activity['price_range'] ?? 2;
                                        echo str_repeat('€', $price_range);
                                        ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="activity-status <?php echo isset($activity['is_active']) && $activity['is_active'] ? 'status-active' : 'status-inactive'; ?>">
                                        <?php echo isset($activity['is_active']) && $activity['is_active'] ? 'Actif' : 'Inactif'; ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="actions-cell">
                                        <a href="edit_activity.php?id=<?php echo $activity['id']; ?>" 
                                           class="btn btn-primary" 
                                           style="font-size: 0.75rem; padding: 0.375rem 0.75rem;">
                                            ✏️ Modifier
                                        </a>
                                        <a href="?action=delete&id=<?php echo $activity['id']; ?>" 
                                           class="btn btn-danger" 
                                           style="font-size: 0.75rem; padding: 0.375rem 0.75rem;"
                                           onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette activité ?')">
                                            🗑️ Supprimer
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>