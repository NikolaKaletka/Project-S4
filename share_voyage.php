<?php
// Fichier pour gérer le partage de voyages
session_start();
require_once 'utils/utils.php';

// Vérifier si l'utilisateur est connecté
if (!isUserLoggedIn()) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Vous devez être connecté pour partager un voyage.']);
    exit();
}

// Récupérer l'ID de l'utilisateur
$id_utilisateur = $_SESSION['id_utilisateur'];

// Vérifier si la requête est de type POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Méthode non autorisée.']);
    exit();
}

// Récupérer les données du formulaire
$voyage_id = isset($_POST['voyage_id']) ? intval($_POST['voyage_id']) : 0;
$selected_users = isset($_POST['users']) ? $_POST['users'] : [];

// Valider les données
if (!$voyage_id) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'ID de voyage invalide.']);
    exit();
}

// Vérifier si le voyage existe et appartient à l'utilisateur
$voyage = fetchOne("SELECT id_voyage FROM Voyage WHERE id_voyage = ? AND ref_utilisateur = ?", [$voyage_id, $id_utilisateur]);
if (!$voyage) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Vous n\'êtes pas autorisé à partager ce voyage.']);
    exit();
}

try {
    // Commencer une transaction
    $pdo = getDbConnection();
    $pdo->beginTransaction();

    // Supprimer tous les partages existants pour ce voyage
    executeQuery("DELETE FROM voyage_partage WHERE id_voyage = ?", [$voyage_id]);

    // Ajouter les nouveaux partages
    if (!empty($selected_users)) {
        foreach ($selected_users as $user_id) {
            $user_id = intval($user_id);
            if ($user_id > 0 && $user_id != $id_utilisateur) {
                executeQuery("INSERT INTO voyage_partage (id_voyage, id_utilisateur) VALUES (?, ?)", [$voyage_id, $user_id]);
            }
        }
    }

    // Valider la transaction
    $pdo->commit();

    // Retourner une réponse de succès
    header('Content-Type: application/json');
    echo json_encode(['success' => true]);
} catch (Exception $e) {
    // En cas d'erreur, annuler la transaction
    $pdo->rollBack();
    
    // Retourner une réponse d'erreur
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Une erreur est survenue : ' . $e->getMessage()]);
}
?>
