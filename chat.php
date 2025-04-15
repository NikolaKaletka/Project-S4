<?php
session_start();
// Vérifier si l'utilisateur est connecté
$est_connecte = isset($_SESSION['id_utilisateur']);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assistant de Voyage - TravelDream</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome pour les icônes -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- CSS personnalisé -->
    <link rel="stylesheet" href="static/style.css">
    <link rel="stylesheet" href="static/accueil.css">
    <link rel="stylesheet" href="static/chatbot.css">
</head>
<body>
    <!-- Navigation -->
    <header>
        <nav class="navbar navbar-expand-lg">
            <div class="container">
                <a class="navbar-brand" href="index.php">TravelDream</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="destination.php">Accueil</a>
                        </li>
                        <?php if ($est_connecte): ?>
                            <li class="nav-item">
                                <a class="nav-link" href="mesvoyages.php">Mon Profil</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="logout.php">Déconnexion</a>
                            </li>
                        <?php else: ?>
                            <li class="nav-item">
                                <a class="nav-link btn-connexion" href="pageconnexion.php">Connexion</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link btn-inscription" href="inscription.php">Inscription</a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </nav>
    </header>

    <div class="chatbot-page">
        <div class="container">
            <div class="row">
                <div class="col-lg-5">
                    <div class="chatbot-info">
                        <h1>Assistant de Voyage</h1>
                        <p class="lead">Notre assistant intelligent vous aide à trouver la destination parfaite selon vos préférences et votre budget.</p> 
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-7">
                    <div class="chatbot-container">
                        <div class="chatbot-header">
                            <div class="chatbot-avatar">
                                <i class="fas fa-robot"></i>
                            </div>
                            <div class="chatbot-title">
                                <h2>TravelDream Assistant</h2>
                                <p>En ligne</p>
                            </div>
                        </div>
                        
                        <div class="chatbot-messages" id="chatMessages">
                            <!-- Les messages seront ajoutés ici dynamiquement -->
                        </div>
                        
                        <div class="chatbot-input">
                            <input type="text" id="userInput" placeholder="Écrivez votre message..." autocomplete="off">
                            <button id="sendButton" class="btn-send">
                                <i class="fas fa-paper-plane"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-md-4">
                    <h3>TravelDream</h3>
                    <p>Votre compagnon de voyage idéal pour planifier des aventures inoubliables.</p>
                    <div class="social-links">
                        <a href="#"><i class="fab fa-facebook-f"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-pinterest"></i></a>
                    </div>
                </div>
                
                <div class="col-md-2">
                    <h4>Liens utiles</h4>
                    <ul class="footer-links">
                        <li><a href="index.php">Accueil</a></li>
                        <li><a href="destinations.php">Destinations</a></li>
                        <li><a href="avis.php">Avis</a></li>
                        <li><a href="map.php">Carte</a></li>
                    </ul>
                </div>
                
                <div class="col-md-3">
                    <h4>Assistance</h4>
                    <ul class="footer-links">
                        <li><a href="#">Centre d'aide</a></li>
                        <li><a href="#">FAQ</a></li>
                        <li><a href="#">Conditions d'utilisation</a></li>
                        <li><a href="#">Politique de confidentialité</a></li>
                    </ul>
                </div>
                
                <div class="col-md-3">
                    <h4>Contact</h4>
                    <ul class="contact-info">
                        <li><i class="fas fa-map-marker-alt"></i> 123 Rue du Voyage, Paris</li>
                        <li><i class="fas fa-phone"></i> +33 1 23 45 67 89</li>
                        <li><i class="fas fa-envelope"></i> contact@traveldream.com</li>
                    </ul>
                </div>
            </div>
            
            <div class="footer-bottom">
                <p>&copy; 2025 TravelDream. Tous droits réservés.</p>
            </div>
        </div>
    </footer>

    <!-- Scripts JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Base de données de destinations
        const destinations = [
            {
                nom: "Paris",
                pays: "France",
                continent: "Europe",
                climat: "tempéré",
                type: ["ville", "culture", "gastronomie"],
                budget: "moyen",
                saison: ["printemps", "automne"],
                duree_ideale: "3-5 jours",
                activites: ["Tour Eiffel", "Musée du Louvre", "Montmartre", "Croisière sur la Seine", "Shopping"],
                description: "La ville de l'amour avec ses monuments emblématiques, sa gastronomie et son ambiance romantique."
            },
            {
                nom: "Rome",
                pays: "Italie",
                continent: "Europe",
                climat: "méditerranéen",
                type: ["ville", "culture", "histoire", "gastronomie"],
                budget: "moyen",
                saison: ["printemps", "automne"],
                duree_ideale: "3-5 jours",
                activites: ["Colisée", "Vatican", "Fontaine de Trevi", "Forum Romain", "Dégustation de glaces"],
                description: "La cité éternelle regorge de trésors historiques, d'art et d'une cuisine délicieuse."
            },
            {
                nom: "Bali",
                pays: "Indonésie",
                continent: "Asie",
                climat: "tropical",
                type: ["plage", "nature", "détente", "spiritualité"],
                budget: "économique",
                saison: ["avril à octobre"],
                duree_ideale: "7-14 jours",
                activites: ["Plages de Kuta", "Temples de Ubud", "Rizières en terrasse", "Surf", "Spa"],
                description: "L'île des dieux offre un mélange parfait de plages paradisiaques, culture unique et nature luxuriante."
            },
            {
                nom: "New York",
                pays: "États-Unis",
                continent: "Amérique du Nord",
                climat: "continental",
                type: ["ville", "culture", "shopping"],
                budget: "élevé",
                saison: ["printemps", "automne"],
                duree_ideale: "5-7 jours",
                activites: ["Times Square", "Central Park", "Empire State Building", "Broadway", "Musées"],
                description: "La ville qui ne dort jamais vous impressionnera par ses gratte-ciels, sa diversité culturelle et son énergie."
            },
            {
                nom: "Tokyo",
                pays: "Japon",
                continent: "Asie",
                climat: "tempéré",
                type: ["ville", "culture", "technologie", "gastronomie"],
                budget: "élevé",
                saison: ["printemps", "automne"],
                duree_ideale: "7-10 jours",
                activites: ["Shibuya", "Temples d'Asakusa", "Akihabara", "Parc Ueno", "Cuisine japonaise"],
                description: "Un fascinant mélange de tradition et d'ultra-modernité, avec une culture unique et une cuisine raffinée."
            },
            {
                nom: "Barcelone",
                pays: "Espagne",
                continent: "Europe",
                climat: "méditerranéen",
                type: ["ville", "plage", "culture", "gastronomie"],
                budget: "moyen",
                saison: ["printemps", "été", "automne"],
                duree_ideale: "3-5 jours",
                activites: ["Sagrada Familia", "Parc Güell", "Las Ramblas", "Plage de Barceloneta", "Tapas"],
                description: "Ville dynamique offrant architecture unique, plages urbaines et vie nocturne animée."
            },
            {
                nom: "Marrakech",
                pays: "Maroc",
                continent: "Afrique",
                climat: "désertique",
                type: ["ville", "culture", "shopping", "histoire"],
                budget: "économique",
                saison: ["automne", "hiver", "printemps"],
                duree_ideale: "3-5 jours",
                activites: ["Médina", "Jardins Majorelle", "Place Jemaa el-Fna", "Souks", "Hammam"],
                description: "Cité impériale aux couleurs ocre, avec ses marchés animés, palais et jardins luxuriants."
            },
            {
                nom: "Santorin",
                pays: "Grèce",
                continent: "Europe",
                climat: "méditerranéen",
                type: ["plage", "romantique", "détente"],
                budget: "élevé",
                saison: ["printemps", "été", "automne"],
                duree_ideale: "5-7 jours",
                activites: ["Oia", "Plages volcaniques", "Croisière dans la caldeira", "Dégustation de vins", "Coucher de soleil"],
                description: "Île grecque idyllique avec ses maisons blanches à dômes bleus et ses vues spectaculaires sur la mer Égée."
            },
            {
                nom: "Bangkok",
                pays: "Thaïlande",
                continent: "Asie",
                climat: "tropical",
                type: ["ville", "culture", "gastronomie", "shopping"],
                budget: "économique",
                saison: ["novembre à février"],
                duree_ideale: "3-5 jours",
                activites: ["Grand Palais", "Temples", "Marchés flottants", "Street food", "Vie nocturne"],
                description: "Métropole vibrante où traditions et modernité se côtoient, avec une cuisine de rue exceptionnelle."
            },
            {
                nom: "Maldives",
                pays: "Maldives",
                continent: "Asie",
                climat: "tropical",
                type: ["plage", "luxe", "détente", "romantique"],
                budget: "très élevé",
                saison: ["novembre à avril"],
                duree_ideale: "7-10 jours",
                activites: ["Plongée", "Snorkeling", "Spa", "Villas sur pilotis", "Excursions en bateau"],
                description: "Archipel paradisiaque aux eaux cristallines, idéal pour une escapade romantique ou lune de miel."
            },
            {
                nom: "Le Cap",
                pays: "Afrique du Sud",
                continent: "Afrique",
                climat: "méditerranéen",
                type: ["ville", "nature", "aventure", "gastronomie"],
                budget: "moyen",
                saison: ["octobre à avril"],
                duree_ideale: "5-7 jours",
                activites: ["Montagne de la Table", "Cap de Bonne-Espérance", "Route des vins", "Observation des pingouins", "Safari urbain"],
                description: "Ville côtière spectaculaire entourée de montagnes, vignobles et réserves naturelles."
            },
            {
                nom: "Rio de Janeiro",
                pays: "Brésil",
                continent: "Amérique du Sud",
                climat: "tropical",
                type: ["ville", "plage", "fête", "nature"],
                budget: "moyen",
                saison: ["décembre à mars"],
                duree_ideale: "5-7 jours",
                activites: ["Christ Rédempteur", "Plage de Copacabana", "Pain de Sucre", "Samba", "Carnaval"],
                description: "Ville vibrante entre mer et montagnes, connue pour ses plages, sa musique et son ambiance festive."
            }
        ];

        // Fonction pour ajouter un message au chat
        function addMessage(sender, text, isDestination = false) {
            const chatMessages = document.getElementById('chatMessages');
            const messageDiv = document.createElement('div');
            messageDiv.className = `message ${sender}`;
            
            if (isDestination) {
                // Format spécial pour les recommandations de destinations
                messageDiv.innerHTML = text;
            } else {
                messageDiv.textContent = text;
            }
            
            chatMessages.appendChild(messageDiv);
            chatMessages.scrollTop = chatMessages.scrollHeight;
        }

        // Fonction pour formater une destination en HTML
        function formatDestination(destination) {
            return `
                <div class="destination-card-chat">
                    <h3>${destination.nom}, ${destination.pays}</h3>
                    <p>${destination.description}</p>
                    <div class="destination-details">
                        <span><i class="fas fa-tag"></i> Budget: ${destination.budget}</span>
                        <span><i class="fas fa-calendar-alt"></i> Idéal: ${destination.duree_ideale}</span>
                        <span><i class="fas fa-sun"></i> Meilleure saison: ${destination.saison.join(', ')}</span>
                    </div>
                    <div class="destination-activities">
                        <strong>Activités:</strong> ${destination.activites.slice(0, 3).join(', ')}...
                    </div>
                    <a href="destinations.php?destination=${destination.nom}" class="btn btn-sm btn-primary">En savoir plus</a>
                </div>
            `;
        }

        // Fonction pour recommander des destinations
        function recommanderDestinations(preferences) {
            // Filtrer les destinations selon les préférences
            let resultats = [...destinations];
            
            // Filtrer par continent si spécifié
            if (preferences.continent && preferences.continent !== 'tous') {
                resultats = resultats.filter(d => d.continent.toLowerCase() === preferences.continent.toLowerCase());
            }
            
            // Filtrer par type de voyage
            if (preferences.type && preferences.type.length > 0) {
                resultats = resultats.filter(d => {
                    return preferences.type.some(t => d.type.includes(t.toLowerCase()));
                });
            }
            
            // Filtrer par budget
            if (preferences.budget) {
                resultats = resultats.filter(d => {
                    if (preferences.budget === 'économique') {
                        return d.budget === 'économique';
                    } else if (preferences.budget === 'moyen') {
                        return d.budget === 'économique' || d.budget === 'moyen';
                    } else {
                        return true; // Si budget élevé, toutes les destinations sont possibles
                    }
                });
            }
            
            // Filtrer par climat
            if (preferences.climat && preferences.climat.length > 0) {
                resultats = resultats.filter(d => {
                    return preferences.climat.some(c => d.climat.includes(c.toLowerCase()));
                });
            }
            
            // Limiter à 3 résultats maximum
            resultats = resultats.slice(0, 3);
            
            return resultats;
        }

        // État du chatbot
        let chatbotState = {
            step: 0,
            preferences: {
                continent: null,
                type: [],
                budget: null,
                climat: [],
                duree: null
            }
        };

        // Questions du chatbot
        const chatbotQuestions = [
            "Bonjour ! Je suis l'assistant TravelDream. Je vais vous aider à trouver la destination idéale pour votre prochain voyage. Quel continent souhaitez-vous explorer ? (Europe, Asie, Afrique, Amérique du Nord, Amérique du Sud, Océanie, ou 'tous')",
            "Super ! Quel type de voyage recherchez-vous ? (ville, plage, nature, culture, aventure, détente, gastronomie, romantique)",
            "Excellent choix ! Quel est votre budget approximatif ? (économique, moyen, élevé)",
            "Parfait ! Quel climat préférez-vous ? (tropical, méditerranéen, tempéré, désertique, montagneux)",
            "Dernière question : quelle est la durée idéale de votre voyage ? (week-end, une semaine, deux semaines, plus)"
        ];

        // Fonction pour traiter la réponse de l'utilisateur
        function processUserInput(input) {
            input = input.trim().toLowerCase();
            
            switch(chatbotState.step) {
                case 0: // Continent
                    const continents = ["europe", "asie", "afrique", "amérique du nord", "amérique du sud", "océanie", "tous"];
                    if (continents.includes(input) || input === "tous") {
                        chatbotState.preferences.continent = input === "tous" ? null : input;
                        chatbotState.step++;
                        setTimeout(() => {
                            addMessage('bot', chatbotQuestions[chatbotState.step]);
                        }, 500);
                    } else {
                        addMessage('bot', "Je n'ai pas compris votre choix de continent. Veuillez choisir parmi Europe, Asie, Afrique, Amérique du Nord, Amérique du Sud, Océanie, ou 'tous'.");
                    }
                    break;
                    
                case 1: // Type de voyage
                    const types = ["ville", "plage", "nature", "culture", "aventure", "détente", "gastronomie", "romantique"];
                    const userTypes = input.split(',').map(t => t.trim());
                    const validTypes = userTypes.filter(t => types.includes(t));
                    
                    if (validTypes.length > 0) {
                        chatbotState.preferences.type = validTypes;
                        chatbotState.step++;
                        setTimeout(() => {
                            addMessage('bot', chatbotQuestions[chatbotState.step]);
                        }, 500);
                    } else {
                        addMessage('bot', "Je n'ai pas reconnu les types de voyage. Veuillez choisir parmi ville, plage, nature, culture, aventure, détente, gastronomie, romantique (vous pouvez en sélectionner plusieurs en les séparant par des virgules).");
                    }
                    break;
                    
                case 2: // Budget
                    const budgets = ["économique", "moyen", "élevé"];
                    if (budgets.includes(input)) {
                        chatbotState.preferences.budget = input;
                        chatbotState.step++;
                        setTimeout(() => {
                            addMessage('bot', chatbotQuestions[chatbotState.step]);
                        }, 500);
                    } else {
                        addMessage('bot', "Je n'ai pas compris votre budget. Veuillez choisir parmi économique, moyen, ou élevé.");
                    }
                    break;
                    
                case 3: // Climat
                    const climats = ["tropical", "méditerranéen", "tempéré", "désertique", "montagneux"];
                    const userClimats = input.split(',').map(c => c.trim());
                    const validClimats = userClimats.filter(c => climats.includes(c));
                    
                    if (validClimats.length > 0) {
                        chatbotState.preferences.climat = validClimats;
                        chatbotState.step++;
                        setTimeout(() => {
                            addMessage('bot', chatbotQuestions[chatbotState.step]);
                        }, 500);
                    } else {
                        addMessage('bot', "Je n'ai pas reconnu les climats. Veuillez choisir parmi tropical, méditerranéen, tempéré, désertique, montagneux (vous pouvez en sélectionner plusieurs en les séparant par des virgules).");
                    }
                    break;
                    
                case 4: // Durée
                    const durees = ["week-end", "une semaine", "deux semaines", "plus"];
                    if (durees.includes(input)) {
                        chatbotState.preferences.duree = input;
                        chatbotState.step++;
                        
                        // Générer les recommandations
                        setTimeout(() => {
                            addMessage('bot', "Merci pour vos réponses ! Voici les destinations que je vous recommande :");
                            
                            const recommandations = recommanderDestinations(chatbotState.preferences);
                            
                            if (recommandations.length > 0) {
                                let htmlContent = '<div class="destinations-recommendations">';
                                recommandations.forEach(dest => {
                                    htmlContent += formatDestination(dest);
                                });
                                htmlContent += '</div>';
                                
                                setTimeout(() => {
                                    addMessage('bot', htmlContent, true);
                                }, 500);
                                
                                setTimeout(() => {
                                    addMessage('bot', "Ces destinations vous plaisent-elles ? Vous pouvez me poser des questions spécifiques sur ces lieux ou recommencer la recherche en tapant 'recommencer'.");
                                }, 1000);
                            } else {
                                addMessage('bot', "Je n'ai pas trouvé de destinations correspondant exactement à vos critères. Essayons avec des critères plus larges. Tapez 'recommencer' pour une nouvelle recherche.");
                            }
                        }, 500);
                    } else {
                        addMessage('bot', "Je n'ai pas compris votre choix de durée. Veuillez choisir parmi week-end, une semaine, deux semaines, ou plus.");
                    }
                    break;
                    
                default:
                    // Traitement des questions après les recommandations
                    if (input === "recommencer") {
                        chatbotState = {
                            step: 0,
                            preferences: {
                                continent: null,
                                type: [],
                                budget: null,
                                climat: [],
                                duree: null
                            }
                        };
                        setTimeout(() => {
                            addMessage('bot', "Recommençons ! " + chatbotQuestions[0]);
                        }, 500);
                    } else if (input.includes("merci")) {
                        addMessage('bot', "Je vous en prie ! N'hésitez pas à revenir si vous avez besoin d'autres recommandations de voyage. Bon voyage !");
                    } else {
                        // Recherche de mots-clés dans la question
                        const destinationsNoms = destinations.map(d => d.nom.toLowerCase());
                        const mentionnedDestination = destinationsNoms.find(nom => input.includes(nom.toLowerCase()));
                        
                        if (mentionnedDestination) {
                            const destination = destinations.find(d => d.nom.toLowerCase() === mentionnedDestination);
                            addMessage('bot', `${destination.nom} est une destination ${destination.type.join(', ')} située en ${destination.pays}. ${destination.description} La meilleure période pour y aller est ${destination.saison.join(' ou ')} et le budget est généralement ${destination.budget}.`);
                        } else if (input.includes("budget") || input.includes("prix") || input.includes("coût")) {
                            addMessage('bot', "Les budgets varient selon les destinations. Les destinations économiques incluent Bali, Bangkok et Marrakech. Les destinations à budget moyen comprennent Paris, Rome et Barcelone. Les destinations plus coûteuses sont New York, Tokyo et les Maldives.");
                        } else if (input.includes("quand") || input.includes("saison") || input.includes("période")) {
                            addMessage('bot', "La meilleure période dépend de la destination. Pour l'Europe, le printemps et l'automne sont idéaux. Pour l'Asie tropicale, la saison sèche (novembre à avril) est recommandée. Pour les destinations de l'hémisphère sud, notre hiver correspond à leur été.");
                        } else if (input.includes("activité") || input.includes("faire")) {
                            addMessage('bot', "Chaque destination offre des activités uniques. Les villes européennes sont parfaites pour la culture et la gastronomie. Les destinations tropicales proposent plages et sports nautiques. Les destinations asiatiques combinent souvent culture, temples et cuisine locale.");
                        } else {
                            addMessage('bot', "Je ne suis pas sûr de comprendre votre question. Vous pouvez me demander des informations sur une destination spécifique, sur les budgets, les meilleures périodes pour voyager ou les activités recommandées. Ou tapez 'recommencer' pour une nouvelle recherche.");
                        }
                    }
            }
        }

        // Initialisation du chat
        document.addEventListener('DOMContentLoaded', function() {
            // Message de bienvenue
            setTimeout(() => {
                addMessage('bot', chatbotQuestions[0]);
            }, 500);
            
            // Gestion de l'envoi de messages
            const userInput = document.getElementById('userInput');
            const sendButton = document.getElementById('sendButton');
            
            function sendMessage() {
                const message = userInput.value.trim();
                if (message) {
                    addMessage('user', message);
                    userInput.value = '';
                    processUserInput(message);
                }
            }
            
            sendButton.addEventListener('click', sendMessage);
            
            userInput.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    sendMessage();
                }
            });
        });
    </script>
</body>
</html>
