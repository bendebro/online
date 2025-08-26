<?php
$page_title = 'Accueil';
require_once 'config/database.php';
require_once 'config/seo.php';

// Données SEO personnalisées pour la page d'accueil
$custom_seo_data = [
    'title' => 'Guide touristique d\'Agen - Restaurants, Activités & Événements',
    'description' => 'Découvrez Agen avec notre guide complet : les meilleurs restaurants, activités incontournables et événements à ne pas manquer. Plus de ' . (isset($counts) ? intval($counts['restaurants_count']) + intval($counts['activities_count']) + intval($counts['events_count']) : '50') . ' recommandations locales.',
    'image' => SEO_SITE_URL . '/assets/images/agen-hero.jpg'
];

require_once 'includes/header.php';

// Récupération des données pour la page d'accueil
try {
    $database = new Database();
    $db = $database->getConnection();
    
    if (!$db) {
        throw new Exception("Impossible de se connecter à la base de données");
    }
    
    // Récupération des restaurants (top 3)
    $restaurants_query = "SELECT * FROM restaurants ORDER BY rating DESC LIMIT 3";
    $restaurants_stmt = $db->prepare($restaurants_query);
    $restaurants_stmt->execute();
    $top_restaurants = $restaurants_stmt->fetchAll();
    
    // Récupération des activités (top 3)
    $activities_query = "SELECT * FROM activities ORDER BY rating DESC LIMIT 3";
    $activities_stmt = $db->prepare($activities_query);
    $activities_stmt->execute();
    $top_activities = $activities_stmt->fetchAll();
    
    // Récupération des événements (top 2)
    $events_query = "SELECT * FROM events ORDER BY rating DESC LIMIT 2";
    $events_stmt = $db->prepare($events_query);
    $events_stmt->execute();
    $top_events = $events_stmt->fetchAll();
    
    // Comptage total
    $counts_query = "
        SELECT 
            (SELECT COUNT(*) FROM restaurants) as restaurants_count,
            (SELECT COUNT(*) FROM activities) as activities_count,
            (SELECT COUNT(*) FROM events) as events_count
    ";
    $counts_stmt = $db->prepare($counts_query);
    $counts_stmt->execute();
    $counts = $counts_stmt->fetch();
    
} catch (Exception $e) {
    $error_message = "Erreur lors du chargement des données : " . $e->getMessage();
    $top_restaurants = $top_activities = $top_events = [];
    $counts = ['restaurants_count' => 0, 'activities_count' => 0, 'events_count' => 0];
}

// Générer les données structurées pour la page d'accueil
$structured_data = json_encode([
    "@context" => "https://schema.org",
    "@type" => "WebSite",
    "name" => SEO_SITE_NAME,
    "url" => SEO_SITE_URL,
    "description" => "Guide touristique complet d'Agen avec " . ($counts['restaurants_count'] + $counts['activities_count'] + $counts['events_count']) . " recommandations locales",
    "potentialAction" => [
        "@type" => "SearchAction",
        "target" => SEO_SITE_URL . "/restaurants.php?search={search_term_string}",
        "query-input" => "required name=search_term_string"
    ],
    "mainEntity" => [
        [
            "@type" => "TouristDestination",
            "name" => "Agen",
            "description" => "Ville historique du Lot-et-Garonne connue pour ses pruneaux et son patrimoine",
            "touristType" => ["Gastronomie", "Culture", "Histoire"],
            "includedInDataCatalog" => [
                [
                    "@type" => "DataCatalog",
                    "name" => "Restaurants d'Agen",
                    "description" => $counts['restaurants_count'] . " restaurants référencés"
                ],
                [
                    "@type" => "DataCatalog", 
                    "name" => "Activités à Agen",
                    "description" => $counts['activities_count'] . " activités disponibles"
                ],
                [
                    "@type" => "DataCatalog",
                    "name" => "Événements à Agen", 
                    "description" => $counts['events_count'] . " événements programmés"
                ]
            ]
        ]
    ]
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
?>

<!-- SEO: Contenu riche et optimisé -->
<div class="seo-content" style="display: none;">
    <h1>Guide touristique Agen - Restaurants, Activités et Événements</h1>
    <p>Le petit agenais est votre guide de référence pour découvrir Agen, ville du Lot-et-Garonne en Nouvelle-Aquitaine. 
    Retrouvez <?php echo $counts['restaurants_count']; ?> restaurants, <?php echo $counts['activities_count']; ?> activités 
    et <?php echo $counts['events_count']; ?> événements soigneusement sélectionnés pour vous faire découvrir le meilleur d'Agen.</p>
    
    <h2>Restaurants à Agen</h2>
    <p>De la gastronomie traditionnelle aux cuisines du monde, découvrez les meilleures tables d'Agen. 
    Spécialités locales avec les fameux pruneaux d'Agen, cuisine française raffinée, bistrot convivial : 
    notre sélection couvre tous les goûts et tous les budgets.</p>
    
    <h2>Activités touristiques à Agen</h2>
    <p>Visitez Agen et ses trésors : le musée des Beaux-Arts, la cathédrale Saint-Caprais classée UNESCO, 
    le canal de Garonne, les jardins du Gravier. Entre patrimoine historique et détente au bord de l'eau.</p>
    
    <h2>Événements culturels à Agen</h2>
    <p>Ne manquez aucun événement à Agen : festivals, concerts, marchés, expositions. Votre agenda culturel 
    complet pour profiter de la vie agenaise toute l'année.</p>
</div>

<div class="container">
    <!-- Hero Section optimisé SEO -->
    <header class="page-header">
        <h1 class="page-title"><?php echo t('home_title'); ?></h1>
        <p class="page-subtitle">
            <?php echo t('home_subtitle'); ?> Plus de <?php echo $counts['restaurants_count'] + $counts['activities_count'] + $counts['events_count']; ?> recommandations locales.
        </p>
        <div style="display: flex; flex-wrap: wrap; justify-content: center; gap: 1rem; margin-top: 2rem;">
            <span style="background: rgba(59, 130, 246, 0.1); color: #2563eb; padding: 0.5rem 1rem; border-radius: 9999px; font-size: 1rem;">
                🏛️ <?php echo t('home_heritage'); ?>
            </span>
            <span style="background: rgba(59, 130, 246, 0.1); color: #2563eb; padding: 0.5rem 1rem; border-radius: 9999px; font-size: 1rem;">
                🍇 <?php echo t('home_pruneaux'); ?>
            </span>
            <span style="background: rgba(59, 130, 246, 0.1); color: #2563eb; padding: 0.5rem 1rem; border-radius: 9999px; font-size: 1rem;">
                🛶 <?php echo t('home_canal'); ?>
            </span>
        </div>
    </header>

    <!-- Section statistiques en temps réel -->
    <section style="margin-bottom: 4rem;" aria-labelledby="stats-title">
        <div style="text-align: center; margin-bottom: 3rem;">
            <h2 id="stats-title" style="font-size: 1.5rem; font-weight: 700; color: var(--text-primary); margin-bottom: 1.5rem;"><?php echo t('stats_title'); ?></h2>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 2rem;">
                <div style="text-align: center;">
                    <div style="font-size: 2.5rem; font-weight: 900; color: #059669; margin-bottom: 0.5rem;" data-count="<?php echo $counts['restaurants_count']; ?>">
                        <?php echo $counts['restaurants_count']; ?>
                    </div>
                    <div style="font-size: 1rem; color: var(--text-secondary); font-weight: 500;"><?php echo t('stats_restaurants'); ?></div>
                    <div style="font-size: 0.875rem; color: var(--text-secondary); margin-top: 0.25rem;"><?php echo t('stats_restaurants_desc'); ?></div>
                </div>
                <div style="text-align: center;">
                    <div style="font-size: 2.5rem; font-weight: 900; color: #3b82f6; margin-bottom: 0.5rem;" data-count="<?php echo $counts['activities_count']; ?>">
                        <?php echo $counts['activities_count']; ?>
                    </div>
                    <div style="font-size: 1rem; color: var(--text-secondary); font-weight: 500;"><?php echo t('stats_activities'); ?></div>
                    <div style="font-size: 0.875rem; color: var(--text-secondary); margin-top: 0.25rem;"><?php echo t('stats_activities_desc'); ?></div>
                </div>
                <div style="text-align: center;">
                    <div style="font-size: 2.5rem; font-weight: 900; color: #f59e0b; margin-bottom: 0.5rem;" data-count="<?php echo $counts['events_count']; ?>">
                        <?php echo $counts['events_count']; ?>
                    </div>
                    <div style="font-size: 1rem; color: var(--text-secondary); font-weight: 500;"><?php echo t('stats_events'); ?></div>
                    <div style="font-size: 0.875rem; color: var(--text-secondary); margin-top: 0.25rem;"><?php echo t('stats_events_desc'); ?></div>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Section avantages -->
    <section style="margin-bottom: 4rem;" aria-labelledby="why-title">
        <div style="text-align: center; margin-bottom: 3rem;">
            <h2 id="why-title" style="font-size: 2rem; font-weight: 800; color: var(--text-primary); margin-bottom: 1rem;">Pourquoi choisir Le petit agenais ?</h2>
            <p style="color: var(--text-secondary); font-size: 1.1rem; max-width: 600px; margin: 0 auto;">Votre guide de confiance pour découvrir les trésors cachés d'Agen</p>
        </div>
        
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 2rem;">
            <div style="background: var(--bg-card); border-radius: 1rem; padding: 2rem; text-align: center; box-shadow: 0 4px 6px var(--shadow-color);">
                <div style="font-size: 3rem; margin-bottom: 1rem;">🎯</div>
                <h3 style="font-size: 1.25rem; font-weight: 700; color: var(--text-primary); margin-bottom: 1rem;">Sélection locale</h3>
                <p style="color: var(--text-secondary); line-height: 1.6;">Recommandations authentiques d'agenais passionnés qui connaissent leur ville sur le bout des doigts.</p>
            </div>
            
            <div style="background: var(--bg-card); border-radius: 1rem; padding: 2rem; text-align: center; box-shadow: 0 4px 6px var(--shadow-color);">
                <div style="font-size: 3rem; margin-bottom: 1rem;">⚡</div>
                <h3 style="font-size: 1.25rem; font-weight: 700; color: var(--text-primary); margin-bottom: 1rem;">Toujours à jour</h3>
                <p style="color: var(--text-secondary); line-height: 1.6;">Informations fraîches et actualisées : horaires, événements, nouveautés. Tout est vérifié régulièrement.</p>
            </div>
            
            <div style="background: var(--bg-card); border-radius: 1rem; padding: 2rem; text-align: center; box-shadow: 0 4px 6px var(--shadow-color);">
                <div style="font-size: 3rem; margin-bottom: 1rem;">💎</div>
                <h3 style="font-size: 1.25rem; font-weight: 700; color: var(--text-primary); margin-bottom: 1rem;">Trésors cachés</h3>
                <p style="color: var(--text-secondary); line-height: 1.6;">Au-delà des circuits touristiques classiques, découvrez les pépites que seuls les locaux connaissent.</p>
            </div>
        </div>
    </section>
    


    <!-- Section Surprise-moi ! -->
    <section class="surprise-section" style="margin-bottom: 4rem;">
        <div class="surprise-card">
            <div class="surprise-content">
                <div class="surprise-icon">🎲</div>
                <h2 class="surprise-title">Restaurant surprise à Agen</h2>
                <p class="surprise-description">Indécis pour choisir votre restaurant à Agen ? Notre sélection aléatoire vous propose une découverte gastronomique surprise parmi les meilleures adresses de la ville.</p>
                <a href="surprise-restaurant.php" class="btn-surprise" title="Découvrir un restaurant au hasard à Agen">
                    <span class="surprise-emoji">🎯</span>
                    <span>Surprise-moi !</span>
                    <span class="surprise-arrow">→</span>
                </a>
            </div>
            <div class="surprise-features">
                <div class="feature-item">
                    <span class="feature-icon">🎯</span>
                    <span>Filtres personnalisés</span>
                </div>
                <div class="feature-item">
                    <span class="feature-icon">⚡</span>
                    <span>Sélection instantanée</span>
                </div>
                <div class="feature-item">
                    <span class="feature-icon">🌟</span>
                    <span>Découvertes garanties</span>
                </div>
            </div>
        </div>
    </div>

    <style>
    .surprise-section {
        padding: 2rem 0;
    }
    
    .surprise-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 20px;
        padding: 40px;
        text-align: center;
        color: white;
        box-shadow: 0 15px 35px rgba(102, 126, 234, 0.3);
        position: relative;
        overflow: hidden;
    }
    
    .surprise-card::before {
        content: '';
        position: absolute;
        top: -50%;
        left: -50%;
        width: 200%;
        height: 200%;
        background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
        animation: surpriseFloat 6s ease-in-out infinite;
    }
    
    @keyframes surpriseFloat {
        0%, 100% { transform: translate(-50%, -50%) rotate(0deg); }
        50% { transform: translate(-50%, -50%) rotate(180deg); }
    }
    
    .surprise-content {
        position: relative;
        z-index: 2;
    }
    
    .surprise-icon {
        font-size: 4rem;
        margin-bottom: 1rem;
        animation: surpriseBounce 2s ease-in-out infinite;
    }
    
    @keyframes surpriseBounce {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-10px); }
    }
    
    .surprise-title {
        font-size: 2.2rem;
        font-weight: bold;
        margin-bottom: 1rem;
        text-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    
    .surprise-description {
        font-size: 1.1rem;
        margin-bottom: 2rem;
        opacity: 0.9;
        max-width: 600px;
        margin-left: auto;
        margin-right: auto;
        line-height: 1.6;
    }
    
    .btn-surprise {
        display: inline-flex;
        align-items: center;
        gap: 12px;
        background: linear-gradient(45deg, #ff6b6b, #feca57);
        color: white;
        padding: 18px 36px;
        border-radius: 50px;
        text-decoration: none;
        font-weight: 600;
        font-size: 1.2rem;
        transition: all 0.3s ease;
        box-shadow: 0 8px 25px rgba(255, 107, 107, 0.4);
        position: relative;
        overflow: hidden;
    }
    
    .btn-surprise::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
        transition: left 0.5s ease;
    }
    
    .btn-surprise:hover::before {
        left: 100%;
    }
    
    .btn-surprise:hover {
        transform: translateY(-3px);
        box-shadow: 0 12px 30px rgba(255, 107, 107, 0.5);
        background: linear-gradient(45deg, #ff5252, #ffb300);
    }
    
    .surprise-emoji {
        font-size: 1.4rem;
        animation: surpriseRotate 2s ease-in-out infinite;
    }
    
    @keyframes surpriseRotate {
        0%, 100% { transform: rotate(0deg); }
        25% { transform: rotate(15deg); }
        75% { transform: rotate(-15deg); }
    }
    
    .surprise-arrow {
        font-size: 1.2rem;
        transition: transform 0.3s ease;
    }
    
    .btn-surprise:hover .surprise-arrow {
        transform: translateX(5px);
    }
    
    .surprise-features {
        display: flex;
        justify-content: center;
        gap: 2rem;
        margin-top: 2rem;
        flex-wrap: wrap;
    }
    
    .feature-item {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 0.95rem;
        opacity: 0.9;
    }
    
    .feature-icon {
        font-size: 1.2rem;
    }
    
    @media (max-width: 768px) {
        .surprise-card {
            padding: 30px 20px;
        }
        
        .surprise-title {
            font-size: 1.8rem;
        }
        
        .surprise-description {
            font-size: 1rem;
        }
        
        .btn-surprise {
            padding: 15px 30px;
            font-size: 1.1rem;
        }
        
        .surprise-features {
            flex-direction: column;
            gap: 1rem;
        }
    }
    </style>

    <!-- Categories Grid -->
    <div class="grid grid-3" style="margin-bottom: 4rem;">
        <!-- Restaurants -->
        <div class="card hover-scale">
            <div class="card-content" style="text-align: center;">
                <div style="background: linear-gradient(135deg, #059669, #10b981); padding: 1rem; border-radius: 0.75rem; box-shadow: 0 8px 16px rgba(5, 150, 105, 0.3); display: inline-block; margin-bottom: 1rem;">
                    <svg width="32" height="32" fill="white" viewBox="0 0 16 16">
                        <path d="M2.5 1a1 1 0 0 0-1 1v1a1 1 0 0 0 1 1H3v9a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1V4h.5a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1H2.5z"/>
                    </svg>
                </div>
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                    <h3 style="font-size: 1.25rem; font-weight: 700; margin: 0;">Restaurants</h3>
                    <span style="background: #f3f4f6; color: #374151; padding: 0.25rem 0.5rem; border-radius: 9999px; font-size: 0.875rem;">
                        <?php echo $counts['restaurants_count']; ?> lieux
                    </span>
                </div>
                <p style="color: #6b7280; margin-bottom: 1rem;">Découvrez les meilleurs restaurants d'Agen</p>
                
                <!-- Preview des restaurants -->
                <div style="margin-bottom: 1rem;">
                    <?php foreach (array_slice($top_restaurants, 0, 2) as $restaurant): ?>
                        <div style="display: flex; align-items: center; font-size: 0.875rem; color: #6b7280; margin-bottom: 0.5rem;">
                            <svg width="12" height="12" fill="#fbbf24" viewBox="0 0 16 16" style="margin-right: 0.25rem;">
                                <path d="M3.612 15.443c-.386.198-.824-.149-.746-.592l.83-4.73L.173 6.765c-.329-.314-.158-.888.283-.95l4.898-.696L7.538.792c.197-.39.73-.39.927 0l2.184 4.327 4.898.696c.441.062.612.636.282.95l-3.522 3.356.83 4.73c.078.443-.36.79-.746.592L8 13.187l-4.389 2.256z"/>
                            </svg>
                            <span style="font-weight: 500; margin-right: 0.5rem;"><?php echo $restaurant['rating']; ?></span>
                            <span style="overflow: hidden; text-overflow: ellipsis; white-space: nowrap;"><?php echo h($restaurant['name']); ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <a href="restaurants.php" class="btn btn-primary" style="width: 100%;">
                    Voir tout
                    <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16" style="margin-left: 0.5rem;">
                        <path d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                    </svg>
                </a>
            </div>
        </div>

        <!-- Activités -->
        <div class="card hover-scale">
            <div class="card-content" style="text-align: center;">
                <div style="background: linear-gradient(135deg, #7c3aed, #8b5cf6); padding: 1rem; border-radius: 0.75rem; box-shadow: 0 8px 16px rgba(124, 58, 237, 0.3); display: inline-block; margin-bottom: 1rem;">
                    <svg width="32" height="32" fill="white" viewBox="0 0 16 16">
                        <path d="M6 2a.5.5 0 0 1 .47.33L8 5.29l1.53-2.96a.5.5 0 0 1 .94.06L11 4.5l.71-.71a.5.5 0 1 1 .71.71L12 5l.71.71a.5.5 0 0 1-.71.71L11.5 6l-.5 1.12a.5.5 0 0 1-.94-.06L8.47 4.1 7 6.96a.5.5 0 0 1-.94-.06L5.5 5l-.71.71a.5.5 0 1 1-.71-.71L4.5 4.5l-.71-.71a.5.5 0 1 1 .71-.71L5 4l.53-1.67A.5.5 0 0 1 6 2z"/>
                    </svg>
                </div>
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                    <h3 style="font-size: 1.25rem; font-weight: 700; margin: 0;">Activités</h3>
                    <span style="background: #f3f4f6; color: #374151; padding: 0.25rem 0.5rem; border-radius: 9999px; font-size: 0.875rem;">
                        <?php echo $counts['activities_count']; ?> lieux
                    </span>
                </div>
                <p style="color: #6b7280; margin-bottom: 1rem;">Découvrez les meilleures activités d'Agen</p>
                
                <!-- Preview des activités -->
                <div style="margin-bottom: 1rem;">
                    <?php foreach (array_slice($top_activities, 0, 2) as $activity): ?>
                        <div style="display: flex; align-items: center; font-size: 0.875rem; color: #6b7280; margin-bottom: 0.5rem;">
                            <svg width="12" height="12" fill="#fbbf24" viewBox="0 0 16 16" style="margin-right: 0.25rem;">
                                <path d="M3.612 15.443c-.386.198-.824-.149-.746-.592l.83-4.73L.173 6.765c-.329-.314-.158-.888.283-.95l4.898-.696L7.538.792c.197-.39.73-.39.927 0l2.184 4.327 4.898.696c.441.062.612.636.282.95l-3.522 3.356.83 4.73c.078.443-.36.79-.746.592L8 13.187l-4.389 2.256z"/>
                            </svg>
                            <span style="font-weight: 500; margin-right: 0.5rem;"><?php echo $activity['rating']; ?></span>
                            <span style="overflow: hidden; text-overflow: ellipsis; white-space: nowrap;"><?php echo h($activity['name']); ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <a href="activities.php" class="btn btn-primary" style="width: 100%;">
                    Voir tout
                    <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16" style="margin-left: 0.5rem;">
                        <path d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                    </svg>
                </a>
            </div>
        </div>

        <!-- Événements -->
        <div class="card hover-scale">
            <div class="card-content" style="text-align: center;">
                <div style="background: linear-gradient(135deg, #ea580c, #dc2626); padding: 1rem; border-radius: 0.75rem; box-shadow: 0 8px 16px rgba(234, 88, 12, 0.3); display: inline-block; margin-bottom: 1rem;">
                    <svg width="32" height="32" fill="white" viewBox="0 0 16 16">
                        <path d="M4 .5a.5.5 0 0 0-1 0V1H2a2 2 0 0 0-2 2v1h16V3a2 2 0 0 0-2-2h-1V.5a.5.5 0 0 0-1 0V1H4V.5z"/>
                        <path d="M16 14V5H0v9a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2z"/>
                    </svg>
                </div>
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                    <h3 style="font-size: 1.25rem; font-weight: 700; margin: 0;">Événements</h3>
                    <span style="background: #f3f4f6; color: #374151; padding: 0.25rem 0.5rem; border-radius: 9999px; font-size: 0.875rem;">
                        <?php echo $counts['events_count']; ?> lieux
                    </span>
                </div>
                <p style="color: #6b7280; margin-bottom: 1rem;">Découvrez les meilleurs événements d'Agen</p>
                
                <!-- Preview des événements -->
                <div style="margin-bottom: 1rem;">
                    <?php foreach (array_slice($top_events, 0, 2) as $event): ?>
                        <div style="display: flex; align-items: center; font-size: 0.875rem; color: #6b7280; margin-bottom: 0.5rem;">
                            <svg width="12" height="12" fill="#fbbf24" viewBox="0 0 16 16" style="margin-right: 0.25rem;">
                                <path d="M3.612 15.443c-.386.198-.824-.149-.746-.592l.83-4.73L.173 6.765c-.329-.314-.158-.888.283-.95l4.898-.696L7.538.792c.197-.39.73-.39.927 0l2.184 4.327 4.898.696c.441.062.612.636.282.95l-3.522 3.356.83 4.73c.078.443-.36.79-.746.592L8 13.187l-4.389 2.256z"/>
                            </svg>
                            <span style="font-weight: 500; margin-right: 0.5rem;"><?php echo $event['rating']; ?></span>
                            <span style="overflow: hidden; text-overflow: ellipsis; white-space: nowrap;"><?php echo h($event['name']); ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <a href="events.php" class="btn btn-primary" style="width: 100%;">
                    Voir tout
                    <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16" style="margin-left: 0.5rem;">
                        <path d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                    </svg>
                </a>
            </div>
        </div>
    </div>

    <!-- Highlights Section -->
    <section style="margin-bottom: 4rem;" aria-labelledby="highlights-title">
        <div style="margin-bottom: 2rem;">
            <h2 id="highlights-title" style="font-size: 2rem; font-weight: 800; color: #1f2937; margin-bottom: 0.5rem;">Meilleurs restaurants d'Agen - Nos coups de cœur</h2>
            <p style="color: #6b7280;">Découvrez notre sélection des restaurants incontournables d'Agen, plébiscités par les locaux et les visiteurs</p>
        </div>

        <div class="grid grid-3">
            <?php foreach (array_slice($top_restaurants, 0, 2) as $restaurant): ?>
                <div class="card hover-scale">
                    <div class="card-image">
                        <img src="<?php echo h($restaurant['image']); ?>" alt="<?php echo h($restaurant['name']); ?>">
                        <div class="card-badge">
                            <svg width="16" height="16" fill="#fbbf24" viewBox="0 0 16 16">
                                <path d="M3.612 15.443c-.386.198-.824-.149-.746-.592l.83-4.73L.173 6.765c-.329-.314-.158-.888.283-.95l4.898-.696L7.538.792c.197-.39.73-.39.927 0l2.184 4.327 4.898.696c.441.062.612.636.282.95l-3.522 3.356.83 4.73c.078.443-.36.79-.746.592L8 13.187l-4.389 2.256z"/>
                            </svg>
                            <?php echo $restaurant['rating']; ?>
                        </div>
                        <button class="favorite-btn" data-id="<?php echo $restaurant['id']; ?>" data-type="restaurant">
                            <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                                <path d="m8 2.748-.717-.737C5.6.281 2.514.878 1.4 3.053c-.523 1.023-.641 2.5.314 4.385.92 1.815 2.834 3.989 6.286 6.357 3.452-2.368 5.365-4.542 6.286-6.357.955-1.886.838-3.362.314-4.385C13.486.878 10.4.28 8.717 2.01L8 2.748z"/>
                            </svg>
                        </button>
                        <div style="position: absolute; bottom: 0.75rem; left: 0.75rem;">
                            <span style="background: rgba(0,0,0,0.2); color: white; backdrop-filter: blur(8px); padding: 0.25rem 0.5rem; border-radius: 9999px; font-size: 0.875rem;">
                                <?php echo h($restaurant['price_range']); ?>
                            </span>
                        </div>
                    </div>
                    <div class="card-content">
                        <h4 style="font-size: 1.125rem; font-weight: 700; margin-bottom: 0.25rem;">
                            <?php echo h($restaurant['name']); ?>
                        </h4>
                        <p style="color: #6b7280; font-size: 0.875rem; margin-bottom: 0.5rem; display: flex; align-items: center;">
                            <svg width="12" height="12" fill="currentColor" viewBox="0 0 16 16" style="margin-right: 0.25rem;">
                                <path d="M8 16s6-5.686 6-10A6 6 0 0 0 2 6c0 4.314 6 10 6 10z"/>
                                <path d="M8 8a2 2 0 1 1 0-4 2 2 0 0 1 0 4z"/>
                            </svg>
                            <?php echo h($restaurant['address']); ?>
                        </p>
                        <p style="color: #6b7280; font-size: 0.875rem; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                            <?php echo h($restaurant['description']); ?>
                        </p>
                        <div style="display: flex; flex-wrap: wrap; gap: 0.25rem; margin-top: 0.75rem;">
                            <?php 
                            $tags = json_decode($restaurant['tags'], true) ?? [];
                            foreach (array_slice($tags, 0, 3) as $tag):
                            ?>
                                <span style="border: 1px solid #d1d5db; color: #374151; padding: 0.125rem 0.375rem; border-radius: 0.25rem; font-size: 0.75rem;">
                                    <?php echo h($tag); ?>
                                </span>
                            <?php endforeach; ?>
                        </div>
                        <div style="margin-top: 1rem;">
                            <a href="detail.php?type=restaurant&id=<?php echo $restaurant['id']; ?>" class="btn" style="background: linear-gradient(135deg, #2563eb, #3b82f6); color: white; border: none; padding: 0.5rem 1rem; font-weight: 500; text-decoration: none; border-radius: 0.5rem; display: inline-block; transition: all 0.2s;">
                                En savoir plus
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
            
            <?php if (!empty($top_activities)): ?>
                <div class="card hover-scale">
                    <div class="card-image">
                        <img src="<?php echo h($top_activities[0]['image']); ?>" alt="<?php echo h($top_activities[0]['name']); ?>">
                        <div class="card-badge">
                            <svg width="16" height="16" fill="#fbbf24" viewBox="0 0 16 16">
                                <path d="M3.612 15.443c-.386.198-.824-.149-.746-.592l.83-4.73L.173 6.765c-.329-.314-.158-.888.283-.95l4.898-.696L7.538.792c.197-.39.73-.39.927 0l2.184 4.327 4.898.696c.441.062.612.636.282.95l-3.522 3.356.83 4.73c.078.443-.36.79-.746.592L8 13.187l-4.389 2.256z"/>
                            </svg>
                            <?php echo $top_activities[0]['rating']; ?>
                        </div>
                        <button class="favorite-btn" data-id="<?php echo $top_activities[0]['id']; ?>" data-type="activity">
                            <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                                <path d="m8 2.748-.717-.737C5.6.281 2.514.878 1.4 3.053c-.523 1.023-.641 2.5.314 4.385.92 1.815 2.834 3.989 6.286 6.357 3.452-2.368 5.365-4.542 6.286-6.357.955-1.886.838-3.362.314-4.385C13.486.878 10.4.28 8.717 2.01L8 2.748z"/>
                            </svg>
                        </button>
                        <div style="position: absolute; bottom: 0.75rem; left: 0.75rem;">
                            <span style="background: rgba(0,0,0,0.2); color: white; backdrop-filter: blur(8px); padding: 0.25rem 0.5rem; border-radius: 9999px; font-size: 0.875rem;">
                                <?php echo h($top_activities[0]['price_range']); ?>
                            </span>
                        </div>
                    </div>
                    <div class="card-content">
                        <h4 style="font-size: 1.125rem; font-weight: 700; margin-bottom: 0.25rem;">
                            <?php echo h($top_activities[0]['name']); ?>
                        </h4>
                        <p style="color: #6b7280; font-size: 0.875rem; margin-bottom: 0.5rem; display: flex; align-items: center;">
                            <svg width="12" height="12" fill="currentColor" viewBox="0 0 16 16" style="margin-right: 0.25rem;">
                                <path d="M8 16s6-5.686 6-10A6 6 0 0 0 2 6c0 4.314 6 10 6 10z"/>
                                <path d="M8 8a2 2 0 1 1 0-4 2 2 0 0 1 0 4z"/>
                            </svg>
                            <?php echo h($top_activities[0]['address']); ?>
                        </p>
                        <p style="color: #6b7280; font-size: 0.875rem; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                            <?php echo h($top_activities[0]['description']); ?>
                        </p>
                        <div style="display: flex; flex-wrap: wrap; gap: 0.25rem; margin-top: 0.75rem;">
                            <?php 
                            $tags = json_decode($top_activities[0]['tags'], true) ?? [];
                            foreach (array_slice($tags, 0, 3) as $tag):
                            ?>
                                <span style="border: 1px solid #d1d5db; color: #374151; padding: 0.125rem 0.375rem; border-radius: 0.25rem; font-size: 0.75rem;">
                                    <?php echo h($tag); ?>
                                </span>
                            <?php endforeach; ?>
                        </div>
                        <div style="margin-top: 1rem;">
                            <a href="detail.php?type=activity&id=<?php echo $top_activities[0]['id']; ?>" class="btn" style="background: linear-gradient(135deg, #10b981, #059669); color: white; border: none; padding: 0.5rem 1rem; font-weight: 500; text-decoration: none; border-radius: 0.5rem; display: inline-block; transition: all 0.2s;">
                                En savoir plus
                            </a>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

  
    

    <?php if (isset($error_message)): ?>
        <div style="background: #fef2f2; border: 1px solid #fecaca; color: #b91c1c; padding: 1rem; border-radius: 0.5rem; text-align: center;">
            <?php echo h($error_message); ?>
        </div>
    <?php endif; ?>
</div>

<?php require_once 'includes/footer.php'; ?>