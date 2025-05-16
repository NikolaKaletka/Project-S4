<?php
// Fichier contenant les fonctions liées à la gestion des utilisateurs

// Fonction pour vérifier si l'utilisateur est connecté
function isUserLoggedIn() {
    return isset($_SESSION['id_utilisateur']);
}
