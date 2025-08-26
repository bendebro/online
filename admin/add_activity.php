<?php
/**
 * Ajout d'activité - Admin
 * Guide d'Agen
 */

session_start();

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

require_once __DIR__ . '/../config/database.php';

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $database = new Database();
        $db = $database->getConnection();
        
        $sql = "INSERT INTO activities (name, description, image, address, phone, email, website, rating, price_range, category, type, duration, difficulty, opening_hours, booking_url, latitude, longitude, specialties, features, tags, is_active) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $specialties = !empty($_POST['specialties']) ? json_encode(explode(',', $_POST['specialties'])) : null;
        $features = !empty($_POST['features']) ? json_encode(explode(',', $_POST['features'])) : null;
        $tags = !empty($_POST['tags']) ? json_encode(explode(',', $_POST['tags'])) : null;
        
        $stmt = $db->prepare($sql);
        $stmt->execute([
            $_POST['name'],
            $_POST['description'],
            $_POST['image'] ?: 'https://images.unsplash.com/photo-1544551763-46a013bb70d5?w=800',
            $_POST['address'],
            $_POST['phone'],
            $_POST['email'] ?: null,
            $_POST['website'] ?: null,
            floatval($_POST['rating'] ?: 4.0),
            intval($_POST['price_range'] ?: 2),
            $_POST['category'] ?: 'Loisir',
            $_POST['type'] ?: 'Culturel',
            $_POST['duration'] ?: '2h',
            $_POST['difficulty'] ?: 'Facile',
            $_POST['opening_hours'] ?: 'Lun-Dim: 9h-18h',
            $_POST['booking_url'] ?: null,
            floatval($_POST['latitude'] ?: null),
            floatval($_POST['longitude'] ?: null),
            $specialties,
            $features,
            $tags,
            isset($_POST['is_active']) ? 1 : 0
        ]);
            $_POST['ticket_price'] ?: null
        ]);
        
        $message = 'Activité ajoutée avec succès !';
        
    } catch (Exception $e) {
        $error = 'Erreur lors de l\'ajout : ' . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter une Activité - Guide d'Agen</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 2rem;
        }
        
        .container {
            max-width: 800px;
            margin: 0 auto;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 1rem;
            padding: 2rem;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        }
        
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid #e5e7eb;
        }
        
        .header h1 {
            color: #1f2937;
            font-size: 1.8rem;
        }
        
        .btn {
            padding: 0.5rem 1rem;
            border: none;
            border-radius: 0.5rem;
            text-decoration: none;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s;
        }
        
        .btn-outline {
            background: transparent;
            border: 1px solid #d1d5db;
            color: #374151;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #2563eb, #3b82f6);
            color: white;
            font-size: 1rem;
            padding: 0.75rem 2rem;
        }
        
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: #374151;
        }
        
        .form-group input,
        .form-group textarea,
        .form-group select {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #d1d5db;
            border-radius: 0.5rem;
            font-size: 1rem;
        }
        
        .form-group textarea {
            min-height: 100px;
            resize: vertical;
        }
        
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }
        
        .message {
            padding: 1rem;
            border-radius: 0.5rem;
            margin-bottom: 1rem;
        }
        
        .message.success {
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
            color: #166534;
        }
        
        .message.error {
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #b91c1c;
        }
        
        .form-help {
            font-size: 0.875rem;
            color: #6b7280;
            margin-top: 0.25rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🎪 Ajouter une Activité</h1>
            <a href="dashboard.php" class="btn btn-outline">← Retour au dashboard</a>
        </div>
        
        <?php if ($message): ?>
            <div class="message success"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="message error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="form-group">
                <label for="name">Nom de l'activité *</label>
                <input type="text" id="name" name="name" required>
            </div>
            
            <div class="form-group">
                <label for="description">Description</label>
                <textarea id="description" name="description" placeholder="Décrivez l'activité..."></textarea>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="category">Catégorie</label>
                    <select id="category" name="category">
                        <option value="Loisir" selected>Loisir</option>
                        <option value="Culture">Culture</option>
                        <option value="Nature">Nature</option>
                        <option value="Sport">Sport</option>
                        <option value="Famille">Famille</option>
                        <option value="Détente">Détente</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="type">Type</label>
                    <select id="type" name="type">
                        <option value="Culturel" selected>Culturel</option>
                        <option value="Musée">Musée</option>
                        <option value="Monument">Monument</option>
                        <option value="Nautique">Nautique</option>
                        <option value="Randonnée">Randonnée</option>
                        <option value="Visite">Visite</option>
                        <option value="Spectacle">Spectacle</option>
                    </select>
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="duration">Durée</label>
                    <input type="text" id="duration" name="duration" value="2h" placeholder="2h, 1h30, 1 journée...">
                </div>
                
                <div class="form-group">
                    <label for="difficulty">Difficulté</label>
                    <select id="difficulty" name="difficulty">
                        <option value="Facile" selected>Facile</option>
                        <option value="Modérée">Modérée</option>
                        <option value="Difficile">Difficile</option>
                        <option value="Tous niveaux">Tous niveaux</option>
                    </select>
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="price_range">Gamme de prix</label>
                    <select id="price_range" name="price_range">
                        <option value="Gratuit">Gratuit</option>
                        <option value="€">€ (Économique)</option>
                        <option value="€€" selected>€€ (Modéré)</option>
                        <option value="€€€">€€€ (Élevé)</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="rating">Note (sur 5)</label>
                    <input type="number" id="rating" name="rating" min="0" max="5" step="0.1" value="4.0">
                </div>
            </div>
            
            <div class="form-group">
                <label for="address">Adresse</label>
                <input type="text" id="address" name="address" placeholder="123 Rue Example, 47000 Agen">
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="phone">Téléphone</label>
                    <input type="tel" id="phone" name="phone" placeholder="05 53 XX XX XX">
                </div>
                
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" placeholder="contact@activite.fr">
                </div>
            </div>
            
            <div class="form-group">
                <label for="website">Site web</label>
                <input type="url" id="website" name="website" placeholder="https://www.activite.fr">
            </div>
            
            <div class="form-group">
                <label for="opening_hours">Horaires d'ouverture</label>
                <input type="text" id="opening_hours" name="opening_hours" value="Lun-Dim: 9h-18h">
            </div>
            
            <div class="form-group">
                <label for="image">URL de l'image</label>
                <input type="url" id="image" name="image" placeholder="https://images.unsplash.com/photo-...">
                <div class="form-help">Laissez vide pour utiliser une image par défaut</div>
            </div>
            
            <div class="form-group">
                <label for="specialties">Points forts</label>
                <input type="text" id="specialties" name="specialties" placeholder="Guide expert, Visite commentée, Audio-guide">
                <div class="form-help">Séparez par des virgules</div>
            </div>
            
            <div class="form-group">
                <label for="tags">Tags</label>
                <input type="text" id="tags" name="tags" placeholder="Histoire, Art, Patrimoine">
                <div class="form-help">Séparez par des virgules</div>
            </div>
            
            <h3 style="margin: 2rem 0 1rem 0; color: #1f2937;">🎫 Informations de réservation</h3>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="ticket_price">Prix du billet</label>
                    <input type="text" id="ticket_price" name="ticket_price" placeholder="8€ adulte, gratuit -18 ans">
                </div>
                
                <div class="form-group">
                    <label for="booking_platform">Plateforme de réservation</label>
                    <input type="text" id="booking_platform" name="booking_platform" placeholder="Billetterie officielle, Office de tourisme...">
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="booking_phone">Téléphone réservation</label>
                    <input type="tel" id="booking_phone" name="booking_phone" placeholder="05 53 XX XX XX">
                </div>
                
                <div class="form-group">
                    <label for="booking_url">Lien de réservation</label>
                    <input type="url" id="booking_url" name="booking_url" placeholder="https://www.billetterie.com/...">
                </div>
            </div>
            
            <button type="submit" class="btn btn-primary">✅ Ajouter l'activité</button>
        </form>
    </div>
</body>
</html>