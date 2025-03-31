<?php
// Connexion à la base de données
$serveur = "127.0.0.1"; 
$utilisateur = "root";  
$motDePasse = "rootroot";  
$baseDeDonnees = "PlanVoyages";

$conn = new mysqli($serveur, $utilisateur, $motDePasse, $baseDeDonnees);

// Vérifier la connexion
if ($conn->connect_error) {
    die("❌ Connexion échouée : " . $conn->connect_error);
}

// Vérifier si le formulaire a été soumis
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST["email"];
    $nouveau_mdp = $_POST["nouveau_mdp"];  // Nouveau mot de passe
    $confirm_mdp = $_POST["confirm_mdp"];  // Confirmation du mot de passe

    // Vérifier que les mots de passe correspondent
    if ($nouveau_mdp !== $confirm_mdp) {
        echo "❌ Les mots de passe ne correspondent pas.";
        exit();  // Arrêter l'exécution si les mots de passe ne correspondent pas
    }

    // Vérifier si l'email existe
    $stmt = $conn->prepare("SELECT id_utilisateur FROM Utilisateur WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        // L'email existe, on peut mettre à jour le mot de passe
        $stmt->bind_result($id_utilisateur);
        $stmt->fetch();
        $stmt->close();

        // Hash du mot de passe pour plus de sécurité
        $mdp_hash = password_hash($nouveau_mdp, PASSWORD_DEFAULT);

        $stmt = $conn->prepare("UPDATE Utilisateur SET mot_de_passe = ? WHERE id_utilisateur = ?");
        $stmt->bind_param("si", $mdp_hash, $id_utilisateur);
        if ($stmt->execute()) {
            echo "✅ Mot de passe mis à jour avec succès.";
        } else {
            echo "❌ Erreur lors de la mise à jour du mot de passe.";
        }
        $stmt->close();
    } else {
        echo "❌ Adresse email non trouvée.";
    }
}

// Fermer la connexion
$conn->close();
?>
``


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Réinitialisation</title>
    <link rel="stylesheet" href="static/style.css">
</head>
<body>
    <section>
        <img src="pagedeconnexion/bg.webp" class="bg">
        <div class="Bienvenue">
            <h2><em>Réinitialisation</em></h2>
            <form action="traitement.php" method="POST">
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
            <div class="groupe">
				<a href="pageconnexion.php" target="_blank">Se connecter</a>
			</div>
        </div>
        
    </section>
</body>
</html>
