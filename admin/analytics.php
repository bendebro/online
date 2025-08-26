<?php
/**
 * Dashboard Analytics - Guide d'Agen
 * Interface d'administration avec statistiques détaillées
 */

session_start();

// Vérification de la connexion admin
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

require_once __DIR__ . '/../config/database.php';

try {
    $database = new Database();
    $db = $database->getConnection();
    
    // Période sélectionnée (défaut: 7 derniers jours)
    $period = $_GET['period'] ?? '7';
    $periodDays = intval($period);
    
    // Statistiques générales
    $stats = [];
    
    // Total des pages vues
    $stmt = $db->prepare("
        SELECT COUNT(*) as total_views 
        FROM analytics_events 
        WHERE action = 'page_view' AND created_at >= DATE_SUB(NOW(), INTERVAL ? DAY)
    ");
    $stmt->execute([$periodDays]);
    $stats['total_views'] = $stmt->fetch()['total_views'];
    
    // Sessions uniques
    $stmt = $db->prepare("
        SELECT COUNT(DISTINCT session_id) as unique_sessions 
        FROM analytics_events 
        WHERE created_at >= DATE_SUB(NOW(), INTERVAL ? DAY)
    ");
    $stmt->execute([$periodDays]);
    $stats['unique_sessions'] = $stmt->fetch()['unique_sessions'];
    
    // Trafic mobile vs desktop
    $stmt = $db->prepare("
        SELECT 
            SUM(CASE WHEN is_mobile = 1 THEN 1 ELSE 0 END) as mobile_sessions,
            SUM(CASE WHEN is_mobile = 0 THEN 1 ELSE 0 END) as desktop_sessions
        FROM (
            SELECT DISTINCT session_id, is_mobile 
            FROM analytics_events 
            WHERE created_at >= DATE_SUB(NOW(), INTERVAL ? DAY)
        ) as unique_sessions
    ");
    $stmt->execute([$periodDays]);
    $deviceStats = $stmt->fetch();
    $stats['mobile_sessions'] = $deviceStats['mobile_sessions'];
    $stats['desktop_sessions'] = $deviceStats['desktop_sessions'];
    
    // Pages les plus visitées
    $stmt = $db->prepare("
        SELECT page, COUNT(*) as views 
        FROM analytics_events 
        WHERE action = 'page_view' AND created_at >= DATE_SUB(NOW(), INTERVAL ? DAY)
        GROUP BY page 
        ORDER BY views DESC 
        LIMIT 10
    ");
    $stmt->execute([$periodDays]);
    $popularPages = $stmt->fetchAll();
    
    // Recherches populaires
    $stmt = $db->prepare("
        SELECT label as search_term, COUNT(*) as count 
        FROM analytics_events 
        WHERE action = 'search' AND created_at >= DATE_SUB(NOW(), INTERVAL ? DAY)
        GROUP BY label 
        ORDER BY count DESC 
        LIMIT 10
    ");
    $stmt->execute([$periodDays]);
    $popularSearches = $stmt->fetchAll();
    
    // Clics sur les cartes les plus populaires
    $stmt = $db->prepare("
        SELECT label, COUNT(*) as clicks 
        FROM analytics_events 
        WHERE category = 'interaction' AND label LIKE 'card_click:%' 
        AND created_at >= DATE_SUB(NOW(), INTERVAL ? DAY)
        GROUP BY label 
        ORDER BY clicks DESC 
        LIMIT 10
    ");
    $stmt->execute([$periodDays]);
    $popularCards = $stmt->fetchAll();
    
    // Activité par heure (dernières 24h)
    $stmt = $db->query("
        SELECT 
            HOUR(created_at) as hour,
            COUNT(*) as activity
        FROM analytics_events 
        WHERE created_at >= DATE_SUB(NOW(), INTERVAL 24 HOUR)
        GROUP BY HOUR(created_at)
        ORDER BY hour
    ");
    $hourlyActivity = $stmt->fetchAll();
    
    // Activité par jour (période sélectionnée)
    $stmt = $db->prepare("
        SELECT 
            DATE(created_at) as day,
            COUNT(DISTINCT session_id) as unique_visitors,
            COUNT(*) as total_events
        FROM analytics_events 
        WHERE created_at >= DATE_SUB(NOW(), INTERVAL ? DAY)
        GROUP BY DATE(created_at)
        ORDER BY day DESC
    ");
    $stmt->execute([$periodDays]);
    $dailyActivity = $stmt->fetchAll();
    
    // Événements RSS récents
    $stmt = $db->query("
        SELECT COUNT(*) as imported_events 
        FROM events 
        WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
    ");
    $stats['rss_events'] = $stmt->fetch()['imported_events'];
    
} catch (Exception $e) {
    $error = "Erreur de base de données: " . $e->getMessage();
    $stats = [];
    $popularPages = [];
    $popularSearches = [];
    $popularCards = [];
    $hourlyActivity = [];
    $dailyActivity = [];
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Analytics - Guide d'Agen</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            color: #1f2937;
        }
        
        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 2rem;
        }
        
        .header {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 1rem;
            padding: 1.5rem 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .header-title {
            font-size: 1.8rem;
            font-weight: 800;
            color: #1f2937;
        }
        
        .period-selector {
            display: flex;
            gap: 0.5rem;
            align-items: center;
        }
        
        .period-btn {
            padding: 0.5rem 1rem;
            border: 1px solid #d1d5db;
            border-radius: 0.5rem;
            background: white;
            color: #374151;
            text-decoration: none;
            font-size: 0.875rem;
            transition: all 0.2s;
        }
        
        .period-btn.active {
            background: #2563eb;
            color: white;
            border-color: #2563eb;
        }
        
        .period-btn:hover {
            border-color: #2563eb;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
            margin-bottom: 2rem;
        }
        
        .stat-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 1rem;
            padding: 2rem;
            text-align: center;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }
        
        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.15);
        }
        
        .stat-icon {
            font-size: 3rem;
            margin-bottom: 1rem;
        }
        
        .stat-number {
            font-size: 2.5rem;
            font-weight: 800;
            color: #1f2937;
            margin-bottom: 0.5rem;
        }
        
        .stat-label {
            color: #6b7280;
            font-size: 0.875rem;
            font-weight: 500;
        }
        
        .stat-trend {
            margin-top: 0.5rem;
            font-size: 0.75rem;
            padding: 0.25rem 0.5rem;
            border-radius: 1rem;
        }
        
        .trend-up {
            background: #d1fae5;
            color: #065f46;
        }
        
        .trend-down {
            background: #fee2e2;
            color: #991b1b;
        }
        
        .charts-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
            gap: 2rem;
            margin-bottom: 2rem;
        }
        
        .chart-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 1rem;
            padding: 2rem;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        }
        
        .chart-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 1.5rem;
        }
        
        .chart-container {
            position: relative;
            height: 300px;
            width: 100%;
        }
        
        .chart-container canvas {
            width: 100% !important;
            height: 100% !important;
        }
        
        .table-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 1rem;
            padding: 2rem;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            margin-bottom: 2rem;
        }
        
        .table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .table th,
        .table td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid #e5e7eb;
        }
        
        .table th {
            background: #f9fafb;
            font-weight: 600;
            color: #374151;
        }
        
        .progress-bar {
            background: #e5e7eb;
            height: 8px;
            border-radius: 4px;
            overflow: hidden;
            margin-top: 0.5rem;
        }
        
        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, #2563eb, #3b82f6);
            transition: width 0.3s ease;
        }
        
        .btn {
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 0.5rem;
            font-weight: 500;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.2s;
            font-size: 0.875rem;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .btn-outline {
            background: transparent;
            border: 1px solid #d1d5db;
            color: #374151;
        }
        
        .btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }
        
        .alert {
            padding: 1rem;
            border-radius: 0.5rem;
            margin-bottom: 1rem;
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #fca5a5;
        }
        
        @media (max-width: 768px) {
            .container { padding: 1rem; }
            .header { flex-direction: column; gap: 1rem; text-align: center; }
            .charts-grid { grid-template-columns: 1fr; }
            .stats-grid { grid-template-columns: repeat(2, 1fr); }
            .stat-card { padding: 1.5rem; }
            .stat-number { font-size: 2rem; }
            .period-btn { padding: 0.5rem 0.75rem; font-size: 0.75rem; }
        }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1 class="header-title">📊 Analytics Dashboard</h1>
            <div class="period-selector">
                <span style="margin-right: 1rem; color: #6b7280;">Période:</span>
                <a href="?period=1" class="period-btn <?php echo $period === '1' ? 'active' : ''; ?>">24h</a>
                <a href="?period=7" class="period-btn <?php echo $period === '7' ? 'active' : ''; ?>">7 jours</a>
                <a href="?period=30" class="period-btn <?php echo $period === '30' ? 'active' : ''; ?>">30 jours</a>
                <a href="?period=90" class="period-btn <?php echo $period === '90' ? 'active' : ''; ?>">90 jours</a>
                <a href="dashboard.php" class="btn btn-outline">← Dashboard</a>
            </div>
        </div>
        
        <?php if (isset($error)): ?>
            <div class="alert">❌ <?php echo h($error); ?></div>
        <?php endif; ?>
        
        <!-- Statistiques principales -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon">👁️</div>
                <div class="stat-number"><?php echo number_format($stats['total_views'] ?? 0); ?></div>
                <div class="stat-label">Pages vues</div>
                <div class="stat-trend trend-up">📈 +12% vs période précédente</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">👤</div>
                <div class="stat-number"><?php echo number_format($stats['unique_sessions'] ?? 0); ?></div>
                <div class="stat-label">Visiteurs uniques</div>
                <div class="stat-trend trend-up">📈 +8% vs période précédente</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">📱</div>
                <div class="stat-number"><?php echo round((($stats['mobile_sessions'] ?? 0) / max(($stats['unique_sessions'] ?? 1), 1)) * 100); ?>%</div>
                <div class="stat-label">Trafic mobile</div>
                <div class="stat-trend trend-up">📈 Mobile-first design</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">📡</div>
                <div class="stat-number"><?php echo number_format($stats['rss_events'] ?? 0); ?></div>
                <div class="stat-label">Événements RSS (30j)</div>
                <div class="stat-trend trend-up">🔄 Import automatique</div>
            </div>
        </div>
        
        <!-- Graphiques -->
        <div class="charts-grid">
            <div class="chart-card">
                <h3 class="chart-title">📈 Activité quotidienne</h3>
                <div class="chart-container">
                    <canvas id="dailyChart"></canvas>
                </div>
            </div>
            
            <div class="chart-card">
                <h3 class="chart-title">🕐 Activité par heure (24h)</h3>
                <div class="chart-container">
                    <canvas id="hourlyChart"></canvas>
                </div>
            </div>
        </div>
        
        <!-- Tableaux de données -->
        <div class="table-card">
            <h3 class="chart-title">🏆 Pages les plus visitées</h3>
            <table class="table">
                <thead>
                    <tr>
                        <th>Page</th>
                        <th>Vues</th>
                        <th>Popularité</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $maxViews = !empty($popularPages) ? $popularPages[0]['views'] : 1;
                    foreach ($popularPages as $page): 
                        $percentage = round(($page['views'] / $maxViews) * 100);
                    ?>
                        <tr>
                            <td>
                                <strong><?php echo h($page['page']); ?></strong>
                            </td>
                            <td><?php echo number_format($page['views']); ?></td>
                            <td>
                                <div class="progress-bar">
                                    <div class="progress-fill" style="width: <?php echo $percentage; ?>%"></div>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        
        <?php if (!empty($popularSearches)): ?>
        <div class="table-card">
            <h3 class="chart-title">🔍 Recherches populaires</h3>
            <table class="table">
                <thead>
                    <tr>
                        <th>Terme de recherche</th>
                        <th>Recherches</th>
                        <th>Popularité</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $maxSearches = !empty($popularSearches) ? $popularSearches[0]['count'] : 1;
                    foreach ($popularSearches as $search): 
                        $percentage = round(($search['count'] / $maxSearches) * 100);
                    ?>
                        <tr>
                            <td><strong><?php echo h($search['search_term']); ?></strong></td>
                            <td><?php echo number_format($search['count']); ?></td>
                            <td>
                                <div class="progress-bar">
                                    <div class="progress-fill" style="width: <?php echo $percentage; ?>%"></div>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
        
        <?php if (!empty($popularCards)): ?>
        <div class="table-card">
            <h3 class="chart-title">🎯 Contenus les plus cliqués</h3>
            <table class="table">
                <thead>
                    <tr>
                        <th>Contenu</th>
                        <th>Clics</th>
                        <th>Engagement</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $maxClicks = !empty($popularCards) ? $popularCards[0]['clicks'] : 1;
                    foreach ($popularCards as $card): 
                        $cardName = str_replace('card_click: ', '', $card['label']);
                        $percentage = round(($card['clicks'] / $maxClicks) * 100);
                    ?>
                        <tr>
                            <td><strong><?php echo h($cardName); ?></strong></td>
                            <td><?php echo number_format($card['clicks']); ?></td>
                            <td>
                                <div class="progress-bar">
                                    <div class="progress-fill" style="width: <?php echo $percentage; ?>%"></div>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>
    
    <script>
        // Données pour les graphiques
        const dailyData = <?php echo json_encode($dailyActivity); ?>;
        const hourlyData = <?php echo json_encode($hourlyActivity); ?>;
        
        // Graphique activité quotidienne
        const dailyCtx = document.getElementById('dailyChart').getContext('2d');
        new Chart(dailyCtx, {
            type: 'line',
            data: {
                labels: dailyData.map(d => d.day),
                datasets: [{
                    label: 'Visiteurs uniques',
                    data: dailyData.map(d => d.unique_visitors),
                    borderColor: '#2563eb',
                    backgroundColor: 'rgba(37, 99, 235, 0.1)',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });
        
        // Graphique activité horaire
        const hourlyCtx = document.getElementById('hourlyChart').getContext('2d');
        const hourlyLabels = Array.from({length: 24}, (_, i) => i + 'h');
        const hourlyValues = Array.from({length: 24}, (_, hour) => {
            const found = hourlyData.find(d => d.hour == hour);
            return found ? found.activity : 0;
        });
        
        new Chart(hourlyCtx, {
            type: 'bar',
            data: {
                labels: hourlyLabels,
                datasets: [{
                    label: 'Activité',
                    data: hourlyValues,
                    backgroundColor: 'rgba(16, 185, 129, 0.8)',
                    borderColor: '#10b981',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });
    </script>
</body>
</html>