<?php
// Activer le mode erreur pour voir les problèmes
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Connexion à la base de données
$serveur = "127.0.0.1";
$utilisateur = "root";
$motDePasse = "rootroot";
$baseDeDonnees = "PlanVoyages";

$conn = new mysqli($serveur, $utilisateur, $motDePasse, $baseDeDonnees);
if ($conn->connect_error) {
    die(json_encode(["status" => "error", "message" => "Connexion échouée : " . $conn->connect_error]));
}

// Vérifier si on reçoit bien des données
$data = json_decode(file_get_contents("php://input"), true);

if (!$data || !isset($data['query']) || !isset($data['lat']) || !isset($data['lon'])) {
    die(json_encode(["status" => "error", "message" => "Données invalides reçues"]));
}

$query = $data['query'];
$lat = $data['lat'];
$lon = $data['lon'];

// Insérer dans la base de données
$sql = "INSERT INTO Recherches (lieu, latitude, longitude) VALUES (?, ?, ?)";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    die(json_encode(["status" => "error", "message" => "Erreur SQL : " . $conn->error]));
}

$stmt->bind_param("sdd", $query, $lat, $lon);

if ($stmt->execute()) {
    echo json_encode(["status" => "success", "message" => "Donnée enregistrée"]);
} else {
    echo json_encode(["status" => "error", "message" => "Erreur lors de l'insertion : " . $stmt->error]);
}

$stmt->close();
$conn->close();
?>
