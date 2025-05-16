<?php

include 'header.php';
require_once 'utils/utils.php';

// Vérifier si le formulaire a été soumis
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST["email"];
    $mot_de_passe = $_POST["mot_de_passe"];

    // Vérifier l'utilisateur dans la base de données
    $user = fetchOne("SELECT id_utilisateur, nom, mot_de_passe FROM Utilisateur WHERE email = ?", [$email]);

    if ($user) {
        // Vérifier le mot de passe
        if (password_verify($mot_de_passe, $user['mot_de_passe'])) {
            // Stocker les informations de l'utilisateur en session
            $_SESSION['id_utilisateur'] = $user['id_utilisateur'];
            $_SESSION['nom_utilisateur'] = $user['nom'];
            $_SESSION['email'] = $email;

            // Rediriger vers la page de profil
            header("Location: mesvoyages.php");
            exit();
        } else {
            $erreur = "Mot de passe incorrect.";
        }
    } else {
        $erreur = "Email non trouvé.";
    }
}
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
        <img src="static/bg.webp" class="bg">

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
