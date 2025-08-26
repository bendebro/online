<?php
/**
 * API pour récupérer les informations d'entreprise
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

require_once __DIR__ . '/../config/database.php';

try {
    $database = new Database();
    $db = $database->getConnection();
    
    if (!$db) {
        throw new Exception("Erreur de connexion à la base de données");
    }
    
    // Récupérer les informations d'entreprise
    $stmt = $db->query("SELECT * FROM company_info LIMIT 1");
    $company_info = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$company_info) {
        // Données par défaut si aucune information n'est trouvée
        $company_info = [
            'company_name' => 'Le petit agenais',
            'phone' => '05 53 XX XX XX',
            'email' => 'contact@lepetitagenais.fr',
            'address' => '123 Boulevard de la République, 47000 Agen',
            'website' => 'https://lepetitagenais.fr',
            'description' => 'Votre guide touristique complet pour découvrir Agen et ses environs.',
            'opening_hours' => 'Lun-Ven: 9h-18h, Sam: 9h-17h',
            'contact_person' => 'Équipe Le petit agenais',
            'facebook_url' => '',
            'instagram_url' => '',
            'twitter_url' => '',
            'siret' => ''
        ];
    }
    
    echo json_encode([
        'success' => true,
        'data' => $company_info
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>