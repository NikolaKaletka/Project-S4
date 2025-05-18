create database planvoyages;
use planvoyages;

create table utilisateur(
                          id_utilisateur integer primary key auto_increment,
                          nom varchar(100) not null,
                          mot_de_passe varchar(100) not null,
                          email text not null
);
insert into utilisateur(id_utilisateur, nom, mot_de_passe, email)
values
  (1, 'Elsa', 'Frozen2', 'elsa.queen@gmail.com'),
  (2, 'Anna', 'Frozen1!', 'anna.priness@gmail.com'),
  (3, 'Melissa', 'I_love_this_project','44008939@parisnanterre.fr');


create table voyage(
                     id_voyage integer primary key auto_increment,
                     destination varchar(100) not null,
                     date_debut date not null,
                     date_fin date not null,
                     check (date_debut < date_fin),
                     image text,
                     ref_utilisateur integer,

                     foreign key (ref_utilisateur) references utilisateur(id_utilisateur)
);
insert into voyage(id_voyage, destination, date_debut, date_fin, ref_utilisateur)
values
  (1, 'Rome', '2025-06-20','2025-06-30',3),
  (2, 'Varsovie', '2025-09-10','2025-09-30',3),
  (3, 'Berlin', '2025-06-29','2025-07-08',3),
  (4, 'Paris', '2025-03-20','2025-03-25',1),
  (5, 'Londres', '2025-05-10','2025-05-24',2);

CREATE TABLE voyage_partage (
                            id_voyage INTEGER,
                            id_utilisateur INTEGER,
                            PRIMARY KEY (id_voyage, id_utilisateur),
                            FOREIGN KEY (id_voyage) REFERENCES voyage(id_voyage),
                            FOREIGN KEY (id_utilisateur) REFERENCES utilisateur(id_utilisateur)
);


create table activite(
                       id_activite integer primary key auto_increment,
                       nom varchar(250) not null,
                       adresse varchar(250),
                       description_activite text,
                       horaire time,
                       avec_ticket char(3),
                       constraint check_ticket check (avec_ticket = 'oui' or avec_ticket = 'non'),
                       date_activite date,
                       ref_voyage integer,
                       foreign key (ref_voyage) references voyage(id_voyage)
);
insert into activite(id_activite, nom, adresse, avec_ticket, ref_voyage)
values
  (1,'Tour Eiffel' ,'Trocadero', 'oui', 4 );


create table logement(
                       id_logement integer primary key auto_increment,
                       nom varchar(250) not null,
                       adresse text not null,
                       horaire_check_in time,
                       horaire_check_out time,
                       petit_dejeuner tinyint, -- 1 si oui, 0 sinon
                       numero_reservation varchar(100),
                       ref_voyage integer,
                       foreign key (ref_voyage) references voyage(id_voyage)
);
insert into logement(id_logement, nom, adresse, horaire_check_in, horaire_check_out, petit_dejeuner, numero_reservation, ref_voyage)
values
  (1, 'Grand Hotel', '12 Rue de Leon', '15:00', '11:00', '1', 'HY7RFE4QVB90', 4);

create table transport(
                        id_transport integer primary key auto_increment,
                        type_transport varchar(100) not null,
                        date_transport date,
                        horaire_depart time,
                        horaire_arrive time,
                        place_depart varchar(250), -- station/aeroport
                        numero_terminal integer,
                        bagage text, -- nombre et taille de valises
                        adresse_station_service text,
                        adress_parking text,
                        prix_parking decimal(10,2),
                        ref_voyage integer,
                        foreign key (ref_voyage) references voyage(id_voyage)
);
insert into transport(id_transport, type_transport, date_transport, horaire_depart, horaire_arrive, place_depart,
                      numero_terminal, bagage, ref_voyage)
values
  (1, 'avion', '20-03-20','10:35', '12:40', 'aeroport Orly Paris', 1,
   'une sac à dos 15x25x40 et une petite valise 20x30x55' ,4);

create table depense(
                      id_depense integer primary key auto_increment,
                      categorie varchar(100),
                      montant integer,
                      ref_voyage integer,
                      foreign key (ref_voyage) references voyage(id_voyage)
);
insert into depense(id_depense, categorie, montant, ref_voyage)
values
  (1, 'souvenirs', 150, 4);

create table restaurant(
                         id_restaurant integer primary key auto_increment,
                         nom varchar(150) not null,
                         adresse text,
                         type_restaurant varchar(100),
                         date_restaurant date,
                         ref_voyage integer,
                         foreign key (ref_voyage) references voyage(id_voyage)
);
insert into restaurant(id_restaurant, nom, adresse, ref_voyage)
values
  (1, 'Chez Pierre', '10 Avenue de la liberté', 4);

create table transport_ville(
                             id_transport integer primary key auto_increment,
                             type_billet text,
                             prix decimal(10,2),
                             place_achat_billet text,
                             informations text,
                             ref_voyage integer,
                             foreign key (ref_voyage) references voyage(id_voyage)
);
insert into transport_ville(id_transport, type_billet,  prix, place_achat_billet, informations, ref_voyage)
values
  (1, '1 semaine', 30, 'online', 'Ticket pour zoons 1-5 sans aeroprt',4);

create table ticket_activite(
                             id_ticket integer primary key auto_increment,
                             nom varchar(150),
                             place_achat_billet text,
                             prix decimal(10,2),
                             ref_activite integer,
                             foreign key (ref_activite) references activite(id_activite)
);
insert into ticket_activite(id_ticket, nom, prix, place_achat_billet, ref_activite)
values
	(1, 'Ticket jeune 18-26', 18, 'online', 1);

create table item_checklist_avant_depart( -- one iteam
                                       id_checklist integer primary key auto_increment,
                                       nom varchar(250),
                                       description_tache text,
                                       est_fait tinyint, -- 1 si une tâche est faite, 0 sinon
                                       ref_voyage integer,
                                       foreign key (ref_voyage) references voyage(id_voyage)
);
insert into item_checklist_avant_depart(id_checklist, description_tache, est_fait, ref_voyage)
values
	(1, 'packer la brosse les dents', 0, 4);

create table avis (
                    id_avis integer primary key auto_increment,
                    commentaire text not null,
                    note integer check (note >= 1 and note <= 5), -- Note entre 1 et 5
                    date_avis date default (date(current_timestamp)), -- Date de publication de l'avis
                    ref_utilisateur integer,
                    ref_voyage integer,
                    foreign key (ref_utilisateur) references utilisateur(id_utilisateur),
                    foreign key (ref_voyage) references voyage(id_voyage)
);
insert into avis (commentaire, note, ref_utilisateur, ref_voyage)
values
  ('Super voyage, Rome était magnifique !', 5, 3, 1),
  ('Voyage agréable mais quelques soucis avec le logement.', 3, 3, 2);
