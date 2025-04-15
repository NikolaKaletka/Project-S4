<?php
// Définir les variables pour le header
$page_title = "Destinations";
$current_page = "destinations";
$additional_css = ["static/accueil.css", "static/destinations.css"];

// Inclure le header
include 'header.php';

// Connexion à la base de données
$pdo = getDbConnection();

// Récupérer la destination recherchée si elle existe
$search_destination = isset($_GET['destination']) ? sanitize($_GET['destination']) : '';

// Récupérer toutes les destinations (voyages uniques)
$sql = "SELECT DISTINCT destination FROM Voyage";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$destinations = $stmt->fetchAll(PDO::FETCH_COLUMN);

// Informations sur les destinations populaires
$destinations_info = [
    'Paris' => [
        'image' => 'https://images.unsplash.com/photo-1499856871958-5b9627545d1a',
        'description' => 'La ville de l\'amour avec ses monuments emblématiques, sa gastronomie et son ambiance romantique.',
        'activites' => ['Tour Eiffel', 'Musée du Louvre', 'Montmartre', 'Croisière sur la Seine', 'Shopping'],
        'meilleure_periode' => 'Printemps et automne',
        'budget' => 'Moyen à élevé'
    ],
    'Rome' => [
        'image' => 'https://images.unsplash.com/photo-1523906834658-6e24ef2386f9',
        'description' => 'La cité éternelle regorge de trésors historiques, d\'art et d\'une cuisine délicieuse.',
        'activites' => ['Colisée', 'Vatican', 'Fontaine de Trevi', 'Forum Romain', 'Dégustation de glaces'],
        'meilleure_periode' => 'Printemps et automne',
        'budget' => 'Moyen'
    ],
    'Bali' => [
        'image' => 'https://images.unsplash.com/photo-1513635269975-59663e0ac1ad',
        'description' => 'L\'île des dieux offre un mélange parfait de plages paradisiaques, culture unique et nature luxuriante.',
        'activites' => ['Plages de Kuta', 'Temples de Ubud', 'Rizières en terrasse', 'Surf', 'Spa'],
        'meilleure_periode' => 'Avril à octobre',
        'budget' => 'Économique à moyen'
    ],
    'New York' => [
        'image' => 'https://images.unsplash.com/photo-1496442226666-8d4d0e62e6e9',
        'description' => 'La ville qui ne dort jamais vous impressionnera par ses gratte-ciels, sa diversité culturelle et son énergie.',
        'activites' => ['Times Square', 'Central Park', 'Empire State Building', 'Broadway', 'Musées'],
        'meilleure_periode' => 'Printemps et automne',
        'budget' => 'Élevé'
    ],
    'Tokyo' => [
        'image' => 'https://images.unsplash.com/photo-1503899036084-c55cdd92da26',
        'description' => 'Un fascinant mélange de tradition et d\'ultra-modernité, avec une culture unique et une cuisine raffinée.',
        'activites' => ['Shibuya', 'Temples d\'Asakusa', 'Akihabara', 'Parc Ueno', 'Cuisine japonaise'],
        'meilleure_periode' => 'Printemps et automne',
        'budget' => 'Élevé'
    ],
    'Barcelone' => [
        'image' => 'https://images.unsplash.com/photo-1539037116277-4db20889f2d4',
        'description' => 'Ville dynamique offrant architecture unique, plages urbaines et vie nocturne animée.',
        'activites' => ['Sagrada Familia', 'Parc Güell', 'Las Ramblas', 'Plage de Barceloneta', 'Tapas'],
        'meilleure_periode' => 'Printemps, été et automne',
        'budget' => 'Moyen'
    ]
];

// Destinations par défaut à afficher
$default_destinations = ['Paris', 'Rome', 'Bali', 'New York', 'Tokyo', 'Barcelone'];

// Si une recherche est effectuée, filtrer les destinations
if (!empty($search_destination)) {
    $filtered_destinations = array_filter($default_destinations, function($dest) use ($search_destination) {
        return stripos($dest, $search_destination) !== false;
    });
    
    if (!empty($filtered_destinations)) {
        $destinations_to_show = $filtered_destinations;
    } else {
        $destinations_to_show = [];
    }
} else {
    $destinations_to_show = $default_destinations;
}
?>

<div class="destinations-page">
    <div class="destinations-hero">
        <div class="hero-background">
            <img src="static/bg.webp" alt="Destinations de voyage" class="bg">
            <div class="overlay"></div>
        </div>
        <div class="container">
            <div class="hero-content text-center">
                <h1>Explorez nos destinations</h1>
                <p class="lead">Découvrez des lieux incroyables et planifiez votre prochain voyage</p>
                
                <div class="search-box">
                    <form action="destinations.php" method="GET">
                        <div class="input-group">
                            <input type="text" class="form-control" placeholder="Rechercher une destination..." name="destination" value="<?php echo $search_destination; ?>">
                            <button class="btn btn-search" type="submit">
                                <i class="fas fa-search"></i> Rechercher
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="container py-5">
        <?php if (!empty($search_destination)): ?>
            <div class="search-results mb-4">
                <h2>Résultats pour "<?php echo $search_destination; ?>"</h2>
                <?php if (empty($destinations_to_show)): ?>
                    <div class="alert alert-info">
                        Aucune destination trouvée pour "<?php echo $search_destination; ?>". 
                        <a href="chatbot.php" class="alert-link">Consultez notre assistant</a> pour des recommandations personnalisées.
                    </div>
                <?php endif; ?>
                <a href="destination.php" class="btn btn-outline-primary btn-sm">Voir toutes les destinations</a>
            </div>
        <?php else: ?>
            <h2 class="section-title">Destinations populaires</h2>
            <p class="section-description">Explorez nos destinations les plus appréciées par nos voyageurs</p>
        <?php endif; ?>

        <div class="row">
            <?php foreach ($destinations_to_show as $destination): ?>
                <?php if (isset($destinations_info[$destination])): ?>
                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="destination-card">
                            <div class="destination-image">
                                <img src="<?php echo $destinations_info[$destination]['image']; ?>" alt="<?php echo $destination; ?>">
                                <div class="destination-overlay">
                                    <a href="destination.php?destination=<?php echo urlencode($destination); ?>" class="btn btn-sm btn-light">Explorer</a>
                                </div>
                            </div>
                            <div class="destination-info">
                                <h3><?php echo $destination; ?></h3>
                                <p><?php echo $destinations_info[$destination]['description']; ?></p>
                                <div class="destination-meta">
                                    <span><i class="fas fa-calendar-alt"></i> <?php echo $destinations_info[$destination]['meilleure_periode']; ?></span>
                                    <span><i class="fas fa-euro-sign"></i> <?php echo $destinations_info[$destination]['budget']; ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>

        <?php if (!empty($search_destination) && !empty($destinations_to_show)): ?>
            <div class="destination-details mt-5">
                <h2>À propos de <?php echo reset($destinations_to_show); ?></h2>
                <?php $selected_destination = reset($destinations_to_show); ?>
                
                <div class="row mt-4">
                    <div class="col-md-6">
                        <div class="detail-card">
                            <h3><i class="fas fa-info-circle"></i> Description</h3>
                            <p><?php echo $destinations_info[$selected_destination]['description']; ?></p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="detail-card">
                            <h3><i class="fas fa-calendar-alt"></i> Meilleure période</h3>
                            <p><?php echo $destinations_info[$selected_destination]['meilleure_periode']; ?></p>
                            <h3><i class="fas fa-euro-sign"></i> Budget</h3>
                            <p><?php echo $destinations_info[$selected_destination]['budget']; ?></p>
                        </div>
                    </div>
                </div>
                
                <div class="detail-card mt-4">
                    <h3><i class="fas fa-list"></i> Activités recommandées</h3>
                    <ul class="activities-list">
                        <?php foreach ($destinations_info[$selected_destination]['activites'] as $activite): ?>
                            <li><?php echo $activite; ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                
                <div class="text-center mt-4">
                    <?php if ($est_connecte): ?>
                        <a href="lister.php?new=1&destination=<?php echo urlencode($selected_destination); ?>" class="btn btn-primary">
                            <i class="fas fa-plane"></i> Planifier un voyage à <?php echo $selected_destination; ?>
                        </a>
                    <?php else: ?>
                        <a href="pageconnexion.php" class="btn btn-primary">
                            <i class="fas fa-sign-in-alt"></i> Connectez-vous pour planifier votre voyage
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php
// Variables pour le footer
$additional_js = [];
$custom_script = "
    document.addEventListener('DOMContentLoaded', function() {
        // Animation au défilement
        const cards = document.querySelectorAll('.destination-card');
        
        function checkScroll() {
            cards.forEach((card, index) => {
                const cardTop = card.getBoundingClientRect().top;
                const windowHeight = window.innerHeight;
                
                if (cardTop < windowHeight * 0.9) {
                    setTimeout(() => {
                        card.classList.add('visible');
                    }, index * 100);
                }
            });
        }
        
        window.addEventListener('scroll', checkScroll);
        checkScroll(); // Vérifier au chargement initial
    });
";

// Inclure le footer
include 'footer.php';
?>

