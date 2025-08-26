<?php
/**
 * Gestion des événements
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

// Suppression d'un événement
if ($action === 'delete' && $id) {
    try {
        $stmt = $db->prepare("DELETE FROM events WHERE id = ?");
        $stmt->execute([$id]);
        $message = '<div class="success-message">Événement supprimé avec succès!</div>';
    } catch (Exception $e) {
        $message = '<div class="error-message">Erreur lors de la suppression: ' . $e->getMessage() . '</div>';
    }
}

// Récupération des événements
$events = [];
try {
    $stmt = $db->prepare("SELECT * FROM events ORDER BY event_date DESC");
    $stmt->execute();
    $events = $stmt->fetchAll();
} catch (Exception $e) {
    $message = '<div class="error-message">Erreur de base de données: ' . $e->getMessage() . '</div>';
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des événements - Le petit agenais</title>
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
        
        .events-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
        }
        
        .events-table th,
        .events-table td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid #e5e7eb;
        }
        
        .events-table th {
            background: #f9fafb;
            font-weight: 600;
            color: #374151;
        }
        
        .events-table tr:hover {
            background: #f9fafb;
        }
        
        .event-image {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 0.5rem;
        }
        
        .event-rating {
            color: #fbbf24;
            font-weight: 600;
        }
        
        .event-price {
            color: #10b981;
            font-weight: 600;
        }
        
        .event-date {
            color: #6366f1;
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
        
        .event-status {
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
        
        .status-past {
            background: #f3f4f6;
            color: #6b7280;
        }
        
        .status-upcoming {
            background: #dbeafe;
            color: #1d4ed8;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1 class="header-title">🎭 Gestion des événements</h1>
            <div class="header-actions">
                <a href="add_event.php" class="btn btn-success">➕ Ajouter un événement</a>
                <a href="dashboard.php" class="btn btn-outline">← Retour au dashboard</a>
            </div>
        </div>
        
        <?php echo $message; ?>
        
        <div class="content-card">
            <div class="stats-bar">
                <div>
                    <strong><?php echo count($events); ?></strong> événements au total
                </div>
            </div>
            
            <?php if (empty($events)): ?>
                <div class="empty-state">
                    <div class="empty-state-icon">🎭</div>
                    <h3>Aucun événement trouvé</h3>
                    <p>Commencez par ajouter votre premier événement</p>
                    <a href="add_event.php" class="btn btn-primary" style="margin-top: 1rem;">Ajouter un événement</a>
                </div>
            <?php else: ?>
                <table class="events-table">
                    <thead>
                        <tr>
                            <th>Image</th>
                            <th>Nom</th>
                            <th>Date</th>
                            <th>Lieu</th>
                            <th>Type</th>
                            <th>Prix</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($events as $event): ?>
                            <?php 
                            $event_date = new DateTime($event['event_date']);
                            $now = new DateTime();
                            $is_past = $event_date < $now;
                            ?>
                            <tr>
                                <td>
                                    <?php if ($event['image']): ?>
                                        <img src="<?php echo htmlspecialchars($event['image']); ?>" 
                                             alt="<?php echo htmlspecialchars($event['name']); ?>" 
                                             class="event-image"
                                             onerror="this.src='../assets/images/placeholder.jpg'">
                                    <?php else: ?>
                                        <div class="event-image" style="background: #f3f4f6; display: flex; align-items: center; justify-content: center;">
                                            🎭
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div>
                                        <strong><?php echo htmlspecialchars($event['name']); ?></strong>
                                        <?php if ($event['phone']): ?>
                                            <br><small style="color: #6b7280;"><?php echo htmlspecialchars($event['phone']); ?></small>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td>
                                    <span class="event-date">
                                        <?php echo $event_date->format('d/m/Y'); ?>
                                        <?php if ($event['event_time']): ?>
                                            <br><small style="color: #6b7280;"><?php echo htmlspecialchars($event['event_time']); ?></small>
                                        <?php endif; ?>
                                    </span>
                                </td>
                                <td>
                                    <div>
                                        <?php echo htmlspecialchars($event['address']); ?>
                                        <?php if ($event['latitude'] && $event['longitude']): ?>
                                            <br><small style="color: #6b7280;">📍 <?php echo $event['latitude']; ?>, <?php echo $event['longitude']; ?></small>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td>
                                    <span class="event-type">
                                        <?php echo htmlspecialchars($event['category'] ?? 'Événement'); ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="event-price">
                                        <?php 
                                        $price_range = $event['price_range'] ?? 2;
                                        if ($price_range == 0) {
                                            echo 'Gratuit';
                                        } else {
                                            echo str_repeat('€', $price_range);
                                        }
                                        ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="event-status <?php echo $is_past ? 'status-past' : 'status-upcoming'; ?>">
                                        <?php echo $is_past ? 'Passé' : 'À venir'; ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="actions-cell">
                                        <a href="edit_event.php?id=<?php echo $event['id']; ?>" 
                                           class="btn btn-primary" 
                                           style="font-size: 0.75rem; padding: 0.375rem 0.75rem;">
                                            ✏️ Modifier
                                        </a>
                                        <a href="?action=delete&id=<?php echo $event['id']; ?>" 
                                           class="btn btn-danger" 
                                           style="font-size: 0.75rem; padding: 0.375rem 0.75rem;"
                                           onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet événement ?')">
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