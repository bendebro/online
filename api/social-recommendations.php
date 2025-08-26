<?php
/**
 * API des recommandations sociales
 * Le petit agenais - Système de recommandations entre utilisateurs
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
            // Paramètres de requête
            $type = isset($_GET['type']) ? $_GET['type'] : null;
            $user_id = isset($_GET['user_id']) ? $_GET['user_id'] : null;
            $nearby = isset($_GET['nearby']) && $_GET['nearby'] === 'true';
            $lat = isset($_GET['lat']) ? floatval($_GET['lat']) : null;
            $lng = isset($_GET['lng']) ? floatval($_GET['lng']) : null;
            $radius = isset($_GET['radius']) ? floatval($_GET['radius']) : 5.0; // 5km par défaut
            $limit = isset($_GET['limit']) ? intval($_GET['limit']) : 20;
            
            // Construction de la requête de base
            $query = "SELECT 
                sr.*,
                CASE 
                    WHEN sr.item_lat IS NOT NULL AND sr.item_lng IS NOT NULL THEN
                        ROUND(
                            6371 * acos(
                                cos(radians(?)) * cos(radians(sr.item_lat)) * 
                                cos(radians(sr.item_lng) - radians(?)) + 
                                sin(radians(?)) * sin(radians(sr.item_lat))
                            ), 2
                        )
                    ELSE NULL
                END as distance
                FROM social_recommendations sr 
                WHERE 1=1";
            
            $params = [];
            
            // Ajouter les paramètres de géolocalisation si fournis
            if ($nearby && $lat !== null && $lng !== null) {
                $params[] = $lat;
                $params[] = $lng;
                $params[] = $lat;
                
                $query .= " HAVING distance IS NOT NULL AND distance <= ?";
                $params[] = $radius;
            } else {
                // Ajouter des paramètres fictifs pour la formule de distance
                $params[] = 0;
                $params[] = 0;  
                $params[] = 0;
            }
            
            // Filtrer par type si spécifié
            if ($type) {
                if ($nearby && $lat !== null && $lng !== null) {
                    $query = str_replace("WHERE 1=1", "WHERE sr.item_type = ?", $query);
                    array_unshift($params, $type);
                    // Réorganiser les paramètres
                    $new_params = [$type];
                    for ($i = 0; $i < 3; $i++) {
                        $new_params[] = $params[$i];
                    }
                    if (count($params) > 3) {
                        $new_params[] = $params[3];
                    }
                    $params = $new_params;
                } else {
                    $query = str_replace("WHERE 1=1", "WHERE sr.item_type = ?", $query);
                    array_splice($params, 3, 0, $type);
                }
            }
            
            // Exclure l'utilisateur courant
            if ($user_id && $user_id !== 'anonymous') {
                if (strpos($query, 'WHERE sr.item_type') !== false) {
                    $query .= " AND sr.user_id != ?";
                } else {
                    $query = str_replace("WHERE 1=1", "WHERE sr.user_id != ?", $query);
                }
                $params[] = $user_id;
            }
            
            // Ordre et limite
            if ($nearby && $lat !== null && $lng !== null) {
                $query .= " ORDER BY distance ASC, sr.created_at DESC";
            } else {
                $query .= " ORDER BY sr.created_at DESC";
            }
            
            $query .= " LIMIT " . intval($limit);
            
            $stmt = $db->prepare($query);
            $stmt->execute($params);
            $recommendations = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            echo json_encode([
                'success' => true,
                'count' => count($recommendations),
                'recommendations' => $recommendations,
                'debug' => [
                    'query_params' => $params,
                    'filters' => [
                        'type' => $type,
                        'user_id' => $user_id,
                        'nearby' => $nearby,
                        'coordinates' => $lat && $lng ? [$lat, $lng] : null,
                        'radius' => $radius
                    ]
                ]
            ]);
            break;
            
        case 'POST':
            $input = json_decode(file_get_contents('php://input'), true);
            
            $stmt = $db->prepare("INSERT INTO social_recommendations 
                (user_id, user_name, user_avatar, item_type, item_id, item_name, item_lat, item_lng, recommendation_text, rating, platform) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            
            $stmt->execute([
                $input['user_id'],
                $input['user_name'],
                $input['user_avatar'] ?? null,
                $input['item_type'],
                $input['item_id'],
                $input['item_name'],
                $input['item_lat'] ?? null,
                $input['item_lng'] ?? null,
                $input['recommendation_text'],
                $input['rating'],
                $input['platform'] ?? 'web'
            ]);
            
            echo json_encode([
                'success' => true, 
                'recommendation_id' => $db->lastInsertId(),
                'message' => 'Recommandation ajoutée avec succès'
            ]);
            break;
            
        default:
            http_response_code(405);
            echo json_encode(['error' => 'Méthode non autorisée']);
    }
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'error' => $e->getMessage(),
        'trace' => $e->getTraceAsString()
    ]);
}
?>