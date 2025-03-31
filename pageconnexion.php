<?php
session_start();

// Connexion à la base de données
$serveur = "127.0.0.1";
$utilisateur = "root";
$motDePasse = "rootroot";
$baseDeDonnees = "PlanVoyages";

$conn = new mysqli($serveur, $utilisateur, $motDePasse, $baseDeDonnees);
if ($conn->connect_error) {
    die("Connexion échouée : " . $conn->connect_error);
}

// Vérifier si le formulaire a été soumis
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST["email"];
    $mot_de_passe = $_POST["mot_de_passe"];

    // Vérifier l'utilisateur dans la base de données
    $stmt = $conn->prepare("SELECT id_utilisateur, nom, mot_de_passe FROM Utilisateur WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id_utilisateur, $nom, $hashed_password);
        $stmt->fetch();

        // Vérifier le mot de passe
        if (password_verify($mot_de_passe, $hashed_password)) {
            // Stocker les informations de l'utilisateur en session
            $_SESSION['id_utilisateur'] = $id_utilisateur;
            $_SESSION['nom_utilisateur'] = $nom;
            $_SESSION['email'] = $email;

            // Rediriger vers la page de profil
            header("Location: profil.php");
            exit();
        } else {
            $erreur = "Mot de passe incorrect.";
        }
    } else {
        $erreur = "Email non trouvé.";
    }
    $stmt->close();
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion</title>
    <link rel="stylesheet" href="static/style.css">
</head>
<body>
    <section>
        <img src="pagedeconnexion/bg.webp" class="bg">
        <div class="Bienvenue">
            <h2> <em>Accéder à mon voyage</em> </h2>
            <form action="" method="POST">
                <div class="inputBox">
                    <input type="email" name="email" placeholder="Email" required>
                </div>
                <div class="inputBox">
                    <input type="password" name="mot_de_passe" placeholder="Mot de passe" required>
                </div>
                <div class="inputBox">
                    <input type="submit" value="Connecter" id="btn">
                </div>
                <div class="groupe">
                    <a href="Réinitialisation.php" target="_blank">Mot de passe oublié</a>
                    <a href="inscription.php" target="_blank">S'inscrire</a>
                </div>
            </form>
            <?php if (isset($erreur)) { echo "<p style='color: red;'>$erreur</p>"; } ?>
        </div>
    </section>
</body>
</html>
