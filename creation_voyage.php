<?php
$host = '127.0.0.1'; // Adresse du serveur
$username = 'root'; // Nom d'utilisateur MySQL
$password = 'rootroot';
// Mot de passe MySQL (par défaut vide dans XAMPP)
$database = 'PlanVoyages'; // Nom de la base de données
// Connexion à la base de données
$conn = new mysqli($host, $username, $password, $database);
// Vérification de la connexion
if ($conn->connect_error) {
    die("Erreur de connexion : " . $conn->connect_error);
}
// Fermer la connexion (optionnel)
$conn->close();
?>

<!DOCTYPE html>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Creation de voyage</title>

</head>

<body topmargin="3" leftmargin="500" rightmargin = "1">
    <h1 style="color:blue;text-align:center;">Rome  20-30.06.2025</h1>


    <br>
    <form action="creation_voyage_envoyer.php" method="POST">
    <div >
      <h3>Liste à faire avant de voyage <button>Ajouter une tâche</button ></h3>
      <div class="line">
        <input type = "checkbox">
        <input type="text" id="name1" name="name1" size="45"><button>Supprimer</button>
      </div>
    <br>
    <div>
        <input type = "checkbox">
        <input type="text" id="name2" name="name2" size="45"><button>Supprimer</button>
    </div>
    <br>
    <div>
      <input type = "checkbox">
      <input type="text" id="name3" name=fname3" size="45"><button>Supprimer</button>
    </div>
    <br>
    <br>
    <h3>Transport <button>Ajoutez un transport </button></h3>

    <h4>Transport 1 <button style="margin-right: 0.5rem"> Modifier la nom </button>
    <button> Supprimer</button></h4>
    <div>
      <label for="transport">Type: </label>
      <select name="transport" id="transport">
        <option value="Avion">Avion</option>
        <option value="Train">Train</option>
        <option value="Bus">Bus</option>
        <option value="Bateu">Bateu</option>
        <option value="Voiture">Voiture</option>
      </select>
    </div>

    <br>
    <style>
        .form-group {
            display: flex;
            gap: 1%;
            align-items: center;
        }
    </style>
    <div class="form-group">
        <label for="destination">Destination:</label>
        <input type="text" id="destination" name="destination" size="25" placeholder="la ville de départure...">

        <label for="arrive"></label>
        <input type="text" id="arrive" name="arrive" size="25" placeholder="la ville d'arrivé...">
    </div>
    <br>
    <div>
        <label for="ddate">Date: </label>
        <input type="date" id="ddate" name="ddate">
    </div>
    <br>
    <div>
        <label for="time">Horaire de departure: </label>
        <input type="time" id="time" name="time">
    </div>

    <br>
    <div>
        <label for="time">Horaire d'arrive: </label>
        <input type="time" id="atime" name="timee">
    </div>

    <br>
    <div>
        <label for="time">Baggage: </label>
        <label for="oui"> oui</label>
        <input type="radio" id="oui" name="laguage" >
        <label for="non"> non</label>
        <input type="radio" id="non" name="laguage">
    </div>
    <br>


    <style>
        .form-group {
            display: flex;
            gap: 1%;
        }
        .form-group label {
            white-space: nowrap;
        }
    </style>

    <div class="form-group">
        <label for="detaillesb">Detailles de baggage:</label>
        <input type="text" id="detaillesb" name="detaillesb" size="45" placeholder="taille, nombre de baggage...">
    </div>
    <br>
    <div>
        <label for="numero">Numéro de terminale/gare:</label>
        <input type="number" id="numero" name="numero" min="0" max="100000" >
    </div>

<br><br>
    <h3>Logement</button> <button>Ajoutez une logement</button> </h3>

    <h4>Hotel 1 <button style="margin-right: 0.5rem">Modifier le nom</button><button>Supprimer</button></h4>

    <div>
        <label for="datel">Date: </label> du
        <input type="date" id="datel" name="ddate">
        <label for="datell"></label> au
        <input type="date" id="datell" name="ddate">
    </div>
    <br>
    <div>
        <label for="adress">Adresse:</label>
        <input type="text" id="adress" name="adress" size=25 placeholder = "la ville de départure...">
    </div>
    <br>
    <div>
        <label for="intime">Horaire de check-in: </label>
        <input type="time" id="intime" name="intime">
    </div>
    <br>
    <div>
        <label for="outtime">Horaire de check-out: </label>
        <input type="time" id="outtime" name="outtime">
    </div>
    <br>
    <div>
        <label for="Numeroreservation">Numero de reservation:</label>
        <input type="text" id="Numeroreservation" name="Numeroreservation" >
    </div>
    <br>
    <div>
        <label for="time">Petite déjeuner:   </label>
        <label for="ouipd"> oui</label>
        <input type="radio" id="ouipd" name="time" >
        <label for="nonpd"> non</label>
        <input type="radio" id="nonpd" name="time">
    </div>
    <br>
    <br>
    <h3>Transport dans la ville <button>Ajoutez une logement</button></h3>

    <h4>Transport dans la ville 1 <button style="margin-right: 0.5rem">Modifier le nom</button> <button>Supprimer</button></h4>

    <div>
        <label for="type">Type de transport:</label>
        <input type="text" id="type" name="type" size="30" placeholder ="ex: tram et bus, métro..." >
    </div>
<br>
    <div>
        <label for="typeticket">Type de ticket:</label>
        <input type="text" id="typeticket" name="typeticket" size="31" placeholder ="ex: 1 jour ticket, 90 minutes ticket..." >
    </div>
    <br>
    <div>
        <label for="prix">Prix:</label>
        <input type="text" id="prix" name="prix" size="10" >
    </div>
    <br>
    <div>
        <label for="information">Informations:</label>
        <input type="text" id="information" name="information" size="40" placeholder ="ex: ticket pour les 1-3 zoons sans aéroport..." >
    </div>
    <br>
    <div>
        <label for="place_achat">Place d'achat:</label>
        <input type="text" id="place_achat" name="iplace_achat" >
    </div>
    <br>
    <br>
    <h3>Activités <button>Ajoutez une activité</button></h3>

    <h4>Activité 1 <button style="margin-right: 0.5rem">Modifier le nom</button> <button>Supprimer</button></h4>
    <div>
        <label for="datea">Date: </label>
        <input type="date" id="datea" name="ddate">

    </div>
    <br>
    <div>
        <label for="aatime">Horaire: </label>
        <input type="time" id="aatime" name="aatime">
    </div>
    <br>
    <div>
        <label for="placeachat">Adresse:</label>
        <input type="text" id="placeachat" name="iplaceachat" size="30" >
    </div>

    <br>
    <div>
        <label for="placeachat">Informations: </label>
        <input type="text" id="info" name="info" size="40" placeholder ="ex: Il faut arriver 30 minutes en avance..." >
    </div>
    <br>
    <div>
        <label for="time">Ticket:  </label>
        <label for="ouit"> oui</label>
        <input type="radio" id="ouit" name="time" >
        <label for="nont"> non</label>
        <input type="radio" id="nont" name="time">
    </div>
    <br>
    <div>
        <label for="nomp">Nom de ticket:</label>
        <input type="text" id="nomp" name="nomp" size="40" placeholder ="ex: mieux de venir avant 7 heure de matain... " >
    </div>
    <br>
    <div>
        <label for="prixt">Prix de ticket:</label>
        <input type="text" id="prixt" name="prixt" size="10" >
    </div>

    <br>
    <div>
        <label for="lien">Lien de la réservation de ticket:</label>
        <input type="text" id="lien" name="lien" >
    </div>
    <br>
    <br>
    <h3>Restaurants <button>Ajoutez une restaurant</button></h3>

    <h4>Restaurant 1 <button style="margin-right: 0.5rem">Modifier le nom</button> <button>Supprimer</button></h4>
    <div>
        <label for="placeachatt">Adresse:</label>
        <input type="text" id="placeachatt" name="iplaceachatt" size="30" >
    </div>

    <br>
    <div>
        <label for="placeachat">Type: </label>
        <input type="text" id="infoo" name="infoo" size="40" placeholder ="ex: Cuisine italienne, fast food..." >
    </div>
    <br>
    <div>
        <label for="ddatee">Date: </label>
        <input type="date" id="ddatee" name="ddatee">

    </div>
    <br>
    <div>
        <label for="aaatime">Horaire: </label>
        <input type="time" id="aaatime" name="aaatime">
    </div>
    <br>

    <h4>Restaurant 2 <button style="margin-right: 0.5rem">Modifier le nom</button> <button>Supprimer</button></h4>
    <div>
        <label for="placeachattt">Adresse:</label>
        <input type="text" id="placeachattt" name="iplaceachattt" size="30" >
    </div>

    <br>
    <div>
        <label for="placeachat">Type: </label>
        <input type="text" id="infooo" name="infooo" size="40" placeholder ="ex: Cuisine italienne, fast food..." >
    </div>
    <br>
    <div>
        <label for="ddateee">Date: </label>
        <input type="date" id="ddateee" name="ddateee">

    </div>
    <br>
    <div>
        <label for="aaaatime">Horaire: </label>
        <input type="time" id="aaaatime" name="aaaatime">
    </div>
    <br>
        <br>

        <button type="submit">Save</button>

    </div>
    </form>
</body>
</html>
