<?php
require_once '../config/Database.php';

class ConditionSante {
    private $idDonneur;
    private $raisonNonEligibilite;
    private $porteurHIV;
    private $porteurHBS;
    private $porteurHCV;
    private $opere;
    private $drepanocytaire;
    private $diabetique;
    private $hypertendu;
    private $asthmatique;
    private $cardiaque;
    private $tatoue;
    private $scarifie;
    private $db;

    public function __construct() {
        $this->db = (new Database())->connect();
    }

    // Getters
    public function getIdDonneur() { return $this->idDonneur; }
    public function getRaisonNonEligibilite() { return $this->raisonNonEligibilite; }
    // Ajouter autres getters si nécessaire

    // Setters
    public function setIdDonneur($id) { $this->idDonneur = $id; }
    public function setRaisonNonEligibilite($raison) { $this->raisonNonEligibilite = $raison; }
    public function setPorteurHIV($value) { $this->porteurHIV = $value; }
    public function setPorteurHBS($value) { $this->porteurHBS = $value; }
    public function setPorteurHCV($value) { $this->porteurHCV = $value; }
    public function setOpere($value) { $this->opere = $value; }
    public function setDrepanocytaire($value) { $this->drepanocytaire = $value; }
    public function setDiabetique($value) { $this->diabetique = $value; }
    public function setHypertendu($value) { $this->hypertendu = $value; }
    public function setAsthmatique($value) { $this->asthmatique = $value; }
    public function setCardiaque($value) { $this->cardiaque = $value; }
    public function setTatoue($value) { $this->tatoue = $value; }
    public function setScarifie($value) { $this->scarifie = $value; }

    // Ajouter conditions de santé
    public function ajouter() {
        $query = "INSERT INTO Conditions_Sante (ID_Donneur, Raison_Non_Eligibilite, Porteur_HIV, Porteur_HBS, Porteur_HCV, Opere, Drepanocytaire, Diabetique, Hypertendu, Asthmatique, Cardiaque, Tatoue, Scarifie) 
                  VALUES (:idDonneur, :raison, :hiv, :hbs, :hcv, :opere, :drepano, :diabete, :hyper, :asthme, :cardiaque, :tatoue, :scarifie)";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':idDonneur', $this->idDonneur);
        $stmt->bindParam(':raison', $this->raisonNonEligibilite);
        $stmt->bindParam(':hiv', $this->porteurHIV, PDO::PARAM_BOOL);
        $stmt->bindParam(':hbs', $this->porteurHBS, PDO::PARAM_BOOL);
        $stmt->bindParam(':hcv', $this->porteurHCV, PDO::PARAM_BOOL);
        $stmt->bindParam(':opere', $this->opere, PDO::PARAM_BOOL);
        $stmt->bindParam(':drepano', $this->drepanocytaire, PDO::PARAM_BOOL);
        $stmt->bindParam(':diabete', $this->diabetique, PDO::PARAM_BOOL);
        $stmt->bindParam(':hyper', $this->hypertendu, PDO::PARAM_BOOL);
        $stmt->bindParam(':asthme', $this->asthmatique, PDO::PARAM_BOOL);
        $stmt->bindParam(':cardiaque', $this->cardiaque, PDO::PARAM_BOOL);
        $stmt->bindParam(':tatoue', $this->tatoue, PDO::PARAM_BOOL);
        $stmt->bindParam(':scarifie', $this->scarifie, PDO::PARAM_BOOL);
        return $stmt->execute();
    }

    // Lire les conditions d’un donneur
    public function lire($idDonneur) {
        $query = "SELECT * FROM Conditions_Sante WHERE ID_Donneur = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $idDonneur);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Mettre à jour les conditions
    public function mettreAJour($idDonneur) {
        $query = "UPDATE Conditions_Sante SET Raison_Non_Eligibilite = :raison, Porteur_HIV = :hiv, Porteur_HBS = :hbs, Porteur_HCV = :hcv, Opere = :opere, Drepanocytaire = :drepano, Diabetique = :diabete, Hypertendu = :hyper, Asthmatique = :asthme, Cardiaque = :cardiaque, Tatoue = :tatoue, Scarifie = :scarifie WHERE ID_Donneur = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $idDonneur);
        $stmt->bindParam(':raison', $this->raisonNonEligibilite);
        $stmt->bindParam(':hiv', $this->porteurHIV, PDO::PARAM_BOOL);
        $stmt->bindParam(':hbs', $this->porteurHBS, PDO::PARAM_BOOL);
        $stmt->bindParam(':hcv', $this->porteurHCV, PDO::PARAM_BOOL);
        $stmt->bindParam(':opere', $this->opere, PDO::PARAM_BOOL);
        $stmt->bindParam(':drepano', $this->drepanocytaire, PDO::PARAM_BOOL);
        $stmt->bindParam(':diabete', $this->diabetique, PDO::PARAM_BOOL);
        $stmt->bindParam(':hyper', $this->hypertendu, PDO::PARAM_BOOL);
        $stmt->bindParam(':asthme', $this->asthmatique, PDO::PARAM_BOOL);
        $stmt->bindParam(':cardiaque', $this->cardiaque, PDO::PARAM_BOOL);
        $stmt->bindParam(':tatoue', $this->tatoue, PDO::PARAM_BOOL);
        $stmt->bindParam(':scarifie', $this->scarifie, PDO::PARAM_BOOL);
        return $stmt->execute();
    }

    // Supprimer les conditions
    public function supprimer($idDonneur) {
        $query = "DELETE FROM Conditions_Sante WHERE ID_Donneur = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $idDonneur);
        return $stmt->execute();
    }

    // Vérifier l’éligibilité
    public function estEligible($idDonneur) {
        $conditions = $this->lire($idDonneur);
        if (!$conditions) return false;
        return !($conditions['Porteur_HIV'] || $conditions['Porteur_HBS'] || $conditions['Porteur_HCV'] || 
                 $conditions['Drepanocytaire'] || $conditions['Diabetique'] || $conditions['Hypertendu'] || 
                 $conditions['Cardiaque'] || $conditions['Opere']);
    }
}
?>