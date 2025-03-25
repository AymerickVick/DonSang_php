<?php

class Database {
    // Paramètres de connexion à la base de données
    private $host = "localhost";        // Hôte (généralement localhost sur WAMP)
    private $dbname = "gestion_donneurs"; // Nom de la base de données
    private $username = "root";         // Utilisateur par défaut de WAMP
    private $password = "Keyce-2024";             // Mot de passe par défaut (vide sur WAMP)
    private $pdo;                       // Instance PDO pour la connexion

    // Méthode pour établir la connexion
    public function connect() {
        // Vérifie si la connexion existe déjà
        if ($this->pdo === null) {
            try {
                // Création de l'instance PDO
                $this->pdo = new PDO(
                    "mysql:host={$this->host};dbname={$this->dbname};charset=utf8",
                    $this->username,
                    $this->password
                );
                // Configuration des attributs PDO
                $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            } catch (PDOException $e) {
                // Gestion des erreurs de connexion
                die("Erreur de connexion à la base de données : " . $e->getMessage());
            }
        }
        return $this->pdo;
    }

    // Méthode pour fermer la connexion (optionnelle)
    public function disconnect() {
        $this->pdo = null;
    }
}

?>