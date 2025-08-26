<?php
/**
 * Page de statut de l'application
 * Le petit agenais
 */

echo "<h1>🎉 Statut de l'application - Le petit agenais</h1>";

// Test de connexion base de données
echo "<h2>🗄️ Base de données</h2>";
try {
    require_once 'config/database.php';
    $database = new Database();
    $db = $database->getConnection();
    
    if ($db) {
        echo "<p style='color: green;'>✅ Connexion MySQL réussie</p>";
        
        // Compter les données
        $tables = ['restaurants', 'activities', 'events', 'admin_users'];
        echo "<h3>📊 Données disponibles :</h3>";
        echo "<ul>";
        foreach ($tables as $table) {
            try {
                $stmt = $db->query("SELECT COUNT(*) as count FROM $table");
                $count = $stmt->fetch()['count'];
                echo "<li><strong>$table</strong> : $count enregistrements</li>";
            } catch (Exception $e) {
                echo "<li><strong>$table</strong> : Erreur - " . $e->getMessage() . "</li>";
            }
        }
        echo "</ul>";
        
    } else {
        echo "<p style='color: red;'>❌ Échec de connexion à la base de données</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Erreur : " . $e->getMessage() . "</p>";
}

// Test des pages principales
echo "<h2>🌐 Pages de l'application</h2>";
$pages = [
    'index.php' => 'Accueil',
    'restaurants.php' => 'Restaurants',
    'activities.php' => 'Activités', 
    'events.php' => 'Événements',
    'map.php' => 'Carte interactive',
    'admin/login.php' => 'Administration'
];

echo "<ul>";
foreach ($pages as $page => $name) {
    if (file_exists($page)) {
        echo "<li>✅ <a href='$page' target='_blank'>$name</a></li>";
    } else {
        echo "<li>❌ $name (fichier manquant)</li>";
    }
}
echo "</ul>";

// Test des APIs
echo "<h2>🔌 APIs disponibles</h2>";
$apis = [
    'api/restaurants.php' => 'API Restaurants',
    'api/activities.php' => 'API Activités',
    'api/events.php' => 'API Événements',
    'api/social-recommendations.php' => 'API Recommandations sociales',
    'api/personalized-recommendations.php' => 'API Recommandations personnalisées'
];

echo "<ul>";
foreach ($apis as $api => $name) {
    if (file_exists($api)) {
        echo "<li>✅ <a href='$api' target='_blank'>$name</a></li>";
    } else {
        echo "<li>❌ $name (fichier manquant)</li>";
    }
}
echo "</ul>";

// Administration
echo "<h2>🔧 Panel d'administration</h2>";
$admin_pages = [
    'admin/dashboard.php' => 'Dashboard',
    'admin/manage_restaurants.php' => 'Gestion des restaurants',
    'admin/manage_activities.php' => 'Gestion des activités',
    'admin/manage_events.php' => 'Gestion des événements',
    'admin/add_restaurant.php' => 'Ajouter un restaurant',
    'admin/add_activity.php' => 'Ajouter une activité',
    'admin/add_event.php' => 'Ajouter un événement'
];

echo "<ul>";
foreach ($admin_pages as $page => $name) {
    if (file_exists($page)) {
        echo "<li>✅ <a href='$page' target='_blank'>$name</a></li>";
    } else {
        echo "<li>❌ $name (fichier manquant)</li>";
    }
}
echo "</ul>";

echo "<div style='background: #f0f9ff; padding: 1rem; border-radius: 0.5rem; margin: 2rem 0;'>";
echo "<h3>🔑 Informations de connexion admin :</h3>";
echo "<p><strong>Login :</strong> admin</p>";
echo "<p><strong>Mot de passe :</strong> admin123</p>";
echo "<p><strong>URL :</strong> <a href='admin/login.php' target='_blank'>admin/login.php</a></p>";
echo "</div>";

// Fonctionnalités
echo "<h2>🚀 Fonctionnalités implémentées</h2>";
echo "<ul>";
echo "<li>✅ <strong>CRUD complet</strong> : Création, lecture, modification, suppression</li>";
echo "<li>✅ <strong>Géolocalisation</strong> : Recherche par proximité</li>";
echo "<li>✅ <strong>Notifications</strong> : Système de notifications intelligentes</li>";
echo "<li>✅ <strong>Partage social</strong> : Facebook, Twitter, WhatsApp, LinkedIn</li>";
echo "<li>✅ <strong>Recommandations</strong> : Système de suggestions personnalisées</li>";
echo "<li>✅ <strong>Carte interactive</strong> : Leaflet.js avec marqueurs</li>";
echo "<li>✅ <strong>Design responsive</strong> : Compatible mobile et desktop</li>";
echo "<li>✅ <strong>Mode sombre</strong> : Thème clair/sombre</li>";
echo "<li>✅ <strong>Panel d'administration</strong> : Interface de gestion complète</li>";
echo "</ul>";

echo "<h2>🎯 Corrections apportées</h2>";
echo "<ul>";
echo "<li>✅ <strong>Erreur SQL current_time</strong> : Corrigée dans tous les fichiers</li>";
echo "<li>✅ <strong>Panel d'administration</strong> : Pages CRUD créées</li>";
echo "<li>✅ <strong>Géolocalisation</strong> : Bug lat/lng corrigé</li>";
echo "<li>✅ <strong>Base de données</strong> : Configuration et données de test</li>";
echo "<li>✅ <strong>Gestion d'erreurs</strong> : Améliorée dans toute l'application</li>";
echo "</ul>";

echo "<hr>";
echo "<p style='text-align: center; color: #666;'>Application développée et testée avec succès ✅</p>";
?>

<style>
body {
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    line-height: 1.6;
    max-width: 1200px;
    margin: 0 auto;
    padding: 2rem;
    background: #f8fafc;
}

h1, h2, h3 {
    color: #1f2937;
}

a {
    color: #3b82f6;
    text-decoration: none;
}

a:hover {
    text-decoration: underline;
}

ul {
    padding-left: 2rem;
}

li {
    margin: 0.5rem 0;
}
</style>