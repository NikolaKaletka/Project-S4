<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

include 'header.php';
require_once 'utils/utils.php';

// Variables pour afficher les messages
$alertMessage = '';
$alertClass = '';

// Vérifier si le formulaire a été soumis
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $alertMessage = "⚠️ Formulaire soumis !";
    $alertClass = "info"; // Classe d'information

    // Récupérer les données du formulaire
    $nom_utilisateur = trim($_POST['nom_utilisateur']);
    $email = trim($_POST['email']);
    $mot_de_passe = password_hash($_POST['mot_de_passe'], PASSWORD_DEFAULT); // Hachage du mot de passe

    // Vérifier si l'email existe déjà
    $user_exists = fetchValue("SELECT COUNT(*) FROM Utilisateur WHERE email = ?", [$email]);

    if ($user_exists > 0) {
        $alertMessage = "Cet email est déjà utilisé.";
        $alertClass = "error"; // Classe d'erreur
    } else {
        try {
            // Insérer l'utilisateur dans la base de données
            $user_id = insert("INSERT INTO Utilisateur (nom, email, mot_de_passe) VALUES (?, ?, ?)", 
                [$nom_utilisateur, $email, $mot_de_passe]);

            $alertMessage = "✅ Inscription réussie ! <a href='pageconnexion.php'>Connectez-vous ici</a>";
            $alertClass = "success"; // Classe de succès
        } catch (Exception $e) {
            $alertMessage = "⚠️ Erreur lors de l'inscription.";
            $alertClass = "error"; // Classe d'erreur
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Inscription</title>
    <link rel="stylesheet" href="static/style.css">
</head>
<body>
    <section>
        <img src="static/bg.webp" class="bg">
        <div class="Bienvenue">
            <h2><em>Ton voyage commence ici !</em></h2>
            <form action="inscription.php" method="POST">
                <div class="inputBox">
                    <input type="text" name="nom_utilisateur" placeholder="Nom utilisateur" required>
                </div>
                <div class="inputBox">
                    <input type="email" name="email" placeholder="Email" required>
                </div>
                <div class="inputBox">
                    <input type="password" name="mot_de_passe" placeholder="Mot de passe" required>
                </div>
                <div class="inputBox">
                    <input type="submit" value="Go !" id="btn">
                </div>
            </form>  
            <div class="groupe">
				<a href="pageconnexion.php" target="_blank">Se connecter</a>
			</div>          
        </div>
    </section>
</body>

</html>
