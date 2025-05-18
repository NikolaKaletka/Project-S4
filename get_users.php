<?php
// Fichier pour récupérer la liste des utilisateurs pour le partage de voyages
session_start();
require_once 'utils/utils.php';

// Vérifier si l'utilisateur est connecté
if (!isUserLoggedIn()) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Vous devez être connecté pour accéder à cette fonctionnalité.']);
    exit();
}

// Récupérer l'ID de l'utilisateur
$id_utilisateur = $_SESSION['id_utilisateur'];

// Récupérer l'ID du voyage
$voyage_id = isset($_GET['id']) ? intval($_GET['id']) : null;

// Vérifier si le voyage existe et appartient à l'utilisateur ou est partagé avec lui
$voyage = fetchOne("SELECT v.*, 
                   CASE WHEN v.ref_utilisateur = ? THEN 1 ELSE 0 END AS is_owner 
                   FROM Voyage v 
                   LEFT JOIN voyage_partage vp ON v.id_voyage = vp.id_voyage AND vp.id_utilisateur = ?
                   WHERE v.id_voyage = ? AND (v.ref_utilisateur = ? OR vp.id_utilisateur = ?)", 
                   [$id_utilisateur, $id_utilisateur, $voyage_id, $id_utilisateur, $id_utilisateur]);

if (!$voyage) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Voyage non trouvé ou vous n\'avez pas les droits d\'accès.']);
    exit();
}

// Vérifier si l'utilisateur est le propriétaire du voyage
if (!$voyage['is_owner']) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Vous n\'êtes pas autorisé à partager ce voyage.']);
    exit();
}

// Récupérer la liste des utilisateurs (sauf l'utilisateur actuel)
$users = fetchAll("SELECT id_utilisateur, nom, email FROM Utilisateur WHERE id_utilisateur != ? ORDER BY nom", [$id_utilisateur]);

// Récupérer les utilisateurs avec qui le voyage est déjà partagé
$shared_users = fetchAll("SELECT id_utilisateur FROM voyage_partage WHERE id_voyage = ?", [$voyage_id]);
$shared_user_ids = array_column($shared_users, 'id_utilisateur');

// Retourner les données au format JSON
header('Content-Type: application/json');
echo json_encode([
    'success' => true,
    'users' => $users,
    'shared_users' => $shared_user_ids
]);
