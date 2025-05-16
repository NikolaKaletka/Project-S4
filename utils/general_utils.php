<?php
// Fichier contenant les fonctions utilitaires générales

// Fonction pour rediriger vers une page
function redirect($page) {
    header("Location: $page");
    exit();
}

// Fonction pour sécuriser les données
function sanitize($data) {
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

// Fonction pour générer un message d'alerte
function generateAlert($message, $type = 'info') {
    return "<div class='alert alert-$type alert-dismissible fade show' role='alert'>
                $message
                <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
            </div>";
}

// Fonction pour formater une date
function formatDate($date, $format = 'd/m/Y') {
    return date($format, strtotime($date));
}
