<?php
/**
 * API REST pour les événements
 * Guide d'Agen
 */

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Gérer les requêtes OPTIONS (CORS preflight)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once '../config/database.php';

try {
    $database = new Database();
    $db = $database->getConnection();
    
    if (!$db) {
        throw new Exception('Connexion à la base de données impossible');
    }
    
    // Construction de la requête SQL
    $sql = "SELECT * FROM events WHERE 1=1";
    $params = [];
    
    // Recherche d'événements à venir pour les notifications
    if (isset($_GET['upcoming']) && $_GET['upcoming'] === 'true') {
        $sql .= " AND event_date IS NOT NULL AND event_date != ''";
        // Note: ici on pourrait ajouter une logique plus sophistiquée pour parser les dates
        $sql .= " ORDER BY event_date ASC";
    } 
    // Recherche géolocalisée pour les notifications
    else if (isset($_GET['nearby']) && $_GET['nearby'] === 'true' && 
        isset($_GET['lat']) && isset($_GET['lng']) && 
        is_numeric($_GET['lat']) && is_numeric($_GET['lng'])) {
        
        $userLat = floatval($_GET['lat']);
        $userLng = floatval($_GET['lng']);
        $radius = isset($_GET['radius']) && is_numeric($_GET['radius']) ? floatval($_GET['radius']) : 5; // 5km par défaut
        
        // Formule de Haversine pour calculer la distance
        $sql = "SELECT *, 
                (6371 * acos(cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) + sin(radians(?)) * sin(radians(latitude)))) AS distance 
                FROM events 
                WHERE latitude IS NOT NULL AND longitude IS NOT NULL 
                HAVING distance <= ? 
                ORDER BY distance ASC";
        
        $params = [$userLat, $userLng, $userLat, $radius];
        
    } else {
        // Filtres de recherche normaux
        if (isset($_GET['search']) && !empty(trim($_GET['search']))) {
            $search = '%' . trim($_GET['search']) . '%';
            $sql .= " AND (name LIKE ? OR description LIKE ? OR address LIKE ?)";
            $params[] = $search;
            $params[] = $search;
            $params[] = $search;
        }
        
        if (isset($_GET['category']) && !empty($_GET['category'])) {
            $sql .= " AND category = ?";
            $params[] = $_GET['category'];
        }
        
        if (isset($_GET['priceRange']) && !empty($_GET['priceRange'])) {
            $sql .= " AND price_range = ?";
            $params[] = $_GET['priceRange'];
        }
        
        if (isset($_GET['minRating']) && is_numeric($_GET['minRating'])) {
            $sql .= " AND rating >= ?";
            $params[] = floatval($_GET['minRating']);
        }
        
        // Tri
        $sql .= " ORDER BY rating DESC, name ASC";
    }
    
    // Exécution de la requête
    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    $events = $stmt->fetchAll();
    
    // Traitement des données JSON
    foreach ($events as &$event) {
        if ($event['specialties']) {
            $event['specialties'] = json_decode($event['specialties'], true) ?? [];
        } else {
            $event['specialties'] = [];
        }
        
        if ($event['tags']) {
            $event['tags'] = json_decode($event['tags'], true) ?? [];
        } else {
            $event['tags'] = [];
        }
        
        // Assurer que tous les champs requis existent
        $event['id'] = (int)$event['id'];
        $event['rating'] = (float)$event['rating'];
    }
    
    // Réponse JSON
    echo json_encode([
        'success' => true,
        'count' => count($events),
        'items' => $events
    ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Erreur serveur: ' . $e->getMessage(),
        'count' => 0,
        'items' => []
    ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
}
?>