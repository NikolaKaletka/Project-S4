<?php

include 'header.php';
require_once 'utils/utils.php';

// Variables pour les messages
$message = '';
$message_type = '';

// Vérifier si le formulaire a été soumis
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Récupération et nettoyage des données
    $email = trim($_POST["email"]);
    $nouveau_mdp = trim($_POST["nouveau_mdp"]);
    $confirm_mdp = trim($_POST["confirm_mdp"]);

    // Vérifier que les mots de passe correspondent
    if ($nouveau_mdp !== $confirm_mdp) {
        $message = "❌ Les mots de passe ne correspondent pas.";
        $message_type = "error";
    } else {
        // Vérifier si l'email existe
        $user = fetchOne("SELECT id_utilisateur FROM Utilisateur WHERE email = ?", [$email]);

        if ($user) {
            // L'email existe
            $id_utilisateur = $user['id_utilisateur'];

            // Hash du mot de passe
            $mdp_hash = password_hash($nouveau_mdp, PASSWORD_DEFAULT);

            try {
                // Mise à jour du mot de passe
                $rows_affected = update("UPDATE Utilisateur SET mot_de_passe = ? WHERE id_utilisateur = ?", 
                    [$mdp_hash, $id_utilisateur]);

                if ($rows_affected > 0) {
                    $message = "✅ Mot de passe mis à jour avec succès.";
                    $message_type = "success";
                } else {
                    $message = "❓ Aucune modification n'a été effectuée.";
                    $message_type = "warning";
                }
            } catch (Exception $e) {
                $message = "❌ Erreur lors de la mise à jour du mot de passe.";
                $message_type = "error";
            }
        } else {
            $message = "❌ Adresse email non trouvée.";
            $message_type = "error";
        }
    }
}

?>



<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Réinitialisation</title>
    <link rel="stylesheet" href="static/style.css">
</head>
<body>
    <section>
        <img src="static/bg.webp" class="bg">
        <div class="Bienvenue">
            <h2><em>Réinitialisation</em></h2>
            <form action="Réinitialisation.php" method="POST">
                <div class="inputBox">
                    <input type="email" name="email" placeholder="Email" required>
                </div>
                <div class="inputBox">
                    <input type="password" name="nouveau_mdp" placeholder="Nouveau mot de passe" required>
                </div>
                <div class="inputBox">
                    <input type="password" name="confirm_mdp" placeholder="Confirmer votre mot de passe" required>
                </div>
                <div class="inputBox">
                    <input type="submit" value="Enregistrer" id="btn">
                </div>
            </form>
            <?php if (!empty($message)): ?>
                <div class="alert alert-<?php echo $message_type; ?>">
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>
            <div class="groupe">
				<a href="pageconnexion.php" target="_blank">Se connecter</a>
			</div>
        </div>

    </section>
</body>
</html>
