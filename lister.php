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
if ($stmt === false) {
    die("Erreur de préparation de la requête SQL : " . $conn->error);
}
$stmt->bind_param("i", $id_utilisateur);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
} else {
    die("Utilisateur introuvable.");
}

// Récupérer les voyages de l'utilisateur
$sql_voyages = "SELECT destination, date_debut, date_fin FROM Voyage WHERE ref_utilisateur = ?";
$stmt_voyages = $conn->prepare($sql_voyages);

if ($stmt_voyages === false) {
    die("Erreur de préparation de la requête SQL pour les voyages : " . $conn->error);
}

$stmt_voyages->bind_param("i", $id_utilisateur);
$stmt_voyages->execute();
$voyages_result = $stmt_voyages->get_result();

$stmt->close();
$stmt_voyages->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accueil</title>
    <link rel="stylesheet" href="static/profil.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="background"></div>
    <header>
        <nav class="navbar">
            <div class="container-fluid">
                <div class="navbar-collapse">
                    <div class="navbar-menu">
                        <a class="nav-link" href="#">Mes Voyages</a>
                        <a class="nav-link" href="#">Contact</a>
                        <a class="nav-link active" href="logout.php">Déconnexion</a>
                    </div>
                </div>
            </div>
        </nav>
    </header>
    <div class="profile-container">
        <h1>Profil de <?php echo htmlspecialchars($user['nom']); ?></h1>
        <p><strong>Email :</strong> <?php echo htmlspecialchars($user['email']); ?></p>
        
        <h2>Mes Voyages</h2>
        <table class="table">
            <thead>
                <tr>
                    <th>Destination</th>
                    <th>Date de départ</th>
                    <th>Date de retour</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($voyages_result->num_rows > 0) {
                    while ($voyage = $voyages_result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($voyage['destination']) . "</td>";
                        echo "<td>" . htmlspecialchars($voyage['date_debut']) . "</td>";
                        echo "<td>" . htmlspecialchars($voyage['date_fin']) . "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='3'>Aucun voyage trouvé.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</body>
</html>
