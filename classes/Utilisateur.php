<?php
require_once '../config/Database.php';

class Utilisateur {
    private $idUtilisateur;
    private $nom;
    private $email;
    private $motDePasse;
    private $role;
    private $dateCreation;
    private $derniereConnexion;
    private $db;

    public function __construct() {
        $this->db = (new Database())->connect();
    }

    // Getters
    public function getIdUtilisateur() { return $this->idUtilisateur; }
    public function getNom() { return $this->nom; }
    public function getEmail() { return $this->email; }
    public function getMotDePasse() { return $this->motDePasse; }
    public function getRole() { return $this->role; }
    public function getDateCreation() { return $this->dateCreation; }
    public function getDerniereConnexion() { return $this->derniereConnexion; }

    // Setters
    public function setNom($nom) { $this->nom = $nom; }
    public function setEmail($email) { $this->email = $email; }
    public function setMotDePasse($motDePasse) { $this->motDePasse = password_hash($motDePasse, PASSWORD_DEFAULT); }
    public function setRole($role) { $this->role = $role; }
    public function setDateCreation($date) { $this->dateCreation = $date; }
    public function setDerniereConnexion($date) { $this->derniereConnexion = $date; }

    // Ajouter un utilisateur
    public function ajouter() {
        $query = "INSERT INTO Utilisateurs (Nom, Email, Mot_de_Passe, Role, Date_Creation) 
                  VALUES (:nom, :email, :motDePasse, :role, NOW())";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':nom', $this->nom);
        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':motDePasse', $this->motDePasse);
        $stmt->bindParam(':role', $this->role);
        return $stmt->execute();
    }

    // Lire un utilisateur spécifique
    public function lire($id) {
        $query = "SELECT * FROM Utilisateurs WHERE ID_Utilisateur = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Lire tous les utilisateurs
    public function lireTous() {
        $query = "SELECT * FROM Utilisateurs";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Mettre à jour un utilisateur
    public function mettreAJour($id) {
        $query = "UPDATE Utilisateurs SET Nom = :nom, Email = :email, Role = :role WHERE ID_Utilisateur = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':nom', $this->nom);
        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':role', $this->role);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    // Mettre à jour le mot de passe
    public function mettreAJourMotDePasse($id) {
        $query = "UPDATE Utilisateurs SET Mot_de_Passe = :motDePasse WHERE ID_Utilisateur = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':motDePasse', $this->motDePasse);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    // Supprimer un utilisateur
    public function supprimer($id) {
        $query = "DELETE FROM Utilisateurs WHERE ID_Utilisateur = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    // Vérifier les identifiants pour la connexion
    public function verifierConnexion($email, $motDePasse) {
        $query = "SELECT * FROM Utilisateurs WHERE Email = :email";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        $utilisateur = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($utilisateur && password_verify($motDePasse, $utilisateur['Mot_de_Passe'])) {
            // Mettre à jour la dernière connexion
            $query = "UPDATE Utilisateurs SET Derniere_Connexion = NOW() WHERE ID_Utilisateur = :id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':id', $utilisateur['ID_Utilisateur']);
            $stmt->execute();
            return $utilisateur;
        }
        return false;
    }
}
?>