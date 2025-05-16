<?php

// Liste des pages qui ne nécessitent pas de connexion
$pages_sans_connexion = [
  'pageconnexion.php',
  'Réinitialisation.php',
  'map.php',
  'destination.php',
  'inscription.php'
];

// Obtenir le nom du script actuel
$script_name = basename($_SERVER['SCRIPT_NAME']);

// Vérifier si l'utilisateur est connecté et si la page actuelle nécessite une connexion
if (!isUserLoggedIn() && !in_array($script_name, $pages_sans_connexion)) {
  include 'header.php';

  echo '<div class="container mt-5 pt-5">';
  echo generateAlert("Vous devez être connecté pour accéder à cette page.", "warning");
  echo '<div class="text-center"><a href="pageconnexion.php" class="btn btn-primary">Se connecter</a></div>';
  echo '</div>';

  // Inclure le footer
  $additional_js = [];
  include 'footer.php';
  exit();
}

// Récupérer les informations de l'utilisateur seulement s'il est connecté
if (isUserLoggedIn()) {
  // Récupérer l'ID de l'utilisateur
  $id_utilisateur = $_SESSION['id_utilisateur'];

  // Récupérer les informations de l'utilisateur
  $user = fetchOne("SELECT nom, email FROM Utilisateur WHERE id_utilisateur = ?", [$id_utilisateur]);

  if (!$user) {
    include 'header.php';

    echo '<div class="container mt-5 pt-5">';
    echo generateAlert("Utilisateur introuvable.", "danger");
    echo '</div>';

    // Inclure le footer
    $additional_js = [];
    include 'footer.php';
    exit();
  }
}

?>
