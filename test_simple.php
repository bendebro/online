<?php
// Test simple sans includes pour éviter les erreurs
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Favoris - Le petit agenais</title>
    <style>
        body { 
            font-family: -apple-system, BlinkMacSystemFont, sans-serif; 
            margin: 2rem; 
            background: #f9fafb;
        }
        .header {
            background: white;
            padding: 1rem 2rem;
            border-radius: 0.5rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            margin-bottom: 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .nav-favorites {
            background: #3b82f6;
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 0.25rem;
            text-decoration: none;
        }
        .favorites-count {
            background: #dc2626;
            color: white;
            font-size: 0.75rem;
            padding: 0.125rem 0.375rem;
            border-radius: 9999px;
            margin-left: 0.5rem;
            display: none;
        }
        .test-card {
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 0.5rem;
            padding: 1.5rem;
            margin: 1rem 0;
            position: relative;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        .favorite-btn {
            position: absolute;
            top: 1rem;
            right: 1rem;
            background: rgba(255,255,255,0.9);
            color: #6b7280;
            border: 1px solid #d1d5db;
            border-radius: 50%;
            width: 2.5rem;
            height: 2.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        .favorite-btn:hover {
            background: white;
            transform: scale(1.1);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }
        .favorite-btn.active {
            color: #dc2626;
            background: rgba(220, 38, 38, 0.1);
            border-color: #dc2626;
        }
        .btn {
            background: #3b82f6;
            color: white;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 0.25rem;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            margin-right: 0.5rem;
        }
        .btn:hover { background: #2563eb; }
        .btn.success { background: #10b981; }
        .btn.danger { background: #ef4444; }
        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1rem;
        }
        .status {
            background: #ecfdf5;
            border: 1px solid #10b981;
            color: #065f46;
            padding: 1rem;
            border-radius: 0.5rem;
            margin-bottom: 2rem;
        }
        .debug {
            background: #f3f4f6;
            padding: 1rem;
            border-radius: 0.5rem;
            margin-top: 1rem;
            font-family: monospace;
            font-size: 0.875rem;
            white-space: pre-wrap;
        }
        .notification {
            position: fixed;
            top: 2rem;
            right: 2rem;
            z-index: 1000;
            background: #10b981;
            color: white;
            padding: 1rem 1.5rem;
            border-radius: 0.5rem;
            transform: translateX(100%);
            transition: transform 0.3s ease;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>🧪 Test Favoris - Le petit agenais</h1>
        <div>
            <span class="nav-favorites">
                ❤️ Favoris <span id="nav-counter" class="favorites-count">0</span>
            </span>
        </div>
    </div>
    
    <div class="status">
        ✅ <strong>Page PHP fonctionnelle !</strong> Serveur configuré correctement.
    </div>
    
    <div class="test-card">
        <h2>🎛️ Contrôles</h2>
        <button onclick="clearAllFavorites()" class="btn danger">Vider tous les favoris</button>
        <button onclick="showDebugInfo()" class="btn">Afficher debug</button>
        <button onclick="testNotification()" class="btn success">Test notification</button>
    </div>
    
    <h2>🍽️ Restaurants de test :</h2>
    <div class="grid">
        <div class="test-card card">
            <button class="favorite-btn" data-id="1" data-type="restaurant" title="Ajouter aux favoris">
                ❤️
            </button>
            <h3>Restaurant Le Margoton</h3>
            <p><strong>Cuisine :</strong> Traditionnelle du Sud-Ouest</p>
            <p style="color: #6b7280;">Spécialités locales, cassoulet, magret de canard</p>
            <p style="color: #9ca3af; font-size: 0.875rem;">📍 52 Rue Garonne, 47000 Agen</p>
        </div>
        
        <div class="test-card card">
            <button class="favorite-btn" data-id="2" data-type="restaurant" title="Ajouter aux favoris">
                ❤️
            </button>
            <h3>Bistro des Halles</h3>
            <p><strong>Cuisine :</strong> Bistro moderne</p>
            <p style="color: #6b7280;">Au cœur du marché couvert, produits frais locaux</p>
            <p style="color: #9ca3af; font-size: 0.875rem;">📍 Place des Halles, 47000 Agen</p>
        </div>
        
        <div class="test-card card">
            <button class="favorite-btn" data-id="3" data-type="restaurant" title="Ajouter aux favoris">
                ❤️
            </button>
            <h3>La Table d'Armagnac</h3>
            <p><strong>Cuisine :</strong> Gastronomique</p>
            <p style="color: #6b7280;">Cuisine raffinée, carte des vins exceptionnelle</p>
            <p style="color: #9ca3af; font-size: 0.875rem;">📍 18 Rue du Président Carnot, 47000 Agen</p>
        </div>
    </div>
    
    <div class="test-card">
        <h2>📤 Test Bouton Partage</h2>
        <button class="btn" onclick="testShare()">
            📤 Tester le partage
        </button>
        <p style="color: #6b7280; font-size: 0.875rem; margin-top: 0.5rem;">
            Teste l'API Web Share ou fallback de copie du lien
        </p>
    </div>
    
    <div class="test-card">
        <h2>🔍 État du système</h2>
        <div id="debug-info" class="debug">Cliquez sur "Afficher debug" pour voir les détails...</div>
    </div>
    
    <script>
    // === SYSTÈME DE FAVORIS SIMPLE ET ROBUSTE ===
    console.log('🚀 Démarrage du système de favoris...');
    
    const FavoriteSystem = {
        storageKey: 'agen_favorites_v2',
        
        init() {
            console.log('📱 Initialisation...');
            this.cleanOldData();
            this.bindEvents();
            this.updateUI();
            this.showDebugInfo();
            console.log('✅ Système initialisé avec succès');
        },
        
        cleanOldData() {
            // Nettoyer les anciennes données corrompues
            const oldKeys = ['agen_favorites', 'favorites_data'];
            oldKeys.forEach(key => {
                if (localStorage.getItem(key)) {
                    localStorage.removeItem(key);
                    console.log('🧹 Nettoyage:', key);
                }
            });
        },
        
        bindEvents() {
            document.addEventListener('click', (e) => {
                if (e.target.classList.contains('favorite-btn')) {
                    e.preventDefault();
                    this.handleFavoriteClick(e.target);
                }
            });
            console.log('🔗 Événements liés');
        },
        
        handleFavoriteClick(btn) {
            const id = parseInt(btn.dataset.id);
            const type = btn.dataset.type;
            
            console.log('👆 Clic favori:', {id, type});
            
            if (!id || !type) {
                console.error('❌ Données manquantes:', {id, type});
                return;
            }
            
            // Récupérer les infos depuis le DOM
            const card = btn.closest('.card');
            const titleEl = card?.querySelector('h3');
            const name = titleEl ? titleEl.textContent.trim() : `Item ${id}`;
            
            const favorite = {
                id: id,
                type: type,
                name: name,
                addedAt: new Date().toISOString()
            };
            
            if (this.isFavorite(id, type)) {
                this.removeFavorite(id, type);
                this.showNotification(`❌ ${name} retiré des favoris`);
            } else {
                this.addFavorite(favorite);
                this.showNotification(`✅ ${name} ajouté aux favoris !`);
            }
            
            this.updateUI();
            this.animateButton(btn);
        },
        
        addFavorite(favorite) {
            const favorites = this.getFavorites();
            
            // Vérifier si pas déjà présent
            const exists = favorites.find(f => f.id === favorite.id && f.type === favorite.type);
            if (exists) return false;
            
            favorites.push(favorite);
            this.saveFavorites(favorites);
            console.log('➕ Favori ajouté:', favorite);
            return true;
        },
        
        removeFavorite(id, type) {
            const favorites = this.getFavorites();
            const index = favorites.findIndex(f => f.id === id && f.type === type);
            
            if (index === -1) return false;
            
            const removed = favorites.splice(index, 1)[0];
            this.saveFavorites(favorites);
            console.log('➖ Favori supprimé:', removed);
            return true;
        },
        
        isFavorite(id, type) {
            const favorites = this.getFavorites();
            return favorites.some(f => f.id === id && f.type === type);
        },
        
        getFavorites() {
            try {
                const data = localStorage.getItem(this.storageKey);
                const parsed = data ? JSON.parse(data) : [];
                console.log('📚 Favoris récupérés:', parsed.length);
                return parsed;
            } catch (e) {
                console.error('❌ Erreur lecture localStorage:', e);
                return [];
            }
        },
        
        saveFavorites(favorites) {
            try {
                localStorage.setItem(this.storageKey, JSON.stringify(favorites));
                console.log('💾 Favoris sauvegardés:', favorites.length);
            } catch (e) {
                console.error('❌ Erreur sauvegarde:', e);
            }
        },
        
        clearAll() {
            if (confirm('🗑️ Supprimer tous les favoris ?')) {
                localStorage.removeItem(this.storageKey);
                this.updateUI();
                this.showNotification('🗑️ Tous les favoris supprimés');
                console.log('🧹 Tous les favoris supprimés');
            }
        },
        
        updateUI() {
            const favorites = this.getFavorites();
            const count = favorites.length;
            
            // Mettre à jour le compteur
            const counter = document.getElementById('nav-counter');
            if (counter) {
                counter.textContent = count;
                counter.style.display = count > 0 ? 'inline-block' : 'none';
            }
            
            // Mettre à jour les boutons
            document.querySelectorAll('.favorite-btn').forEach(btn => {
                const id = parseInt(btn.dataset.id);
                const type = btn.dataset.type;
                
                if (id && type) {
                    const isActive = this.isFavorite(id, type);
                    btn.classList.toggle('active', isActive);
                    btn.textContent = isActive ? '💖' : '❤️';
                    btn.title = isActive ? 'Retirer des favoris' : 'Ajouter aux favoris';
                }
            });
            
            console.log('🔄 UI mise à jour:', count, 'favoris');
        },
        
        animateButton(btn) {
            btn.style.transform = 'scale(1.3)';
            setTimeout(() => {
                btn.style.transform = '';
            }, 200);
        },
        
        showNotification(message) {
            const notification = document.createElement('div');
            notification.className = 'notification';
            notification.textContent = message;
            
            document.body.appendChild(notification);
            
            // Animation d'entrée
            setTimeout(() => {
                notification.style.transform = 'translateX(0)';
            }, 100);
            
            // Suppression automatique
            setTimeout(() => {
                notification.style.transform = 'translateX(100%)';
                setTimeout(() => notification.remove(), 300);
            }, 3000);
        },
        
        showDebugInfo() {
            const favorites = this.getFavorites();
            const debugInfo = {
                'Nombre de favoris': favorites.length,
                'Clé localStorage': this.storageKey,
                'Support localStorage': typeof(Storage) !== "undefined",
                'Favoris actuels': favorites,
                'Taille localStorage': this.getStorageSize(),
                'Timestamp': new Date().toLocaleString()
            };
            
            const debugEl = document.getElementById('debug-info');
            if (debugEl) {
                debugEl.textContent = JSON.stringify(debugInfo, null, 2);
            }
            
            console.table(debugInfo);
        },
        
        getStorageSize() {
            try {
                const data = localStorage.getItem(this.storageKey);
                return data ? `${data.length} caractères` : '0 caractères';
            } catch (e) {
                return 'Erreur';
            }
        }
    };
    
    // === FONCTIONS GLOBALES ===
    function clearAllFavorites() {
        FavoriteSystem.clearAll();
    }
    
    function showDebugInfo() {
        FavoriteSystem.showDebugInfo();
    }
    
    function testNotification() {
        FavoriteSystem.showNotification('🧪 Test de notification réussi !');
    }
    
    function testShare() {
        const shareData = {
            title: 'Test Partage - Le petit agenais',
            text: 'Test du système de partage du guide touristique d\'Agen',
            url: window.location.href
        };
        
        console.log('🔗 Test partage:', shareData);
        
        if (navigator.share) {
            navigator.share(shareData)
                .then(() => {
                    FavoriteSystem.showNotification('✅ Partage Web API réussi !');
                    console.log('✅ Partage natif réussi');
                })
                .catch((error) => {
                    console.log('❌ Erreur partage natif:', error);
                    fallbackShare(shareData);
                });
        } else {
            console.log('ℹ️ Web Share API non supportée, fallback');
            fallbackShare(shareData);
        }
    }
    
    function fallbackShare(shareData) {
        if (navigator.clipboard) {
            navigator.clipboard.writeText(shareData.url)
                .then(() => {
                    FavoriteSystem.showNotification('📋 Lien copié dans le presse-papiers !');
                    console.log('✅ Copie clipboard réussie');
                })
                .catch(() => {
                    console.log('❌ Erreur clipboard, fallback alert');
                    alert('🔗 Lien à partager: ' + shareData.url);
                });
        } else {
            console.log('ℹ️ Clipboard API non supportée, alert');
            alert('🔗 Lien à partager: ' + shareData.url);
        }
    }
    
    // === INITIALISATION ===
    document.addEventListener('DOMContentLoaded', function() {
        console.log('📄 DOM chargé, initialisation...');
        try {
            FavoriteSystem.init();
        } catch (error) {
            console.error('💥 Erreur initialisation:', error);
            document.getElementById('debug-info').textContent = 'ERREUR: ' + error.message;
        }
    });
    
    // Log initial
    console.log('📱 Script chargé - ' + new Date().toLocaleString());
    </script>
</body>
</html>