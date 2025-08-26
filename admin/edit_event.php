<?php
/**
 * Modification d'un événement
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

$id = $_GET['id'] ?? '';
$message = '';

if (!$id) {
    header('Location: manage_events.php');
    exit;
}

// Récupération des données de l'événement
$event = null;
try {
    $stmt = $db->prepare("SELECT * FROM events WHERE id = ?");
    $stmt->execute([$id]);
    $event = $stmt->fetch();
    
    if (!$event) {
        header('Location: manage_events.php');
        exit;
    }
} catch (Exception $e) {
    $message = '<div class="error-message">Erreur lors de la récupération des données: ' . $e->getMessage() . '</div>';
}

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $description = $_POST['description'] ?? '';
    $address = $_POST['address'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $email = $_POST['email'] ?? '';
    $website = $_POST['website'] ?? '';
    $category = $_POST['category'] ?? '';
    $price_range = $_POST['price_range'] ?? 2;
    $rating = $_POST['rating'] ?? null;
    $latitude = $_POST['latitude'] ?? null;
    $longitude = $_POST['longitude'] ?? null;
    $image = $_POST['image'] ?? '';
    $booking_url = $_POST['booking_url'] ?? '';
    $event_date = $_POST['event_date'] ?? '';
    $event_time = $_POST['event_time'] ?? '';
    $end_date = $_POST['end_date'] ?? '';
    $duration = $_POST['duration'] ?? '';
    $capacity = $_POST['capacity'] ?? null;
    $organizer = $_POST['organizer'] ?? '';
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    
    // Validation
    if (empty($name) || empty($address) || empty($event_date)) {
        $message = '<div class="error-message">Le nom, l\'adresse et la date sont obligatoires.</div>';
    } else {
        try {
            // Préparation des données JSON
            $features = [];
            if (isset($_POST['features'])) {
                $features = $_POST['features'];
            }
            
            $specialties = [];
            if (isset($_POST['specialties'])) {
                $specialties = $_POST['specialties'];
            }
            
            $tags = [];
            if (isset($_POST['tags'])) {
                $tags = $_POST['tags'];
            }
            
            $stmt = $db->prepare("UPDATE events SET 
                name = ?, 
                description = ?, 
                address = ?, 
                phone = ?, 
                email = ?, 
                website = ?, 
                category = ?, 
                price_range = ?, 
                rating = ?, 
                latitude = ?, 
                longitude = ?, 
                image = ?, 
                booking_url = ?, 
                event_date = ?, 
                event_time = ?, 
                end_date = ?, 
                duration = ?, 
                capacity = ?, 
                organizer = ?, 
                features = ?, 
                specialties = ?, 
                tags = ?, 
                is_active = ?, 
                updated_at = CURRENT_TIMESTAMP
                WHERE id = ?");
            
            $stmt->execute([
                $name,
                $description,
                $address,
                $phone,
                $email,
                $website,
                $category,
                $price_range,
                $rating,
                $latitude,
                $longitude,
                $image,
                $booking_url,
                $event_date,
                $event_time,
                $end_date,
                $duration,
                $capacity,
                $organizer,
                json_encode($features),
                json_encode($specialties),
                json_encode($tags),
                $is_active,
                $id
            ]);
            
            $message = '<div class="success-message">Événement modifié avec succès!</div>';
            
            // Recharger les données
            $stmt = $db->prepare("SELECT * FROM events WHERE id = ?");
            $stmt->execute([$id]);
            $event = $stmt->fetch();
            
        } catch (Exception $e) {
            $message = '<div class="error-message">Erreur lors de la modification: ' . $e->getMessage() . '</div>';
        }
    }
}

// Décoder les données JSON
$features = json_decode($event['features'] ?? '[]', true) ?: [];
$specialties = json_decode($event['specialties'] ?? '[]', true) ?: [];
$tags = json_decode($event['tags'] ?? '[]', true) ?: [];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier l'événement - Le petit agenais</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
        
        .container {
            max-width: 800px;
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
        
        .form-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 1rem;
            padding: 2rem;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        }
        
        .form-grid {
            display: grid;
            gap: 1.5rem;
        }
        
        .form-group {
            display: flex;
            flex-direction: column;
        }
        
        .form-group label {
            font-weight: 600;
            color: #374151;
            margin-bottom: 0.5rem;
        }
        
        .form-group input,
        .form-group textarea,
        .form-group select {
            padding: 0.75rem;
            border: 1px solid #d1d5db;
            border-radius: 0.5rem;
            font-size: 1rem;
            transition: border-color 0.2s;
        }
        
        .form-group input:focus,
        .form-group textarea:focus,
        .form-group select:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }
        
        .form-group textarea {
            resize: vertical;
            min-height: 100px;
        }
        
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }
        
        .checkbox-group {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .checkbox-group input[type="checkbox"] {
            width: auto;
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
        
        .form-actions {
            display: flex;
            gap: 1rem;
            justify-content: flex-end;
            margin-top: 2rem;
        }
        
        @media (max-width: 768px) {
            .form-row {
                grid-template-columns: 1fr;
            }
            
            .form-actions {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1 class="header-title">✏️ Modifier l'événement</h1>
            <a href="manage_events.php" class="btn btn-outline">← Retour à la liste</a>
        </div>
        
        <?php echo $message; ?>
        
        <div class="form-card">
            <form method="POST" class="form-grid">
                <div class="form-group">
                    <label for="name">Nom de l'événement *</label>
                    <input type="text" id="name" name="name" 
                           value="<?php echo htmlspecialchars($event['name'] ?? ''); ?>" 
                           required>
                </div>
                
                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea id="description" name="description" 
                              placeholder="Décrivez cet événement..."><?php echo htmlspecialchars($event['description'] ?? ''); ?></textarea>
                </div>
                
                <div class="form-group">
                    <label for="address">Adresse *</label>
                    <input type="text" id="address" name="address" 
                           value="<?php echo htmlspecialchars($event['address'] ?? ''); ?>" 
                           required>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="event_date">Date de l'événement *</label>
                        <input type="date" id="event_date" name="event_date" 
                               value="<?php echo htmlspecialchars($event['event_date'] ?? ''); ?>" 
                               required>
                    </div>
                    
                    <div class="form-group">
                        <label for="event_time">Heure</label>
                        <input type="time" id="event_time" name="event_time" 
                               value="<?php echo htmlspecialchars($event['event_time'] ?? ''); ?>">
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="end_date">Date de fin</label>
                        <input type="date" id="end_date" name="end_date" 
                               value="<?php echo htmlspecialchars($event['end_date'] ?? ''); ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="duration">Durée</label>
                        <input type="text" id="duration" name="duration" 
                               value="<?php echo htmlspecialchars($event['duration'] ?? ''); ?>"
                               placeholder="Ex: 2 heures, 1 soirée, 3 jours...">
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="phone">Téléphone</label>
                        <input type="tel" id="phone" name="phone" 
                               value="<?php echo htmlspecialchars($event['phone'] ?? ''); ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" 
                               value="<?php echo htmlspecialchars($event['email'] ?? ''); ?>">
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="website">Site web</label>
                        <input type="url" id="website" name="website" 
                               value="<?php echo htmlspecialchars($event['website'] ?? ''); ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="booking_url">URL de réservation</label>
                        <input type="url" id="booking_url" name="booking_url" 
                               value="<?php echo htmlspecialchars($event['booking_url'] ?? ''); ?>">
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="category">Catégorie</label>
                        <select id="category" name="category">
                            <option value="">Sélectionnez une catégorie</option>
                            <option value="concert" <?php echo ($event['category'] ?? '') === 'concert' ? 'selected' : ''; ?>>Concert</option>
                            <option value="spectacle" <?php echo ($event['category'] ?? '') === 'spectacle' ? 'selected' : ''; ?>>Spectacle</option>
                            <option value="festival" <?php echo ($event['category'] ?? '') === 'festival' ? 'selected' : ''; ?>>Festival</option>
                            <option value="exposition" <?php echo ($event['category'] ?? '') === 'exposition' ? 'selected' : ''; ?>>Exposition</option>
                            <option value="conference" <?php echo ($event['category'] ?? '') === 'conference' ? 'selected' : ''; ?>>Conférence</option>
                            <option value="atelier" <?php echo ($event['category'] ?? '') === 'atelier' ? 'selected' : ''; ?>>Atelier</option>
                            <option value="marché" <?php echo ($event['category'] ?? '') === 'marché' ? 'selected' : ''; ?>>Marché</option>
                            <option value="sport" <?php echo ($event['category'] ?? '') === 'sport' ? 'selected' : ''; ?>>Sport</option>
                            <option value="fête" <?php echo ($event['category'] ?? '') === 'fête' ? 'selected' : ''; ?>>Fête</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="price_range">Prix</label>
                        <select id="price_range" name="price_range">
                            <option value="0" <?php echo ($event['price_range'] ?? 2) == 0 ? 'selected' : ''; ?>>Gratuit</option>
                            <option value="1" <?php echo ($event['price_range'] ?? 2) == 1 ? 'selected' : ''; ?>>€ (Économique)</option>
                            <option value="2" <?php echo ($event['price_range'] ?? 2) == 2 ? 'selected' : ''; ?>>€€ (Modéré)</option>
                            <option value="3" <?php echo ($event['price_range'] ?? 2) == 3 ? 'selected' : ''; ?>>€€€ (Cher)</option>
                            <option value="4" <?php echo ($event['price_range'] ?? 2) == 4 ? 'selected' : ''; ?>>€€€€ (Très cher)</option>
                        </select>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="capacity">Capacité</label>
                        <input type="number" id="capacity" name="capacity" 
                               min="1" 
                               value="<?php echo htmlspecialchars($event['capacity'] ?? ''); ?>"
                               placeholder="Nombre maximum de participants">
                    </div>
                    
                    <div class="form-group">
                        <label for="organizer">Organisateur</label>
                        <input type="text" id="organizer" name="organizer" 
                               value="<?php echo htmlspecialchars($event['organizer'] ?? ''); ?>">
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="rating">Note (sur 5)</label>
                        <input type="number" id="rating" name="rating" 
                               min="0" max="5" step="0.1" 
                               value="<?php echo htmlspecialchars($event['rating'] ?? ''); ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="image">URL de l'image</label>
                        <input type="url" id="image" name="image" 
                               value="<?php echo htmlspecialchars($event['image'] ?? ''); ?>">
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="latitude">Latitude</label>
                        <input type="number" id="latitude" name="latitude" 
                               step="any" 
                               value="<?php echo htmlspecialchars($event['latitude'] ?? ''); ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="longitude">Longitude</label>
                        <input type="number" id="longitude" name="longitude" 
                               step="any" 
                               value="<?php echo htmlspecialchars($event['longitude'] ?? ''); ?>">
                    </div>
                </div>
                
                <div class="checkbox-group">
                    <input type="checkbox" id="is_active" name="is_active" 
                           <?php echo ($event['is_active'] ?? 1) ? 'checked' : ''; ?>>
                    <label for="is_active">Événement actif</label>
                </div>
                
                <div class="form-actions">
                    <a href="manage_events.php" class="btn btn-outline">Annuler</a>
                    <button type="submit" class="btn btn-primary">💾 Enregistrer les modifications</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>