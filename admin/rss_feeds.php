<?php
/**
 * Gestion des flux RSS - Interface d'administration
 * Guide d'Agen
 */

session_start();

// Vérification de la connexion admin
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

require_once '../config/database.php';
require_once '../includes/rss_importer.php';

$message = '';
$error = '';

try {
    $database = new Database();
    $db = $database->getConnection();
    $importer = new RSSEventImporter($db);
    
    // Gestion des actions
    if ($_POST) {
        if (isset($_POST['action'])) {
            switch ($_POST['action']) {
                case 'add_feed':
                    $stmt = $db->prepare("INSERT INTO rss_feeds (name, url, description, category_mapping, price_mapping) VALUES (?, ?, ?, ?, ?)");
                    if ($stmt->execute([$_POST['name'], $_POST['url'], $_POST['description'], $_POST['category_mapping'], $_POST['price_mapping']])) {
                        $message = "Flux RSS ajouté avec succès !";
                    } else {
                        $error = "Erreur lors de l'ajout du flux RSS.";
                    }
                    break;
                    
                case 'toggle_feed':
                    $stmt = $db->prepare("UPDATE rss_feeds SET is_active = NOT is_active WHERE id = ?");
                    $stmt->execute([$_POST['feed_id']]);
                    $message = "Statut du flux modifié !";
                    break;
                    
                case 'delete_feed':
                    $stmt = $db->prepare("DELETE FROM rss_feeds WHERE id = ?");
                    $stmt->execute([$_POST['feed_id']]);
                    $message = "Flux RSS supprimé !";
                    break;
                    
                case 'import_feed':
                    $result = $importer->importFeed($_POST['feed_id']);
                    if ($result['success']) {
                        $message = "Import réussi : {$result['imported']} événements importés, {$result['duplicates']} doublons ignorés.";
                    } else {
                        $error = "Erreur d'import : " . $result['error'];
                    }
                    break;
                    
                case 'import_all':
                    $result = $importer->importAllFeeds();
                    if ($result['success']) {
                        $message = "Import global réussi : {$result['total_imported']} événements importés depuis {$result['feeds_processed']} flux.";
                    } else {
                        $error = "Erreur d'import global : " . $result['error'];
                    }
                    break;
                    
                case 'clean_old':
                    $deleted = $importer->cleanOldImportedEvents();
                    $message = "$deleted anciens événements supprimés.";
                    break;
            }
        }
    }
    
    // Récupération des flux RSS
    $stmt = $db->query("SELECT * FROM rss_feeds ORDER BY name");
    $feeds = $stmt->fetchAll();
    
    // Statistiques
    $stats = $importer->getImportStats();
    
} catch (Exception $e) {
    $error = "Erreur de base de données : " . $e->getMessage();
    $feeds = [];
    $stats = [];
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion RSS - Guide d'Agen</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            color: #1f2937;
        }
        
        .container {
            max-width: 1400px;
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
        }
        
        .btn {
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 0.5rem;
            font-weight: 500;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.2s;
            font-size: 0.875rem;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .btn-primary { background: linear-gradient(135deg, #2563eb, #3b82f6); color: white; }
        .btn-success { background: linear-gradient(135deg, #059669, #10b981); color: white; }
        .btn-warning { background: linear-gradient(135deg, #d97706, #f59e0b); color: white; }
        .btn-danger { background: linear-gradient(135deg, #dc2626, #ef4444); color: white; }
        .btn-outline { background: transparent; border: 1px solid #d1d5db; color: #374151; }
        
        .btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .stat-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 1rem;
            padding: 1.5rem;
            text-align: center;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        }
        
        .stat-icon {
            font-size: 2.5rem;
            margin-bottom: 0.5rem;
        }
        
        .stat-number {
            font-size: 2rem;
            font-weight: 800;
            color: #1f2937;
            margin-bottom: 0.25rem;
        }
        
        .stat-label {
            color: #6b7280;
            font-size: 0.875rem;
        }
        
        .card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 1rem;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        }
        
        .card-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1rem;
            margin-bottom: 1.5rem;
        }
        
        .form-group {
            display: flex;
            flex-direction: column;
        }
        
        .form-label {
            font-weight: 500;
            color: #374151;
            margin-bottom: 0.5rem;
        }
        
        .form-input, .form-select, .form-textarea {
            padding: 0.75rem;
            border: 1px solid #d1d5db;
            border-radius: 0.5rem;
            font-size: 0.875rem;
            transition: all 0.2s;
        }
        
        .form-textarea {
            resize: vertical;
            min-height: 100px;
        }
        
        .form-input:focus, .form-select:focus, .form-textarea:focus {
            outline: none;
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }
        
        .feeds-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
        }
        
        .feeds-table th,
        .feeds-table td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid #e5e7eb;
        }
        
        .feeds-table th {
            background: #f9fafb;
            font-weight: 600;
            color: #374151;
        }
        
        .status-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 500;
        }
        
        .status-active {
            background: #d1fae5;
            color: #065f46;
        }
        
        .status-inactive {
            background: #fee2e2;
            color: #991b1b;
        }
        
        .actions {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
        }
        
        .alert {
            padding: 1rem;
            border-radius: 0.5rem;
            margin-bottom: 1rem;
        }
        
        .alert-success {
            background: #d1fae5;
            color: #065f46;
            border: 1px solid #a7f3d0;
        }
        
        .alert-error {
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #fca5a5;
        }
        
        .logs-list {
            max-height: 300px;
            overflow-y: auto;
            border: 1px solid #e5e7eb;
            border-radius: 0.5rem;
            padding: 1rem;
        }
        
        .log-item {
            padding: 0.5rem 0;
            border-bottom: 1px solid #f3f4f6;
            font-size: 0.875rem;
        }
        
        .log-item:last-child {
            border-bottom: none;
        }
        
        .log-success { color: #059669; }
        .log-duplicate { color: #d97706; }
        .log-error { color: #dc2626; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1 class="header-title">🔗 Gestion des flux RSS</h1>
            <div class="header-actions">
                <a href="dashboard.php" class="btn btn-outline">← Dashboard</a>
                <form method="POST" style="display: inline;">
                    <input type="hidden" name="action" value="import_all">
                    <button type="submit" class="btn btn-success">📡 Import global</button>
                </form>
            </div>
        </div>
        
        <?php if ($message): ?>
            <div class="alert alert-success">✅ <?php echo h($message); ?></div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="alert alert-error">❌ <?php echo h($error); ?></div>
        <?php endif; ?>
        
        <!-- Statistiques -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon">📡</div>
                <div class="stat-number"><?php echo $stats['total_feeds'] ?? 0; ?></div>
                <div class="stat-label">Flux RSS total</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">✅</div>
                <div class="stat-number"><?php echo $stats['active_feeds'] ?? 0; ?></div>
                <div class="stat-label">Flux actifs</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">🎭</div>
                <div class="stat-number"><?php echo $stats['imported_events'] ?? 0; ?></div>
                <div class="stat-label">Événements importés</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">🧹</div>
                <div class="stat-number">
                    <form method="POST" style="display: inline;">
                        <input type="hidden" name="action" value="clean_old">
                        <button type="submit" class="btn btn-warning btn-sm">Nettoyer</button>
                    </form>
                </div>
                <div class="stat-label">Anciens événements</div>
            </div>
        </div>
        
        <!-- Ajouter un flux RSS -->
        <div class="card">
            <h2 class="card-title">➕ Ajouter un flux RSS</h2>
            
            <form method="POST">
                <input type="hidden" name="action" value="add_feed">
                
                <div class="form-grid">
                    <div class="form-group">
                        <label class="form-label">Nom du flux</label>
                        <input type="text" name="name" class="form-input" required placeholder="Ex: Ville d'Agen">
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">URL du flux RSS</label>
                        <input type="url" name="url" class="form-input" required placeholder="https://example.com/rss">
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Catégorie par défaut</label>
                        <select name="category_mapping" class="form-select">
                            <option value="Événement">Événement</option>
                            <option value="Concert">Concert</option>
                            <option value="Théâtre">Théâtre</option>
                            <option value="Festival">Festival</option>
                            <option value="Sport">Sport</option>
                            <option value="Culture">Culture</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Prix par défaut</label>
                        <select name="price_mapping" class="form-select">
                            <option value="Gratuit">Gratuit</option>
                            <option value="€">€</option>
                            <option value="€€">€€</option>
                            <option value="€€€">€€€</option>
                        </select>
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-textarea" placeholder="Description du flux RSS..."></textarea>
                </div>
                
                <button type="submit" class="btn btn-primary">📡 Ajouter le flux</button>
            </form>
        </div>
        
        <!-- Liste des flux RSS -->
        <div class="card">
            <h2 class="card-title">📡 Flux RSS configurés</h2>
            
            <?php if (empty($feeds)): ?>
                <p style="text-align: center; color: #6b7280; padding: 2rem;">Aucun flux RSS configuré.</p>
            <?php else: ?>
                <table class="feeds-table">
                    <thead>
                        <tr>
                            <th>Nom</th>
                            <th>URL</th>
                            <th>Statut</th>
                            <th>Dernière import</th>
                            <th>Total importé</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($feeds as $feed): ?>
                            <tr>
                                <td>
                                    <strong><?php echo h($feed['name']); ?></strong><br>
                                    <small style="color: #6b7280;"><?php echo h($feed['description']); ?></small>
                                </td>
                                <td>
                                    <a href="<?php echo h($feed['url']); ?>" target="_blank" style="color: #2563eb; font-size: 0.875rem;">
                                        <?php echo h(strlen($feed['url']) > 40 ? substr($feed['url'], 0, 40) . '...' : $feed['url']); ?>
                                    </a>
                                </td>
                                <td>
                                    <span class="status-badge <?php echo $feed['is_active'] ? 'status-active' : 'status-inactive'; ?>">
                                        <?php echo $feed['is_active'] ? 'Actif' : 'Inactif'; ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if ($feed['last_fetch']): ?>
                                        <small><?php echo date('d/m/Y H:i', strtotime($feed['last_fetch'])); ?></small>
                                    <?php else: ?>
                                        <small style="color: #6b7280;">Jamais</small>
                                    <?php endif; ?>
                                    
                                    <?php if ($feed['last_error']): ?>
                                        <br><small style="color: #dc2626;">Erreur: <?php echo h(substr($feed['last_error'], 0, 50)); ?>...</small>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <strong><?php echo $feed['total_imported']; ?></strong> événements
                                </td>
                                <td>
                                    <div class="actions">
                                        <form method="POST" style="display: inline;">
                                            <input type="hidden" name="action" value="import_feed">
                                            <input type="hidden" name="feed_id" value="<?php echo $feed['id']; ?>">
                                            <button type="submit" class="btn btn-success" style="padding: 0.5rem;">📥</button>
                                        </form>
                                        
                                        <form method="POST" style="display: inline;">
                                            <input type="hidden" name="action" value="toggle_feed">
                                            <input type="hidden" name="feed_id" value="<?php echo $feed['id']; ?>">
                                            <button type="submit" class="btn <?php echo $feed['is_active'] ? 'btn-warning' : 'btn-success'; ?>" style="padding: 0.5rem;">
                                                <?php echo $feed['is_active'] ? '⏸️' : '▶️'; ?>
                                            </button>
                                        </form>
                                        
                                        <form method="POST" style="display: inline;" onsubmit="return confirm('Supprimer ce flux RSS ?')">
                                            <input type="hidden" name="action" value="delete_feed">
                                            <input type="hidden" name="feed_id" value="<?php echo $feed['id']; ?>">
                                            <button type="submit" class="btn btn-danger" style="padding: 0.5rem;">🗑️</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
        
        <!-- Logs récents -->
        <?php if (!empty($stats['recent_logs'])): ?>
        <div class="card">
            <h2 class="card-title">📊 Activité récente</h2>
            
            <div class="logs-list">
                <?php foreach ($stats['recent_logs'] as $log): ?>
                    <div class="log-item log-<?php echo $log['action']; ?>">
                        <strong><?php echo h($log['name']); ?></strong> - 
                        <?php echo h($log['event_title']); ?>
                        <span style="color: #6b7280;">
                            (<?php echo date('d/m/Y H:i', strtotime($log['created_at'])); ?>)
                        </span>
                        <br>
                        <small><?php echo h($log['message']); ?></small>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
</body>
</html>