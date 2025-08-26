<?php
/**
 * Modification d'une activité
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
    header('Location: manage_activities.php');
    exit;
}

// Récupération des données de l'activité
$activity = null;
try {
    $stmt = $db->prepare("SELECT * FROM activities WHERE id = ?");
    $stmt->execute([$id]);
    $activity = $stmt->fetch();
    
    if (!$activity) {
        header('Location: manage_activities.php');
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
    $opening_hours = $_POST['opening_hours'] ?? '';
    $duration = $_POST['duration'] ?? '';
    $difficulty = $_POST['difficulty'] ?? '';
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    
    // Validation
    if (empty($name) || empty($address)) {
        $message = '<div class="error-message">Le nom et l\'adresse sont obligatoires.</div>';
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
            
            $stmt = $db->prepare("UPDATE activities SET 
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
                opening_hours = ?, 
                duration = ?, 
                difficulty = ?, 
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
                $opening_hours,
                $duration,
                $difficulty,
                json_encode($features),
                json_encode($specialties),
                json_encode($tags),
                $is_active,
                $id
            ]);
            
            $message = '<div class="success-message">Activité modifiée avec succès!</div>';
            
            // Recharger les données
            $stmt = $db->prepare("SELECT * FROM activities WHERE id = ?");
            $stmt->execute([$id]);
            $activity = $stmt->fetch();
            
        } catch (Exception $e) {
            $message = '<div class="error-message">Erreur lors de la modification: ' . $e->getMessage() . '</div>';
        }
    }
}

// Décoder les données JSON
$features = json_decode($activity['features'] ?? '[]', true) ?: [];
$specialties = json_decode($activity['specialties'] ?? '[]', true) ?: [];
$tags = json_decode($activity['tags'] ?? '[]', true) ?: [];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier l'activité - Le petit agenais</title>
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
            <h1 class="header-title">✏️ Modifier l'activité</h1>
            <a href="manage_activities.php" class="btn btn-outline">← Retour à la liste</a>
        </div>
        
        <?php echo $message; ?>
        
        <div class="form-card">
            <form method="POST" class="form-grid">
                <div class="form-group">
                    <label for="name">Nom de l'activité *</label>
                    <input type="text" id="name" name="name" 
                           value="<?php echo htmlspecialchars($activity['name'] ?? ''); ?>" 
                           required>
                </div>
                
                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea id="description" name="description" 
                              placeholder="Décrivez cette activité..."><?php echo htmlspecialchars($activity['description'] ?? ''); ?></textarea>
                </div>
                
                <div class="form-group">
                    <label for="address">Adresse *</label>
                    <input type="text" id="address" name="address" 
                           value="<?php echo htmlspecialchars($activity['address'] ?? ''); ?>" 
                           required>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="phone">Téléphone</label>
                        <input type="tel" id="phone" name="phone" 
                               value="<?php echo htmlspecialchars($activity['phone'] ?? ''); ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" 
                               value="<?php echo htmlspecialchars($activity['email'] ?? ''); ?>">
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="website">Site web</label>
                        <input type="url" id="website" name="website" 
                               value="<?php echo htmlspecialchars($activity['website'] ?? ''); ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="booking_url">URL de réservation</label>
                        <input type="url" id="booking_url" name="booking_url" 
                               value="<?php echo htmlspecialchars($activity['booking_url'] ?? ''); ?>">
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="category">Catégorie</label>
                        <select id="category" name="category">
                            <option value="">Sélectionnez une catégorie</option>
                            <option value="sport" <?php echo ($activity['category'] ?? '') === 'sport' ? 'selected' : ''; ?>>Sport</option>
                            <option value="culture" <?php echo ($activity['category'] ?? '') === 'culture' ? 'selected' : ''; ?>>Culture</option>
                            <option value="loisir" <?php echo ($activity['category'] ?? '') === 'loisir' ? 'selected' : ''; ?>>Loisir</option>
                            <option value="nature" <?php echo ($activity['category'] ?? '') === 'nature' ? 'selected' : ''; ?>>Nature</option>
                            <option value="musée" <?php echo ($activity['category'] ?? '') === 'musée' ? 'selected' : ''; ?>>Musée</option>
                            <option value="visite" <?php echo ($activity['category'] ?? '') === 'visite' ? 'selected' : ''; ?>>Visite guidée</option>
                            <option value="atelier" <?php echo ($activity['category'] ?? '') === 'atelier' ? 'selected' : ''; ?>>Atelier</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="price_range">Gamme de prix</label>
                        <select id="price_range" name="price_range">
                            <option value="0" <?php echo ($activity['price_range'] ?? 2) == 0 ? 'selected' : ''; ?>>Gratuit</option>
                            <option value="1" <?php echo ($activity['price_range'] ?? 2) == 1 ? 'selected' : ''; ?>>€ (Économique)</option>
                            <option value="2" <?php echo ($activity['price_range'] ?? 2) == 2 ? 'selected' : ''; ?>>€€ (Modéré)</option>
                            <option value="3" <?php echo ($activity['price_range'] ?? 2) == 3 ? 'selected' : ''; ?>>€€€ (Cher)</option>
                            <option value="4" <?php echo ($activity['price_range'] ?? 2) == 4 ? 'selected' : ''; ?>>€€€€ (Très cher)</option>
                        </select>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="rating">Note (sur 5)</label>
                        <input type="number" id="rating" name="rating" 
                               min="0" max="5" step="0.1" 
                               value="<?php echo htmlspecialchars($activity['rating'] ?? ''); ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="difficulty">Difficulté</label>
                        <select id="difficulty" name="difficulty">
                            <option value="">Sélectionnez une difficulté</option>
                            <option value="facile" <?php echo ($activity['difficulty'] ?? '') === 'facile' ? 'selected' : ''; ?>>Facile</option>
                            <option value="moyen" <?php echo ($activity['difficulty'] ?? '') === 'moyen' ? 'selected' : ''; ?>>Moyen</option>
                            <option value="difficile" <?php echo ($activity['difficulty'] ?? '') === 'difficile' ? 'selected' : ''; ?>>Difficile</option>
                        </select>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="duration">Durée</label>
                        <input type="text" id="duration" name="duration" 
                               value="<?php echo htmlspecialchars($activity['duration'] ?? ''); ?>"
                               placeholder="Ex: 2 heures, 1 journée, 3 jours...">
                    </div>
                    
                    <div class="form-group">
                        <label for="image">URL de l'image</label>
                        <input type="url" id="image" name="image" 
                               value="<?php echo htmlspecialchars($activity['image'] ?? ''); ?>">
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="latitude">Latitude</label>
                        <input type="number" id="latitude" name="latitude" 
                               step="any" 
                               value="<?php echo htmlspecialchars($activity['latitude'] ?? ''); ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="longitude">Longitude</label>
                        <input type="number" id="longitude" name="longitude" 
                               step="any" 
                               value="<?php echo htmlspecialchars($activity['longitude'] ?? ''); ?>">
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="opening_hours">Horaires d'ouverture</label>
                    <textarea id="opening_hours" name="opening_hours" 
                              placeholder="Lundi-Vendredi: 9h-17h, Samedi: 10h-16h..."><?php echo htmlspecialchars($activity['opening_hours'] ?? ''); ?></textarea>
                </div>
                
                <div class="checkbox-group">
                    <input type="checkbox" id="is_active" name="is_active" 
                           <?php echo ($activity['is_active'] ?? 1) ? 'checked' : ''; ?>>
                    <label for="is_active">Activité active</label>
                </div>
                
                <div class="form-actions">
                    <a href="manage_activities.php" class="btn btn-outline">Annuler</a>
                    <button type="submit" class="btn btn-primary">💾 Enregistrer les modifications</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>