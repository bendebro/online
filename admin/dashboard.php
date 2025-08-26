<?php
/**
 * Dashboard admin
 * Guide d'Agen
 */

session_start();

// Vérification de la connexion admin
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

require_once __DIR__ . '/../config/database.php';

// Récupération des statistiques
$stats = ['restaurants' => 0, 'activities' => 0, 'events' => 0];

try {
    $database = new Database();
    $db = $database->getConnection();
    
    foreach (['restaurants', 'activities', 'events'] as $table) {
        $stmt = $db->prepare("SELECT COUNT(*) as count FROM $table");
        $stmt->execute();
        $result = $stmt->fetch();
        $stats[$table] = $result['count'];
    }
} catch (Exception $e) {
    $error_message = 'Erreur de base de données: ' . $e->getMessage();
}

// Gestion de la déconnexion
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Guide d'Agen</title>
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
        
        .welcome-text {
            color: #6b7280;
            font-size: 0.9rem;
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
        
        .btn-outline {
            background: transparent;
            border: 1px solid #d1d5db;
            color: #374151;
        }
        
        .btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
            margin-bottom: 2rem;
        }
        
        .stat-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 1rem;
            padding: 2rem;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            text-align: center;
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
            font-weight: 500;
        }
        
        .quick-actions {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 1rem;
            padding: 2rem;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        }
        
        .quick-actions h2 {
            color: #1f2937;
            margin-bottom: 1.5rem;
            font-size: 1.5rem;
        }
        
        .actions-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
        }
        
        .action-card {
            background: linear-gradient(135deg, #f8fafc, #e2e8f0);
            border-radius: 0.75rem;
            padding: 1.5rem;
            text-align: center;
            transition: all 0.2s;
            text-decoration: none;
            color: #374151;
        }
        
        .action-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
        }
        
        .action-icon {
            font-size: 2rem;
            margin-bottom: 0.5rem;
        }
        
        .action-title {
            font-weight: 600;
            margin-bottom: 0.25rem;
        }
        
        .action-desc {
            font-size: 0.8rem;
            color: #6b7280;
        }
        
        .error-message {
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #b91c1c;
            padding: 1rem;
            border-radius: 0.5rem;
            margin-bottom: 2rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1 class="header-title">📊 Dashboard Administration</h1>
            <div class="header-actions">
                <span class="welcome-text">Bienvenue, <?php echo htmlspecialchars($_SESSION['admin_username']); ?> !</span>
                <a href="../index.php" class="btn btn-outline" target="_blank">🌐 Voir le site</a>
                <a href="?logout=1" class="btn btn-primary">🚪 Déconnexion</a>
            </div>
        </div>
        
        <?php if (isset($error_message)): ?>
            <div class="error-message">
                <?php echo htmlspecialchars($error_message); ?>
            </div>
        <?php endif; ?>
        
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
        </div>
        
        <div class="quick-actions">
            <h2>Actions rapides</h2>
            <div class="actions-grid">
                <a href="manage_restaurants.php" class="action-card">
                    <div class="action-icon">🍽️</div>
                    <div class="action-title">Gérer les restaurants</div>
                    <div class="action-desc">Voir, modifier, supprimer</div>
                </a>
                
                <a href="manage_activities.php" class="action-card">
                    <div class="action-icon">🎯</div>
                    <div class="action-title">Gérer les activités</div>
                    <div class="action-desc">Voir, modifier, supprimer</div>
                </a>
                
                <a href="manage_events.php" class="action-card">
                    <div class="action-icon">🎭</div>
                    <div class="action-title">Gérer les événements</div>
                    <div class="action-desc">Voir, modifier, supprimer</div>
                </a>
                
                <a href="add_restaurant.php" class="action-card">
                    <div class="action-icon">➕</div>
                    <div class="action-title">Ajouter un restaurant</div>
                    <div class="action-desc">Nouveau restaurant à Agen</div>
                </a>
                
                <a href="add_activity.php" class="action-card">
                    <div class="action-icon">🎪</div>
                    <div class="action-title">Ajouter une activité</div>
                    <div class="action-desc">Nouvelle activité touristique</div>
                </a>
                
                <a href="add_event.php" class="action-card">
                    <div class="action-icon">📅</div>
                    <div class="action-title">Ajouter un événement</div>
                    <div class="action-desc">Nouvel événement culturel</div>
                </a>
                
                <a href="rss_feeds.php" class="action-card">
                    <div class="action-icon">📡</div>
                    <div class="action-title">Flux RSS</div>
                    <div class="action-desc">Gérer les imports automatiques</div>
                </a>
                
                <a href="analytics.php" class="action-card">
                    <div class="action-icon">📊</div>
                    <div class="action-title">Analytics</div>
                    <div class="action-desc">Statistiques et performances</div>
                </a>
                
                <a href="company_info.php" class="action-card">
                    <div class="action-icon">🏢</div>
                    <div class="action-title">Informations d'entreprise</div>
                    <div class="action-desc">Gérer vos coordonnées</div>
                </a>
                
                <a href="highlights.php" class="action-card">
                    <div class="action-icon">🌟</div>
                    <div class="action-title">Coups de cœur</div>
                    <div class="action-desc">Gérer les éléments mis en avant</div>
                </a>
                
                <a href="booking_management.php" class="action-card">
                    <div class="action-icon">🔗</div>
                    <div class="action-title">Réservations</div>
                    <div class="action-desc">Gérer les liens de réservation</div>
                </a>
                
                <a href="../api/restaurants.php" class="action-card" target="_blank">
                    <div class="action-icon">📊</div>
                    <div class="action-title">API Restaurants</div>
                    <div class="action-desc">Tester l'API REST</div>
                </a>
                
                <a href="../install.php" class="action-card" target="_blank">
                    <div class="action-icon">⚙️</div>
                    <div class="action-title">Installation</div>
                    <div class="action-desc">Vérifier le système</div>
                </a>
            </div>
        </div>
    </div>
</body>
</html>