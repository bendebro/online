<?php
/**
 * Gestion des liens de réservation
 * Interface d'administration - Guide d'Agen
 */

session_start();

// Vérification de la connexion admin
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

require_once '../config/database.php';

$message = '';
$error = '';

try {
    $database = new Database();
    $db = $database->getConnection();
    
    // Gestion des actions
    if ($_POST) {
        if (isset($_POST['action']) && $_POST['action'] === 'update_booking') {
            $table = $_POST['table'];
            $id = intval($_POST['id']);
            $booking_url = $_POST['booking_url'];
            $booking_platform = $_POST['booking_platform'];
            $booking_phone = $_POST['booking_phone'];
            $ticket_price = $_POST['ticket_price'] ?? null;
            
            if (in_array($table, ['restaurants', 'activities', 'events'])) {
                $sql = "UPDATE {$table} SET booking_url = ?, booking_platform = ?, booking_phone = ?";
                $params = [$booking_url, $booking_platform, $booking_phone];
                
                if ($table !== 'restaurants' && $ticket_price !== null) {
                    $sql .= ", ticket_price = ?";
                    $params[] = $ticket_price;
                }
                
                $sql .= " WHERE id = ?";
                $params[] = $id;
                
                $stmt = $db->prepare($sql);
                if ($stmt->execute($params)) {
                    $message = "Informations de réservation mises à jour !";
                } else {
                    $error = "Erreur lors de la mise à jour.";
                }
            }
        }
    }
    
    // Récupération de tous les items
    $restaurants = $db->query("SELECT id, name, booking_url, booking_platform, booking_phone FROM restaurants ORDER BY name")->fetchAll();
    $activities = $db->query("SELECT id, name, booking_url, booking_platform, booking_phone, ticket_price FROM activities ORDER BY name")->fetchAll(); 
    $events = $db->query("SELECT id, name, booking_url, booking_platform, booking_phone, ticket_price FROM events ORDER BY name")->fetchAll();
    
} catch (Exception $e) {
    $error = "Erreur de base de données : " . $e->getMessage();
    $restaurants = $activities = $events = [];
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion Réservations - Guide d'Agen</title>
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
        
        .btn-outline {
            background: transparent;
            border: 1px solid #d1d5db;
            color: #374151;
        }
        
        .btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }
        
        .section-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 1rem;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        }
        
        .section-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .items-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(400px, 1fr));
            gap: 1.5rem;
        }
        
        .item-card {
            background: #f8fafc;
            border-radius: 0.75rem;
            padding: 1.5rem;
            border: 1px solid #e2e8f0;
        }
        
        .item-name {
            font-size: 1.1rem;
            font-weight: 600;
            color: #1f2937;
            margin-bottom: 1rem;
        }
        
        .form-group {
            margin-bottom: 1rem;
        }
        
        .form-label {
            display: block;
            font-weight: 500;
            color: #374151;
            margin-bottom: 0.5rem;
            font-size: 0.875rem;
        }
        
        .form-input, .form-select {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #d1d5db;
            border-radius: 0.5rem;
            font-size: 0.875rem;
            transition: all 0.2s;
        }
        
        .form-input:focus, .form-select:focus {
            outline: none;
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }
        
        .btn-save {
            background: linear-gradient(135deg, #10b981, #059669);
            color: white;
            padding: 0.5rem 1rem;
            border: none;
            border-radius: 0.5rem;
            font-size: 0.875rem;
            cursor: pointer;
            transition: all 0.2s;
        }
        
        .btn-save:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
        }
        
        .current-info {
            background: #f0f9ff;
            border: 1px solid #bae6fd;
            padding: 0.75rem;
            border-radius: 0.5rem;
            margin-bottom: 1rem;
            font-size: 0.875rem;
        }
        
        .current-info strong {
            color: #0369a1;
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
        
        .booking-preview {
            background: #fffbeb;
            border: 1px solid #fed7aa;
            padding: 0.75rem;
            border-radius: 0.5rem;
            margin-top: 0.5rem;
            font-size: 0.875rem;
        }
        
        .booking-preview a {
            color: #ea580c;
            text-decoration: none;
            font-weight: 500;
        }
        
        @media (max-width: 768px) {
            .container { padding: 1rem; }
            .header { flex-direction: column; gap: 1rem; text-align: center; }
            .items-grid { grid-template-columns: 1fr; }
            .item-card { padding: 1rem; }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1 class="header-title">🔗 Gestion des Réservations</h1>
            <a href="dashboard.php" class="btn btn-outline">← Dashboard</a>
        </div>
        
        <?php if ($message): ?>
            <div class="alert alert-success">✅ <?php echo h($message); ?></div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="alert alert-error">❌ <?php echo h($error); ?></div>
        <?php endif; ?>
        
        <!-- Restaurants -->
        <div class="section-card">
            <h2 class="section-title">🍽️ Restaurants</h2>
            <div class="items-grid">
                <?php foreach ($restaurants as $restaurant): ?>
                    <div class="item-card">
                        <div class="item-name"><?php echo h($restaurant['name']); ?></div>
                        
                        <?php if ($restaurant['booking_url']): ?>
                            <div class="current-info">
                                <strong>Réservation actuelle:</strong><br>
                                <?php echo h($restaurant['booking_platform'] ?: 'Non spécifié'); ?><br>
                                <a href="<?php echo h($restaurant['booking_url']); ?>" target="_blank">
                                    <?php echo h(substr($restaurant['booking_url'], 0, 50)); ?>...
                                </a>
                            </div>
                        <?php endif; ?>
                        
                        <form method="POST" style="display: inline;">
                            <input type="hidden" name="action" value="update_booking">
                            <input type="hidden" name="table" value="restaurants">
                            <input type="hidden" name="id" value="<?php echo $restaurant['id']; ?>">
                            
                            <div class="form-group">
                                <label class="form-label">URL de réservation</label>
                                <input type="url" name="booking_url" class="form-input" 
                                       value="<?php echo h($restaurant['booking_url']); ?>"
                                       placeholder="https://www.lafourchette.com/restaurant/...">
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label">Plateforme</label>
                                <select name="booking_platform" class="form-select">
                                    <option value="">Sélectionner...</option>
                                    <option value="LaFourchette" <?php echo $restaurant['booking_platform'] === 'LaFourchette' ? 'selected' : ''; ?>>LaFourchette</option>
                                    <option value="OpenTable" <?php echo $restaurant['booking_platform'] === 'OpenTable' ? 'selected' : ''; ?>>OpenTable</option>
                                    <option value="Resy" <?php echo $restaurant['booking_platform'] === 'Resy' ? 'selected' : ''; ?>>Resy</option>
                                    <option value="Téléphone" <?php echo $restaurant['booking_platform'] === 'Téléphone' ? 'selected' : ''; ?>>Téléphone uniquement</option>
                                    <option value="Site web" <?php echo $restaurant['booking_platform'] === 'Site web' ? 'selected' : ''; ?>>Site web propre</option>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label">Téléphone de réservation</label>
                                <input type="tel" name="booking_phone" class="form-input"
                                       value="<?php echo h($restaurant['booking_phone']); ?>"
                                       placeholder="05 53 XX XX XX">
                            </div>
                            
                            <button type="submit" class="btn-save">💾 Sauvegarder</button>
                        </form>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        
        <!-- Activités -->
        <div class="section-card">
            <h2 class="section-title">🎯 Activités</h2>
            <div class="items-grid">
                <?php foreach ($activities as $activity): ?>
                    <div class="item-card">
                        <div class="item-name"><?php echo h($activity['name']); ?></div>
                        
                        <?php if ($activity['booking_url']): ?>
                            <div class="current-info">
                                <strong>Réservation actuelle:</strong><br>
                                <?php echo h($activity['booking_platform'] ?: 'Non spécifié'); ?><br>
                                Tarif: <?php echo h($activity['ticket_price'] ?: 'Non spécifié'); ?>
                            </div>
                        <?php endif; ?>
                        
                        <form method="POST">
                            <input type="hidden" name="action" value="update_booking">
                            <input type="hidden" name="table" value="activities">
                            <input type="hidden" name="id" value="<?php echo $activity['id']; ?>">
                            
                            <div class="form-group">
                                <label class="form-label">URL de réservation</label>
                                <input type="url" name="booking_url" class="form-input"
                                       value="<?php echo h($activity['booking_url']); ?>"
                                       placeholder="https://www.musee-agen.fr/billetterie">
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label">Plateforme</label>
                                <select name="booking_platform" class="form-select">
                                    <option value="">Sélectionner...</option>
                                    <option value="Billetterie officielle" <?php echo $activity['booking_platform'] === 'Billetterie officielle' ? 'selected' : ''; ?>>Billetterie officielle</option>
                                    <option value="Fnac Spectacles" <?php echo $activity['booking_platform'] === 'Fnac Spectacles' ? 'selected' : ''; ?>>Fnac Spectacles</option>
                                    <option value="Ticketmaster" <?php echo $activity['booking_platform'] === 'Ticketmaster' ? 'selected' : ''; ?>>Ticketmaster</option>
                                    <option value="Réservation en ligne" <?php echo $activity['booking_platform'] === 'Réservation en ligne' ? 'selected' : ''; ?>>Réservation en ligne</option>
                                    <option value="Sur place" <?php echo $activity['booking_platform'] === 'Sur place' ? 'selected' : ''; ?>>Sur place uniquement</option>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label">Téléphone</label>
                                <input type="tel" name="booking_phone" class="form-input"
                                       value="<?php echo h($activity['booking_phone']); ?>"
                                       placeholder="05 53 XX XX XX">
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label">Tarifs</label>
                                <input type="text" name="ticket_price" class="form-input"
                                       value="<?php echo h($activity['ticket_price']); ?>"
                                       placeholder="8€ adulte, gratuit -18 ans">
                            </div>
                            
                            <button type="submit" class="btn-save">💾 Sauvegarder</button>
                        </form>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        
        <!-- Événements -->
        <div class="section-card">
            <h2 class="section-title">🎭 Événements</h2>
            <div class="items-grid">
                <?php foreach ($events as $event): ?>
                    <div class="item-card">
                        <div class="item-name"><?php echo h($event['name']); ?></div>
                        
                        <?php if ($event['booking_url']): ?>
                            <div class="current-info">
                                <strong>Billetterie actuelle:</strong><br>
                                <?php echo h($event['booking_platform'] ?: 'Non spécifié'); ?><br>
                                Prix: <?php echo h($event['ticket_price'] ?: 'Non spécifié'); ?>
                            </div>
                        <?php endif; ?>
                        
                        <form method="POST">
                            <input type="hidden" name="action" value="update_booking">
                            <input type="hidden" name="table" value="events">
                            <input type="hidden" name="id" value="<?php echo $event['id']; ?>">
                            
                            <div class="form-group">
                                <label class="form-label">URL de billetterie</label>
                                <input type="url" name="booking_url" class="form-input"
                                       value="<?php echo h($event['booking_url']); ?>"
                                       placeholder="https://festival-agen.fr/billetterie">
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label">Plateforme</label>
                                <select name="booking_platform" class="form-select">
                                    <option value="">Sélectionner...</option>
                                    <option value="Billetterie festival" <?php echo $event['booking_platform'] === 'Billetterie festival' ? 'selected' : ''; ?>>Billetterie festival</option>
                                    <option value="Fnac Spectacles" <?php echo $event['booking_platform'] === 'Fnac Spectacles' ? 'selected' : ''; ?>>Fnac Spectacles</option>
                                    <option value="Ticketmaster" <?php echo $event['booking_platform'] === 'Ticketmaster' ? 'selected' : ''; ?>>Ticketmaster</option>
                                    <option value="Eventbrite" <?php echo $event['booking_platform'] === 'Eventbrite' ? 'selected' : ''; ?>>Eventbrite</option>
                                    <option value="Entrée gratuite" <?php echo $event['booking_platform'] === 'Entrée gratuite' ? 'selected' : ''; ?>>Entrée gratuite</option>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label">Téléphone info</label>
                                <input type="tel" name="booking_phone" class="form-input"
                                       value="<?php echo h($event['booking_phone']); ?>"
                                       placeholder="05 53 XX XX XX">
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label">Prix des billets</label>
                                <input type="text" name="ticket_price" class="form-input"
                                       value="<?php echo h($event['ticket_price']); ?>"
                                       placeholder="Gratuit / 15€ / 20€ plein tarif, 15€ réduit">
                            </div>
                            
                            <button type="submit" class="btn-save">💾 Sauvegarder</button>
                        </form>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</body>
</html>