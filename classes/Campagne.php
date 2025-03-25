<?php
require_once '../config/Database.php';

class Campagne {
    private $idCampagne;
    private $nomCampagne;
    private $dateDebut;
    private $dateFin;
    private $lieu;
    private $nombreDonneurs;
    private $db;

    public function __construct() {
        $this->db = (new Database())->connect();
    }

    // Getters
    public function getIdCampagne() { return $this->idCampagne; }
    public function getNomCampagne() { return $this->nomCampagne; }
    public function getDateDebut() { return $this->dateDebut; }
    public function getDateFin() { return $this->dateFin; }
    public function getLieu() { return $this->lieu; }
    public function getNombreDonneurs() { return $this->nombreDonneurs; }

    // Setters
    public function setNomCampagne($nom) { $this->nomCampagne = $nom; }
    public function setDateDebut($date) { $this->dateDebut = $date; }
    public function setDateFin($date) { $this->dateFin = $date; }
    public function setLieu($lieu) { $this->lieu = $lieu; }
    public function setNombreDonneurs($nombre) { $this->nombreDonneurs = $nombre; }

    // Ajouter une campagne
    public function ajouter() {
        $query = "INSERT INTO Campagnes (Nom_Campagne, Date_Debut, Date_Fin, Lieu, Nombre_Donneurs) 
                  VALUES (:nom, :dateDebut, :dateFin, :lieu, :nombre)";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':nom', $this->nomCampagne);
        $stmt->bindParam(':dateDebut', $this->dateDebut);
        $stmt->bindParam(':dateFin', $this->dateFin);
        $stmt->bindParam(':lieu', $this->lieu);
        $stmt->bindParam(':nombre', $this->nombreDonneurs);
        return $stmt->execute();
    }

    // Lire une campagne
    public function lire($idCampagne) {
        $query = "SELECT * FROM Campagnes WHERE ID_Campagne = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $idCampagne);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Lire toutes les campagnes
    public function lireTous() {
        $query = "SELECT * FROM Campagnes";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Mettre à jour une campagne
    public function mettreAJour($idCampagne) {
        $query = "UPDATE Campagnes SET Nom_Campagne = :nom, Date_Debut = :dateDebut, Date_Fin = :dateFin, Lieu = :lieu, Nombre_Donneurs = :nombre WHERE ID_Campagne = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $idCampagne);
        $stmt->bindParam(':nom', $this->nomCampagne);
        $stmt->bindParam(':dateDebut', $this->dateDebut);
        $stmt->bindParam(':dateFin', $this->dateFin);
        $stmt->bindParam(':lieu', $this->lieu);
        $stmt->bindParam(':nombre', $this->nombreDonneurs);
        return $stmt->execute();
    }

    // Supprimer une campagne
    public function supprimer($idCampagne) {
        $query = "DELETE FROM Campagnes WHERE ID_Campagne = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $idCampagne);
        return $stmt->execute();
    }
}
?>