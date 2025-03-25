<?php
require_once '../config/Database.php';

class AutreRaison {
    private $idDonneur;
    private $raisonSupplementaire;
    private $db;

    public function __construct() {
        $this->db = (new Database())->connect();
    }

    // Getters
    public function getIdDonneur() { return $this->idDonneur; }
    public function getRaisonSupplementaire() { return $this->raisonSupplementaire; }

    // Setters
    public function setIdDonneur($id) { $this->idDonneur = $id; }
    public function setRaisonSupplementaire($raison) { $this->raisonSupplementaire = $raison; }

    // Ajouter une raison supplémentaire
    public function ajouter() {
        $query = "INSERT INTO Autres_Raisons (ID_Donneur, Raison_Supplementaire) VALUES (:idDonneur, :raison)";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':idDonneur', $this->idDonneur);
        $stmt->bindParam(':raison', $this->raisonSupplementaire);
        return $stmt->execute();
    }

    // Lire les raisons d’un donneur
    public function lire($idDonneur) {
        $query = "SELECT * FROM Autres_Raisons WHERE ID_Donneur = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $idDonneur);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Mettre à jour une raison
    public function mettreAJour($idDonneur) {
        $query = "UPDATE Autres_Raisons SET Raison_Supplementaire = :raison WHERE ID_Donneur = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $idDonneur);
        $stmt->bindParam(':raison', $this->raisonSupplementaire);
        return $stmt->execute();
    }

    // Supprimer une raison
    public function supprimer($idDonneur) {
        $query = "DELETE FROM Autres_Raisons WHERE ID_Donneur = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $idDonneur);
        return $stmt->execute();
    }
}
?>