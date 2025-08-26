<?php
/**
 * API REST pour les restaurants
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
    $sql = "SELECT * FROM restaurants WHERE 1=1";
    $params = [];
    
    // Recherche géolocalisée pour les notifications
    if (isset($_GET['nearby']) && $_GET['nearby'] === 'true' && 
        isset($_GET['lat']) && isset($_GET['lng']) && 
        is_numeric($_GET['lat']) && is_numeric($_GET['lng'])) {
        
        $userLat = floatval($_GET['lat']);
        $userLng = floatval($_GET['lng']);
        $radius = isset($_GET['radius']) && is_numeric($_GET['radius']) ? floatval($_GET['radius']) : 5; // 5km par défaut
        
        // Formule de Haversine pour calculer la distance
        $sql = "SELECT *, 
                (6371 * acos(cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) + sin(radians(?)) * sin(radians(latitude)))) AS distance 
                FROM restaurants 
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
    $restaurants = $stmt->fetchAll();
    
    // Traitement des données JSON
    foreach ($restaurants as &$restaurant) {
        if ($restaurant['specialties']) {
            $restaurant['specialties'] = json_decode($restaurant['specialties'], true) ?? [];
        } else {
            $restaurant['specialties'] = [];
        }
        
        if ($restaurant['tags']) {
            $restaurant['tags'] = json_decode($restaurant['tags'], true) ?? [];
        } else {
            $restaurant['tags'] = [];
        }
        
        // Assurer que tous les champs requis existent
        $restaurant['id'] = (int)$restaurant['id'];
        $restaurant['rating'] = (float)$restaurant['rating'];
    }
    
    // Réponse JSON
    echo json_encode([
        'success' => true,
        'count' => count($restaurants),
        'items' => $restaurants
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