<?php
/**
 * API Activities pour Le petit agenais
 */

require_once '../config/database.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

try {
    $database = new Database();
    $db = $database->getConnection();
    
    if (!$db) {
        throw new Exception("Connexion à la base de données échouée");
    }
    
    $method = $_SERVER['REQUEST_METHOD'];
    
    switch ($method) {
        case 'GET':
            // Construction de la requête avec filtres
            $whereConditions = [];
            $params = [];
            
            // Filtre recherche
            if (isset($_GET['search']) && !empty($_GET['search'])) {
                $whereConditions[] = "(name LIKE ? OR description LIKE ?)";
                $searchParam = '%' . $_GET['search'] . '%';
                $params[] = $searchParam;
                $params[] = $searchParam;
            }
            
            // Filtre catégorie
            if (isset($_GET['category']) && !empty($_GET['category'])) {
                $whereConditions[] = "category = ?";
                $params[] = $_GET['category'];
            }
            
            // Filtre prix
            if (isset($_GET['priceRange']) && !empty($_GET['priceRange'])) {
                if ($_GET['priceRange'] === 'Gratuit') {
                    $whereConditions[] = "price_range = 0";
                } elseif ($_GET['priceRange'] === '€') {
                    $whereConditions[] = "price_range = 1";
                } elseif ($_GET['priceRange'] === '€€') {
                    $whereConditions[] = "price_range = 2";
                } elseif ($_GET['priceRange'] === '€€€') {
                    $whereConditions[] = "price_range >= 3";
                }
            }
            
            // Filtre note minimale
            if (isset($_GET['minRating']) && !empty($_GET['minRating'])) {
                $whereConditions[] = "rating >= ?";
                $params[] = floatval($_GET['minRating']);
            }
            
            // Construction de la requête
            $sql = "SELECT * FROM activities";
            if (!empty($whereConditions)) {
                $sql .= " WHERE " . implode(" AND ", $whereConditions);
            }
            $sql .= " ORDER BY created_at DESC";
            
            $stmt = $db->prepare($sql);
            $stmt->execute($params);
            $activities = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Traitement des données JSON et ajout de champs manquants
            foreach ($activities as &$activity) {
                // Décoder les champs JSON
                if (isset($activity['specialties']) && is_string($activity['specialties'])) {
                    $activity['specialties'] = json_decode($activity['specialties'], true) ?: [];
                }
                if (isset($activity['tags']) && is_string($activity['tags'])) {
                    $activity['tags'] = json_decode($activity['tags'], true) ?: ['Activité', 'Agen'];
                }
                
                // Ajouter des champs manquants
                if (empty($activity['tags'])) {
                    $activity['tags'] = ['Activité', 'Agen'];
                }
                
                // Normaliser les champs
                $activity['type_original'] = $activity['category'] ?? 'Activité';
                $activity['image'] = $activity['image'] ?? 'https://images.unsplash.com/photo-1544551763-46a013bb70d5?w=800';
                $activity['rating'] = $activity['rating'] ?? 4.0;
                $activity['phone'] = $activity['phone'] ?? 'Non renseigné';
                $activity['address'] = $activity['address'] ?? 'Agen';
            }
            
            echo json_encode([
                'success' => true,
                'count' => count($activities),
                'items' => $activities  // Changé 'data' en 'items' pour correspondre au JS
            ]);
            break;
            
        case 'POST':
            $input = json_decode(file_get_contents('php://input'), true);
            
            $stmt = $db->prepare("INSERT INTO activities (name, description, type, duration, difficulty, price_range, address, lat, lng, rating, specialties, tags) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            
            $specialties = json_encode($input['specialties'] ?? []);
            $tags = json_encode($input['tags'] ?? []);
            
            $stmt->execute([
                $input['name'],
                $input['description'],
                $input['type'],
                $input['duration'],
                $input['difficulty'],
                $input['price_range'],
                $input['address'],
                $input['lat'],
                $input['lng'],
                $input['rating'],
                $specialties,
                $tags
            ]);
            
            echo json_encode(['success' => true, 'id' => $db->lastInsertId()]);
            break;
            
        default:
            http_response_code(405);
            echo json_encode(['error' => 'Méthode non autorisée']);
    }
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?>