<?php
// Définir les variables pour le header
$page_title = "Carte Interactive";
$current_page = "map";
$additional_css = ["static/map.css", "https://unpkg.com/leaflet/dist/leaflet.css"];

// Inclure le header
include 'header.php';
require_once 'utils/utils.php';
?>

<div class="map-page">
    <div class="destinations-hero">
        <div class="hero-background">
            <img src="static/bg.webp" alt="Carte interactive" class="bg">
            <div class="overlay"></div>
        </div>
        <div class="container">
            <div class="hero-content text-center">
                <h1>Carte Interactive</h1>
                <p class="lead">Explorez le monde et trouvez votre prochaine destination</p>

                <div class="search-box">
                    <div class="input-group">
                        <input type="text" class="form-control" id="search" placeholder="Rechercher un lieu...">
                        <button class="btn btn-outline-primary" id="geolocateBtn">
                            <i class="fas fa-map-marker-alt"></i> Utiliser ma position
                        </button>
                        <button class="btn btn-search" id="searchBtn">
                            <i class="fas fa-search"></i> Rechercher
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

        <div class="map-container">
            <div id="map-2"></div>
        </div>
</div>

<!-- Include Leaflet.js library -->
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

<!-- Custom JavaScript for Map Functionality -->
<script>
    // Fonction de debounce pour limiter les appels de fonction
    function debounce(func, wait) {
        let timeout;
        return function() {
            const context = this;
            const args = arguments;
            clearTimeout(timeout);
            timeout = setTimeout(function() {
                func.apply(context, args);
            }, wait);
        };
    }

    // Fonction de throttle pour limiter les appels de fonction
    function throttle(func, limit) {
        let inThrottle;
        return function() {
            const context = this;
            const args = arguments;
            if (!inThrottle) {
                func.apply(context, args);
                inThrottle = true;
                setTimeout(() => inThrottle = false, limit);
            }
        };
    }

    // Gestionnaire de marqueurs pour éviter les fuites de mémoire
    class MarkerManager {
        constructor(map) {
            this.map = map;
            this.markers = [];
        }

        addMarker(lat, lon, popupContent) {
            const marker = L.marker([lat, lon]).addTo(this.map)
                .bindPopup(popupContent)
                .openPopup();
            this.markers.push(marker);
            return marker;
        }

        clearAllExcept(keepMarker) {
            this.markers.forEach(marker => {
                if (marker !== keepMarker) {
                    this.map.removeLayer(marker);
                }
            });
            this.markers = this.markers.filter(marker => marker === keepMarker);
        }

        clear() {
            this.markers.forEach(marker => {
                this.map.removeLayer(marker);
            });
            this.markers = [];
        }
    }

    // Initialisation de la carte avec lazy loading
    function initMap() {
        // Vérifier si la carte est visible dans la fenêtre
        const mapElement = document.getElementById('map-2');
        if (!mapElement) return;

        // Ajouter un indicateur de chargement
        const loadingIndicator = document.createElement('div');
        loadingIndicator.className = 'map-loading';
        loadingIndicator.innerHTML = '<div class="spinner"></div><p>Chargement de la carte...</p>';
        mapElement.parentNode.insertBefore(loadingIndicator, mapElement);

        // Initialisation de la carte
      const map = L.map('map-2').setView([20, 0], 2); // Vue globale

      //const map = L.map('map-2').setView([20, 0], 2); // Vue globale

        // Gestionnaire de marqueurs
        const markerManager = new MarkerManager(map);

        // Ajouter les fonds de carte avec chargement optimisé
        const streets = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors',
            maxZoom: 19,
            crossOrigin: true
        }).addTo(map);

        // Supprimer l'indicateur de chargement une fois la carte chargée
        streets.on('load', function() {
            if (loadingIndicator.parentNode) {
                loadingIndicator.parentNode.removeChild(loadingIndicator);
            }
        });

        // Fonction de géolocalisation optimisée
        document.getElementById('geolocateBtn')?.addEventListener('click', function() {
            // Ajouter un indicateur de chargement au bouton
            const originalText = this.innerHTML;
            this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Localisation en cours...';
            this.disabled = true;

            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(function(position) {
                    const lat = position.coords.latitude;
                    const lon = position.coords.longitude;

                    // Utiliser le gestionnaire de marqueurs
                    const userMarker = markerManager.addMarker(lat, lon, 'Vous êtes ici!');
                    markerManager.clearAllExcept(userMarker);

                    map.setView([lat, lon], 13); // Centrer la carte sur la position de l'utilisateur

                    // Restaurer le bouton
                    document.getElementById('geolocateBtn').innerHTML = originalText;
                    document.getElementById('geolocateBtn').disabled = false;
                }, function() {
                    alert('Erreur lors de la géolocalisation.');
                    // Restaurer le bouton
                    document.getElementById('geolocateBtn').innerHTML = originalText;
                    document.getElementById('geolocateBtn').disabled = false;
                }, {
                    enableHighAccuracy: true,
                    timeout: 5000,
                    maximumAge: 0
                });
            } else {
                alert('La géolocalisation n\'est pas supportée par votre navigateur.');
                // Restaurer le bouton
                document.getElementById('geolocateBtn').innerHTML = originalText;
                document.getElementById('geolocateBtn').disabled = false;
            }
        });

        // Recherche de lieu avec l'API Nominatim (avec debounce)
        const searchInput = document.getElementById('search');
        const searchBtn = document.getElementById('searchBtn');

        // Activer la recherche en appuyant sur Entrée
        searchInput?.addEventListener('keyup', function(event) {
            if (event.key === 'Enter') {
                performSearch();
            }
        });

        searchBtn?.addEventListener('click', performSearch);

        function performSearch() {
            const query = searchInput.value;
            if (!query) return;

            // Ajouter un indicateur de chargement au bouton
            const originalText = searchBtn.innerHTML;
            searchBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Recherche...';
            searchBtn.disabled = true;

            const url = 'https://nominatim.openstreetmap.org/search?format=json&q=' + encodeURIComponent(query);

            fetch(url)
                .then(function(response) { return response.json(); })
                .then(function(data) {
                    if (data.length > 0) {
                        const lat = data[0].lat;
                        const lon = data[0].lon;

                        // Utiliser le gestionnaire de marqueurs
                        markerManager.clear();
                        markerManager.addMarker(lat, lon, '<b>' + data[0].display_name + '</b>');

                        map.setView([lat, lon], 10);
                    } else {
                        alert('Lieu introuvable !');
                    }
                    // Restaurer le bouton
                    searchBtn.innerHTML = originalText;
                    searchBtn.disabled = false;
                })
                .catch(function(error) { 
                    console.error('Erreur:', error);
                    alert('Erreur lors de la recherche. Veuillez réessayer.');
                    // Restaurer le bouton
                    searchBtn.innerHTML = originalText;
                    searchBtn.disabled = false;
                });
        }

        // Animation au défilement pour la carte avec throttling
        const checkScroll = throttle(function() {
            const mapContainer = document.querySelector('.map-container');
            const mapTop = mapContainer.getBoundingClientRect().top;
            const windowHeight = window.innerHeight;

            if (mapTop < windowHeight * 0.9) {
                mapContainer.classList.add('visible');
                // Désactiver l'écouteur d'événement une fois l'animation déclenchée
                window.removeEventListener('scroll', checkScroll);
            }
        }, 100); // Limiter à une exécution toutes les 100ms

        window.addEventListener('scroll', checkScroll);
        checkScroll(); // Vérifier au chargement initial

        return map;
    }

    // Initialiser la carte lorsque le DOM est chargé
    let map;
    document.addEventListener('DOMContentLoaded', function() {
        // Utiliser un IntersectionObserver pour charger la carte uniquement lorsqu'elle est visible
        const mapContainer = document.querySelector('.map-container');

        if ('IntersectionObserver' in window) {
            const observer = new IntersectionObserver(
                (entries) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting && !map) {
                            map = initMap();
                            observer.disconnect();
                        }
                    });
                },
                { threshold: 0.1 }
            );

            observer.observe(mapContainer);
        } else {
            // Fallback pour les navigateurs qui ne supportent pas IntersectionObserver
            map = initMap();
        }
    });
</script>

<style>
  #map-2 {
    height: 500px; /* ou la hauteur souhaitée */
    width: 100%;
  }

</style>

<?php
// Inclure le footer
include 'footer.php';
?>
