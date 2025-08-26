<?php
/**
 * API Analytics pour Le petit agenais
 * Collecte des données d'utilisation
 */

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Gérer les requêtes OPTIONS (CORS preflight)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Seules les requêtes POST sont acceptées
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Méthode non autorisée']);
    exit();
}

require_once '../config/database.php';

try {
    $database = new Database();
    $db = $database->getConnection();
    
    if (!$db) {
        throw new Exception('Connexion à la base de données impossible');
    }
    
    // Récupération des données
    $action = $_POST['action'] ?? '';
    $category = $_POST['category'] ?? '';
    $label = $_POST['label'] ?? '';
    $value = intval($_POST['value'] ?? 0);
    $page = $_POST['page'] ?? '';
    $timestamp = intval($_POST['timestamp'] ?? time() * 1000);
    
    // Informations de session
    $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';
    $ip_address = $_SERVER['REMOTE_ADDR'] ?? '';
    $referrer = $_SERVER['HTTP_REFERER'] ?? '';
    
    // Détection de l'appareil
    $is_mobile = preg_match('/Mobile|Android|iPhone|iPad/', $user_agent) ? 1 : 0;
    
    // Session ID basée sur IP + User Agent (anonymisé)
    $session_id = md5($ip_address . $user_agent . date('Y-m-d'));
    
    // Insertion en base de données
    $stmt = $db->prepare("
        INSERT INTO analytics_events 
        (session_id, action, category, label, value, page, user_agent, ip_address, referrer, is_mobile, created_at) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, FROM_UNIXTIME(?))
    ");
    
    $success = $stmt->execute([
        $session_id,
        $action,
        $category,
        $label,
        $value,
        $page,
        substr($user_agent, 0, 255), // Limiter la taille
        $ip_address,
        $referrer,
        $is_mobile,
        $timestamp / 1000 // Convertir ms en secondes
    ]);
    
    if ($success) {
        echo json_encode([
            'success' => true,
            'message' => 'Événement enregistré'
        ]);
    } else {
        throw new Exception('Erreur lors de l\'enregistrement');
    }
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Erreur serveur: ' . $e->getMessage()
    ]);
}
?>