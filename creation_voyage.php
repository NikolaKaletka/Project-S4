<?php
include 'header.php';
// Inclure le fichier de base de données
require_once 'utils/utils.php';

// Récupérer l'ID de l'utilisateur
$id_utilisateur = $_SESSION['id_utilisateur'];

// Récupérer les informations du voyage si un ID est fourni
$voyage = null;
$voyage_id = isset($_GET['id']) ? intval($_GET['id']) : null;

// Variables pour stocker les données des différentes catégories
$checklist_items = [];
$transport_items = [];
$logement_items = [];
$transport_ville_items = [];
$activite_items = [];
$restaurant_items = [];

if ($voyage_id) {
    $voyage = fetchOne("SELECT * FROM Voyage WHERE id_voyage = ? AND ref_utilisateur = ?", [$voyage_id, $id_utilisateur]);
    if (!$voyage) {
        header("Location: mesvoyages.php");
        exit();
    }

    // Récupérer les éléments de la checklist
    $checklist_items = fetchAll("SELECT * FROM ItemChecklistAvantDepart WHERE ref_voyage = ?", [$voyage_id]);

    // Récupérer les transports
    $transport_items = fetchAll("SELECT * FROM Transport WHERE ref_voyage = ?", [$voyage_id]);

    // Récupérer les logements
    $logement_items = fetchAll("SELECT * FROM Logement WHERE ref_voyage = ?", [$voyage_id]);

    // Récupérer les transports dans la ville
    $transport_ville_items = fetchAll("SELECT * FROM TransportVille WHERE ref_voyage = ?", [$voyage_id]);

    // Récupérer les activités
    $activite_items = fetchAll("SELECT a.*, t.nom as ticket_nom, t.prix as ticket_prix, t.place_achat_billet as ticket_lien 
                               FROM Activite a 
                               LEFT JOIN TicketActivite t ON a.id_activite = t.ref_activite 
                               WHERE a.ref_voyage = ?", [$voyage_id]);

    // Récupérer les restaurants
    $restaurant_items = fetchAll("SELECT * FROM Restaurant WHERE ref_voyage = ?", [$voyage_id]);
}

// Définir les variables pour le header
$page_title = $voyage ? "Modifier le voyage" : "Créer un voyage";
$additional_css = ["static/style.css", "static/global.css"];

// Inclure le header

?>
<div style="padding-top: 80px;">
<div class="container py-5">
    <div class="">
        <h1 class="text-center mb-4"><?php echo $voyage ? "Modifier le voyage" : "Nouveau Voyage"; ?></h1>

        <form action="creation_voyage_envoyer.php" method="POST" id="voyageForm">
            <?php if ($voyage_id): ?>
                <input type="hidden" name="voyage_id" value="<?php echo $voyage_id; ?>">
            <?php endif; ?>

            <!-- Section Informations du voyage -->
            <div class="card mb-4">
                <div class="card-header">
                    <h3 class="mb-0">Informations du voyage</h3>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="voyage_destination" class="form-label">Titre du voyage:</label>
                        <input type="text" class="form-control" id="voyage_destination" name="voyage_destination" value="<?php echo $voyage ? htmlspecialchars($voyage['destination']) : ''; ?>" placeholder="Nom de la destination">
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="voyage_date_debut" class="form-label">Date de début:</label>
                            <input type="date" class="form-control" id="voyage_date_debut" name="voyage_date_debut" value="<?php echo $voyage ? $voyage['date_debut'] : ''; ?>">
                        </div>
                        <div class="col-md-6">
                            <label for="voyage_date_fin" class="form-label">Date de fin:</label>
                            <input type="date" class="form-control" id="voyage_date_fin" name="voyage_date_fin" value="<?php echo $voyage ? $voyage['date_fin'] : ''; ?>">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section Checklist -->
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="mb-0">Checklist avant voyage</h3>
                    <button type="button" class="btn btn-sm btn-primary" id="addChecklist">Ajouter une tâche</button>
                </div>
                <div class="card-body" id="checklistContainer">
                    <?php if ($voyage_id && !empty($checklist_items)): ?>
                        <?php foreach ($checklist_items as $index => $item): ?>
                            <div class="checklist-item mb-2">
                                <div class="form-check d-flex align-items-center">
                                    <input type="checkbox" class="form-check-input me-2" id="check<?php echo $index; ?>" 
                                           name="checklist[<?php echo $index; ?>][est_fait]" value="1" 
                                           <?php echo (isset($item['est_fait']) && $item['est_fait']) ? 'checked' : ''; ?>>
                                    <input type="text" class="form-control me-2" 
                                           name="checklist[<?php echo $index; ?>][description]" 
                                           value="<?php echo isset($item['description_tache']) ? htmlspecialchars($item['description_tache']) : ''; ?>" 
                                           placeholder="Description de la tâche">
                                    <button type="button" class="btn btn-sm btn-danger remove-item">Supprimer</button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="empty-state text-center py-4" id="emptyChecklist">
                            <i class="fas fa-clipboard-list fa-3x text-muted mb-3"></i>
                            <h5>Pas de tâches</h5>
                            <p class="text-muted">Cliquez sur "Ajouter une tâche" pour commencer votre checklist</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Section Transport -->
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="mb-0">Transports</h3>
                    <button type="button" class="btn btn-sm btn-primary" id="addTransport">Ajouter un transport</button>
                </div>
                <div class="card-body" id="transportContainer">
                    <?php if ($voyage_id && !empty($transport_items)): ?>
                        <?php foreach ($transport_items as $index => $item): ?>
                            <div class="transport-item mb-4">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h4 class="transport-title"><?php echo htmlspecialchars(isset($item['type_transport']) ? $item['type_transport'] : 'Transport'); ?></h4>
                                    <div>
                                        <button type="button" class="btn btn-sm btn-outline-secondary me-2 rename-transport">Modifier le nom</button>
                                        <button type="button" class="btn btn-sm btn-danger remove-item">Supprimer</button>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="transport_type_<?php echo $index; ?>" class="form-label">Type:</label>
                                    <select class="form-select" id="transport_type_<?php echo $index; ?>" name="transport[<?php echo $index; ?>][type]">
                                        <option value="Avion" <?php echo (isset($item['type_transport']) && $item['type_transport'] == 'Avion') ? 'selected' : ''; ?>>Avion</option>
                                        <option value="Train" <?php echo (isset($item['type_transport']) && $item['type_transport'] == 'Train') ? 'selected' : ''; ?>>Train</option>
                                        <option value="Bus" <?php echo (isset($item['type_transport']) && $item['type_transport'] == 'Bus') ? 'selected' : ''; ?>>Bus</option>
                                        <option value="Bateau" <?php echo (isset($item['type_transport']) && $item['type_transport'] == 'Bateau') ? 'selected' : ''; ?>>Bateau</option>
                                        <option value="Voiture" <?php echo (isset($item['type_transport']) && $item['type_transport'] == 'Voiture') ? 'selected' : ''; ?>>Voiture</option>
                                    </select>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label for="transport_depart_<?php echo $index; ?>" class="form-label">Ville de départ:</label>
                                        <input type="text" class="form-control" id="transport_depart_<?php echo $index; ?>" name="transport[<?php echo $index; ?>][depart]" placeholder="Ville de départ" value="<?php echo htmlspecialchars(isset($item['place_depart']) ? $item['place_depart'] : ''); ?>">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="transport_arrivee_<?php echo $index; ?>" class="form-label">Ville d'arrivée:</label>
                                        <input type="text" class="form-control" id="transport_arrivee_<?php echo $index; ?>" name="transport[<?php echo $index; ?>][arrivee]" placeholder="Ville d'arrivée">
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="transport_date_<?php echo $index; ?>" class="form-label">Date:</label>
                                    <input type="date" class="form-control" id="transport_date_<?php echo $index; ?>" name="transport[<?php echo $index; ?>][date]" value="<?php echo isset($item['date_transport']) ? $item['date_transport'] : ''; ?>">
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label for="transport_hdepart_<?php echo $index; ?>" class="form-label">Horaire de départ:</label>
                                        <input type="time" class="form-control" id="transport_hdepart_<?php echo $index; ?>" name="transport[<?php echo $index; ?>][heure_depart]" value="<?php echo isset($item['horaire_depart']) ? $item['horaire_depart'] : ''; ?>">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="transport_harrivee_<?php echo $index; ?>" class="form-label">Horaire d'arrivée:</label>
                                        <input type="time" class="form-control" id="transport_harrivee_<?php echo $index; ?>" name="transport[<?php echo $index; ?>][heure_arrivee]" value="<?php echo isset($item['horaire_arrive']) ? $item['horaire_arrive'] : ''; ?>">
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Bagages:</label>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="transport[<?php echo $index; ?>][bagage]" id="bagage_oui_<?php echo $index; ?>" value="oui" <?php echo (isset($item['bagage']) && !empty($item['bagage'])) ? 'checked' : ''; ?>>
                                        <label class="form-check-label" for="bagage_oui_<?php echo $index; ?>">Oui</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="transport[<?php echo $index; ?>][bagage]" id="bagage_non_<?php echo $index; ?>" value="non" <?php echo (!isset($item['bagage']) || empty($item['bagage'])) ? 'checked' : ''; ?>>
                                        <label class="form-check-label" for="bagage_non_<?php echo $index; ?>">Non</label>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="transport_details_<?php echo $index; ?>" class="form-label">Détails des bagages:</label>
                                    <input type="text" class="form-control" id="transport_details_<?php echo $index; ?>" name="transport[<?php echo $index; ?>][details_bagage]" placeholder="Taille, nombre de bagages..." value="<?php echo htmlspecialchars(isset($item['bagage']) ? $item['bagage'] : ''); ?>">
                                </div>

                                <div class="mb-3">
                                    <label for="transport_terminal_<?php echo $index; ?>" class="form-label">Numéro de terminal/gare:</label>
                                    <input type="number" class="form-control" id="transport_terminal_<?php echo $index; ?>" name="transport[<?php echo $index; ?>][terminal]" min="0" value="<?php echo isset($item['numero_terminal']) ? $item['numero_terminal'] : ''; ?>">
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="empty-state text-center py-4" id="emptyTransport">
                            <i class="fas fa-plane fa-3x text-muted mb-3"></i>
                            <h5>Pas de transports</h5>
                            <p class="text-muted">Cliquez sur "Ajouter un transport" pour planifier vos déplacements</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Section Hébergement -->
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="mb-0">Hébergements</h3>
                    <button type="button" class="btn btn-sm btn-primary" id="addHebergement">Ajouter un hébergement</button>
                </div>
                <div class="card-body" id="logementContainer">
                    <?php if ($voyage_id && !empty($logement_items)): ?>
                        <?php foreach ($logement_items as $index => $item): ?>
                            <div class="logement-item mb-4">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h4 class="logement-title"><?php echo htmlspecialchars(isset($item['nom']) ? $item['nom'] : 'Hébergement'); ?></h4>
                                    <div>
                                        <button type="button" class="btn btn-sm btn-outline-secondary me-2 rename-logement">Modifier le nom</button>
                                        <button type="button" class="btn btn-sm btn-danger remove-item">Supprimer</button>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label for="logement_debut_<?php echo $index; ?>" class="form-label">Date d'arrivée:</label>
                                        <input type="date" class="form-control" id="logement_debut_<?php echo $index; ?>" name="logement[<?php echo $index; ?>][date_debut]">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="logement_fin_<?php echo $index; ?>" class="form-label">Date de départ:</label>
                                        <input type="date" class="form-control" id="logement_fin_<?php echo $index; ?>" name="logement[<?php echo $index; ?>][date_fin]">
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="logement_adresse_<?php echo $index; ?>" class="form-label">Adresse:</label>
                                    <input type="text" class="form-control" id="logement_adresse_<?php echo $index; ?>" name="logement[<?php echo $index; ?>][adresse]" placeholder="Adresse complète" value="<?php echo htmlspecialchars(isset($item['adresse']) ? $item['adresse'] : ''); ?>">
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label for="logement_checkin_<?php echo $index; ?>" class="form-label">Horaire de check-in:</label>
                                        <input type="time" class="form-control" id="logement_checkin_<?php echo $index; ?>" name="logement[<?php echo $index; ?>][checkin]" value="<?php echo isset($item['horaire_check_in']) ? $item['horaire_check_in'] : ''; ?>">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="logement_checkout_<?php echo $index; ?>" class="form-label">Horaire de check-out:</label>
                                        <input type="time" class="form-control" id="logement_checkout_<?php echo $index; ?>" name="logement[<?php echo $index; ?>][checkout]" value="<?php echo isset($item['horaire_check_out']) ? $item['horaire_check_out'] : ''; ?>">
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="logement_reservation_<?php echo $index; ?>" class="form-label">Numéro de réservation:</label>
                                    <input type="text" class="form-control" id="logement_reservation_<?php echo $index; ?>" name="logement[<?php echo $index; ?>][reservation]" value="<?php echo isset($item['numero_reservation']) ? htmlspecialchars($item['numero_reservation']) : ''; ?>">
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Petit déjeuner:</label>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="logement[<?php echo $index; ?>][petit_dejeuner]" id="pdj_oui_<?php echo $index; ?>" value="1" <?php echo (isset($item['petit_dejeuner']) && $item['petit_dejeuner']) ? 'checked' : ''; ?>>
                                        <label class="form-check-label" for="pdj_oui_<?php echo $index; ?>">Oui</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="logement[<?php echo $index; ?>][petit_dejeuner]" id="pdj_non_<?php echo $index; ?>" value="0" <?php echo (!isset($item['petit_dejeuner']) || !$item['petit_dejeuner']) ? 'checked' : ''; ?>>
                                        <label class="form-check-label" for="pdj_non_<?php echo $index; ?>">Non</label>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="empty-state text-center py-4" id="emptyHebergement">
                            <i class="fas fa-hotel fa-3x text-muted mb-3"></i>
                            <h5>Pas d'hébergements</h5>
                            <p class="text-muted">Cliquez sur "Ajouter un logement" pour enregistrer vos hébergements</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Section Transport urbain -->
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="mb-0">Transports urbain</h3>
                    <button type="button" class="btn btn-sm btn-primary" id="addTransportVille">Ajouter un transport</button>
                </div>
                <div class="card-body" id="transportVilleContainer">
                    <?php if ($voyage_id && !empty($transport_ville_items)): ?>
                        <?php foreach ($transport_ville_items as $index => $item): ?>
                            <div class="transport-ville-item mb-4">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h4 class="transport-ville-title">Transports urbain <?php echo $index + 1; ?></h4>
                                    <div>
                                        <button type="button" class="btn btn-sm btn-outline-secondary me-2 rename-transport-ville">Modifier le nom</button>
                                        <button type="button" class="btn btn-sm btn-danger remove-item">Supprimer</button>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="transport_ville_type_<?php echo $index; ?>" class="form-label">Type de transport:</label>
                                    <input type="text" class="form-control" id="transport_ville_type_<?php echo $index; ?>" name="transport_ville[<?php echo $index; ?>][type]" placeholder="Ex: tram et bus, métro...">
                                </div>

                                <div class="mb-3">
                                    <label for="transport_ville_ticket_<?php echo $index; ?>" class="form-label">Type de ticket:</label>
                                    <input type="text" class="form-control" id="transport_ville_ticket_<?php echo $index; ?>" name="transport_ville[<?php echo $index; ?>][type_ticket]" placeholder="Ex: 1 jour ticket, 90 minutes ticket..." value="<?php echo htmlspecialchars(isset($item['type_billet']) ? $item['type_billet'] : ''); ?>">
                                </div>

                                <div class="mb-3">
                                    <label for="transport_ville_prix_<?php echo $index; ?>" class="form-label">Prix:</label>
                                    <input type="text" class="form-control" id="transport_ville_prix_<?php echo $index; ?>" name="transport_ville[<?php echo $index; ?>][prix]" value="<?php echo isset($item['prix']) ? $item['prix'] : ''; ?>">
                                </div>

                                <div class="mb-3">
                                    <label for="transport_ville_info_<?php echo $index; ?>" class="form-label">Informations:</label>
                                    <input type="text" class="form-control" id="transport_ville_info_<?php echo $index; ?>" name="transport_ville[<?php echo $index; ?>][informations]" placeholder="Ex: ticket pour les 1-3 zones sans aéroport..." value="<?php echo htmlspecialchars(isset($item['informations']) ? $item['informations'] : ''); ?>">
                                </div>

                                <div class="mb-3">
                                    <label for="transport_ville_achat_<?php echo $index; ?>" class="form-label">Lieu d'achat:</label>
                                    <input type="text" class="form-control" id="transport_ville_achat_<?php echo $index; ?>" name="transport_ville[<?php echo $index; ?>][lieu_achat]" value="<?php echo htmlspecialchars(isset($item['place_achat_billet']) ? $item['place_achat_billet'] : ''); ?>">
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="empty-state text-center py-4" id="emptyTransportVille">
                            <i class="fas fa-subway fa-3x text-muted mb-3"></i>
                            <h5>Pas de transports urbain</h5>
                            <p class="text-muted">Cliquez sur "Ajouter un transport" pour planifier vos déplacements en ville</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Section Activités -->
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="mb-0">Activités</h3>
                    <button type="button" class="btn btn-sm btn-primary" id="addActivite">Ajouter une activité</button>
                </div>
                <div class="card-body" id="activiteContainer">
                    <?php if ($voyage_id && !empty($activite_items)): ?>
                        <?php foreach ($activite_items as $index => $item): ?>
                            <div class="activite-item mb-4">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h4 class="activite-title"><?php echo htmlspecialchars(isset($item['nom']) ? $item['nom'] : 'Activité'); ?></h4>
                                    <div>
                                        <button type="button" class="btn btn-sm btn-outline-secondary me-2 rename-activite">Modifier le nom</button>
                                        <button type="button" class="btn btn-sm btn-danger remove-item">Supprimer</button>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="activite_date_<?php echo $index; ?>" class="form-label">Date:</label>
                                    <input type="date" class="form-control" id="activite_date_<?php echo $index; ?>" name="activite[<?php echo $index; ?>][date]" value="<?php echo isset($item['date_activite']) ? $item['date_activite'] : ''; ?>">
                                </div>

                                <div class="mb-3">
                                    <label for="activite_horaire_<?php echo $index; ?>" class="form-label">Horaire:</label>
                                    <input type="time" class="form-control" id="activite_horaire_<?php echo $index; ?>" name="activite[<?php echo $index; ?>][horaire]" value="<?php echo isset($item['horaire']) ? $item['horaire'] : ''; ?>">
                                </div>

                                <div class="mb-3">
                                    <label for="activite_adresse_<?php echo $index; ?>" class="form-label">Adresse:</label>
                                    <input type="text" class="form-control" id="activite_adresse_<?php echo $index; ?>" name="activite[<?php echo $index; ?>][adresse]" placeholder="Adresse de l'activité" value="<?php echo htmlspecialchars(isset($item['adresse']) ? $item['adresse'] : ''); ?>">
                                </div>

                                <div class="mb-3">
                                    <label for="activite_info_<?php echo $index; ?>" class="form-label">Informations:</label>
                                    <input type="text" class="form-control" id="activite_info_<?php echo $index; ?>" name="activite[<?php echo $index; ?>][informations]" placeholder="Ex: Il faut arriver 30 minutes en avance..." value="<?php echo htmlspecialchars(isset($item['description_activite']) ? $item['description_activite'] : ''); ?>">
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Ticket:</label>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="activite[<?php echo $index; ?>][ticket]" id="ticket_oui_<?php echo $index; ?>" value="oui" <?php echo (isset($item['avec_ticket']) && $item['avec_ticket'] === 'oui') ? 'checked' : ''; ?>>
                                        <label class="form-check-label" for="ticket_oui_<?php echo $index; ?>">Oui</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="activite[<?php echo $index; ?>][ticket]" id="ticket_non_<?php echo $index; ?>" value="non" <?php echo (!isset($item['avec_ticket']) || $item['avec_ticket'] !== 'oui') ? 'checked' : ''; ?>>
                                        <label class="form-check-label" for="ticket_non_<?php echo $index; ?>">Non</label>
                                    </div>
                                </div>

                                <div class="ticket-details">
                                    <div class="mb-3">
                                        <label for="activite_ticket_nom_<?php echo $index; ?>" class="form-label">Nom du ticket:</label>
                                        <input type="text" class="form-control" id="activite_ticket_nom_<?php echo $index; ?>" name="activite[<?php echo $index; ?>][ticket_nom]" placeholder="Ex: Ticket jeune 18-26" value="<?php echo htmlspecialchars(isset($item['ticket_nom']) ? $item['ticket_nom'] : ''); ?>">
                                    </div>

                                    <div class="mb-3">
                                        <label for="activite_ticket_prix_<?php echo $index; ?>" class="form-label">Prix du ticket:</label>
                                        <input type="text" class="form-control" id="activite_ticket_prix_<?php echo $index; ?>" name="activite[<?php echo $index; ?>][ticket_prix]" value="<?php echo isset($item['ticket_prix']) ? $item['ticket_prix'] : ''; ?>">
                                    </div>

                                    <div class="mb-3">
                                        <label for="activite_ticket_lien_<?php echo $index; ?>" class="form-label">Lien de réservation:</label>
                                        <input type="text" class="form-control" id="activite_ticket_lien_<?php echo $index; ?>" name="activite[<?php echo $index; ?>][ticket_lien]" value="<?php echo htmlspecialchars(isset($item['ticket_lien']) ? $item['ticket_lien'] : ''); ?>">
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="empty-state text-center py-4" id="emptyActivite">
                            <i class="fas fa-map-marked-alt fa-3x text-muted mb-3"></i>
                            <h5>Pas d'activités</h5>
                            <p class="text-muted">Cliquez sur "Ajouter une activité" pour planifier vos visites et loisirs</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Section Restaurants -->
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="mb-0">Restaurants</h3>
                    <button type="button" class="btn btn-sm btn-primary" id="addRestaurant">Ajouter un restaurant</button>
                </div>
                <div class="card-body" id="restaurantContainer">
                    <?php if ($voyage_id && !empty($restaurant_items)): ?>
                        <?php foreach ($restaurant_items as $index => $item): ?>
                            <div class="restaurant-item mb-4">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h4 class="restaurant-title"><?php echo htmlspecialchars(isset($item['nom']) ? $item['nom'] : 'Restaurant'); ?></h4>
                                    <div>
                                        <button type="button" class="btn btn-sm btn-outline-secondary me-2 rename-restaurant">Modifier le nom</button>
                                        <button type="button" class="btn btn-sm btn-danger remove-item">Supprimer</button>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="restaurant_adresse_<?php echo $index; ?>" class="form-label">Adresse:</label>
                                    <input type="text" class="form-control" id="restaurant_adresse_<?php echo $index; ?>" name="restaurant[<?php echo $index; ?>][adresse]" placeholder="Adresse du restaurant" value="<?php echo htmlspecialchars(isset($item['adresse']) ? $item['adresse'] : ''); ?>">
                                </div>

                                <div class="mb-3">
                                    <label for="restaurant_type_<?php echo $index; ?>" class="form-label">Type:</label>
                                    <input type="text" class="form-control" id="restaurant_type_<?php echo $index; ?>" name="restaurant[<?php echo $index; ?>][type]" placeholder="Ex: Cuisine italienne, fast food..." value="<?php echo htmlspecialchars(isset($item['type_restaurant']) ? $item['type_restaurant'] : ''); ?>">
                                </div>

                                <div class="mb-3">
                                    <label for="restaurant_date_<?php echo $index; ?>" class="form-label">Date:</label>
                                    <input type="date" class="form-control" id="restaurant_date_<?php echo $index; ?>" name="restaurant[<?php echo $index; ?>][date]" value="<?php echo isset($item['date_restaurant']) ? $item['date_restaurant'] : ''; ?>">
                                </div>

                                <div class="mb-3">
                                    <label for="restaurant_horaire_<?php echo $index; ?>" class="form-label">Horaire:</label>
                                    <input type="time" class="form-control" id="restaurant_horaire_<?php echo $index; ?>" name="restaurant[<?php echo $index; ?>][horaire]">
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="empty-state text-center py-4" id="emptyRestaurant">
                            <i class="fas fa-utensils fa-3x text-muted mb-3"></i>
                            <h5>Pas de restaurants</h5>
                            <p class="text-muted">Cliquez sur "Ajouter un restaurant" pour enregistrer vos lieux de restauration</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="d-flex justify-content-between mt-4">
                <a href="mesvoyages.php" class="btn btn-outline-secondary">Annuler</a>
                <button type="submit" class="btn btn-primary">Enregistrer</button>
            </div>
        </form>
    </div>
</div>
</div>

<!-- JavaScript pour rendre le formulaire dynamique -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Compteurs pour les différentes sections
    let checklistCounter = <?php echo !empty($checklist_items) ? count($checklist_items) : 0; ?>;
    let transportCounter = <?php echo !empty($transport_items) ? count($transport_items) : 0; ?>;
    let logementCounter = <?php echo !empty($logement_items) ? count($logement_items) : 0; ?>;
    let transportVilleCounter = <?php echo !empty($transport_ville_items) ? count($transport_ville_items) : 0; ?>;
    let activiteCounter = <?php echo !empty($activite_items) ? count($activite_items) : 0; ?>;
    let restaurantCounter = <?php echo !empty($restaurant_items) ? count($restaurant_items) : 0; ?>;
        // No initialization needed as we're using PHP to render the form elements directly

    // No initialization needed as we're using PHP to render the form elements directly
        <?php if ($voyage_id): ?>
            // Initialiser la checklist
            <?php foreach ($checklist_items as $item): ?>
                const checklistItem = addChecklistItem();
                const descInput = checklistItem.querySelector('input[type="text"]');
                const checkBox = checklistItem.querySelector('input[type="checkbox"]');

                descInput.value = <?php echo json_encode($item['description_tache']); ?>;
                if (<?php echo $item['est_fait'] ? 'true' : 'false'; ?>) {
                    checkBox.checked = true;
                }
            <?php endforeach; ?>

    const transportContainer = document.getElementById('transportContainer');
    const emptyTransport = document.getElementById('emptyTransport');  
            // Initialiser les transports
            <?php foreach ($transport_items as $item): ?>
                (function() {
                    if (emptyTransport) {
                        emptyTransport.style.display = 'none';
                    }

                    const transportItem = document.createElement('div');
                    transportItem.className = 'transport-item mb-4';
                    transportItem.innerHTML = `
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="transport-title"><?php echo htmlspecialchars($item['type_transport']); ?></h4>
                        <div>
                            <button type="button" class="btn btn-sm btn-outline-secondary me-2 rename-transport">Modifier le nom</button>
                            <button type="button" class="btn btn-sm btn-danger remove-item">Supprimer</button>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="transport_type_${transportCounter}" class="form-label">Type:</label>
                        <select class="form-select" id="transport_type_${transportCounter}" name="transport[${transportCounter}][type]">
                            <option value="Avion" ${(<?php echo json_encode($item['type_transport']) ?>) === 'Avion' ? 'selected' : ''}>Avion</option>
                            <option value="Train" ${(<?php echo json_encode($item['type_transport']) ?>) === 'Train' ? 'selected' : ''}>Train</option>
                            <option value="Bus" ${(<?php echo json_encode($item['type_transport']) ?>) === 'Bus' ? 'selected' : ''}>Bus</option>
                            <option value="Bateau" ${(<?php echo json_encode($item['type_transport']) ?>) === 'Bateau' ? 'selected' : ''}>Bateau</option>
                            <option value="Voiture" ${(<?php echo json_encode($item['type_transport']) ?>) === 'Voiture' ? 'selected' : ''}>Voiture</option>
                        </select>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="transport_depart_${transportCounter}" class="form-label">Ville de départ:</label>
                            <input type="text" class="form-control" id="transport_depart_${transportCounter}" name="transport[${transportCounter}][depart]" placeholder="Ville de départ" value="<?php echo htmlspecialchars($item['place_depart'] ?? ''); ?>">
                        </div>
                        <div class="col-md-6">
                            <label for="transport_arrivee_${transportCounter}" class="form-label">Ville d'arrivée:</label>
                            <input type="text" class="form-control" id="transport_arrivee_${transportCounter}" name="transport[${transportCounter}][arrivee]" placeholder="Ville d'arrivée">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="transport_date_${transportCounter}" class="form-label">Date:</label>
                        <input type="date" class="form-control" id="transport_date_${transportCounter}" name="transport[${transportCounter}][date]" value="<?php echo $item['date_transport'] ?? ''; ?>">
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="transport_hdepart_${transportCounter}" class="form-label">Horaire de départ:</label>
                            <input type="time" class="form-control" id="transport_hdepart_${transportCounter}" name="transport[${transportCounter}][heure_depart]" value="<?php echo $item['horaire_depart'] ?? ''; ?>">
                        </div>
                        <div class="col-md-6">
                            <label for="transport_harrivee_${transportCounter}" class="form-label">Horaire d'arrivée:</label>
                            <input type="time" class="form-control" id="transport_harrivee_${transportCounter}" name="transport[${transportCounter}][heure_arrivee]" value="<?php echo $item['horaire_arrive'] ?? ''; ?>">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Bagages:</label>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="transport[${transportCounter}][bagage]" id="bagage_oui_${transportCounter}" value="oui" ${(<?php echo !empty($item['bagage']) ? 'true' : 'false'; ?>) ? 'checked' : ''}>
                            <label class="form-check-label" for="bagage_oui_${transportCounter}">Oui</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="transport[${transportCounter}][bagage]" id="bagage_non_${transportCounter}" value="non" ${(<?php echo empty($item['bagage']) ? 'true' : 'false'; ?>) ? 'checked' : ''}>
                            <label class="form-check-label" for="bagage_non_${transportCounter}">Non</label>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="transport_details_${transportCounter}" class="form-label">Détails des bagages:</label>
                        <input type="text" class="form-control" id="transport_details_${transportCounter}" name="transport[${transportCounter}][details_bagage]" placeholder="Taille, nombre de bagages..." value="<?php echo htmlspecialchars($item['bagage'] ?? ''); ?>">
                    </div>

                    <div class="mb-3">
                        <label for="transport_terminal_${transportCounter}" class="form-label">Numéro de terminal/gare:</label>
                        <input type="number" class="form-control" id="transport_terminal_${transportCounter}" name="transport[${transportCounter}][terminal]" min="0" value="<?php echo $item['numero_terminal'] ?? ''; ?>">
                    </div>
                `;
                transportContainer.appendChild(transportItem);

                // Ajouter les événements
                addRemoveEvent(transportItem.querySelector('.remove-item'));
                addRenameEvent(transportItem.querySelector('.rename-transport'), transportItem.querySelector('.transport-title'));

                transportCounter++;
                })();
            <?php endforeach; ?>

    const logementContainer = document.getElementById('logementContainer');
    const emptyHebergement = document.getElementById('emptyHebergement');

            // Initialiser les logements
            <?php foreach ($logement_items as $item): ?>
                (function() {
                    if (emptyHebergement) {
                        emptyHebergement.style.display = 'none';
                    }

                    const logementItem = document.createElement('div');
                    logementItem.className = 'logement-item mb-4';
                logementItem.innerHTML = `
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="logement-title"><?php echo htmlspecialchars($item['nom']); ?></h4>
                        <div>
                            <button type="button" class="btn btn-sm btn-outline-secondary me-2 rename-logement">Modifier le nom</button>
                            <button type="button" class="btn btn-sm btn-danger remove-item">Supprimer</button>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="logement_debut_${logementCounter}" class="form-label">Date d'arrivée:</label>
                            <input type="date" class="form-control" id="logement_debut_${logementCounter}" name="logement[${logementCounter}][date_debut]">
                        </div>
                        <div class="col-md-6">
                            <label for="logement_fin_${logementCounter}" class="form-label">Date de départ:</label>
                            <input type="date" class="form-control" id="logement_fin_${logementCounter}" name="logement[${logementCounter}][date_fin]">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="logement_adresse_${logementCounter}" class="form-label">Adresse:</label>
                        <input type="text" class="form-control" id="logement_adresse_${logementCounter}" name="logement[${logementCounter}][adresse]" placeholder="Adresse complète" value="<?php echo htmlspecialchars($item['adresse']); ?>">
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="logement_checkin_${logementCounter}" class="form-label">Horaire de check-in:</label>
                            <input type="time" class="form-control" id="logement_checkin_${logementCounter}" name="logement[${logementCounter}][checkin]" value="<?php echo $item['horaire_check_in'] ?? ''; ?>">
                        </div>
                        <div class="col-md-6">
                            <label for="logement_checkout_${logementCounter}" class="form-label">Horaire de check-out:</label>
                            <input type="time" class="form-control" id="logement_checkout_${logementCounter}" name="logement[${logementCounter}][checkout]" value="<?php echo $item['horaire_check_out'] ?? ''; ?>">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="logement_reservation_${logementCounter}" class="form-label">Numéro de réservation:</label>
                        <input type="text" class="form-control" id="logement_reservation_${logementCounter}" name="logement[${logementCounter}][reservation]" value="<?php echo htmlspecialchars($item['numero_reservation'] ?? ''); ?>">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Petit déjeuner:</label>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="logement[${logementCounter}][petit_dejeuner]" id="pdj_oui_${logementCounter}" value="1" ${(<?php echo $item['petit_dejeuner'] ? 'true' : 'false'; ?>) ? 'checked' : ''}>
                            <label class="form-check-label" for="pdj_oui_${logementCounter}">Oui</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="logement[${logementCounter}][petit_dejeuner]" id="pdj_non_${logementCounter}" value="0" ${(<?php echo !$item['petit_dejeuner'] ? 'true' : 'false'; ?>) ? 'checked' : ''}>
                            <label class="form-check-label" for="pdj_non_${logementCounter}">Non</label>
                        </div>
                    </div>
                `;
                logementContainer.appendChild(logementItem);

                // Ajouter les événements
                addRemoveEvent(logementItem.querySelector('.remove-item'));
                addRenameEvent(logementItem.querySelector('.rename-logement'), logementItem.querySelector('.logement-title'));

                logementCounter++;
                })();
            <?php endforeach; ?>

    const transportVilleContainer = document.getElementById('transportVilleContainer');
    const emptyTransportVille = document.getElementById('emptyTransportVille');
    // Initialiser les transports dans la ville
            <?php foreach ($transport_ville_items as $item): ?>
                (function() {
                    if (emptyTransportVille) {
                        emptyTransportVille.style.display = 'none';
                    }

                    const transportVilleItem = document.createElement('div');
                    transportVilleItem.className = 'transport-ville-item mb-4';
                transportVilleItem.innerHTML = `
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="transport-ville-title">Transport urbain ${transportVilleCounter + 1}</h4>
                        <div>
                            <button type="button" class="btn btn-sm btn-outline-secondary me-2 rename-transport-ville">Modifier le nom</button>
                            <button type="button" class="btn btn-sm btn-danger remove-item">Supprimer</button>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="transport_ville_type_${transportVilleCounter}" class="form-label">Type de transport:</label>
                        <input type="text" class="form-control" id="transport_ville_type_${transportVilleCounter}" name="transport_ville[${transportVilleCounter}][type]" placeholder="Ex: tram et bus, métro...">
                    </div>

                    <div class="mb-3">
                        <label for="transport_ville_ticket_${transportVilleCounter}" class="form-label">Type de ticket:</label>
                        <input type="text" class="form-control" id="transport_ville_ticket_${transportVilleCounter}" name="transport_ville[${transportVilleCounter}][type_ticket]" placeholder="Ex: 1 jour ticket, 90 minutes ticket..." value="<?php echo htmlspecialchars($item['type_billet'] ?? ''); ?>">
                    </div>

                    <div class="mb-3">
                        <label for="transport_ville_prix_${transportVilleCounter}" class="form-label">Prix:</label>
                        <input type="text" class="form-control" id="transport_ville_prix_${transportVilleCounter}" name="transport_ville[${transportVilleCounter}][prix]" value="<?php echo $item['prix'] ?? ''; ?>">
                    </div>

                    <div class="mb-3">
                        <label for="transport_ville_info_${transportVilleCounter}" class="form-label">Informations:</label>
                        <input type="text" class="form-control" id="transport_ville_info_${transportVilleCounter}" name="transport_ville[${transportVilleCounter}][informations]" placeholder="Ex: ticket pour les 1-3 zones sans aéroport..." value="<?php echo htmlspecialchars($item['informations'] ?? ''); ?>">
                    </div>

                    <div class="mb-3">
                        <label for="transport_ville_achat_${transportVilleCounter}" class="form-label">Lieu d'achat:</label>
                        <input type="text" class="form-control" id="transport_ville_achat_${transportVilleCounter}" name="transport_ville[${transportVilleCounter}][lieu_achat]" value="<?php echo htmlspecialchars($item['place_achat_billet'] ?? ''); ?>">
                    </div>
                `;
                transportVilleContainer.appendChild(transportVilleItem);

                // Ajouter les événements
                addRemoveEvent(transportVilleItem.querySelector('.remove-item'));
                addRenameEvent(transportVilleItem.querySelector('.rename-transport-ville'), transportVilleItem.querySelector('.transport-ville-title'));

                transportVilleCounter++;
                })();
            <?php endforeach; ?>
    const activiteContainer = document.getElementById('activiteContainer');
    const emptyActivite = document.getElementById('emptyActivite');
            // Initialiser les activités
            <?php foreach ($activite_items as $item): ?>
                (function() {
                    if (emptyActivite) {
                        emptyActivite.style.display = 'none';
                    }

                    const activiteItem = document.createElement('div');
                    activiteItem.className = 'activite-item mb-4';
                activiteItem.innerHTML = `
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="activite-title"><?php echo htmlspecialchars($item['nom']); ?></h4>
                        <div>
                            <button type="button" class="btn btn-sm btn-outline-secondary me-2 rename-activite">Modifier le nom</button>
                            <button type="button" class="btn btn-sm btn-danger remove-item">Supprimer</button>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="activite_date_${activiteCounter}" class="form-label">Date:</label>
                        <input type="date" class="form-control" id="activite_date_${activiteCounter}" name="activite[${activiteCounter}][date]" value="<?php echo $item['date_activite'] ?? ''; ?>">
                    </div>

                    <div class="mb-3">
                        <label for="activite_horaire_${activiteCounter}" class="form-label">Horaire:</label>
                        <input type="time" class="form-control" id="activite_horaire_${activiteCounter}" name="activite[${activiteCounter}][horaire]" value="<?php echo $item['horaire'] ?? ''; ?>">
                    </div>

                    <div class="mb-3">
                        <label for="activite_adresse_${activiteCounter}" class="form-label">Adresse:</label>
                        <input type="text" class="form-control" id="activite_adresse_${activiteCounter}" name="activite[${activiteCounter}][adresse]" placeholder="Adresse de l'activité" value="<?php echo htmlspecialchars($item['adresse'] ?? ''); ?>">
                    </div>

                    <div class="mb-3">
                        <label for="activite_info_${activiteCounter}" class="form-label">Informations:</label>
                        <input type="text" class="form-control" id="activite_info_${activiteCounter}" name="activite[${activiteCounter}][informations]" placeholder="Ex: Il faut arriver 30 minutes en avance..." value="<?php echo htmlspecialchars($item['description_activite'] ?? ''); ?>">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Ticket:</label>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="activite[${activiteCounter}][ticket]" id="ticket_oui_${activiteCounter}" value="oui" ${(<?php echo $item['avec_ticket'] === 'oui' ? 'true' : 'false'; ?>) ? 'checked' : ''}>
                            <label class="form-check-label" for="ticket_oui_${activiteCounter}">Oui</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="activite[${activiteCounter}][ticket]" id="ticket_non_${activiteCounter}" value="non" ${(<?php echo $item['avec_ticket'] !== 'oui' ? 'true' : 'false'; ?>) ? 'checked' : ''}>
                            <label class="form-check-label" for="ticket_non_${activiteCounter}">Non</label>
                        </div>
                    </div>

                    <div class="ticket-details">
                        <div class="mb-3">
                            <label for="activite_ticket_nom_${activiteCounter}" class="form-label">Nom du ticket:</label>
                            <input type="text" class="form-control" id="activite_ticket_nom_${activiteCounter}" name="activite[${activiteCounter}][ticket_nom]" placeholder="Ex: Ticket jeune 18-26" value="<?php echo htmlspecialchars($item['ticket_nom'] ?? ''); ?>">
                        </div>

                        <div class="mb-3">
                            <label for="activite_ticket_prix_${activiteCounter}" class="form-label">Prix du ticket:</label>
                            <input type="text" class="form-control" id="activite_ticket_prix_${activiteCounter}" name="activite[${activiteCounter}][ticket_prix]" value="<?php echo $item['ticket_prix'] ?? ''; ?>">
                        </div>

                        <div class="mb-3">
                            <label for="activite_ticket_lien_${activiteCounter}" class="form-label">Lien de réservation:</label>
                            <input type="text" class="form-control" id="activite_ticket_lien_${activiteCounter}" name="activite[${activiteCounter}][ticket_lien]" value="<?php echo htmlspecialchars($item['ticket_lien'] ?? ''); ?>">
                        </div>
                    </div>
                `;
                activiteContainer.appendChild(activiteItem);

                // Ajouter les événements
                addRemoveEvent(activiteItem.querySelector('.remove-item'));
                addRenameEvent(activiteItem.querySelector('.rename-activite'), activiteItem.querySelector('.activite-title'));

                activiteCounter++;
                })();
            <?php endforeach; ?>
    const restaurantContainer = document.getElementById('restaurantContainer');
    const emptyRestaurant = document.getElementById('emptyRestaurant');
            // Initialiser les restaurants
            <?php foreach ($restaurant_items as $item): ?>
                (function() {
                    if (emptyRestaurant) {
                        emptyRestaurant.style.display = 'none';
                    }

                    const restaurantItem = document.createElement('div');
                    restaurantItem.className = 'restaurant-item mb-4';
                restaurantItem.innerHTML = `
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="restaurant-title"><?php echo htmlspecialchars($item['nom']); ?></h4>
                        <div>
                            <button type="button" class="btn btn-sm btn-outline-secondary me-2 rename-restaurant">Modifier le nom</button>
                            <button type="button" class="btn btn-sm btn-danger remove-item">Supprimer</button>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="restaurant_adresse_${restaurantCounter}" class="form-label">Adresse:</label>
                        <input type="text" class="form-control" id="restaurant_adresse_${restaurantCounter}" name="restaurant[${restaurantCounter}][adresse]" placeholder="Adresse du restaurant" value="<?php echo htmlspecialchars($item['adresse'] ?? ''); ?>">
                    </div>

                    <div class="mb-3">
                        <label for="restaurant_type_${restaurantCounter}" class="form-label">Type:</label>
                        <input type="text" class="form-control" id="restaurant_type_${restaurantCounter}" name="restaurant[${restaurantCounter}][type]" placeholder="Ex: Cuisine italienne, fast food..." value="<?php echo htmlspecialchars($item['type_restaurant'] ?? ''); ?>">
                    </div>

                    <div class="mb-3">
                        <label for="restaurant_date_${restaurantCounter}" class="form-label">Date:</label>
                        <input type="date" class="form-control" id="restaurant_date_${restaurantCounter}" name="restaurant[${restaurantCounter}][date]" value="<?php echo $item['date_restaurant'] ?? ''; ?>">
                    </div>

                    <div class="mb-3">
                        <label for="restaurant_horaire_${restaurantCounter}" class="form-label">Horaire:</label>
                        <input type="time" class="form-control" id="restaurant_horaire_${restaurantCounter}" name="restaurant[${restaurantCounter}][horaire]">
                    </div>
                `;
                restaurantContainer.appendChild(restaurantItem);

                // Ajouter les événements
                addRemoveEvent(restaurantItem.querySelector('.remove-item'));
                addRenameEvent(restaurantItem.querySelector('.rename-restaurant'), restaurantItem.querySelector('.restaurant-title'));

                restaurantCounter++;
                })();
            <?php endforeach; ?>
        <?php endif; ?>


    // Fonction pour initialiser les données existantes si on édite un voyage
    function initExistingData() {
        // Cette fonction est vide car l'initialisation est déjà gérée dans le code PHP ci-dessus
    }

    // Fonction pour ajouter une tâche à la checklist
    function addChecklistItem() {
        // Masquer le message "Pas de tâches"
        const emptyState = document.getElementById('emptyChecklist');
        if (emptyState) {
            emptyState.style.display = 'none';
        }

        const container = document.getElementById('checklistContainer');
        const newItem = document.createElement('div');
        newItem.className = 'checklist-item mb-2';
        newItem.innerHTML = `
            <div class="form-check d-flex align-items-center">
                <input type="checkbox" class="form-check-input me-2" id="check${checklistCounter}" name="checklist[${checklistCounter}][est_fait]" value="1">
                <input type="text" class="form-control me-2" name="checklist[${checklistCounter}][description]" placeholder="Description de la tâche">
                <button type="button" class="btn btn-sm btn-danger remove-item">Supprimer</button>
            </div>
        `;
        container.appendChild(newItem);

        // Ajouter l'événement de suppression
        addRemoveEvent(newItem.querySelector('.remove-item'));

        // Ajouter l'événement d'appui sur Entrée
        const input = newItem.querySelector('input[type="text"]');
        input.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                addChecklistItem();
                // Focus sur le nouvel élément ajouté
                setTimeout(() => {
                    const newInputs = document.querySelectorAll('#checklistContainer input[type="text"]');
                    newInputs[newInputs.length - 1].focus();
                }, 0);
            }
        });

        checklistCounter++;
        return newItem;
    }

    document.getElementById('addChecklist').addEventListener('click', function() {
      console.log("add checklist option");
        addChecklistItem();
    });

    // Fonction pour ajouter un transport
    document.getElementById('addTransport').addEventListener('click', function() {
        // Masquer le message "Pas de transport"
        const emptyState = document.getElementById('emptyTransport');
        if (emptyState) {
            emptyState.style.display = 'none';
        }

        const container = document.getElementById('transportContainer');
        const newItem = document.createElement('div');
        newItem.className = 'transport-item mb-4';
        newItem.innerHTML = `
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h4 class="transport-title">Transport ${transportCounter + 1}</h4>
                <div>
                    <button type="button" class="btn btn-sm btn-outline-secondary me-2 rename-transport">Modifier le nom</button>
                    <button type="button" class="btn btn-sm btn-danger remove-item">Supprimer</button>
                </div>
            </div>

            <div class="mb-3">
                <label for="transport_type_${transportCounter}" class="form-label">Type:</label>
                <select class="form-select" id="transport_type_${transportCounter}" name="transport[${transportCounter}][type]">
                    <option value="Avion">Avion</option>
                    <option value="Train">Train</option>
                    <option value="Bus">Bus</option>
                    <option value="Bateau">Bateau</option>
                    <option value="Voiture">Voiture</option>
                </select>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="transport_depart_${transportCounter}" class="form-label">Ville de départ:</label>
                    <input type="text" class="form-control" id="transport_depart_${transportCounter}" name="transport[${transportCounter}][depart]" placeholder="Ville de départ">
                </div>
                <div class="col-md-6">
                    <label for="transport_arrivee_${transportCounter}" class="form-label">Ville d'arrivée:</label>
                    <input type="text" class="form-control" id="transport_arrivee_${transportCounter}" name="transport[${transportCounter}][arrivee]" placeholder="Ville d'arrivée">
                </div>
            </div>

            <div class="mb-3">
                <label for="transport_date_${transportCounter}" class="form-label">Date:</label>
                <input type="date" class="form-control" id="transport_date_${transportCounter}" name="transport[${transportCounter}][date]">
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="transport_hdepart_${transportCounter}" class="form-label">Horaire de départ:</label>
                    <input type="time" class="form-control" id="transport_hdepart_${transportCounter}" name="transport[${transportCounter}][heure_depart]">
                </div>
                <div class="col-md-6">
                    <label for="transport_harrivee_${transportCounter}" class="form-label">Horaire d'arrivée:</label>
                    <input type="time" class="form-control" id="transport_harrivee_${transportCounter}" name="transport[${transportCounter}][heure_arrivee]">
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Bagages:</label>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="transport[${transportCounter}][bagage]" id="bagage_oui_${transportCounter}" value="oui">
                    <label class="form-check-label" for="bagage_oui_${transportCounter}">Oui</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="transport[${transportCounter}][bagage]" id="bagage_non_${transportCounter}" value="non">
                    <label class="form-check-label" for="bagage_non_${transportCounter}">Non</label>
                </div>
            </div>

            <div class="mb-3">
                <label for="transport_details_${transportCounter}" class="form-label">Détails des bagages:</label>
                <input type="text" class="form-control" id="transport_details_${transportCounter}" name="transport[${transportCounter}][details_bagage]" placeholder="Taille, nombre de bagages...">
            </div>

            <div class="mb-3">
                <label for="transport_terminal_${transportCounter}" class="form-label">Numéro de terminal/gare:</label>
                <input type="number" class="form-control" id="transport_terminal_${transportCounter}" name="transport[${transportCounter}][terminal]" min="0">
            </div>
        `;
        container.appendChild(newItem);
        transportCounter++;

        // Ajouter les événements
        addRemoveEvent(newItem.querySelector('.remove-item'));
        addRenameEvent(newItem.querySelector('.rename-transport'), newItem.querySelector('.transport-title'));
    });

    // Fonction pour ajouter un logement
    document.getElementById('addHebergement').addEventListener('click', function() {
        // Masquer le message "Pas de logement"
        const emptyState = document.getElementById('emptyHebergement');
        if (emptyState) {
            emptyState.style.display = 'none';
        }

        const container = document.getElementById('logementContainer');
        const newItem = document.createElement('div');
        newItem.className = 'logement-item mb-4';
        newItem.innerHTML = `
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h4 class="logement-title">Hôtel ${logementCounter + 1}</h4>
                <div>
                    <button type="button" class="btn btn-sm btn-outline-secondary me-2 rename-logement">Modifier le nom</button>
                    <button type="button" class="btn btn-sm btn-danger remove-item">Supprimer</button>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="logement_debut_${logementCounter}" class="form-label">Date d'arrivée:</label>
                    <input type="date" class="form-control" id="logement_debut_${logementCounter}" name="logement[${logementCounter}][date_debut]">
                </div>
                <div class="col-md-6">
                    <label for="logement_fin_${logementCounter}" class="form-label">Date de départ:</label>
                    <input type="date" class="form-control" id="logement_fin_${logementCounter}" name="logement[${logementCounter}][date_fin]">
                </div>
            </div>

            <div class="mb-3">
                <label for="logement_adresse_${logementCounter}" class="form-label">Adresse:</label>
                <input type="text" class="form-control" id="logement_adresse_${logementCounter}" name="logement[${logementCounter}][adresse]" placeholder="Adresse complète">
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="logement_checkin_${logementCounter}" class="form-label">Horaire de check-in:</label>
                    <input type="time" class="form-control" id="logement_checkin_${logementCounter}" name="logement[${logementCounter}][checkin]">
                </div>
                <div class="col-md-6">
                    <label for="logement_checkout_${logementCounter}" class="form-label">Horaire de check-out:</label>
                    <input type="time" class="form-control" id="logement_checkout_${logementCounter}" name="logement[${logementCounter}][checkout]">
                </div>
            </div>

            <div class="mb-3">
                <label for="logement_reservation_${logementCounter}" class="form-label">Numéro de réservation:</label>
                <input type="text" class="form-control" id="logement_reservation_${logementCounter}" name="logement[${logementCounter}][reservation]">
            </div>

            <div class="mb-3">
                <label class="form-label">Petit déjeuner:</label>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="logement[${logementCounter}][petit_dejeuner]" id="pdj_oui_${logementCounter}" value="1">
                    <label class="form-check-label" for="pdj_oui_${logementCounter}">Oui</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="logement[${logementCounter}][petit_dejeuner]" id="pdj_non_${logementCounter}" value="0">
                    <label class="form-check-label" for="pdj_non_${logementCounter}">Non</label>
                </div>
            </div>
        `;
        container.appendChild(newItem);
        logementCounter++;

        // Ajouter les événements
        addRemoveEvent(newItem.querySelector('.remove-item'));
        addRenameEvent(newItem.querySelector('.rename-logement'), newItem.querySelector('.logement-title'));
    });

    // Fonction pour ajouter un transport dans la ville
    document.getElementById('addTransportVille').addEventListener('click', function() {
        // Masquer le message "Pas de transport urbain"
        const emptyState = document.getElementById('emptyTransportVille');
        if (emptyState) {
            emptyState.style.display = 'none';
        }

        let container = document.getElementById('transportVilleContainer');
        const newItem = document.createElement('div');
        newItem.className = 'transport-ville-item mb-4';
        newItem.innerHTML = `
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h4 class="transport-ville-title">Transport urbain ${transportVilleCounter + 1}</h4>
                <div>
                    <button type="button" class="btn btn-sm btn-outline-secondary me-2 rename-transport-ville">Modifier le nom</button>
                    <button type="button" class="btn btn-sm btn-danger remove-item">Supprimer</button>
                </div>
            </div>

            <div class="mb-3">
                <label for="transport_ville_type_${transportVilleCounter}" class="form-label">Type de transport:</label>
                <input type="text" class="form-control" id="transport_ville_type_${transportVilleCounter}" name="transport_ville[${transportVilleCounter}][type]" placeholder="Ex: tram et bus, métro...">
            </div>

            <div class="mb-3">
                <label for="transport_ville_ticket_${transportVilleCounter}" class="form-label">Type de ticket:</label>
                <input type="text" class="form-control" id="transport_ville_ticket_${transportVilleCounter}" name="transport_ville[${transportVilleCounter}][type_ticket]" placeholder="Ex: 1 jour ticket, 90 minutes ticket...">
            </div>

            <div class="mb-3">
                <label for="transport_ville_prix_${transportVilleCounter}" class="form-label">Prix:</label>
                <input type="text" class="form-control" id="transport_ville_prix_${transportVilleCounter}" name="transport_ville[${transportVilleCounter}][prix]">
            </div>

            <div class="mb-3">
                <label for="transport_ville_info_${transportVilleCounter}" class="form-label">Informations:</label>
                <input type="text" class="form-control" id="transport_ville_info_${transportVilleCounter}" name="transport_ville[${transportVilleCounter}][informations]" placeholder="Ex: ticket pour les 1-3 zones sans aéroport...">
            </div>

            <div class="mb-3">
                <label for="transport_ville_achat_${transportVilleCounter}" class="form-label">Lieu d'achat:</label>
                <input type="text" class="form-control" id="transport_ville_achat_${transportVilleCounter}" name="transport_ville[${transportVilleCounter}][lieu_achat]">
            </div>
        `;
        container.appendChild(newItem);
        transportVilleCounter++;

        // Ajouter les événements
        addRemoveEvent(newItem.querySelector('.remove-item'));
        addRenameEvent(newItem.querySelector('.rename-transport-ville'), newItem.querySelector('.transport-ville-title'));
    });

    // Fonction pour ajouter une activité
    document.getElementById('addActivite').addEventListener('click', function() {
        // Masquer le message "Pas d'activités"
        const emptyState = document.getElementById('emptyActivite');
        if (emptyState) {
            emptyState.style.display = 'none';
        }

        const container = document.getElementById('activiteContainer');
        const newItem = document.createElement('div');
        newItem.className = 'activite-item mb-4';
        newItem.innerHTML = `
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h4 class="activite-title">Activité ${activiteCounter + 1}</h4>
                <div>
                    <button type="button" class="btn btn-sm btn-outline-secondary me-2 rename-activite">Modifier le nom</button>
                    <button type="button" class="btn btn-sm btn-danger remove-item">Supprimer</button>
                </div>
            </div>

            <div class="mb-3">
                <label for="activite_date_${activiteCounter}" class="form-label">Date:</label>
                <input type="date" class="form-control" id="activite_date_${activiteCounter}" name="activite[${activiteCounter}][date]">
            </div>

            <div class="mb-3">
                <label for="activite_horaire_${activiteCounter}" class="form-label">Horaire:</label>
                <input type="time" class="form-control" id="activite_horaire_${activiteCounter}" name="activite[${activiteCounter}][horaire]">
            </div>

            <div class="mb-3">
                <label for="activite_adresse_${activiteCounter}" class="form-label">Adresse:</label>
                <input type="text" class="form-control" id="activite_adresse_${activiteCounter}" name="activite[${activiteCounter}][adresse]" placeholder="Adresse de l'activité">
            </div>

            <div class="mb-3">
                <label for="activite_info_${activiteCounter}" class="form-label">Informations:</label>
                <input type="text" class="form-control" id="activite_info_${activiteCounter}" name="activite[${activiteCounter}][informations]" placeholder="Ex: Il faut arriver 30 minutes en avance...">
            </div>

            <div class="mb-3">
                <label class="form-label">Ticket:</label>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="activite[${activiteCounter}][ticket]" id="ticket_oui_${activiteCounter}" value="oui">
                    <label class="form-check-label" for="ticket_oui_${activiteCounter}">Oui</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="activite[${activiteCounter}][ticket]" id="ticket_non_${activiteCounter}" value="non">
                    <label class="form-check-label" for="ticket_non_${activiteCounter}">Non</label>
                </div>
            </div>

            <div class="ticket-details">
                <div class="mb-3">
                    <label for="activite_ticket_nom_${activiteCounter}" class="form-label">Nom du ticket:</label>
                    <input type="text" class="form-control" id="activite_ticket_nom_${activiteCounter}" name="activite[${activiteCounter}][ticket_nom]" placeholder="Ex: Ticket jeune 18-26">
                </div>

                <div class="mb-3">
                    <label for="activite_ticket_prix_${activiteCounter}" class="form-label">Prix du ticket:</label>
                    <input type="text" class="form-control" id="activite_ticket_prix_${activiteCounter}" name="activite[${activiteCounter}][ticket_prix]">
                </div>

                <div class="mb-3">
                    <label for="activite_ticket_lien_${activiteCounter}" class="form-label">Lien de réservation:</label>
                    <input type="text" class="form-control" id="activite_ticket_lien_${activiteCounter}" name="activite[${activiteCounter}][ticket_lien]">
                </div>
            </div>
        `;
        container.appendChild(newItem);
        activiteCounter++;

        // Ajouter les événements
        addRemoveEvent(newItem.querySelector('.remove-item'));
        addRenameEvent(newItem.querySelector('.rename-activite'), newItem.querySelector('.activite-title'));
    });

    // Fonction pour ajouter un restaurant
    document.getElementById('addRestaurant').addEventListener('click', function() {
        // Masquer le message "Pas de restaurants"
        const emptyState = document.getElementById('emptyRestaurant');
        if (emptyState) {
            emptyState.style.display = 'none';
        }

        const container = document.getElementById('restaurantContainer');
        const newItem = document.createElement('div');
        newItem.className = 'restaurant-item mb-4';
        newItem.innerHTML = `
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h4 class="restaurant-title">Restaurant ${restaurantCounter + 1}</h4>
                <div>
                    <button type="button" class="btn btn-sm btn-outline-secondary me-2 rename-restaurant">Modifier le nom</button>
                    <button type="button" class="btn btn-sm btn-danger remove-item">Supprimer</button>
                </div>
            </div>

            <div class="mb-3">
                <label for="restaurant_adresse_${restaurantCounter}" class="form-label">Adresse:</label>
                <input type="text" class="form-control" id="restaurant_adresse_${restaurantCounter}" name="restaurant[${restaurantCounter}][adresse]" placeholder="Adresse du restaurant">
            </div>

            <div class="mb-3">
                <label for="restaurant_type_${restaurantCounter}" class="form-label">Type:</label>
                <input type="text" class="form-control" id="restaurant_type_${restaurantCounter}" name="restaurant[${restaurantCounter}][type]" placeholder="Ex: Cuisine italienne, fast food...">
            </div>

            <div class="mb-3">
                <label for="restaurant_date_${restaurantCounter}" class="form-label">Date:</label>
                <input type="date" class="form-control" id="restaurant_date_${restaurantCounter}" name="restaurant[${restaurantCounter}][date]">
            </div>

            <div class="mb-3">
                <label for="restaurant_horaire_${restaurantCounter}" class="form-label">Horaire:</label>
                <input type="time" class="form-control" id="restaurant_horaire_${restaurantCounter}" name="restaurant[${restaurantCounter}][horaire]">
            </div>
        `;
        container.appendChild(newItem);
        restaurantCounter++;

        // Ajouter les événements
        addRemoveEvent(newItem.querySelector('.remove-item'));
        addRenameEvent(newItem.querySelector('.rename-restaurant'), newItem.querySelector('.restaurant-title'));
    });

    // Fonction pour ajouter l'événement de suppression
    function addRemoveEvent(button) {
        button.addEventListener('click', function() {
            const item = this.closest('.checklist-item, .transport-item, .logement-item, .transport-ville-item, .activite-item, .restaurant-item');
            const container = item.parentElement;
            item.remove();

            // Vérifier s'il reste des éléments dans le conteneur
            const remainingItems = container.querySelectorAll('.checklist-item, .transport-item, .logement-item, .transport-ville-item, .activite-item, .restaurant-item');
            if (remainingItems.length === 0) {
                // Afficher le message "Pas de..." approprié
                if (container.id === 'checklistContainer') {
                    document.getElementById('emptyChecklist').style.display = 'block';
                } else if (container.id === 'transportContainer') {
                    document.getElementById('emptyTransport').style.display = 'block';
                } else if (container.id === 'logementContainer') {
                    document.getElementById('emptyHebergement').style.display = 'block';
                } else if (container.id === 'transportVilleContainer') {
                    document.getElementById('emptyTransportVille').style.display = 'block';
                } else if (container.id === 'activiteContainer') {
                    document.getElementById('emptyActivite').style.display = 'block';
                } else if (container.id === 'restaurantContainer') {
                    document.getElementById('emptyRestaurant').style.display = 'block';
                }
            }
        });
    }

    // Fonction pour ajouter l'événement de renommage
    function addRenameEvent(button, titleElement) {
        button.addEventListener('click', function() {
            const newName = prompt('Entrez le nouveau nom:', titleElement.textContent);
            if (newName && newName.trim() !== '') {
                titleElement.textContent = newName;
            }
        });
    }

    // Ajouter les événements aux boutons existants
    document.querySelectorAll('.remove-item').forEach(button => {
        addRemoveEvent(button);
    });

    document.querySelectorAll('.rename-transport').forEach(button => {
        const titleElement = button.closest('.transport-item').querySelector('.transport-title');
        addRenameEvent(button, titleElement);
    });

    document.querySelectorAll('.rename-logement').forEach(button => {
        const titleElement = button.closest('.logement-item').querySelector('.logement-title');
        addRenameEvent(button, titleElement);
    });

    document.querySelectorAll('.rename-transport-ville').forEach(button => {
        const titleElement = button.closest('.transport-ville-item').querySelector('.transport-ville-title');
        addRenameEvent(button, titleElement);
    });

    document.querySelectorAll('.rename-activite').forEach(button => {
        const titleElement = button.closest('.activite-item').querySelector('.activite-title');
        addRenameEvent(button, titleElement);
    });

    document.querySelectorAll('.rename-restaurant').forEach(button => {
        const titleElement = button.closest('.restaurant-item').querySelector('.restaurant-title');
        addRenameEvent(button, titleElement);
    });
});
</script>


<?php
// Inclure le footer
include 'footer.php';
?>
