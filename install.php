<?php
/**
 * Script d'installation du Panel d'Administration
 * Guide d'Agen
 */

echo "<h1>🚀 Installation du Panel d'Administration - Le petit agenais</h1>";

// Vérifications préalables
echo "<h2>Vérifications système</h2>";

// 1. Vérifier PHP
if (version_compare(PHP_VERSION, '7.4.0') >= 0) {
    echo "✅ PHP " . PHP_VERSION . " - OK<br>";
} else {
    echo "❌ PHP version >= 7.4 requise<br>";
    exit;
}

// 2. Vérifier PDO MySQL
if (extension_loaded('pdo_mysql')) {
    echo "✅ Extension PDO MySQL - OK<br>";
} else {
    echo "❌ Extension PDO MySQL requise<br>";
    exit;
}

// 3. Vérifier les fichiers nécessaires
$required_files = [
    'config/database.php',
    'config/config.php', 
    'sql/setup.sql',
    'admin/login.php',
    'admin/dashboard.php'
];

$missing_files = [];
foreach ($required_files as $file) {
    if (!file_exists($file)) {
        $missing_files[] = $file;
    }
}

if (empty($missing_files)) {
    echo "✅ Fichiers nécessaires - OK<br>";
} else {
    echo "❌ Fichiers manquants :<br>";
    foreach ($missing_files as $file) {
        echo "- " . $file . "<br>";
    }
    exit;
}

// 4. Test de connexion à la base de données
echo "<h2>Test de connexion à la base de données</h2>";

try {
    require_once 'config/database.php';
    $database = new Database();
    $db = $database->getConnection();
    
    if ($db) {
        echo "✅ Connexion à la base de données - OK<br>";
        
        // Vérifier si les tables existent
        $tables = ['restaurants', 'activities', 'events', 'admin_users'];
        $existing_tables = [];
        
        foreach ($tables as $table) {
            $query = "SHOW TABLES LIKE '$table'";
            $stmt = $db->prepare($query);
            $stmt->execute();
            if ($stmt->rowCount() > 0) {
                $existing_tables[] = $table;
            }
        }
        
        if (count($existing_tables) === count($tables)) {
            echo "✅ Toutes les tables existent<br>";
        } else {
            echo "⚠️ Tables manquantes, installation de la base de données...<br>";
            
            // Exécuter le script SQL
            $sql = file_get_contents('sql/setup.sql');
            $db->exec($sql);
            
            echo "✅ Base de données installée<br>";
        }
        
    } else {
        echo "❌ Impossible de se connecter à la base de données<br>";
        exit;
    }
    
} catch (Exception $e) {
    echo "❌ Erreur de base de données : " . $e->getMessage() . "<br>";
    echo "<p><strong>Vérifiez votre configuration dans config/database.php</strong></p>";
    exit;
}

// 5. Vérifier les permissions
echo "<h2>Vérifications des permissions</h2>";

$writable_dirs = ['admin', 'assets'];
foreach ($writable_dirs as $dir) {
    if (is_writable($dir)) {
        echo "✅ Dossier $dir - Écriture OK<br>";
    } else {
        echo "⚠️ Dossier $dir - Permissions limitées<br>";
    }
}

// 6. Tester l'admin par défaut
echo "<h2>Compte administrateur</h2>";

try {
    $query = "SELECT username FROM admin_users WHERE username = 'admin'";
    $stmt = $db->prepare($query);
    $stmt->execute();
    
    if ($stmt->rowCount() > 0) {
        echo "✅ Compte admin existe<br>";
        echo "<p><strong>Identifiants par défaut :</strong></p>";
        echo "<ul>";
        echo "<li>Username: <code>admin</code></li>";
        echo "<li>Password: <code>password</code></li>";
        echo "</ul>";
    } else {
        echo "❌ Compte admin introuvable<br>";
    }
    
} catch (Exception $e) {
    echo "⚠️ Impossible de vérifier le compte admin<br>";
}

echo "<h2>🎉 Installation terminée !</h2>";
echo "<p>Vous pouvez maintenant accéder au panel d'administration :</p>";
echo "<p><a href='admin/login.php' style='background: #2563eb; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>🔐 Accéder au Panel Admin</a></p>";

echo "<h3>Étapes suivantes :</h3>";
echo "<ol>";
echo "<li>Changez le mot de passe par défaut</li>";
echo "<li>Ajoutez vos propres restaurants, activités et événements</li>";
echo "<li>Personnalisez les couleurs et le design si nécessaire</li>";
echo "<li>Supprimez ce fichier install.php pour la sécurité</li>";
echo "</ol>";

echo "<hr>";
echo "<p><small>Le petit agenais - Panel d'Administration v1.0</small></p>";
?>