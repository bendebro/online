<?php
/**
 * API des recommandations personnalisées IA
 * Le petit agenais - Intelligence artificielle et machine learning
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
            // Paramètres de personnalisation
            $user_id = isset($_GET['user_id']) ? $_GET['user_id'] : 'anonymous';
            $lat = isset($_GET['lat']) ? floatval($_GET['lat']) : null;
            $lng = isset($_GET['lng']) ? floatval($_GET['lng']) : null;
            $preferences = isset($_GET['preferences']) ? json_decode($_GET['preferences'], true) : null;
            $limit = isset($_GET['limit']) ? intval($_GET['limit']) : 10;
            $type = isset($_GET['type']) ? $_GET['type'] : null;
            $exclude_id = isset($_GET['exclude_id']) ? intval($_GET['exclude_id']) : null;
            
            // Requête de base pour récupérer tous les éléments
            $all_items = [];
            $tables = ['restaurants', 'activities', 'events'];
            
            foreach ($tables as $table) {
                $table_type = rtrim($table, 's'); // restaurant, activity, event
                
                // Ignorer si on filtre par type et que ce n'est pas le bon type
                if ($type && $type !== $table_type) {
                    continue;
                }
                
                $query = "SELECT *, '$table_type' as type FROM $table";
                $params = [];
                
                // Exclure un élément spécifique si demandé
                if ($exclude_id && $type === $table_type) {
                    $query .= " WHERE id != ?";
                    $params[] = $exclude_id;
                }
                
                $stmt = $db->prepare($query);
                $stmt->execute($params);
                $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                $all_items = array_merge($all_items, $items);
            }
            
            // Algorithme de scoring personnalisé
            $scored_items = [];
            
            foreach ($all_items as $item) {
                $score = 0;
                $confidence = 0;
                $match_reasons = [];
                
                // 1. Facteur catégorie (25%)
                if ($preferences && isset($preferences['categories'])) {
                    $category_preference = $preferences['categories'][$item['type']] ?? 3;
                    $category_score = $category_preference / 5.0; // Normaliser sur 0-1
                    $score += $category_score * 0.25;
                    $confidence += 0.25;
                    if ($category_score > 0.6) {
                        $match_reasons[] = "Catégorie préférée ({$item['type']})";
                    }
                }
                
                // 2. Facteur géolocalisation (20%)
                if ($lat !== null && $lng !== null && isset($item['lat']) && isset($item['lng'])) {
                    $distance = calculateDistance($lat, $lng, floatval($item['lat']), floatval($item['lng']));
                    $geo_score = max(0, 1 - ($distance / 10)); // Score diminue avec la distance (max 10km)
                    $score += $geo_score * 0.20;
                    $confidence += 0.20;
                    if ($distance < 2) {
                        $match_reasons[] = "Très proche (" . round($distance, 1) . "km)";
                    } elseif ($distance < 5) {
                        $match_reasons[] = "À proximité (" . round($distance, 1) . "km)";
                    }
                }
                
                // 3. Facteur temporel (15%)
                $hour = intval(date('H'));
                $day_of_week = intval(date('N')); // 1 (lundi) à 7 (dimanche)
                
                $time_score = 0.5; // Score de base
                if ($item['type'] === 'restaurant') {
                    if (($hour >= 11 && $hour <= 14) || ($hour >= 19 && $hour <= 22)) {
                        $time_score = 0.9; // Heures de repas
                        $match_reasons[] = "Heure idéale pour un repas";
                    }
                } elseif ($item['type'] === 'activity') {
                    if ($hour >= 10 && $hour <= 18) {
                        $time_score = 0.8; // Heures d'activité
                    }
                    if ($day_of_week >= 6) { // Weekend
                        $time_score += 0.1;
                        $match_reasons[] = "Parfait pour le weekend";
                    }
                }
                
                $score += $time_score * 0.15;
                $confidence += 0.15;
                
                // 4. Facteur social (15%) - basé sur le rating
                if (isset($item['rating'])) {
                    $social_score = floatval($item['rating']) / 5.0;
                    $score += $social_score * 0.15;
                    $confidence += 0.15;
                    if (floatval($item['rating']) >= 4.5) {
                        $match_reasons[] = "Excellentes notes (" . $item['rating'] . "⭐)";
                    } elseif (floatval($item['rating']) >= 4.0) {
                        $match_reasons[] = "Bien noté (" . $item['rating'] . "⭐)";
                    }
                }
                
                // 5. Facteur rating personnel (10%)
                $rating_score = isset($item['rating']) ? floatval($item['rating']) / 5.0 : 0.5;
                $score += $rating_score * 0.10;
                $confidence += 0.10;
                
                // 6. Facteur prix (10%)
                $price_score = 0.5; // Score neutre par défaut
                if (isset($item['price_range'])) {
                    // Préférer les prix moyens (€€)
                    if ($item['price_range'] === '€€') {
                        $price_score = 0.8;
                        $match_reasons[] = "Prix raisonnable";
                    } elseif ($item['price_range'] === '€') {
                        $price_score = 0.7;
                        $match_reasons[] = "Très abordable";
                    } elseif ($item['price_range'] === '€€€') {
                        $price_score = 0.6;
                    }
                }
                $score += $price_score * 0.10;
                $confidence += 0.10;
                
                // 7. Facteur récence (5%)
                $recency_score = 0.5; // Score de base pour les éléments existants
                if (isset($item['created_at'])) {
                    $created_timestamp = strtotime($item['created_at']);
                    $now = time();
                    $days_old = ($now - $created_timestamp) / (24 * 3600);
                    
                    if ($days_old < 30) {
                        $recency_score = 0.9; // Nouveauté
                        $match_reasons[] = "Nouveauté";
                    } elseif ($days_old < 90) {
                        $recency_score = 0.7;
                    }
                }
                $score += $recency_score * 0.05;
                $confidence += 0.05;
                
                // Ajouter l'élément avec son score
                $scored_items[] = [
                    'id' => $item['id'],
                    'name' => $item['name'],
                    'type' => $item['type'],
                    'description' => $item['description'] ?? '',
                    'image' => $item['image'] ?? '',
                    'rating' => $item['rating'] ?? 0,
                    'price_range' => $item['price_range'] ?? '',
                    'address' => $item['address'] ?? '',
                    'lat' => $item['lat'] ?? null,
                    'lng' => $item['lng'] ?? null,
                    'personalized_score' => round($score, 3),
                    'confidence' => round($confidence, 2),
                    'match_reasons' => $match_reasons
                ];
            }
            
            // Trier par score décroissant
            usort($scored_items, function($a, $b) {
                return $b['personalized_score'] <=> $a['personalized_score'];
            });
            
            // Limiter les résultats
            $recommendations = array_slice($scored_items, 0, $limit);
            
            echo json_encode([
                'success' => true,
                'count' => count($recommendations),
                'recommendations' => $recommendations,
                'algorithm_version' => '1.0',
                'generated_at' => date('Y-m-d H:i:s'),
                'personalization_factors' => [
                    'user_id' => $user_id,
                    'location' => $lat && $lng ? [$lat, $lng] : null,
                    'preferences' => $preferences,
                    'scoring_weights' => [
                        'category' => '25%',
                        'geolocation' => '20%',
                        'temporal' => '15%',
                        'social' => '15%',
                        'rating' => '10%',
                        'price' => '10%',
                        'recency' => '5%'
                    ]
                ]
            ]);
            break;
            
        case 'POST':
            // Enregistrer le comportement utilisateur pour l'apprentissage
            $input = json_decode(file_get_contents('php://input'), true);
            
            $stmt = $db->prepare("INSERT INTO user_behavior 
                (user_id, action_type, item_type, item_id, context_data) 
                VALUES (?, ?, ?, ?, ?)");
            
            $stmt->execute([
                $input['user_id'] ?? 'anonymous',
                $input['action_type'], // view, like, share, click, etc.
                $input['item_type'],
                $input['item_id'],
                json_encode($input['context_data'] ?? [])
            ]);
            
            echo json_encode([
                'success' => true,
                'behavior_id' => $db->lastInsertId(),
                'message' => 'Comportement enregistré pour améliorer les recommandations'
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

// Fonction helper pour calculer la distance
function calculateDistance($lat1, $lng1, $lat2, $lng2) {
    $earth_radius = 6371; // Rayon de la Terre en kilomètres
    
    $dLat = deg2rad($lat2 - $lat1);
    $dLng = deg2rad($lng2 - $lng1);
    
    $a = sin($dLat/2) * sin($dLat/2) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLng/2) * sin($dLng/2);
    $c = 2 * atan2(sqrt($a), sqrt(1-$a));
    
    return $earth_radius * $c;
}
?>