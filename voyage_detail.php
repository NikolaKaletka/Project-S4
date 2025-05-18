<?php
// Démarrer la session si elle n'est pas déjà démarrée
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Définir les variables pour le header
$page_title = "Détails du Voyage";
$additional_css = ["static/style.css", "static/global.css"];

// Inclure le fichier de base de données
require_once 'utils/utils.php';

// Vérifier si l'utilisateur est connecté
if (!isUserLoggedIn()) {
    // L'utilisateur n'est pas connecté, rediriger vers la page de connexion
    header("Location: pageconnexion.php");
    exit();
}

// Récupérer l'ID de l'utilisateur
$id_utilisateur = $_SESSION['id_utilisateur'];

// Récupérer l'ID du voyage
$voyage_id = isset($_GET['id']) ? intval($_GET['id']) : null;

// Vérifier si le voyage existe et appartient à l'utilisateur
if (!$voyage_id) {
    header("Location: mesvoyages.php");
    exit();
}

// Inclure le header
include 'header.php';

// Vérifier si le voyage existe et appartient à l'utilisateur ou est partagé avec lui
$voyage = fetchOne("SELECT v.*, 
                   CASE WHEN v.ref_utilisateur = ? THEN 1 ELSE 0 END AS is_owner 
                   FROM Voyage v 
                   LEFT JOIN voyage_partage vp ON v.id_voyage = vp.id_voyage AND vp.id_utilisateur = ?
                   WHERE v.id_voyage = ? AND (v.ref_utilisateur = ? OR vp.id_utilisateur = ?)", 
                   [$id_utilisateur, $id_utilisateur, $voyage_id, $id_utilisateur, $id_utilisateur]);

if (!$voyage) {
    header("Location: mesvoyages.php");
    exit();
}

// Récupérer les éléments de la checklist
$checklist_items = fetchAll("SELECT * FROM item_checklist_avant_depart WHERE ref_voyage = ?", [$voyage_id]);

// Récupérer les transports
$transport_items = fetchAll("SELECT * FROM Transport WHERE ref_voyage = ?", [$voyage_id]);

// Récupérer les logements
$logement_items = fetchAll("SELECT * FROM Logement WHERE ref_voyage = ?", [$voyage_id]);

// Récupérer les transports dans la ville
$transport_ville_items = fetchAll("SELECT * FROM transport_ville WHERE ref_voyage = ?", [$voyage_id]);

// Récupérer les activités
$activite_items = fetchAll("SELECT a.*, t.nom as ticket_nom, t.prix as ticket_prix, t.place_achat_billet as ticket_lien 
                           FROM Activite a 
                           LEFT JOIN ticket_activite t ON a.id_activite = t.ref_activite 
                           WHERE a.ref_voyage = ?", [$voyage_id]);

// Récupérer les restaurants
$restaurant_items = fetchAll("SELECT * FROM Restaurant WHERE ref_voyage = ?", [$voyage_id]);

// Formater une date
function formatDateFr($date) {
    if (empty($date)) return '';
    $timestamp = strtotime($date);
    return date('d/m/Y', $timestamp);
}

// Formater une heure
function formatHeure($heure) {
    if (empty($heure)) return '';
    return substr($heure, 0, 5); // Retourne seulement HH:MM
}

// Calculer la durée du voyage
$date_debut = new DateTime($voyage['date_debut']);
$date_fin = new DateTime($voyage['date_fin']);
$interval = $date_debut->diff($date_fin);
$duree_voyage = $interval->days + 1; // +1 pour inclure le jour de départ
?>

<div style="padding-top: 80px;">
<div class="py-5">
    <div style="margin: 0 auto; max-width: 1200px;">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1><?php echo htmlspecialchars($voyage['destination']); ?></h1>
            <a href="mesvoyages.php" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Retour
            </a>
        </div>

        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card h-100">
                    <div class="card-body">
                        <h5 class="card-title">Informations générales</h5>
                        <div class="info-item">
                            <i class="fas fa-calendar-alt"></i>
                            <div>
                                <span class="info-label">Dates</span>
                                <span class="info-value">Du <?php echo formatDateFr($voyage['date_debut']); ?> au <?php echo formatDateFr($voyage['date_fin']); ?></span>
                            </div>
                        </div>
                        <div class="info-item">
                            <i class="fas fa-clock"></i>
                            <div>
                                <span class="info-label">Durée</span>
                                <span class="info-value"><?php echo $duree_voyage; ?> jours</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card h-100">
                    <div class="card-body">
                        <h5 class="card-title">Actions</h5>
                        <div class="d-flex gap-2">
                            <a href="creation_voyage.php?id=<?php echo $voyage_id; ?>" class="btn btn-primary">
                                <i class="fas fa-edit"></i> Modifier
                            </a>
                            <?php if ($voyage['is_owner']): ?>
                            <a href="#" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#shareModal">
                                <i class="fas fa-share-alt"></i> Partager
                            </a>
                            <?php endif; ?>
                            <a href="#" class="btn btn-outline-primary" onclick="window.print()">
                                <i class="fas fa-print"></i> Imprimer
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Checklist avant départ -->
        <?php if (!empty($checklist_items)): ?>
        <div class="detail-section">
            <h2 class="section-title">
                <i class="fas fa-clipboard-list"></i> Checklist avant départ
            </h2>
            <div class="card">
                <div class="card-body">
                    <ul class="checklist">
                        <?php foreach ($checklist_items as $item): ?>
                            <li class="<?php echo $item['est_fait'] ? 'completed' : ''; ?>">
                                <i class="<?php echo $item['est_fait'] ? 'fas fa-check-circle' : 'far fa-circle'; ?>"></i>
                                <span><?php echo htmlspecialchars($item['description_tache']); ?></span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Transports -->
        <?php if (!empty($transport_items)): ?>
        <div class="detail-section">
            <h2 class="section-title">
                <i class="fas fa-plane"></i> Transports
            </h2>
            <?php foreach ($transport_items as $item): ?>
                <div class="card mb-3">
                    <div class="card-header">
                        <h3 class="mb-0"><?php echo htmlspecialchars($item['type_transport']); ?></h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="info-item">
                                    <i class="fas fa-map-marker-alt"></i>
                                    <div>
                                        <span class="info-label">Lieu de départ</span>
                                        <span class="info-value"><?php echo htmlspecialchars($item['place_depart'] ?? 'Non spécifié'); ?></span>
                                    </div>
                                </div>
                                <div class="info-item">
                                    <i class="fas fa-calendar-day"></i>
                                    <div>
                                        <span class="info-label">Date</span>
                                        <span class="info-value"><?php echo formatDateFr($item['date_transport']); ?></span>
                                    </div>
                                </div>
                                <div class="info-item">
                                    <i class="fas fa-clock"></i>
                                    <div>
                                        <span class="info-label">Horaire de départ</span>
                                        <span class="info-value"><?php echo formatHeure($item['horaire_depart']); ?></span>
                                    </div>
                                </div>
                                <?php if (!empty($item['numero_terminal'])): ?>
                                <div class="info-item">
                                    <i class="fas fa-building"></i>
                                    <div>
                                        <span class="info-label">Terminal/Gare</span>
                                        <span class="info-value"><?php echo htmlspecialchars($item['numero_terminal']); ?></span>
                                    </div>
                                </div>
                                <?php endif; ?>
                            </div>
                            <div class="col-md-6">
                                <?php if (!empty($item['horaire_arrive'])): ?>
                                <div class="info-item">
                                    <i class="fas fa-clock"></i>
                                    <div>
                                        <span class="info-label">Horaire d'arrivée</span>
                                        <span class="info-value"><?php echo formatHeure($item['horaire_arrive']); ?></span>
                                    </div>
                                </div>
                                <?php endif; ?>
                                <?php if (!empty($item['bagage'])): ?>
                                <div class="info-item">
                                    <i class="fas fa-suitcase"></i>
                                    <div>
                                        <span class="info-label">Bagages</span>
                                        <span class="info-value"><?php echo htmlspecialchars($item['bagage']); ?></span>
                                    </div>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <!-- Hébergements -->
        <?php if (!empty($logement_items)): ?>
        <div class="detail-section">
            <h2 class="section-title">
                <i class="fas fa-hotel"></i> Hébergements
            </h2>
            <?php foreach ($logement_items as $item): ?>
                <div class="card mb-3">
                    <div class="card-header">
                        <h3 class="mb-0"><?php echo htmlspecialchars($item['nom']); ?></h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="info-item">
                                    <i class="fas fa-map-marker-alt"></i>
                                    <div>
                                        <span class="info-label">Adresse</span>
                                        <span class="info-value"><?php echo htmlspecialchars($item['adresse']); ?></span>
                                    </div>
                                </div>
                                <?php if (!empty($item['horaire_check_in'])): ?>
                                <div class="info-item">
                                    <i class="fas fa-sign-in-alt"></i>
                                    <div>
                                        <span class="info-label">Check-in</span>
                                        <span class="info-value"><?php echo formatHeure($item['horaire_check_in']); ?></span>
                                    </div>
                                </div>
                                <?php endif; ?>
                            </div>
                            <div class="col-md-6">
                                <?php if (!empty($item['horaire_check_out'])): ?>
                                <div class="info-item">
                                    <i class="fas fa-sign-out-alt"></i>
                                    <div>
                                        <span class="info-label">Check-out</span>
                                        <span class="info-value"><?php echo formatHeure($item['horaire_check_out']); ?></span>
                                    </div>
                                </div>
                                <?php endif; ?>
                                <?php if (!empty($item['numero_reservation'])): ?>
                                <div class="info-item">
                                    <i class="fas fa-receipt"></i>
                                    <div>
                                        <span class="info-label">Numéro de réservation</span>
                                        <span class="info-value"><?php echo htmlspecialchars($item['numero_reservation']); ?></span>
                                    </div>
                                </div>
                                <?php endif; ?>
                                <div class="info-item">
                                    <i class="fas fa-coffee"></i>
                                    <div>
                                        <span class="info-label">Petit déjeuner</span>
                                        <span class="info-value"><?php echo $item['petit_dejeuner'] ? 'Inclus' : 'Non inclus'; ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <!-- Transports urbains -->
        <?php if (!empty($transport_ville_items)): ?>
        <div class="detail-section">
            <h2 class="section-title">
                <i class="fas fa-subway"></i> Transports urbains
            </h2>
            <?php foreach ($transport_ville_items as $item): ?>
                <div class="card mb-3">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <?php if (!empty($item['type_billet'])): ?>
                                <div class="info-item">
                                    <i class="fas fa-ticket-alt"></i>
                                    <div>
                                        <span class="info-label">Type de billet</span>
                                        <span class="info-value"><?php echo htmlspecialchars($item['type_billet']); ?></span>
                                    </div>
                                </div>
                                <?php endif; ?>
                                <?php if (!empty($item['prix'])): ?>
                                <div class="info-item">
                                    <i class="fas fa-euro-sign"></i>
                                    <div>
                                        <span class="info-label">Prix</span>
                                        <span class="info-value"><?php echo htmlspecialchars($item['prix']); ?> €</span>
                                    </div>
                                </div>
                                <?php endif; ?>
                            </div>
                            <div class="col-md-6">
                                <?php if (!empty($item['place_achat_billet'])): ?>
                                <div class="info-item">
                                    <i class="fas fa-store"></i>
                                    <div>
                                        <span class="info-label">Lieu d'achat</span>
                                        <span class="info-value"><?php echo htmlspecialchars($item['place_achat_billet']); ?></span>
                                    </div>
                                </div>
                                <?php endif; ?>
                                <?php if (!empty($item['informations'])): ?>
                                <div class="info-item">
                                    <i class="fas fa-info-circle"></i>
                                    <div>
                                        <span class="info-label">Informations</span>
                                        <span class="info-value"><?php echo htmlspecialchars($item['informations']); ?></span>
                                    </div>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <!-- Activités -->
        <?php if (!empty($activite_items)): ?>
        <div class="detail-section">
            <h2 class="section-title">
                <i class="fas fa-map-marked-alt"></i> Activités
            </h2>
            <?php foreach ($activite_items as $item): ?>
                <div class="card mb-3">
                    <div class="card-header">
                        <h3 class="mb-0"><?php echo htmlspecialchars($item['nom']); ?></h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <?php if (!empty($item['date_activite'])): ?>
                                <div class="info-item">
                                    <i class="fas fa-calendar-day"></i>
                                    <div>
                                        <span class="info-label">Date</span>
                                        <span class="info-value"><?php echo formatDateFr($item['date_activite']); ?></span>
                                    </div>
                                </div>
                                <?php endif; ?>
                                <?php if (!empty($item['horaire'])): ?>
                                <div class="info-item">
                                    <i class="fas fa-clock"></i>
                                    <div>
                                        <span class="info-label">Horaire</span>
                                        <span class="info-value"><?php echo formatHeure($item['horaire']); ?></span>
                                    </div>
                                </div>
                                <?php endif; ?>
                                <?php if (!empty($item['adresse'])): ?>
                                <div class="info-item">
                                    <i class="fas fa-map-marker-alt"></i>
                                    <div>
                                        <span class="info-label">Adresse</span>
                                        <span class="info-value"><?php echo htmlspecialchars($item['adresse']); ?></span>
                                    </div>
                                </div>
                                <?php endif; ?>
                            </div>
                            <div class="col-md-6">
                                <?php if (!empty($item['description_activite'])): ?>
                                <div class="info-item">
                                    <i class="fas fa-info-circle"></i>
                                    <div>
                                        <span class="info-label">Informations</span>
                                        <span class="info-value"><?php echo htmlspecialchars($item['description_activite']); ?></span>
                                    </div>
                                </div>
                                <?php endif; ?>

                                <?php if ($item['avec_ticket'] === 'oui'): ?>
                                <div class="ticket-info mt-3">
                                    <h5><i class="fas fa-ticket-alt"></i> Informations sur le billet</h5>
                                    <?php if (!empty($item['ticket_nom'])): ?>
                                    <div class="info-item">
                                        <i class="fas fa-tag"></i>
                                        <div>
                                            <span class="info-label">Type de billet</span>
                                            <span class="info-value"><?php echo htmlspecialchars($item['ticket_nom']); ?></span>
                                        </div>
                                    </div>
                                    <?php endif; ?>
                                    <?php if (!empty($item['ticket_prix'])): ?>
                                    <div class="info-item">
                                        <i class="fas fa-euro-sign"></i>
                                        <div>
                                            <span class="info-label">Prix</span>
                                            <span class="info-value"><?php echo htmlspecialchars($item['ticket_prix']); ?> €</span>
                                        </div>
                                    </div>
                                    <?php endif; ?>
                                    <?php if (!empty($item['ticket_lien'])): ?>
                                    <div class="info-item">
                                        <i class="fas fa-link"></i>
                                        <div>
                                            <span class="info-label">Lien de réservation</span>
                                            <span class="info-value">
                                                <a href="<?php echo htmlspecialchars($item['ticket_lien']); ?>" target="_blank">
                                                    <?php echo htmlspecialchars($item['ticket_lien']); ?>
                                                </a>
                                            </span>
                                        </div>
                                    </div>
                                    <?php endif; ?>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <!-- Restaurants -->
        <?php if (!empty($restaurant_items)): ?>
        <div class="detail-section">
            <h2 class="section-title">
                <i class="fas fa-utensils"></i> Restaurants
            </h2>
            <?php foreach ($restaurant_items as $item): ?>
                <div class="card mb-3">
                    <div class="card-header">
                        <h3 class="mb-0"><?php echo htmlspecialchars($item['nom']); ?></h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <?php if (!empty($item['adresse'])): ?>
                                <div class="info-item">
                                    <i class="fas fa-map-marker-alt"></i>
                                    <div>
                                        <span class="info-label">Adresse</span>
                                        <span class="info-value"><?php echo htmlspecialchars($item['adresse']); ?></span>
                                    </div>
                                </div>
                                <?php endif; ?>
                                <?php if (!empty($item['type_restaurant'])): ?>
                                <div class="info-item">
                                    <i class="fas fa-utensils"></i>
                                    <div>
                                        <span class="info-label">Type de cuisine</span>
                                        <span class="info-value"><?php echo htmlspecialchars($item['type_restaurant']); ?></span>
                                    </div>
                                </div>
                                <?php endif; ?>
                            </div>
                            <div class="col-md-6">
                                <?php if (!empty($item['date_restaurant'])): ?>
                                <div class="info-item">
                                    <i class="fas fa-calendar-day"></i>
                                    <div>
                                        <span class="info-label">Date</span>
                                        <span class="info-value"><?php echo formatDateFr($item['date_restaurant']); ?></span>
                                    </div>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</div>
</div>

<!-- Modal de partage -->
<?php if ($voyage['is_owner']): ?>
<div class="modal fade" id="shareModal" tabindex="-1" aria-labelledby="shareModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="shareModalLabel">Partager le voyage</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Sélectionnez les utilisateurs avec qui vous souhaitez partager ce voyage :</p>
                <form id="shareForm">
                    <input type="hidden" name="voyage_id" id="share_voyage_id" value="<?php echo $voyage_id; ?>">
                    <div class="user-list" id="userListContainer">
                        <p class="text-center"><i class="fas fa-spinner fa-spin"></i> Chargement des utilisateurs...</p>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <button type="button" class="btn btn-primary" id="shareButton">Partager</button>
            </div>
        </div>
    </div>
</div>

<!-- Script pour gérer le partage -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const shareModal = document.getElementById('shareModal');
    const shareVoyageIdInput = document.getElementById('share_voyage_id');
    const userListContainer = document.getElementById('userListContainer');
    const shareButton = document.getElementById('shareButton');
    const shareForm = document.getElementById('shareForm');

    // Charger la liste des utilisateurs lorsque le modal est ouvert
    shareModal.addEventListener('show.bs.modal', function() {
        const voyageId = shareVoyageIdInput.value;

        // Charger la liste des utilisateurs
        fetch(`get_users.php?id=${voyageId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    let html = '';
                    if (data.users.length === 0) {
                        html = '<p class="text-muted">Aucun utilisateur disponible pour le partage.</p>';
                    } else {
                        data.users.forEach(user => {
                            const isChecked = data.shared_users.includes(user.id_utilisateur) ? 'checked' : '';
                            html += `
                                <div class="form-check user-item">
                                    <input class="form-check-input" type="checkbox" name="users[]" 
                                           value="${user.id_utilisateur}" 
                                           id="user${user.id_utilisateur}"
                                           ${isChecked}>
                                    <label class="form-check-label" for="user${user.id_utilisateur}">
                                        <strong>${user.nom}</strong>
                                        <span class="text-muted">${user.email}</span>
                                    </label>
                                </div>
                            `;
                        });
                    }
                    userListContainer.innerHTML = html;
                } else {
                    userListContainer.innerHTML = `<p class="text-danger">${data.message}</p>`;
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                userListContainer.innerHTML = '<p class="text-danger">Une erreur est survenue lors du chargement des utilisateurs.</p>';
            });
    });

    // Lorsque le bouton "Partager" est cliqué, envoyer les données au serveur
    shareButton.addEventListener('click', function() {
        const formData = new FormData(shareForm);

        // Créer une requête AJAX
        fetch('share_voyage.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Fermer le modal
                const modal = bootstrap.Modal.getInstance(shareModal);
                modal.hide();

                // Afficher un message de succès
                alert('Le voyage a été partagé avec succès !');
            } else {
                alert('Erreur : ' + data.message);
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            alert('Une erreur est survenue lors du partage du voyage.');
        });
    });
});
</script>
<?php endif; ?>

<style>
/* Styles spécifiques à la page de détail du voyage */
.detail-section {
    margin-bottom: 30px;
}

.section-title {
    font-size: 1.4rem;
    margin-bottom: 20px;
    padding-bottom: 10px;
    border-bottom: 2px solid var(--primary-color);
    color: var(--primary-color);
    display: flex;
    align-items: center;
    gap: 10px;
}

.section-title i {
    font-size: 1.2em;
}

.info-item {
    display: flex;
    align-items: flex-start;
    margin-bottom: 15px;
    gap: 15px;
}

.info-item i {
    color: var(--primary-color);
    width: 20px;
    text-align: center;
    font-size: 1.1rem;
}

.info-label {
    display: block;
    font-size: 0.85rem;
    color: var(--text-light);
    margin-bottom: 3px;
}

.info-value {
    font-weight: 500;
}

.card-header h3 {
    font-size: 1.2rem;
    margin: 0;
}

.checklist {
    list-style: none;
    padding: 0;
    margin: 0;
}

.checklist li {
    display: flex;
    align-items: center;
    padding: 10px 0;
    border-bottom: 1px solid var(--border-color);
    gap: 15px;
}

.checklist li:last-child {
    border-bottom: none;
}

.checklist li i {
    color: var(--text-lighter);
    font-size: 1.2rem;
}

.checklist li.completed i {
    color: var(--success-color);
}

.checklist li.completed span {
    text-decoration: line-through;
    color: var(--text-lighter);
}

.ticket-info {
    background-color: var(--gray-light);
    padding: 15px;
    border-radius: 10px;
    border-left: 4px solid var(--primary-color);
}

.ticket-info h5 {
    font-size: 1rem;
    margin-bottom: 15px;
    color: var(--primary-color);
}

/* Styles pour le modal de partage */
.user-list {
    max-height: 300px;
    overflow-y: auto;
    margin-bottom: 15px;
}

.user-item {
    padding: 10px;
    border-bottom: 1px solid #eee;
    display: flex;
    align-items: center;
}

.user-item:last-child {
    border-bottom: none;
}

.user-item label {
    display: flex;
    flex-direction: column;
    margin-left: 10px;
    cursor: pointer;
}

.user-item .text-muted {
    font-size: 0.85rem;
}

@media print {
    .navbar, .footer, .btn {
        display: none !important;
    }

    body {
        padding: 0;
        margin: 0;
    }

    .container {
        width: 100%;
        max-width: 100%;
        padding: 0;
        margin: 0;
    }

    .content-container {
        box-shadow: none;
        border: none;
    }

    .card {
        box-shadow: none;
        border: 1px solid #ddd;
    }

    .section-title {
        color: #000;
        border-bottom-color: #000;
    }
}
</style>

<?php
// Inclure le footer
include 'footer.php';
?>
