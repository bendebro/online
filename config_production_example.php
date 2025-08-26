<?php
/**
 * Configuration d'environnement pour la production
 * 
 * Instructions d'utilisation :
 * 1. Renommez ce fichier en "config_production.php"
 * 2. Modifiez les valeurs selon votre environnement
 * 3. Dans config.php, incluez ce fichier au lieu des valeurs par défaut
 */

// ===========================================
// CONFIGURATION POUR VOTRE SERVEUR
// ===========================================

// Pour lepetitagenais.fr, utilisez ces paramètres :
define('BASE_URL', '');                    // Vide si à la racine du domaine
define('ASSETS_URL', '/assets');           // Chemin vers les assets

// Configuration de la base de données
define('DB_HOST', 'localhost');            // Ou l'IP de votre serveur MySQL
define('DB_NAME', 'guide_agen');           // Nom de votre base de données
define('DB_USER', 'votre_user_mysql');     // Votre utilisateur MySQL
define('DB_PASS', 'votre_password_mysql'); // Votre mot de passe MySQL
define('DB_CHARSET', 'utf8mb4');

// Configuration des erreurs (désactiver en production)
error_reporting(0);                        // Masquer les erreurs en production
ini_set('display_errors', 0);

// ===========================================
// ALTERNATIVE : Configuration avec sous-dossier
// ===========================================
// Si vous installez dans un sous-dossier comme /guide/
// Décommentez ces lignes :
/*
define('BASE_URL', '/guide');
define('ASSETS_URL', BASE_URL . '/assets');
*/

// ===========================================
// SÉCURITÉ AVANCÉE (optionnel)
// ===========================================

// Token admin plus sécurisé (changez cette valeur !)
define('ADMIN_TOKEN', 'votre-token-securise-' . date('Y'));

// Configuration HTTPS forcé (si vous avez un certificat SSL)
/*
if (!isset($_SERVER['HTTPS']) || $_SERVER['HTTPS'] !== 'on') {
    $secure_url = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    header("Location: $secure_url", true, 301);
    exit;
}
*/

// ===========================================
// NOTES D'INSTALLATION
// ===========================================
/*
ÉTAPES D'INSTALLATION :

1. Uploadez tous les fichiers sur votre serveur
2. Créez une base de données MySQL
3. Modifiez ce fichier avec vos paramètres
4. Renommez-le en "config_production.php"
5. Visitez /install.php pour vérifier l'installation
6. Connectez-vous avec admin/password
7. Supprimez le fichier install.php

STRUCTURE DE FICHIERS REQUISE :
- admin/ (dossier complet)
- config/ (dossier complet)
- assets/ (dossier complet)
- sql/setup.sql
- index.php, restaurants.php, activities.php, events.php
- includes/ (header.php, footer.php)

PERMISSIONS RECOMMANDÉES :
- Dossiers : 755
- Fichiers PHP : 644
- Fichiers de config : 600 (plus sécurisé)
*/
?>