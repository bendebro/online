<?php
/**
 * API des avis sociaux
 * Le petit agenais - Système d'avis et commentaires utilisateurs
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
            $item_type = isset($_GET['item_type']) ? $_GET['item_type'] : null;
            $item_id = isset($_GET['item_id']) ? $_GET['item_id'] : null;
            $user_id = isset($_GET['user_id']) ? $_GET['user_id'] : null;
            $limit = isset($_GET['limit']) ? intval($_GET['limit']) : 20;
            
            if ($item_type && $item_id) {
                // Récupérer les avis pour un élément spécifique
                $stmt = $db->prepare("SELECT * FROM social_reviews 
                    WHERE item_type = ? AND item_id = ? 
                    ORDER BY created_at DESC 
                    LIMIT ?");
                $stmt->execute([$item_type, $item_id, $limit]);
                $reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                // Calculer les statistiques
                $stats_stmt = $db->prepare("SELECT 
                    COUNT(*) as total_reviews,
                    AVG(rating) as average_rating,
                    SUM(CASE WHEN rating = 5 THEN 1 ELSE 0 END) as five_stars,
                    SUM(CASE WHEN rating = 4 THEN 1 ELSE 0 END) as four_stars,
                    SUM(CASE WHEN rating = 3 THEN 1 ELSE 0 END) as three_stars,
                    SUM(CASE WHEN rating = 2 THEN 1 ELSE 0 END) as two_stars,
                    SUM(CASE WHEN rating = 1 THEN 1 ELSE 0 END) as one_star
                    FROM social_reviews 
                    WHERE item_type = ? AND item_id = ?");
                $stats_stmt->execute([$item_type, $item_id]);
                $stats = $stats_stmt->fetch(PDO::FETCH_ASSOC);
                
                echo json_encode([
                    'success' => true,
                    'count' => count($reviews),
                    'reviews' => $reviews,
                    'statistics' => [
                        'total_reviews' => intval($stats['total_reviews']),
                        'average_rating' => round(floatval($stats['average_rating']), 1),
                        'rating_distribution' => [
                            '5' => intval($stats['five_stars']),
                            '4' => intval($stats['four_stars']),
                            '3' => intval($stats['three_stars']),
                            '2' => intval($stats['two_stars']),
                            '1' => intval($stats['one_star'])
                        ]
                    ]
                ]);
            } else {
                // Récupérer tous les avis récents
                $query = "SELECT * FROM social_reviews ORDER BY created_at DESC LIMIT ?";
                $params = [$limit];
                
                if ($user_id) {
                    $query = "SELECT * FROM social_reviews WHERE user_id = ? ORDER BY created_at DESC LIMIT ?";
                    $params = [$user_id, $limit];
                }
                
                $stmt = $db->prepare($query);
                $stmt->execute($params);
                $reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                echo json_encode([
                    'success' => true,
                    'count' => count($reviews),
                    'reviews' => $reviews
                ]);
            }
            break;
            
        case 'POST':
            $input = json_decode(file_get_contents('php://input'), true);
            
            // Valider les champs requis
            $required_fields = ['user_id', 'user_name', 'item_type', 'item_id', 'rating', 'comment'];
            foreach ($required_fields as $field) {
                if (!isset($input[$field]) || empty($input[$field])) {
                    throw new Exception("Le champ '$field' est requis");
                }
            }
            
            $stmt = $db->prepare("INSERT INTO social_reviews 
                (user_id, user_name, user_avatar, item_type, item_id, rating, comment, platform) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            
            $stmt->execute([
                $input['user_id'],
                $input['user_name'],
                $input['user_avatar'] ?? null,
                $input['item_type'],
                $input['item_id'],
                $input['rating'],
                $input['comment'],
                $input['platform'] ?? 'web'
            ]);
            
            echo json_encode([
                'success' => true, 
                'review_id' => $db->lastInsertId(),
                'message' => 'Avis ajouté avec succès'
            ]);
            break;
            
        case 'PUT':
            $input = json_decode(file_get_contents('php://input'), true);
            $review_id = $input['id'] ?? null;
            
            if (!$review_id) {
                throw new Exception("ID de l'avis requis pour la modification");
            }
            
            $stmt = $db->prepare("UPDATE social_reviews 
                SET rating = ?, comment = ?, updated_at = CURRENT_TIMESTAMP 
                WHERE id = ?");
            
            $stmt->execute([
                $input['rating'],
                $input['comment'],
                $review_id
            ]);
            
            echo json_encode([
                'success' => true,
                'message' => 'Avis mis à jour avec succès'
            ]);
            break;
            
        case 'DELETE':
            $input = json_decode(file_get_contents('php://input'), true);
            $review_id = $input['id'] ?? null;
            
            if (!$review_id) {
                throw new Exception("ID de l'avis requis pour la suppression");
            }
            
            $stmt = $db->prepare("DELETE FROM social_reviews WHERE id = ?");
            $stmt->execute([$review_id]);
            
            echo json_encode([
                'success' => true,
                'message' => 'Avis supprimé avec succès'
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