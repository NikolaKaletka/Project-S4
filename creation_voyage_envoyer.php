<?php
include 'header.php';
// Inclure le fichier de base de données
require_once 'utils/utils.php';

echo '<div style="padding-top: 80px;"></div>';

// Récupérer l'ID de l'utilisateur
$id_utilisateur = $_SESSION['id_utilisateur'];

// Vérifier si le formulaire a été soumis
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: mesvoyages.php");
    exit();
}

// Récupérer l'ID du voyage s'il existe (pour modification)
$voyage_id = isset($_POST['voyage_id']) ? intval($_POST['voyage_id']) : null;

// Récupérer les informations du voyage
$destination = isset($_POST['voyage_destination']) ? $_POST['voyage_destination'] : "Nouveau voyage";
$date_debut = isset($_POST['voyage_date_debut']) && !empty($_POST['voyage_date_debut']) ? $_POST['voyage_date_debut'] : date('Y-m-d');
$date_fin = isset($_POST['voyage_date_fin']) && !empty($_POST['voyage_date_fin']) ? $_POST['voyage_date_fin'] : date('Y-m-d', strtotime('+7 days'));

// Si pas d'ID de voyage, créer un nouveau voyage
if (!$voyage_id) {
    try {
        // Créer un nouveau voyage
        $voyage_id = insert("INSERT INTO Voyage (destination, date_debut, date_fin, ref_utilisateur) 
                            VALUES (?, ?, ?, ?)", 
                            [$destination, $date_debut, $date_fin, $id_utilisateur]);

        if (!$voyage_id) {
            throw new Exception("Erreur lors de la création du voyage");
        }
    } catch (Exception $e) {
        // Rediriger avec un message d'erreur
        header("Location: mesvoyages.php?error=" . urlencode($e->getMessage()));
        exit();
    }
} else {
    // Mettre à jour les informations du voyage existant
    try {
        executeQuery("UPDATE Voyage SET destination = ?, date_debut = ?, date_fin = ? WHERE id_voyage = ? AND ref_utilisateur = ?", 
                    [$destination, $date_debut, $date_fin, $voyage_id, $id_utilisateur]);
    } catch (Exception $e) {
        // Rediriger avec un message d'erreur
        header("Location: mesvoyages.php?error=" . urlencode($e->getMessage()));
        exit();
    }
}

// Traiter les données de la checklist
if (isset($_POST['checklist']) && is_array($_POST['checklist'])) {
    // Supprimer les anciennes tâches pour ce voyage
    executeQuery("DELETE FROM item_checklist_avant_depart WHERE ref_voyage = ?", [$voyage_id]);

    foreach ($_POST['checklist'] as $item) {
        if (!empty($item['description'])) {
            $est_fait = isset($item['est_fait']) ? 1 : 0;

            insert("INSERT INTO item_checklist_avant_depart (description_tache, est_fait, ref_voyage) 
                   VALUES (?, ?, ?)", 
                   [$item['description'], $est_fait, $voyage_id]);
        }
    }
}

// Traiter les données de transport
if (isset($_POST['transport']) && is_array($_POST['transport'])) {
    // Supprimer les anciens transports pour ce voyage
  executeQuery("DELETE FROM Transport WHERE ref_voyage = ?", [$voyage_id]);

    foreach ($_POST['transport'] as $item) {
        if (!empty($item['type'])) {
            $type = $item['type'];
            $date = !empty($item['date']) ? $item['date'] : null;
            $heure_depart = !empty($item['heure_depart']) ? $item['heure_depart'] : null;
            $heure_arrivee = !empty($item['heure_arrivee']) ? $item['heure_arrivee'] : null;
            $depart = !empty($item['depart']) ? $item['depart'] : null;
            $terminal = !empty($item['terminal']) ? $item['terminal'] : null;
            $bagage = !empty($item['details_bagage']) ? $item['details_bagage'] : null;

            insert("INSERT INTO Transport (type_transport, date_transport, horaire_depart, horaire_arrive, 
                                          place_depart, numero_terminal, bagage, ref_voyage) 
                   VALUES (?, ?, ?, ?, ?, ?, ?, ?)", 
                   [$type, $date, $heure_depart, $heure_arrivee, $depart, $terminal, $bagage, $voyage_id]);
        }
    }
}

// Traiter les données de logement
if (isset($_POST['logement']) && is_array($_POST['logement'])) {
    // Supprimer les anciens logements pour ce voyage
  executeQuery("DELETE FROM Logement WHERE ref_voyage = ?", [$voyage_id]);

    foreach ($_POST['logement'] as $item) {
        if (!empty($item['adresse'])) {
            $nom = !empty($item['nom']) ? $item['nom'] : "Hôtel";
            $adresse = $item['adresse'];
            $checkin = !empty($item['checkin']) ? $item['checkin'] : null;
            $checkout = !empty($item['checkout']) ? $item['checkout'] : null;
            $petit_dejeuner = isset($item['petit_dejeuner']) ? intval($item['petit_dejeuner']) : 0;
            $reservation = !empty($item['reservation']) ? $item['reservation'] : null;

            insert("INSERT INTO Logement (nom, adresse, horaire_check_in, horaire_check_out, 
                                         petit_dejeuner, numero_reservation, ref_voyage) 
                   VALUES (?, ?, ?, ?, ?, ?, ?)", 
                   [$nom, $adresse, $checkin, $checkout, $petit_dejeuner, $reservation, $voyage_id]);
        }
    }
}

// Traiter les données de transport dans la ville
if (isset($_POST['transport_ville']) && is_array($_POST['transport_ville'])) {
    // Supprimer les anciens transports dans la ville pour ce voyage
  executeQuery("DELETE FROM transport_ville WHERE ref_voyage = ?", [$voyage_id]);

    foreach ($_POST['transport_ville'] as $item) {
        if (!empty($item['type']) || !empty($item['type_ticket'])) {
            $type_billet = !empty($item['type_ticket']) ? $item['type_ticket'] : null;
            $prix = !empty($item['prix']) ? $item['prix'] : null;
            $lieu_achat = !empty($item['lieu_achat']) ? $item['lieu_achat'] : null;
            $informations = !empty($item['informations']) ? $item['informations'] : null;

            insert("INSERT INTO transport_ville (type_billet, prix, place_achat_billet, informations, ref_voyage) 
                   VALUES (?, ?, ?, ?, ?)", 
                   [$type_billet, $prix, $lieu_achat, $informations, $voyage_id]);
        }
    }
}

// Traiter les données d'activités
if (isset($_POST['activite']) && is_array($_POST['activite'])) {
    // Supprimer les anciennes activités pour ce voyage
  executeQuery("DELETE FROM Activite WHERE ref_voyage = ?", [$voyage_id]);
  executeQuery("DELETE FROM Ticket_activite WHERE ref_activite IN (SELECT id_activite FROM Activite WHERE ref_voyage = ?)", [$voyage_id]);

    foreach ($_POST['activite'] as $item) {
        if (!empty($item['adresse']) || !empty($item['informations'])) {
            $nom = !empty($item['nom']) ? $item['nom'] : "Activité";
            $adresse = !empty($item['adresse']) ? $item['adresse'] : null;
            $description = !empty($item['informations']) ? $item['informations'] : null;
            $horaire = !empty($item['horaire']) ? $item['horaire'] : null;
            $avec_ticket = isset($item['ticket']) && $item['ticket'] === 'oui' ? 'oui' : 'non';
            $date = !empty($item['date']) ? $item['date'] : null;

            $activite_id = insert("INSERT INTO Activite (nom, adresse, description_activite, horaire, 
                                                       avec_ticket, date_activite, ref_voyage) 
                                  VALUES (?, ?, ?, ?, ?, ?, ?)", 
                                  [$nom, $adresse, $description, $horaire, $avec_ticket, $date, $voyage_id]);

            // Si l'activité a un ticket, ajouter les informations du ticket
            if ($avec_ticket === 'oui' && $activite_id) {
                $ticket_nom = !empty($item['ticket_nom']) ? $item['ticket_nom'] : null;
                $ticket_prix = !empty($item['ticket_prix']) ? $item['ticket_prix'] : null;
                $ticket_lien = !empty($item['ticket_lien']) ? $item['ticket_lien'] : null;

                insert("INSERT INTO Ticket_activite (nom, place_achat_billet, prix, ref_activite) 
                       VALUES (?, ?, ?, ?)", 
                       [$ticket_nom, $ticket_lien, $ticket_prix, $activite_id]);
            }
        }
    }
}

// Traiter les données de restaurants
if (isset($_POST['restaurant']) && is_array($_POST['restaurant'])) {
    // Supprimer les anciens restaurants pour ce voyage
  executeQuery("DELETE FROM Restaurant WHERE ref_voyage = ?", [$voyage_id]);

    foreach ($_POST['restaurant'] as $item) {
        if (!empty($item['adresse']) || !empty($item['type'])) {
            $nom = !empty($item['nom']) ? $item['nom'] : "Restaurant";
            $adresse = !empty($item['adresse']) ? $item['adresse'] : null;
            $type = !empty($item['type']) ? $item['type'] : null;
            $date = !empty($item['date']) ? $item['date'] : null;

            insert("INSERT INTO Restaurant (nom, adresse, type_restaurant, date_restaurant, ref_voyage) 
                   VALUES (?, ?, ?, ?, ?)", 
                   [$nom, $adresse, $type, $date, $voyage_id]);
        }
    }
}

// Rediriger vers la page des voyages avec un message de succès
header("Location: mesvoyages.php?success=1");
exit();
?>
