<?php
/**
 * Diagnostic d'erreur de connexion
 * Le petit agenais - Aide au dépannage
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

try {
    // Test 1: Vérifier l'inclusion du fichier de config
    echo "Test 1: Inclusion du fichier de configuration...\n";
    if (file_exists('../config/database_mysql.php')) {
        require_once '../config/database_mysql.php';
        echo "✅ Fichier database_mysql.php trouvé\n";
    } else {
        echo "❌ Fichier database_mysql.php manquant\n";
        // Fallback vers l'ancien système
        if (file_exists('../config/database_auth.php')) {
            require_once '../config/database_auth.php';
            echo "✅ Utilisation du système de fallback (JSON)\n";
        } else {
            throw new Exception("Aucun fichier de configuration trouvé");
        }
    }
    
    // Test 2: Création de l'objet Database
    echo "\nTest 2: Création de l'objet Database...\n";
    $database = new Database();
    echo "✅ Objet Database créé\n";
    
    // Test 3: Test de connexion
    echo "\nTest 3: Test de connexion...\n";
    $db = $database->getConnection();
    
    if ($db) {
        echo "✅ Connexion réussie\n";
        
        // Test 4: Test de requête simple
        echo "\nTest 4: Test de requête...\n";
        try {
            if (method_exists($db, 'query')) {
                $stmt = $db->query("SELECT 1 as test");
                echo "✅ Requête de test réussie\n";
            } else {
                echo "ℹ️ Utilisation du système JSON (pas de requête SQL)\n";
            }
        } catch (Exception $e) {
            echo "❌ Erreur de requête: " . $e->getMessage() . "\n";
        }
    } else {
        echo "❌ Connexion échouée\n";
    }
    
    // Test 5: Test de l'API d'authentification
    echo "\nTest 5: Test session...\n";
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    echo "✅ Session démarrée\n";
    
    // Retourner un diagnostic JSON
    echo "\n" . json_encode([
        'success' => true,
        'diagnostic' => 'Tests de diagnostic complétés',
        'database_available' => ($db !== null),
        'session_active' => (session_status() === PHP_SESSION_ACTIVE),
        'recommended_action' => $db ? 'Connexion OK' : 'Utiliser le système de fallback'
    ]);
    
} catch (Exception $e) {
    echo "\n❌ ERREUR CRITIQUE: " . $e->getMessage() . "\n";
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage(),
        'recommended_action' => 'Vérifier la configuration de la base de données'
    ]);
}
?>