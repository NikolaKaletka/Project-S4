CREATE DATABASE PlanVoyages;
USE PlanVoyages;

CREATE TABLE Utilisateur(
	id_utilisateur INTEGER PRIMARY KEY AUTO_INCREMENT,
    nom VARCHAR(100) NOT NULL,
    mot_de_passe VARCHAR(100) NOT NULL,
    email TEXT NOT NULL
);
INSERT INTO Utilisateur(id_utilisateur, nom, mot_de_passe, email)
VALUES 
	(1, 'Elsa', 'Frozen2', 'elsa.queen@gmail.com'),
    (2, 'Anna', 'Frozen1!', 'anna.priness@gmail.com'),
	(3, 'Melissa', 'I_love_this_project','44008939@parisnanterre.fr');


CREATE TABLE Voyage(
	id_voyage INTEGER PRIMARY KEY AUTO_INCREMENT,
    destination VARCHAR(100) NOT NULL,
    date_debut DATE NOT NULL,
    date_fin DATE NOT NULL,
    CHECK (date_debut < date_fin),
    image TEXT,
    ref_utilisateur INTEGER,
    FOREIGN KEY (ref_utilisateur) REFERENCES Utilisateur(id_utilisateur)
);
INSERT INTO Voyage(id_voyage, destination, date_debut, date_fin, ref_utilisateur)
VALUES
	(1, 'Roma', '2025-06-20','2025-06-30',3),
    (2, 'Varsovie', '2025-09-10','2025-09-30',3),
    (3, 'Berlin', '2025-06-29','2025-07-08',3),
    (4, 'Paris', '2025-03-20','2025-03-25',1),
    (5, 'Londre', '2025-05-10','2025-05-24',2);




CREATE TABLE Activite(
	id_activite INTEGER PRIMARY KEY AUTO_INCREMENT,
    nom VARCHAR(250) NOT NULL,
    adresse VARCHAR(250),
    description_activite TEXT,
    horaire TIME,
    avec_ticket CHAR(3),
    CONSTRAINT check_ticket CHECK (avec_ticket = 'oui' OR avec_ticket = 'non'),
    date_activite DATE,
    ref_voyage INTEGER,
    FOREIGN KEY (ref_voyage) REFERENCES Voyage(id_voyage)
);
INSERT INTO Activite(id_activite, nom, adresse, avec_ticket, ref_voyage)
VALUES
	(1,'Tour Eiffle' ,'Trocadero', 'oui', 4 );


CREATE TABLE Logement(
	id_logement INTEGER PRIMARY KEY AUTO_INCREMENT,
    nom VARCHAR(250) NOT NULL,
	adresse TEXT NOT NULL,
    horaire_check_in TIME,
    horaire_check_out TIME,
	petit_dejeuner TINYINT, -- 1 si oui, 0 sinon
    numero_reservation VARCHAR(100),
    ref_voyage INTEGER,
    FOREIGN KEY (ref_voyage) REFERENCES Voyage(id_voyage)
);
INSERT INTO Logement(id_logement, nom, adresse, horaire_check_in, horaire_check_out, petit_dejeuner, numero_reservation, ref_voyage)
VALUES
	(1, 'Grand Hotel', '12 Rue de Leon', '15:00', '11:00', '1', 'HY7RFE4QVB90', 4);

CREATE TABLE Transport(
	id_transport INTEGER PRIMARY KEY AUTO_INCREMENT,
    type_transport VARCHAR(100) NOT NULL,
    date_transport DATE,
    horaire_depart TIME,
    horaire_arrive TIME,
    place_depart VARCHAR(250), -- station/aeroport
    numero_terminal INTEGER,
    bagage TEXT, -- nombre et taille de valises
    adresse_station_service TEXT,
    adress_parking TEXT,
    prix_parking DECIMAL(10,2),
    ref_voyage INTEGER,
    FOREIGN KEY (ref_voyage) REFERENCES Voyage(id_voyage)
);
INSERT INTO Transport(id_transport, type_transport, date_transport, horaire_depart, horaire_arrive, place_depart, 
					  numero_terminal, bagage, ref_voyage)
VALUES 
	(1, 'avion', '20-03-20','10:35', '12:40', 'aeroport Orly Paris', 1,
    'une sac à dos 15x25x40 et une petite valise 20x30x55' ,4);

CREATE TABLE Depense(
	id_depense INTEGER PRIMARY KEY AUTO_INCREMENT,
    categorie VARCHAR(100),
    montant INTEGER,
	ref_voyage INTEGER,
    FOREIGN KEY (ref_voyage) REFERENCES Voyage(id_voyage)
);
INSERT INTO Depense(id_depense, categorie, montant, ref_voyage)
VALUES
	(1, 'souvenirs', 150, 4);

CREATE TABLE Restaurant(
	id_restaurant INTEGER PRIMARY KEY AUTO_INCREMENT,
    nom VARCHAR(150) NOT NULL,
    adresse TEXT,
    type_restaurant VARCHAR(100),
    date_restaurant DATE,
    ref_voyage INTEGER,
    FOREIGN KEY (ref_voyage) REFERENCES Voyage(id_voyage)
);
INSERT INTO Restaurant(id_restaurant, nom, adresse, ref_voyage)
VALUES
	(1, "Chez Pierre", '10 Avenue de la liberté', 4);

CREATE TABLE Transport_ville(
	id_transport INTEGER PRIMARY KEY AUTO_INCREMENT,
    type_billet TEXT,
    prix DECIMAL(10,2),
    place_achat_billet TEXT,
    informations TEXT,
    ref_voyage INTEGER,
    FOREIGN KEY (ref_voyage) REFERENCES Voyage(id_voyage)
);
INSERT INTO Transport_ville(id_transport, type_billet,  prix, place_achat_billet, informations, ref_voyage)
VALUES
	(1, '1 semaine', 30, 'online', 'Ticket pour zoons 1-5 sans aeroprt',4);

CREATE TABLE Ticket_activite(
	id_ticket INTEGER PRIMARY KEY AUTO_INCREMENT,
    nom VARCHAR(150),
    place_achat_billet TEXT,
    prix DECIMAL(10,2),
    ref_activite INTEGER,
    FOREIGN KEY (ref_activite) REFERENCES  Activite(id_activite)
);
INSERT Ticket_activite(id_ticket, nom, prix, place_achat_billet, ref_activite)
VALUES
	(1, 'Ticket jeune 18-26', 18, 'online', 1);

CREATE TABLE iteam_de_checklist_avant_depart( -- one iteam
	id_checklist INTEGER PRIMARY KEY AUTO_INCREMENT,
    nom VARCHAR(250),
    description_tache TEXT,
	est_fait TINYINT, -- 1 si une tâche est faite, 0 sinon
    ref_voyage INTEGER,
    FOREIGN KEY (ref_voyage) REFERENCES Voyage(id_voyage)
);
INSERT Checklist_avant_depart(id_checklist, description_tache, est_fait, ref_voyage)
VALUES 
	(1, 'packer la brosse les dents', 0, 4);
    
 








