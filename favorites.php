<?php
$page_title = 'Mes Favoris';
require_once 'includes/header.php';
?>

<div class="container">
    <!-- Header -->
    <div class="page-header">
        <h1 class="page-title" style="background: linear-gradient(135deg, #dc2626, #db2777); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;">
            Mes Favoris
        </h1>
        <p class="page-subtitle">
            Retrouvez tous vos lieux préférés à Agen en un seul endroit.
        </p>
        <div style="display: flex; justify-content: center; align-items: center; margin-top: 1rem; gap: 1rem;">
            <span id="favorites-count-display" style="background: #f3f4f6; color: #374151; padding: 0.5rem 0.75rem; border-radius: 9999px;">
                <svg width="16" height="16" fill="#dc2626" viewBox="0 0 16 16" style="margin-right: 0.25rem;">
                    <path d="m8 2.748-.717-.737C5.6.281 2.514.878 1.4 3.053c-.523 1.023-.641 2.5.314 4.385.92 1.815 2.834 3.989 6.286 6.357 3.452-2.368 5.365-4.542 6.286-6.357.955-1.886.838-3.362.314-4.385C13.486.878 10.4.28 8.717 2.01L8 2.748z"/>
                </svg>
                <span id="total-favorites">0</span> favori<span id="plural-s">s</span>
            </span>
            <button id="clear-all-btn" style="background: #fee2e2; border: 1px solid #fecaca; color: #dc2626; padding: 0.5rem 0.75rem; border-radius: 9999px; cursor: pointer; display: none;" onclick="clearAllFavorites()">
                <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16" style="margin-right: 0.25rem;">
                    <path d="m8 2.748-.717-.737C5.6.281 2.514.878 1.4 3.053c-.523 1.023-.641 2.5.314 4.385.92 1.815 2.834 3.989 6.286 6.357 3.452-2.368 5.365-4.542 6.286-6.357.955-1.886.838-3.362.314-4.385C13.486.878 10.4.28 8.717 2.01L8 2.748zM8 15C-7.333 4.868 3.279-3.04 7.824 1.143c.06.055.119.112.176.171a3.12 3.12 0 0 1 .176-.17C12.72-3.042 23.333 4.867 8 15z"/>
                </svg>
                Tout supprimer
            </button>
        </div>
    </div>

    <!-- Tabs -->
    <div style="margin-bottom: 2rem;">
        <div class="filters" style="padding: 1rem;">
            <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 0.5rem; background: rgba(255,255,255,0.8); backdrop-filter: blur(8px); border-radius: 0.75rem; padding: 0.25rem;">
                <button class="tab-btn active" data-tab="all" style="padding: 0.75rem; border: none; border-radius: 0.5rem; background: white; box-shadow: 0 4px 12px rgba(0,0,0,0.1); font-weight: 500; cursor: pointer; transition: all 0.2s;">
                    Tous (<span id="count-all">0</span>)
                </button>
                <button class="tab-btn" data-tab="restaurants" style="padding: 0.75rem; border: none; border-radius: 0.5rem; background: transparent; color: #6b7280; font-weight: 500; cursor: pointer; transition: all 0.2s;">
                    🍽️ Restaurants (<span id="count-restaurants">0</span>)
                </button>
                <button class="tab-btn" data-tab="activities" style="padding: 0.75rem; border: none; border-radius: 0.5rem; background: transparent; color: #6b7280; font-weight: 500; cursor: pointer; transition: all 0.2s;">
                    🎯 Activités (<span id="count-activities">0</span>)
                </button>
                <button class="tab-btn" data-tab="events" style="padding: 0.75rem; border: none; border-radius: 0.5rem; background: transparent; color: #6b7280; font-weight: 500; cursor: pointer; transition: all 0.2s;">
                    🎭 Événements (<span id="count-events">0</span>)
                </button>
            </div>
        </div>
    </div>

    <!-- Content Container -->
    <div id="favorites-content">
        <!-- Le contenu sera généré par JavaScript -->
    </div>

    <!-- Empty State (hidden by default) -->
    <div id="empty-state" class="empty-state" style="display: none;">
        <div class="empty-icon">💝</div>
        <h3>Vos Favoris</h3>
        <p>Vous n'avez pas encore ajouté de favoris. Explorez Agen et sauvegardez vos lieux préférés !</p>
        <div style="margin-top: 2rem;">
            <a href="restaurants.php" class="btn" style="margin-right: 1rem;">🍽️ Restaurants</a>
            <a href="activities.php" class="btn" style="margin-right: 1rem;">🎯 Activités</a>
            <a href="events.php" class="btn">📅 Événements</a>
        </div>
    </div>

    <!-- Tab Empty State (hidden by default) -->
    <div id="tab-empty-state" class="empty-state" style="display: none;">
        <div class="empty-icon">💔</div>
        <h3>Aucun favori dans cette catégorie</h3>
        <p>Ajoutez des éléments à vos favoris en cliquant sur le cœur.</p>
    </div>
</div>

<style>
.tab-btn {
    padding: 0.75rem;
    border: none;
    border-radius: 0.5rem;
    background: transparent;
    color: #6b7280;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s;
}

.tab-btn.active {
    background: white;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    color: #1f2937;
}

.empty-state {
    text-align: center;
    padding: 4rem 2rem;
    color: var(--text-secondary);
}

.empty-icon {
    font-size: 4rem;
    margin-bottom: 1rem;
    opacity: 0.7;
}

.empty-state h3 {
    color: var(--text-primary);
    margin-bottom: 1rem;
}

.favorites-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 1.5rem;
    margin-top: 2rem;
}
</style>

<?php require_once 'includes/footer.php'; ?>