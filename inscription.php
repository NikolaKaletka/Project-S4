<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Connexion à la base de données
$host = '127.0.0.1'; 
$dbname = 'PlanVoyages';
$username = 'root'; 
$password = 'rootroot'; 
include 'header.php';
// Variables pour afficher les messages
$alertMessage = '';
$alertClass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $alertMessage = "✅ Connexion réussie à la base de données !";
    $alertClass = "success";
} catch (PDOException $e) {
    $alertMessage = "Erreur de connexion : " . $e->getMessage();
    $alertClass = "error";
}

// Vérifier si le formulaire a été soumis
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $alertMessage = "⚠️ Formulaire soumis !";
    $alertClass = "info"; // Classe d'information
    
    // Récupérer les données du formulaire
    $nom_utilisateur = trim($_POST['nom_utilisateur']);
    $email = trim($_POST['email']);
    $mot_de_passe = password_hash($_POST['mot_de_passe'], PASSWORD_DEFAULT); // Hachage du mot de passe

    // Vérifier si l'email existe déjà
    $stmt = $pdo->prepare("SELECT id_utilisateur FROM Utilisateur WHERE email = :email");
    $stmt->execute(['email' => $email]);

    if ($stmt->rowCount() > 0) {
        $alertMessage = "";
        $alertClass = "error"; // Classe d'erreur
    } else {
        // Insérer l'utilisateur dans la base de données
        $sql = "INSERT INTO Utilisateur (nom, email, mot_de_passe) VALUES (:nom, :email, :mot_de_passe)";
        $stmt = $pdo->prepare($sql);
        
        // Exécuter la requête
        if ($stmt->execute(['nom' => $nom_utilisateur, 'email' => $email, 'mot_de_passe' => $mot_de_passe])) {
            $alertMessage = "✅ Inscription réussie ! <a href='login.php'>Connectez-vous ici</a>";
            $alertClass = "success"; // Classe de succès
        } else {
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
