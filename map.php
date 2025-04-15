<?php
include 'header.php';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carte Interactive avec Géolocalisation</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
</head>
<body>
    <style>
        body {
    font-family: Arial, sans-serif;
    margin: 0;
    padding: 0;
    text-align: center;
}

header {
    background-color:rgb(171, 214, 246);
    color: white;
    padding: 15px;
}

#search {
    padding: 8px;
    width: 250px;
    margin-right: 5px;
}

#searchBtn, #geolocateBtn {
    padding: 8px;
    cursor: pointer;
    background-color: white;
    border: none;
    border-radius: 5px;
}

#map {
    width: 100%;
    height: 80vh;
    margin-top: 10px;
    border-radius: 10px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.3);
}

    </style>
    <header>
        <h1>Carte Interactive</h1>
        <input type="text" id="search" placeholder="Rechercher un lieu...">
        <button id="searchBtn">Rechercher</button>
        <button id="geolocateBtn">Utiliser ma position</button>
    </header>
    
    <div id="map"></div>

    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <script> 
        document.addEventListener("DOMContentLoaded", function() {
    // Initialisation de la carte
    var map = L.map('map').setView([20, 0], 2); // Vue globale

    // Ajouter les fonds de carte
    var streets = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    var satellite = L.tileLayer('https://{s}.tile.thunderforest.com/spinal-map/{z}/{x}/{y}.png?apikey=YOUR_API_KEY', {
        attribution: '© Thunderforest',
    });

    // Variable de géolocalisation
    var userMarker;
    
    // Fonction de géolocalisation
    document.getElementById("geolocateBtn").addEventListener("click", function() {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function(position) {
                var lat = position.coords.latitude;
                var lon = position.coords.longitude;
                
                if (userMarker) {
                    map.removeLayer(userMarker);
                }

                userMarker = L.marker([lat, lon]).addTo(map)
                    .bindPopup("Vous êtes ici!")
                    .openPopup();

                map.setView([lat, lon], 13); // Centrer la carte sur la position de l'utilisateur
            }, function() {
                alert("Erreur lors de la géolocalisation.");
            });
        } else {
            alert("La géolocalisation n'est pas supportée par votre navigateur.");
        }
    });

    // Recherche de lieu avec l'API Nominatim
    document.getElementById("searchBtn").addEventListener("click", function() {
        var query = document.getElementById("search").value;
        if (!query) return;

        var url = `https://nominatim.openstreetmap.org/search?format=json&q=${query}`;

        fetch(url)
            .then(response => response.json())
            .then(data => {
                if (data.length > 0) {
                    var lat = data[0].lat;
                    var lon = data[0].lon;

                    // Ajouter un marqueur sur la carte
                    L.marker([lat, lon]).addTo(map)
                        .bindPopup(`<b>${data[0].display_name}</b>`)
                        .openPopup();

                    map.setView([lat, lon], 10);
                } else {
                    alert("Lieu introuvable !");
                }
            })
            .catch(error => console.error('Erreur:', error));
    });

    // Sauvegarder les recherches dans une base de données MySQL (facultatif)
    function saveSearchToDatabase(query, lat, lon) {
    fetch('save_search.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            query: query,
            lat: lat,
            lon: lon,
        })
    })
    .then(response => response.json())  // Convertir la réponse en JSON
    .then(data => {
        console.log("Réponse du serveur :", data);
        if (data.status === "error") {
            alert("Erreur : " + data.message);
        } else {
            console.log("Donnée enregistrée avec succès");
        }
    })
    .catch(error => console.error('Erreur lors de la requête fetch:', error));
}

});

    </script>
</body>
</html>
