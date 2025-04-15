<?php
// Fichier de configuration pour les paramètres de connexion à la base de données
// et autres paramètres globaux

// Paramètres de connexion à la base de données
$config = [
    'db' => [
        'host' => '127.0.0.1',
        'dbname' => 'PlanVoyages',
        'username' => 'root',
        'password' => 'rootroot',
        'charset' => 'utf8mb4'
    ],
    'site' => [
        'name' => 'TravelDream',
        'url' => 'http://localhost/projet_voyage',
        'email' => 'contact@traveldream.com'
    ]
];

// Fonction pour établir une connexion PDO à la base de données
function getDbConnection() {
    global $config;
    
    try {
        $dsn = "mysql:host={$config['db']['host']};dbname={$config['db']['dbname']};charset={$config['db']['charset']}";
        $pdo = new PDO($dsn, $config['db']['username'], $config['db']['password']);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    } catch (PDOException $e) {
        die("Erreur de connexion à la base de données : " . $e->getMessage());
    }
}

// Fonction pour vérifier si l'utilisateur est connecté
function isUserLoggedIn() {
    return isset($_SESSION['id_utilisateur']);
}

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
