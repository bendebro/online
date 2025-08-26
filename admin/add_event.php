<?php
/**
 * Ajout d'événement - Admin
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
        
        $sql = "INSERT INTO events (name, description, image, address, phone, email, website, rating, price_range, category, type, event_date, duration, price, opening_hours, specialties, tags) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $specialties = !empty($_POST['specialties']) ? json_encode(explode(',', $_POST['specialties'])) : null;
        $tags = !empty($_POST['tags']) ? json_encode(explode(',', $_POST['tags'])) : null;
        
        // Mapping price_range to price text
        $price_text = 'Gratuit';
        if (!empty($_POST['price'])) {
            $price_text = $_POST['price'];
        } elseif (!empty($_POST['price_range'])) {
            $price_text = $_POST['price_range'];
        }
        
        // Combine event_date and event_time for opening_hours field
        $opening_hours = '';
        if (!empty($_POST['event_date'])) {
            $opening_hours = $_POST['event_date'];
            if (!empty($_POST['event_time'])) {
                $opening_hours .= ' à ' . $_POST['event_time'];
            }
        }
        
        $stmt = $db->prepare($sql);
        $stmt->execute([
            $_POST['name'],
            $_POST['description'],
            $_POST['image'] ?: 'https://images.unsplash.com/photo-1492684223066-81342ee5ff30?w=800',
            $_POST['address'],
            $_POST['phone'],
            $_POST['email'] ?: null,
            $_POST['website'] ?: null,
            floatval($_POST['rating'] ?: 4.0),
            $_POST['price_range'] ?: '€',
            $_POST['category'] ?: 'Événement',
            $_POST['type'] ?: 'Culturel',
            $_POST['event_date'] ?: null,
            $_POST['duration'] ?: '2h',
            $price_text,
            $opening_hours,
            $specialties,
            $tags
        ]);
        
        $message = 'Événement ajouté avec succès !';
        
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
    <title>Ajouter un Événement - Guide d'Agen</title>
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
            <h1>📅 Ajouter un Événement</h1>
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
                <label for="name">Nom de l'événement *</label>
                <input type="text" id="name" name="name" required>
            </div>
            
            <div class="form-group">
                <label for="description">Description</label>
                <textarea id="description" name="description" placeholder="Décrivez l'événement..."></textarea>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="category">Catégorie</label>
                    <select id="category" name="category">
                        <option value="Événement" selected>Événement</option>
                        <option value="Festival">Festival</option>
                        <option value="Concert">Concert</option>
                        <option value="Exposition">Exposition</option>
                        <option value="Marché">Marché</option>
                        <option value="Spectacle">Spectacle</option>
                        <option value="Conférence">Conférence</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="type">Type</label>
                    <select id="type" name="type">
                        <option value="Culturel" selected>Culturel</option>
                        <option value="Musical">Musical</option>
                        <option value="Sportif">Sportif</option>
                        <option value="Gastronomique">Gastronomique</option>
                        <option value="Familial">Familial</option>
                        <option value="Artistique">Artistique</option>
                        <option value="Traditionnel">Traditionnel</option>
                    </select>
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="event_date">Date de l'événement</label>
                    <input type="text" id="event_date" name="event_date" placeholder="12-15 Juillet 2024, Chaque vendredi...">
                </div>
                
                <div class="form-group">
                    <label for="event_time">Heure</label>
                    <input type="text" id="event_time" name="event_time" placeholder="20h30, 14h-18h...">
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="duration">Durée</label>
                    <input type="text" id="duration" name="duration" value="2h" placeholder="2h, 1 journée, 3 jours...">
                </div>
                
                <div class="form-group">
                    <label for="price">Prix</label>
                    <input type="text" id="price" name="price" value="Gratuit" placeholder="Gratuit, 10€, 5€-15€...">
                </div>
            </div>
            
            <div class="form-group">
                <label for="rating">Note (sur 5)</label>
                <input type="number" id="rating" name="rating" min="0" max="5" step="0.1" value="4.0">
            </div>
            
            <div class="form-group">
                <label for="address">Adresse</label>
                <input type="text" id="address" name="address" placeholder="Place Armand Fallières, 47000 Agen">
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="phone">Téléphone</label>
                    <input type="tel" id="phone" name="phone" placeholder="05 53 XX XX XX">
                </div>
                
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" placeholder="contact@evenement.fr">
                </div>
            </div>
            
            <div class="form-group">
                <label for="website">Site web</label>
                <input type="url" id="website" name="website" placeholder="https://www.evenement.fr">
            </div>
            
            <div class="form-group">
                <label for="image">URL de l'image</label>
                <input type="url" id="image" name="image" placeholder="https://images.unsplash.com/photo-...">
                <div class="form-help">Laissez vide pour utiliser une image par défaut</div>
            </div>
            
            <div class="form-group">
                <label for="specialties">Points forts</label>
                <input type="text" id="specialties" name="specialties" placeholder="Animation live, Artistes locaux, Dégustation">
                <div class="form-help">Séparez par des virgules</div>
            </div>
            
            <div class="form-group">
                <label for="tags">Tags</label>
                <input type="text" id="tags" name="tags" placeholder="Festival, Musique, Famille">
                <div class="form-help">Séparez par des virgules</div>
            </div>
            
            <button type="submit" class="btn btn-primary">✅ Ajouter l'événement</button>
        </form>
    </div>
</body>
</html>