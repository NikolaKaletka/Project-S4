<?php
// Définir les variables pour le header
$page_title = "Mes Voyages";
$current_page = "mesvoyages";
$additional_css = ["static/map.css", "static/style.css", "static/profil_new.css"];

// Inclure le header
include 'header.php';
require_once 'utils/utils.php';

// Vérifier si l'utilisateur est connecté
if (!isUserLoggedIn()) {
    header("Location: pageconnexion.php");
    exit();
}

// Récupérer l'ID de l'utilisateur
$id_utilisateur = $_SESSION['id_utilisateur'];

// Récupérer les informations de l'utilisateur
$user = fetchOne("SELECT nom, email FROM Utilisateur WHERE id_utilisateur = ?", [$id_utilisateur]);

if (!$user) {
    header("Location: pageconnexion.php");
    exit();
}

// Récupérer les voyages de l'utilisateur
$voyages = fetchAll("SELECT id_voyage, destination, date_debut, date_fin FROM Voyage WHERE ref_utilisateur = ? ORDER BY date_debut DESC", [$id_utilisateur]);

// Vérifier si un nouveau voyage doit être créé
if (isset($_GET['new']) && $_GET['new'] == 1) {
    $destination = isset($_GET['destination']) ? sanitize($_GET['destination']) : '';

    // Afficher le formulaire de création de voyage
    $show_form = true;
} else {
    $show_form = false;
}

// Traitement du formulaire de création de voyage
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_voyage'])) {
    $destination = sanitize($_POST['destination']);
    $date_debut = sanitize($_POST['date_debut']);
    $date_fin = sanitize($_POST['date_fin']);

    // Validation des données
    $errors = [];

    if (empty($destination)) {
        $errors[] = "La destination est requise.";
    }

    if (empty($date_debut)) {
        $errors[] = "La date de départ est requise.";
    }

    if (empty($date_fin)) {
        $errors[] = "La date de retour est requise.";
    }

    if (strtotime($date_fin) < strtotime($date_debut)) {
        $errors[] = "La date de retour doit être postérieure à la date de départ.";
    }

    // Si pas d'erreurs, créer le voyage
    if (empty($errors)) {
        try {
            $voyage_id = insert("INSERT INTO Voyage (destination, date_debut, date_fin, ref_utilisateur) VALUES (?, ?, ?, ?)", 
                [$destination, $date_debut, $date_fin, $id_utilisateur]);

            // Rediriger vers la page des voyages
            header("Location: lister.php?success=1");
            exit();
        } catch (Exception $e) {
            $errors[] = "Une erreur est survenue lors de la création du voyage.";
        }
    }
}

// Message de succès après création d'un voyage
$success_message = '';
if (isset($_GET['success']) && $_GET['success'] == 1) {
    $success_message = "Votre voyage a été créé avec succès !";
}
?>

<div class="map-page content-page">
    <div class="container py-5">
        <div class="row">
            <div class="col-lg-4 mb-4">
                <div class="content-container">
                    <div class="profile-summary">
                        <div class="profile-avatar">
                            <i class="fas fa-user-circle"></i>
                        </div>
                        <h2><?php echo htmlspecialchars($user['nom']); ?></h2>
                        <p class="text-muted"><?php echo htmlspecialchars($user['email']); ?></p>

                        <div class="profile-stats">
                            <div class="stat-item">
                                <div class="stat-value"><?php echo count($voyages); ?></div>
                                <div class="stat-label">Voyages</div>
                            </div>
                            <div class="stat-item">
                                <div class="stat-value"><?php echo count(array_unique(array_column($voyages, 'destination'))); ?></div>
                                <div class="stat-label">Destinations</div>
                            </div>
                        </div>

                        <div class="profile-actions mt-4">
                            <a href="creation_voyage.php" class="btn btn-primary btn-sm">
                                <i class="fas fa-plus"></i> Nouveau voyage
                            </a>
                        </div>
                    </div>
                </div>

                <?php if (!empty($voyages)): ?>
                    <div class="content-container">
                        <h3>Statistiques</h3>
                        <div class="stats-info">
                            <?php
                            // Calculer la durée totale des voyages
                            $total_days = 0;
                            foreach ($voyages as $voyage) {
                                $start = new DateTime($voyage['date_debut']);
                                $end = new DateTime($voyage['date_fin']);
                                $interval = $start->diff($end);
                                $total_days += $interval->days + 1; // +1 pour inclure le jour de départ
                            }

                            // Trouver le prochain voyage
                            $prochain_voyage = null;
                            $today = new DateTime();
                            foreach ($voyages as $voyage) {
                                $depart = new DateTime($voyage['date_debut']);
                                if ($depart > $today) {
                                    if ($prochain_voyage === null || $depart < new DateTime($prochain_voyage['date_debut'])) {
                                        $prochain_voyage = $voyage;
                                    }
                                }
                            }
                            ?>

                            <div class="stat-detail">
                                <i class="fas fa-calendar-day"></i>
                                <div>
                                    <span class="stat-label">Jours de voyage</span>
                                    <span class="stat-value"><?php echo $total_days; ?> jours</span>
                                </div>
                            </div>

                            <?php if ($prochain_voyage): ?>
                                <div class="stat-detail">
                                    <i class="fas fa-plane-departure"></i>
                                    <div>
                                        <span class="stat-label">Prochain voyage</span>
                                        <span class="stat-value"><?php echo htmlspecialchars($prochain_voyage['destination']); ?></span>
                                        <span class="stat-date">
                                            <?php 
                                            $depart = new DateTime($prochain_voyage['date_debut']);
                                            $interval = $today->diff($depart);
                                            echo "Dans " . $interval->days . " jours";
                                            ?>
                                        </span>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <div class="col-lg-8">
                <?php if (!empty($success_message)): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?php echo $success_message; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <?php if ($show_form): ?>
                    <div class="content-container">
                        <h2>Créer un nouveau voyage</h2>

                        <?php if (!empty($errors)): ?>
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    <?php foreach ($errors as $error): ?>
                                        <li><?php echo $error; ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endif; ?>

                        <form action="lister.php" method="POST">
                            <div class="mb-3">
                                <label for="destination" class="form-label">Destination</label>
                                <input type="text" class="form-control" id="destination" name="destination" value="<?php echo isset($destination) ? htmlspecialchars($destination) : ''; ?>" required>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="date_debut" class="form-label">Date de départ</label>
                                    <input type="date" class="form-control" id="date_debut" name="date_debut" required>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="date_fin" class="form-label">Date de retour</label>
                                    <input type="date" class="form-control" id="date_fin" name="date_fin" required>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between">
                                <a href="lister.php" class="btn btn-outline-secondary">Annuler</a>
                                <button type="submit" name="create_voyage" class="btn btn-primary">Créer le voyage</button>
                            </div>
                        </form>
                    </div>
                <?php else: ?>
                    <div class="content-container">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h2 class="mb-0">Mes Voyages</h2>
                            <a href="creation_voyage.php" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Nouveau voyage
                            </a>
                        </div>

                        <?php if (empty($voyages)): ?>
                            <div class="empty-state">
                                <i class="fas fa-suitcase-rolling"></i>
                                <h3>Aucun voyage planifié</h3>
                                <p>Commencez à planifier votre première aventure !</p>
                                <a href="creation_voyage.php" class="btn btn-primary">Créer un voyage</a>
                            </div>
                        <?php else: ?>
                            <?php
                            // Séparer les voyages en cours et les voyages passés/à venir
                            $voyages_en_cours = [];
                            $voyages_autres = [];

                            foreach ($voyages as $voyage) {
                                $start = new DateTime($voyage['date_debut']);
                                $end = new DateTime($voyage['date_fin']);
                                $today = new DateTime();

                                if ($today >= $start && $today <= $end) {
                                    $voyages_en_cours[] = $voyage;
                                } else {
                                    $voyages_autres[] = $voyage;
                                }
                            }
                            ?>

                            <!-- Voyages en cours -->
                            <div class="voyage-section">
                                <h3 class="section-title">Voyages en cours</h3>
                                <div class="voyages-list">
                                    <?php if (empty($voyages_en_cours)): ?>
                                        <p class="text-muted">Aucun voyage en cours actuellement.</p>
                                    <?php else: ?>
                                        <?php foreach ($voyages_en_cours as $voyage): ?>
                                            <div class="voyage-card">
                                                <div class="voyage-header">
                                                    <h3><?php echo htmlspecialchars($voyage['destination']); ?></h3>
                                                    <span class="voyage-status ongoing">En cours</span>
                                                </div>

                                                <div class="voyage-dates">
                                                    <div class="date-item">
                                                        <i class="fas fa-plane-departure"></i>
                                                        <div>
                                                            <span class="date-label">Départ</span>
                                                            <span class="date-value"><?php echo formatDate($voyage['date_debut']); ?></span>
                                                        </div>
                                                    </div>

                                                    <div class="date-separator">
                                                        <i class="fas fa-arrow-right"></i>
                                                    </div>

                                                    <div class="date-item">
                                                        <i class="fas fa-plane-arrival"></i>
                                                        <div>
                                                            <span class="date-label">Retour</span>
                                                            <span class="date-value"><?php echo formatDate($voyage['date_fin']); ?></span>
                                                        </div>
                                                    </div>

                                                    <?php
                                                    $start = new DateTime($voyage['date_debut']);
                                                    $end = new DateTime($voyage['date_fin']);
                                                    $interval = $start->diff($end);
                                                    $duration = $interval->days + 1; // +1 pour inclure le jour de départ
                                                    ?>
                                                    <div class="voyage-duration">
                                                        <i class="fas fa-clock"></i>
                                                        <span><?php echo $duration; ?> jours</span>
                                                    </div>
                                                </div>

                                                <div class="voyage-actions">
                                                    <a href="creation_voyage.php?id=<?php echo $voyage['id_voyage']; ?>" class="btn btn-sm btn-outline-primary">
                                                        <i class="fas fa-edit"></i> Modifier
                                                    </a>
                                                    <a href="voyage_detail.php?id=<?php echo $voyage['id_voyage']; ?>" class="btn btn-sm btn-outline-secondary">
                                                        <i class="fas fa-list-check"></i> Détails
                                                    </a>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <!-- Voyages passés ou à venir -->
                            <div class="voyage-section mt-4">
                                <h3 class="section-title">Voyages passés / à venir</h3>
                                <div class="voyages-list">
                                    <?php if (empty($voyages_autres)): ?>
                                        <p class="text-muted">Aucun voyage passé ou à venir.</p>
                                    <?php else: ?>
                                        <?php foreach ($voyages_autres as $voyage): ?>
                                            <div class="voyage-card">
                                                <div class="voyage-header">
                                                    <h3><?php echo htmlspecialchars($voyage['destination']); ?></h3>
                                                    <?php
                                                    $start = new DateTime($voyage['date_debut']);
                                                    $end = new DateTime($voyage['date_fin']);
                                                    $today = new DateTime();

                                                    if ($today < $start) {
                                                        $status_class = 'upcoming';
                                                        $status_text = 'À venir';
                                                    } else {
                                                        $status_class = 'completed';
                                                        $status_text = 'Terminé';
                                                    }
                                                    ?>
                                                    <span class="voyage-status <?php echo $status_class; ?>"><?php echo $status_text; ?></span>
                                                </div>

                                                <div class="voyage-dates">
                                                    <div class="date-item">
                                                        <i class="fas fa-plane-departure"></i>
                                                        <div>
                                                            <span class="date-label">Départ</span>
                                                            <span class="date-value"><?php echo formatDate($voyage['date_debut']); ?></span>
                                                        </div>
                                                    </div>

                                                    <div class="date-separator">
                                                        <i class="fas fa-arrow-right"></i>
                                                    </div>

                                                    <div class="date-item">
                                                        <i class="fas fa-plane-arrival"></i>
                                                        <div>
                                                            <span class="date-label">Retour</span>
                                                            <span class="date-value"><?php echo formatDate($voyage['date_fin']); ?></span>
                                                        </div>
                                                    </div>

                                                    <?php
                                                    $interval = $start->diff($end);
                                                    $duration = $interval->days + 1; // +1 pour inclure le jour de départ
                                                    ?>
                                                    <div class="voyage-duration">
                                                        <i class="fas fa-clock"></i>
                                                        <span><?php echo $duration; ?> jours</span>
                                                    </div>
                                                </div>

                                                <div class="voyage-actions">
                                                    <a href="creation_voyage.php?id=<?php echo $voyage['id_voyage']; ?>" class="btn btn-sm btn-outline-primary">
                                                        <i class="fas fa-edit"></i> Modifier
                                                    </a>
                                                    <a href="voyage_detail.php?id=<?php echo $voyage['id_voyage']; ?>" class="btn btn-sm btn-outline-secondary">
                                                        <i class="fas fa-list-check"></i> Détails
                                                    </a>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<style>
/* Styles spécifiques à la page des voyages */
.profile-summary {
    text-align: center;
}

.profile-avatar {
    font-size: 5rem;
    color: var(--primary-color);
    margin-bottom: 15px;
}

.profile-stats {
    display: flex;
    justify-content: center;
    margin-top: 20px;
}

.stat-item {
    padding: 0 15px;
    border-right: 1px solid #eee;
}

.stat-item:last-child {
    border-right: none;
}

.stat-value {
    font-size: 1.8rem;
    font-weight: 700;
    color: var(--primary-color);
}

.stat-label {
    font-size: 0.9rem;
    color: #666;
}

.stats-info {
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.stat-detail {
    display: flex;
    align-items: center;
    gap: 15px;
}

.stat-detail i {
    font-size: 1.5rem;
    color: var(--primary-color);
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    background-color: rgba(227, 175, 16, 0.1);
    border-radius: 50%;
}

.stat-detail .stat-label {
    display: block;
    margin-bottom: 5px;
}

.stat-detail .stat-value {
    font-size: 1.2rem;
}

.stat-date {
    display: block;
    font-size: 0.8rem;
    color: #888;
}

.empty-state {
    text-align: center;
    padding: 50px 0;
}

.empty-state i {
    font-size: 4rem;
    color: #ddd;
    margin-bottom: 20px;
}

.empty-state h3 {
    font-size: 1.5rem;
    margin-bottom: 10px;
}

.empty-state p {
    color: #888;
    margin-bottom: 20px;
}

.voyages-list {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.voyage-card {
    background-color: #f9f9f9;
    border-radius: 10px;
    padding: 20px;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
    transition: all 0.3s ease;
}

.voyage-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
}

.voyage-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 15px;
}

.voyage-header h3 {
    margin: 0;
    font-size: 1.3rem;
}

.voyage-status {
    padding: 5px 10px;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 600;
}

.voyage-status.upcoming {
    background-color: #e3f2fd;
    color: #0d6efd;
}

.voyage-status.ongoing {
    background-color: #e8f5e9;
    color: #198754;
}

.voyage-status.completed {
    background-color: #f5f5f5;
    color: #6c757d;
}

.voyage-dates {
    display: flex;
    align-items: center;
    margin-bottom: 15px;
    flex-wrap: wrap;
    gap: 10px;
}

.date-item {
    display: flex;
    align-items: center;
    gap: 10px;
}

.date-item i {
    color: var(--primary-color);
}

.date-label {
    display: block;
    font-size: 0.8rem;
    color: #666;
}

.date-value {
    display: block;
    font-weight: 600;
}

.date-separator {
    margin: 0 10px;
    color: #ccc;
}

.voyage-duration {
    margin-left: auto;
    font-size: 0.9rem;
    color: #666;
    display: flex;
    align-items: center;
    gap: 5px;
}

.voyage-actions {
    display: flex;
    gap: 10px;
    justify-content: flex-end;
}

.voyage-section {
    margin-bottom: 30px;
}

.section-title {
    font-size: 1.4rem;
    margin-bottom: 20px;
    padding-bottom: 10px;
    border-bottom: 2px solid var(--primary-color);
    color: var(--primary-color);
}

@media (max-width: 768px) {
    .voyage-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 10px;
    }

    .voyage-status {
        align-self: flex-start;
    }

    .voyage-dates {
        flex-direction: column;
        align-items: flex-start;
    }

    .date-separator {
        display: none;
    }

    .voyage-duration {
        margin-left: 0;
        margin-top: 10px;
    }
}
</style>

<!-- Custom JavaScript for Date Validation -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Définir la date minimale pour le champ date_debut (aujourd'hui)
        const dateDebutInput = document.getElementById('date_debut');
        const dateFinInput = document.getElementById('date_fin');

        if (dateDebutInput && dateFinInput) {
            const today = new Date().toISOString().split('T')[0];
            dateDebutInput.setAttribute('min', today);

            // Mettre à jour la date minimale pour date_fin lorsque date_debut change
            dateDebutInput.addEventListener('change', function() {
                dateFinInput.setAttribute('min', this.value);

                // Si date_fin est antérieure à date_debut, la réinitialiser
                if (dateFinInput.value && dateFinInput.value < this.value) {
                    dateFinInput.value = this.value;
                }
            });
        }
    });
</script>

<?php
// Inclure le footer
include 'footer.php';
?>
