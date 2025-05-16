<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once 'utils/utils.php';
$est_connecte = isUserLoggedIn();

// Définir une valeur par défaut pour $current_page s'il n'est pas défini
if (!isset($current_page)) {
    $current_page = '';
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title . ' - ' . 'TravelDream' : 'TravelDream'; ?></title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome pour les icônes -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- CSS personnalisé -->
    <link rel="stylesheet" href="static/global.css">
    <?php if (isset($additional_css)): ?>
        <?php foreach ($additional_css as $css): ?>
            <link rel="stylesheet" href="<?php echo $css; ?>">
        <?php endforeach; ?>
    <?php endif; ?>
</head>
<body>
    <!-- Navigation -->
    <header>
        <nav class="navbar navbar-expand-lg">
            <div class="container">
                <a class="navbar-brand" href="index.php">TravelDream</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item">
                            <a class="nav-link <?php echo $current_page === 'index' ? 'active' : ''; ?>" href="destination.php">Accueil</a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link <?php echo $current_page === 'map' ? 'active' : ''; ?>" href="map.php">Carte</a>
                        </li>
                        <?php if ($est_connecte): ?>
                            <li class="nav-item">
                                <a class="nav-link <?php echo ($current_page === 'profil' || $current_page === 'mesvoyages') ? 'active' : ''; ?>" href="mesvoyages.php">Mon Profil</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="logout.php">Déconnexion</a>
                            </li>
                        <?php else: ?>
                            <li class="nav-item">
                                <a class="nav-link btn-connexion" href="pageconnexion.php">Connexion</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link btn-inscription" href="inscription.php">Inscription</a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </nav>
    </header>
