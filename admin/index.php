<?php
/**
 * Page d'accueil du panel d'administration
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

// Récupération des statistiques
$stats = [
    'restaurants' => 0,
    'activities' => 0,
    'events' => 0,
    'total_items' => 0
];

try {
    // Compter les restaurants
    $stmt = $db->query("SELECT COUNT(*) as count FROM restaurants WHERE is_active = 1");
    $stats['restaurants'] = $stmt->fetch()['count'];
    
    // Compter les activités
    $stmt = $db->query("SELECT COUNT(*) as count FROM activities WHERE is_active = 1");
    $stats['activities'] = $stmt->fetch()['count'];
    
    // Compter les événements
    $stmt = $db->query("SELECT COUNT(*) as count FROM events WHERE is_active = 1");
    $stats['events'] = $stmt->fetch()['count'];
    
    $stats['total_items'] = $stats['restaurants'] + $stats['activities'] + $stats['events'];
    
} catch (Exception $e) {
    // En cas d'erreur, garder les valeurs par défaut
}

// Récupération des derniers éléments ajoutés
$recent_items = [];
try {
    $recent_query = "
        (SELECT 'restaurant' as type, name, created_at FROM restaurants ORDER BY created_at DESC LIMIT 3)
        UNION
        (SELECT 'activity' as type, name, created_at FROM activities ORDER BY created_at DESC LIMIT 3)
        UNION
        (SELECT 'event' as type, name, created_at FROM events ORDER BY created_at DESC LIMIT 3)
        ORDER BY created_at DESC
        LIMIT 6
    ";
    
    $stmt = $db->query($recent_query);
    $recent_items = $stmt->fetchAll();
    
} catch (Exception $e) {
    // En cas d'erreur, garder le tableau vide
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administration - Le petit agenais</title>
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
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        
        .header h1 {
            font-size: 2.5rem;
            font-weight: 800;
            color: #1f2937;
            margin-bottom: 0.5rem;
        }
        
        .header p {
            color: #6b7280;
            font-size: 1.1rem;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .stat-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 1rem;
            padding: 2rem;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            text-align: center;
            transition: transform 0.2s;
        }
        
        .stat-card:hover {
            transform: translateY(-4px);
        }
        
        .stat-icon {
            font-size: 3rem;
            margin-bottom: 1rem;
        }
        
        .stat-number {
            font-size: 2.5rem;
            font-weight: 800;
            color: #1f2937;
            margin-bottom: 0.5rem;
        }
        
        .stat-label {
            color: #6b7280;
            font-size: 1rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        
        .actions-section {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 1rem;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        }
        
        .actions-section h2 {
            color: #1f2937;
            margin-bottom: 1.5rem;
            font-size: 1.5rem;
        }
        
        .actions-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1.5rem;
        }
        
        .action-card {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 0.75rem;
            padding: 1.5rem;
            text-decoration: none;
            color: inherit;
            transition: all 0.2s;
        }
        
        .action-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            border-color: #3b82f6;
        }
        
        .action-icon {
            font-size: 2rem;
            margin-bottom: 1rem;
        }
        
        .action-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: #1f2937;
            margin-bottom: 0.5rem;
        }
        
        .action-desc {
            color: #6b7280;
            font-size: 0.9rem;
        }
        
        .recent-section {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 1rem;
            padding: 2rem;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        }
        
        .recent-section h2 {
            color: #1f2937;
            margin-bottom: 1.5rem;
            font-size: 1.5rem;
        }
        
        .recent-list {
            list-style: none;
        }
        
        .recent-item {
            display: flex;
            align-items: center;
            padding: 1rem;
            background: #f8fafc;
            border-radius: 0.5rem;
            margin-bottom: 0.5rem;
            transition: background-color 0.2s;
        }
        
        .recent-item:hover {
            background: #e2e8f0;
        }
        
        .recent-icon {
            font-size: 1.5rem;
            margin-right: 1rem;
        }
        
        .recent-content {
            flex: 1;
        }
        
        .recent-name {
            font-weight: 600;
            color: #1f2937;
        }
        
        .recent-type {
            color: #6b7280;
            font-size: 0.9rem;
            text-transform: capitalize;
        }
        
        .recent-date {
            color: #9ca3af;
            font-size: 0.8rem;
        }
        
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem 1.5rem;
            background: linear-gradient(135deg, #3b82f6, #2563eb);
            color: white;
            text-decoration: none;
            border-radius: 0.5rem;
            font-weight: 500;
            transition: all 0.2s;
        }
        
        .btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.4);
        }
        
        .btn-outline {
            background: transparent;
            border: 1px solid #d1d5db;
            color: #374151;
        }
        
        .btn-outline:hover {
            background: #f3f4f6;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }
        
        .header-actions {
            display: flex;
            gap: 1rem;
            justify-content: center;
            margin-top: 1.5rem;
        }
        
        @media (max-width: 768px) {
            .stats-grid {
                grid-template-columns: 1fr;
            }
            
            .actions-grid {
                grid-template-columns: 1fr;
            }
            
            .header-actions {
                flex-direction: column;
                align-items: center;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🏛️ Administration</h1>
            <p>Bienvenue dans le panel d'administration du petit agenais</p>
            <div class="header-actions">
                <a href="dashboard.php" class="btn">
                    📊 Dashboard détaillé
                </a>
                <a href="../index.php" class="btn btn-outline" target="_blank">
                    🌐 Voir le site
                </a>
                <a href="logout.php" class="btn btn-outline">
                    🚪 Déconnexion
                </a>
            </div>
        </div>
        
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon">🍽️</div>
                <div class="stat-number"><?php echo $stats['restaurants']; ?></div>
                <div class="stat-label">Restaurants</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">🎯</div>
                <div class="stat-number"><?php echo $stats['activities']; ?></div>
                <div class="stat-label">Activités</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">🎭</div>
                <div class="stat-number"><?php echo $stats['events']; ?></div>
                <div class="stat-label">Événements</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">📊</div>
                <div class="stat-number"><?php echo $stats['total_items']; ?></div>
                <div class="stat-label">Total</div>
            </div>
        </div>
        
        <div class="actions-section">
            <h2>🚀 Actions rapides</h2>
            <div class="actions-grid">
                <a href="manage_restaurants.php" class="action-card">
                    <div class="action-icon">🍽️</div>
                    <div class="action-title">Gérer les restaurants</div>
                    <div class="action-desc">Voir, modifier et supprimer les restaurants</div>
                </a>
                
                <a href="manage_activities.php" class="action-card">
                    <div class="action-icon">🎯</div>
                    <div class="action-title">Gérer les activités</div>
                    <div class="action-desc">Voir, modifier et supprimer les activités</div>
                </a>
                
                <a href="manage_events.php" class="action-card">
                    <div class="action-icon">🎭</div>
                    <div class="action-title">Gérer les événements</div>
                    <div class="action-desc">Voir, modifier et supprimer les événements</div>
                </a>
                
                <a href="add_restaurant.php" class="action-card">
                    <div class="action-icon">➕</div>
                    <div class="action-title">Ajouter un restaurant</div>
                    <div class="action-desc">Créer un nouveau restaurant</div>
                </a>
                
                <a href="add_activity.php" class="action-card">
                    <div class="action-icon">🎪</div>
                    <div class="action-title">Ajouter une activité</div>
                    <div class="action-desc">Créer une nouvelle activité</div>
                </a>
                
                <a href="add_event.php" class="action-card">
                    <div class="action-icon">📅</div>
                    <div class="action-title">Ajouter un événement</div>
                    <div class="action-desc">Créer un nouvel événement</div>
                </a>
            </div>
        </div>
        
        <?php if (!empty($recent_items)): ?>
        <div class="recent-section">
            <h2>🕒 Derniers éléments ajoutés</h2>
            <ul class="recent-list">
                <?php foreach ($recent_items as $item): ?>
                    <li class="recent-item">
                        <div class="recent-icon">
                            <?php 
                            switch ($item['type']) {
                                case 'restaurant': echo '🍽️'; break;
                                case 'activity': echo '🎯'; break;
                                case 'event': echo '🎭'; break;
                                default: echo '📄'; break;
                            }
                            ?>
                        </div>
                        <div class="recent-content">
                            <div class="recent-name"><?php echo htmlspecialchars($item['name']); ?></div>
                            <div class="recent-type"><?php echo htmlspecialchars($item['type']); ?></div>
                        </div>
                        <div class="recent-date">
                            <?php echo date('d/m/Y', strtotime($item['created_at'])); ?>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php endif; ?>
    </div>
</body>
</html>