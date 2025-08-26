<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
    header('Location: login.php');
    exit;
}

require_once __DIR__ . '/../config/database.php';
$database = new Database();
$db = $database->getConnection();

$success_message = '';
$error_message = '';

// Créer la table company_info si elle n'existe pas
$createTableSQL = "
CREATE TABLE IF NOT EXISTS company_info (
    id INT(11) NOT NULL AUTO_INCREMENT,
    company_name VARCHAR(255) NOT NULL DEFAULT 'Le petit agenais',
    phone VARCHAR(50) DEFAULT NULL,
    email VARCHAR(255) DEFAULT NULL,
    address TEXT DEFAULT NULL,
    website VARCHAR(255) DEFAULT NULL,
    description TEXT DEFAULT NULL,
    facebook_url VARCHAR(255) DEFAULT NULL,
    instagram_url VARCHAR(255) DEFAULT NULL,
    twitter_url VARCHAR(255) DEFAULT NULL,
    opening_hours TEXT DEFAULT NULL,
    contact_person VARCHAR(255) DEFAULT NULL,
    siret VARCHAR(50) DEFAULT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
";

try {
    $db->exec($createTableSQL);
    
    // Vérifier s'il y a déjà des données
    $checkStmt = $db->query("SELECT COUNT(*) as count FROM company_info");
    $count = $checkStmt->fetch()['count'];
    
    // Insérer des données par défaut si la table est vide
    if ($count == 0) {
        $defaultData = "
        INSERT INTO company_info (company_name, phone, email, address, website, description, opening_hours, contact_person) 
        VALUES (
            'Le petit agenais',
            '05 53 XX XX XX',
            'contact@lepetitagenais.fr',
            '123 Boulevard de la République, 47000 Agen',
            'https://lepetitagenais.fr',
            'Votre guide touristique complet pour découvrir Agen et ses environs. Restaurants, activités, événements et plus encore !',
            'Lun-Ven: 9h-18h, Sam: 9h-17h, Dim: Fermé',
            'Équipe Le petit agenais'
        )";
        $db->exec($defaultData);
    }
    
} catch (Exception $e) {
    $error_message = "Erreur lors de la création de la table : " . $e->getMessage();
}

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $company_name = $_POST['company_name'];
        $phone = $_POST['phone'];
        $email = $_POST['email'];
        $address = $_POST['address'];
        $website = $_POST['website'];
        $description = $_POST['description'];
        $facebook_url = $_POST['facebook_url'];
        $instagram_url = $_POST['instagram_url'];
        $twitter_url = $_POST['twitter_url'];
        $opening_hours = $_POST['opening_hours'];
        $contact_person = $_POST['contact_person'];
        $siret = $_POST['siret'];
        
        // Vérifier s'il faut faire un UPDATE ou un INSERT
        $existingStmt = $db->query("SELECT id FROM company_info LIMIT 1");
        $existing = $existingStmt->fetch();
        
        if ($existing) {
            // UPDATE
            $sql = "UPDATE company_info SET 
                company_name = ?, phone = ?, email = ?, address = ?, website = ?, 
                description = ?, facebook_url = ?, instagram_url = ?, twitter_url = ?, 
                opening_hours = ?, contact_person = ?, siret = ? 
                WHERE id = ?";
            $stmt = $db->prepare($sql);
            $result = $stmt->execute([
                $company_name, $phone, $email, $address, $website, $description,
                $facebook_url, $instagram_url, $twitter_url, $opening_hours, 
                $contact_person, $siret, $existing['id']
            ]);
        } else {
            // INSERT
            $sql = "INSERT INTO company_info 
                (company_name, phone, email, address, website, description, facebook_url, instagram_url, twitter_url, opening_hours, contact_person, siret) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $db->prepare($sql);
            $result = $stmt->execute([
                $company_name, $phone, $email, $address, $website, $description,
                $facebook_url, $instagram_url, $twitter_url, $opening_hours, 
                $contact_person, $siret
            ]);
        }
        
        if ($result) {
            $success_message = "Informations mises à jour avec succès !";
        } else {
            $error_message = "Erreur lors de la mise à jour.";
        }
        
    } catch (Exception $e) {
        $error_message = "Erreur : " . $e->getMessage();
    }
}

// Récupérer les informations actuelles
$company_info = null;
try {
    $stmt = $db->query("SELECT * FROM company_info LIMIT 1");
    $company_info = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $error_message = "Erreur lors de la récupération des données : " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Informations d'entreprise - Admin</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            max-width: 1000px;
            margin: 0 auto;
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            overflow: hidden;
        }

        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }

        .header h1 {
            font-size: 2.5rem;
            margin-bottom: 10px;
        }

        .header p {
            opacity: 0.9;
            font-size: 1.1rem;
        }

        .nav-links {
            padding: 20px 30px;
            background: #f8f9fa;
            border-bottom: 1px solid #e9ecef;
        }

        .nav-links a {
            color: #667eea;
            text-decoration: none;
            margin-right: 20px;
            font-weight: 500;
            transition: color 0.3s;
        }

        .nav-links a:hover {
            color: #764ba2;
        }

        .content {
            padding: 40px;
        }

        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 10px;
            font-weight: 500;
        }

        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
            margin-bottom: 30px;
        }

        .form-section {
            background: #f8f9ff;
            padding: 25px;
            border-radius: 15px;
            border: 1px solid #e6e9ff;
        }

        .form-section h3 {
            color: #333;
            margin-bottom: 20px;
            font-size: 1.3rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #555;
        }

        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 12px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 1rem;
            transition: border-color 0.3s;
        }

        .form-group input:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #667eea;
        }

        .form-group textarea {
            resize: vertical;
            min-height: 80px;
        }

        .btn {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 15px 30px;
            border: none;
            border-radius: 10px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 10px;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.4);
        }

        .btn-secondary {
            background: #6c757d;
            margin-right: 15px;
        }

        .btn-secondary:hover {
            background: #5a6268;
            box-shadow: 0 10px 20px rgba(108, 117, 125, 0.4);
        }

        .preview-section {
            background: #f8f9fa;
            padding: 25px;
            border-radius: 15px;
            margin-top: 30px;
            border: 1px solid #e9ecef;
        }

        .preview-section h3 {
            color: #333;
            margin-bottom: 20px;
            font-size: 1.3rem;
        }

        .preview-card {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        .preview-card h4 {
            color: #667eea;
            margin-bottom: 15px;
        }

        .preview-item {
            display: flex;
            margin-bottom: 10px;
            align-items: center;
            gap: 10px;
        }

        .preview-item strong {
            min-width: 120px;
            color: #555;
        }

        .social-links {
            display: flex;
            gap: 15px;
            margin-top: 15px;
        }

        .social-link {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            color: #667eea;
            text-decoration: none;
            font-size: 0.9rem;
        }

        .social-link:hover {
            color: #764ba2;
        }

        @media (max-width: 768px) {
            .container {
                margin: 10px;
                border-radius: 15px;
            }
            
            .header {
                padding: 20px;
            }
            
            .header h1 {
                font-size: 2rem;
            }
            
            .content {
                padding: 20px;
            }
            
            .form-grid {
                grid-template-columns: 1fr;
                gap: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🏢 Informations d'entreprise</h1>
            <p>Gérez les informations de votre entreprise et vos coordonnées</p>
        </div>

        <div class="nav-links">
            <a href="dashboard.php">← Retour au tableau de bord</a>
            <a href="manage_restaurants.php">Restaurants</a>
            <a href="manage_activities.php">Activités</a>
            <a href="manage_events.php">Événements</a>
        </div>

        <div class="content">
            <?php if ($success_message): ?>
                <div class="alert alert-success">✅ <?php echo htmlspecialchars($success_message); ?></div>
            <?php endif; ?>

            <?php if ($error_message): ?>
                <div class="alert alert-error">❌ <?php echo htmlspecialchars($error_message); ?></div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="form-grid">
                    <!-- Informations générales -->
                    <div class="form-section">
                        <h3>📋 Informations générales</h3>
                        
                        <div class="form-group">
                            <label for="company_name">Nom de l'entreprise</label>
                            <input type="text" id="company_name" name="company_name" 
                                   value="<?php echo htmlspecialchars($company_info['company_name'] ?? ''); ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="contact_person">Personne de contact</label>
                            <input type="text" id="contact_person" name="contact_person" 
                                   value="<?php echo htmlspecialchars($company_info['contact_person'] ?? ''); ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="siret">SIRET</label>
                            <input type="text" id="siret" name="siret" 
                                   value="<?php echo htmlspecialchars($company_info['siret'] ?? ''); ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="description">Description</label>
                            <textarea id="description" name="description" rows="4"><?php echo htmlspecialchars($company_info['description'] ?? ''); ?></textarea>
                        </div>
                    </div>

                    <!-- Coordonnées -->
                    <div class="form-section">
                        <h3>📞 Coordonnées</h3>
                        
                        <div class="form-group">
                            <label for="phone">Téléphone</label>
                            <input type="tel" id="phone" name="phone" 
                                   value="<?php echo htmlspecialchars($company_info['phone'] ?? ''); ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" id="email" name="email" 
                                   value="<?php echo htmlspecialchars($company_info['email'] ?? ''); ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="address">Adresse</label>
                            <textarea id="address" name="address" rows="3"><?php echo htmlspecialchars($company_info['address'] ?? ''); ?></textarea>
                        </div>
                        
                        <div class="form-group">
                            <label for="website">Site web</label>
                            <input type="url" id="website" name="website" 
                                   value="<?php echo htmlspecialchars($company_info['website'] ?? ''); ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="opening_hours">Horaires d'ouverture</label>
                            <textarea id="opening_hours" name="opening_hours" rows="3"><?php echo htmlspecialchars($company_info['opening_hours'] ?? ''); ?></textarea>
                        </div>
                    </div>

                    <!-- Réseaux sociaux -->
                    <div class="form-section">
                        <h3>🌐 Réseaux sociaux</h3>
                        
                        <div class="form-group">
                            <label for="facebook_url">Facebook</label>
                            <input type="url" id="facebook_url" name="facebook_url" 
                                   value="<?php echo htmlspecialchars($company_info['facebook_url'] ?? ''); ?>" 
                                   placeholder="https://facebook.com/votre-page">
                        </div>
                        
                        <div class="form-group">
                            <label for="instagram_url">Instagram</label>
                            <input type="url" id="instagram_url" name="instagram_url" 
                                   value="<?php echo htmlspecialchars($company_info['instagram_url'] ?? ''); ?>" 
                                   placeholder="https://instagram.com/votre-compte">
                        </div>
                        
                        <div class="form-group">
                            <label for="twitter_url">Twitter</label>
                            <input type="url" id="twitter_url" name="twitter_url" 
                                   value="<?php echo htmlspecialchars($company_info['twitter_url'] ?? ''); ?>" 
                                   placeholder="https://twitter.com/votre-compte">
                        </div>
                    </div>
                </div>

                <div style="text-align: center; margin-top: 30px;">
                    <button type="button" class="btn btn-secondary" onclick="window.location.href='dashboard.php'">
                        ← Annuler
                    </button>
                    <button type="submit" class="btn">
                        💾 Enregistrer les modifications
                    </button>
                </div>
            </form>

            <!-- Aperçu -->
            <?php if ($company_info): ?>
            <div class="preview-section">
                <h3>👀 Aperçu des informations</h3>
                <div class="preview-card">
                    <h4><?php echo htmlspecialchars($company_info['company_name']); ?></h4>
                    
                    <?php if ($company_info['description']): ?>
                        <p style="margin-bottom: 15px; color: #666;"><?php echo htmlspecialchars($company_info['description']); ?></p>
                    <?php endif; ?>
                    
                    <div class="preview-item">
                        <strong>📞 Téléphone:</strong>
                        <span><?php echo htmlspecialchars($company_info['phone'] ?? 'Non renseigné'); ?></span>
                    </div>
                    
                    <div class="preview-item">
                        <strong>📧 Email:</strong>
                        <span><?php echo htmlspecialchars($company_info['email'] ?? 'Non renseigné'); ?></span>
                    </div>
                    
                    <div class="preview-item">
                        <strong>📍 Adresse:</strong>
                        <span><?php echo htmlspecialchars($company_info['address'] ?? 'Non renseignée'); ?></span>
                    </div>
                    
                    <div class="preview-item">
                        <strong>🕒 Horaires:</strong>
                        <span><?php echo htmlspecialchars($company_info['opening_hours'] ?? 'Non renseignés'); ?></span>
                    </div>
                    
                    <div class="social-links">
                        <?php if ($company_info['facebook_url']): ?>
                            <a href="<?php echo htmlspecialchars($company_info['facebook_url']); ?>" target="_blank" class="social-link">
                                📘 Facebook
                            </a>
                        <?php endif; ?>
                        
                        <?php if ($company_info['instagram_url']): ?>
                            <a href="<?php echo htmlspecialchars($company_info['instagram_url']); ?>" target="_blank" class="social-link">
                                📸 Instagram
                            </a>
                        <?php endif; ?>
                        
                        <?php if ($company_info['twitter_url']): ?>
                            <a href="<?php echo htmlspecialchars($company_info['twitter_url']); ?>" target="_blank" class="social-link">
                                🐦 Twitter
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>