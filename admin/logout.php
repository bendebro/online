<?php
/**
 * Déconnexion administrateur
 * Le petit agenais - Admin
 */

session_start();

// Détruire toutes les variables de session
$_SESSION = array();

// Détruire la session
session_destroy();

// Rediriger vers la page de login
header('Location: login.php?message=logout');
exit;
?>