<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Vérifier si l'utilisateur est bien connecté
if (!isset($_SESSION['id_utilisateur'])) {
    die("Erreur : utilisateur non connecté. <a href='page_connexion.php'>Se connecter</a>");
}

// Connexion à la base de données
$serveur = "127.0.0.1";
$utilisateur = "root";
$motDePasse = "rootroot";
$baseDeDonnees = "PlanVoyages";

$conn = new mysqli($serveur, $utilisateur, $motDePasse, $baseDeDonnees);
if ($conn->connect_error) {
    die("Connexion échouée : " . $conn->connect_error);
}

$id_utilisateur = $_SESSION['id_utilisateur'];

// Récupérer les informations de l'utilisateur
$sql = "SELECT nom, email FROM Utilisateur WHERE id_utilisateur = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_utilisateur);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
} else {
    die("Utilisateur introuvable.");
}
$stmt->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>accueil</title>
    <link rel="stylesheet" href="pagedeconnexion/profil.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="background"></div>
    <header>
        <nav class="navbar">
            <div class="container-fluid">
                <div class="navbar-collapse">
                    <div class="navbar-menu">
                        
                        <a class="nav-link" href="lister.php">Mes Voyages</a>
                        <a class="nav-link" href="#">Contact</a>
                        <a class="nav-link" href="map.php">Map</a>
                        <a class="nav-link active" href="logout.php">Déconnexion</a>
                    </div>
                </div>
            </div>
        </nav>
    </header>
    <div class="profile-container">
        <h1>Profil de <?php echo htmlspecialchars($user['nom']); ?></h1>
        <p><strong>Email :</strong> <?php echo htmlspecialchars($user['email']); ?></p>
        
    </div>
</body>
</html>