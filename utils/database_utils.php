<?php
// Fichier contenant les fonctions liées à la base de données

// Inclure le fichier de configuration
require_once __DIR__ . '/../config/config.php';

// Fonction pour établir une connexion PDO à la base de données
function getDbConnection() {
    global $config;

    try {
        $dsn = "mysql:host={$config['db']['host']};dbname={$config['db']['dbname']};charset={$config['db']['charset']}";
        $pdo = new PDO($dsn, $config['db']['username'], $config['db']['password']);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    } catch (PDOException $e) {
        die("Erreur de connexion à la base de données : " . $e->getMessage());
    }
}

// Fonction pour exécuter une requête avec des paramètres
function executeQuery($sql, $params = []) {
    $pdo = getDbConnection();
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    } catch (PDOException $e) {
        die("Erreur d'exécution de la requête : " . $e->getMessage());
    }
}

// Fonction pour récupérer tous les résultats d'une requête
function fetchAll($sql, $params = []) {
    $stmt = executeQuery($sql, $params);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Fonction pour récupérer une seule ligne de résultat
function fetchOne($sql, $params = []) {
    $stmt = executeQuery($sql, $params);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// Fonction pour récupérer une seule valeur
function fetchValue($sql, $params = []) {
    $stmt = executeQuery($sql, $params);
    return $stmt->fetchColumn();
}

// Fonction pour insérer des données et récupérer l'ID généré
function insert($sql, $params = []) {
    $pdo = getDbConnection();
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $pdo->lastInsertId();
    } catch (PDOException $e) {
        die("Erreur d'insertion : " . $e->getMessage());
    }
}

// Fonction pour mettre à jour des données et récupérer le nombre de lignes affectées
function update($sql, $params = []) {
    $stmt = executeQuery($sql, $params);
    return $stmt->rowCount();
}
