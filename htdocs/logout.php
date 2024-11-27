<?php
session_start();

// Détruire toutes les données de la session
session_unset();

// Détruire la session
session_destroy();

// Rediriger l'utilisateur vers la page d'accueil ou une autre page
header("Location: index.php");
exit;
?>
