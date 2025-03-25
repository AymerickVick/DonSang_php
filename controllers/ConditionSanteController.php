<?php
require_once '../classes/ConditionSante.php';

class ConditionSanteController {
    private $conditionSante;

    public function __construct() {
        $this->conditionSante = new ConditionSante();
    }

    // Lister toutes les conditions de santé
    public function listerConditions() {
        $conditions = []; // Pas de méthode lireTous dans ConditionSante, donc on simule avec une jointure si besoin
        $db = (new Database())->connect();
        $query = "SELECT cs.*, d.nom FROM Conditions_Sante cs JOIN Donneurs d ON cs.ID_Donneur = d.ID";
        $stmt = $db->prepare($query);
        $stmt->execute();
        $conditions = $stmt->fetchAll(PDO::FETCH_ASSOC);
        require_once '../views/conditions_sante/liste.php';
    }

    // Afficher le formulaire d’ajout
    public function afficherAjouter() {
        $db = (new Database())->connect();
        $query = "SELECT ID, nom FROM Donneurs";
        $stmt = $db->prepare($query);
        $stmt->execute();
        $donneurs = $stmt->fetchAll(PDO::FETCH_ASSOC);
        require_once '../views/conditions_sante/ajouter.php';
    }

    // Ajouter des conditions de santé
    public function ajouterCondition($data) {
        $this->conditionSante->setIdDonneur($data['id_donneur']);
        $this->conditionSante->setRaisonNonEligibilite($data['raison_non_eligibilite']);
        $this->conditionSante->setPorteurHIV(isset($data['porteur_hiv']) ? 1 : 0);
        $this->conditionSante->setPorteurHBS(isset($data['porteur_hbs']) ? 1 : 0);
        $this->conditionSante->setPorteurHCV(isset($data['porteur_hcv']) ? 1 : 0);
        $this->conditionSante->setOpere(isset($data['opere']) ? 1 : 0);
        $this->conditionSante->setDrepanocytaire(isset($data['drepanocytaire']) ? 1 : 0);
        $this->conditionSante->setDiabetique(isset($data['diabetique']) ? 1 : 0);
        $this->conditionSante->setHypertendu(isset($data['hypertendu']) ? 1 : 0);
        $this->conditionSante->setAsthmatique(isset($data['asthmatique']) ? 1 : 0);
        $this->conditionSante->setCardiaque(isset($data['cardiaque']) ? 1 : 0);
        $this->conditionSante->setTatoue(isset($data['tatoue']) ? 1 : 0);
        $this->conditionSante->setScarifie(isset($data['scarifie']) ? 1 : 0);

        if ($this->conditionSante->ajouter()) {
            header("Location: index.php?action=lister_conditions&message=Conditions ajoutées avec succès");
        } else {
            header("Location: index.php?action=ajouter_condition&error=Erreur lors de l’ajout");
        }
    }

    // Afficher le formulaire de modification
    public function afficherModifier($id) {
        $condition = $this->conditionSante->lire($id);
        $db = (new Database())->connect();
        $query = "SELECT ID, nom FROM Donneurs";
        $stmt = $db->prepare($query);
        $stmt->execute();
        $donneurs = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if ($condition) {
            require_once '../views/conditions_sante/modifier.php';
        } else {
            header("Location: index.php?action=lister_conditions&error=Conditions non trouvées");
        }
    }

    // Mettre à jour des conditions de santé
    public function modifierCondition($id, $data) {
        $this->conditionSante->setIdDonneur($id); // ID_Donneur ne change pas
        $this->conditionSante->setRaisonNonEligibilite($data['raison_non_eligibilite']);
        $this->conditionSante->setPorteurHIV(isset($data['porteur_hiv']) ? 1 : 0);
        $this->conditionSante->setPorteurHBS(isset($data['porteur_hbs']) ? 1 : 0);
        $this->conditionSante->setPorteurHCV(isset($data['porteur_hcv']) ? 1 : 0);
        $this->conditionSante->setOpere(isset($data['opere']) ? 1 : 0);
        $this->conditionSante->setDrepanocytaire(isset($data['drepanocytaire']) ? 1 : 0);
        $this->conditionSante->setDiabetique(isset($data['diabetique']) ? 1 : 0);
        $this->conditionSante->setHypertendu(isset($data['hypertendu']) ? 1 : 0);
        $this->conditionSante->setAsthmatique(isset($data['asthmatique']) ? 1 : 0);
        $this->conditionSante->setCardiaque(isset($data['cardiaque']) ? 1 : 0);
        $this->conditionSante->setTatoue(isset($data['tatoue']) ? 1 : 0);
        $this->conditionSante->setScarifie(isset($data['scarifie']) ? 1 : 0);

        if ($this->conditionSante->mettreAJour($id)) {
            header("Location: index.php?action=lister_conditions&message=Conditions mises à jour avec succès");
        } else {
            header("Location: index.php?action=modifier_condition&id=$id&error=Erreur lors de la mise à jour");
        }
    }

    // Afficher la confirmation de suppression
    public function afficherSupprimer($id) {
        $condition = $this->conditionSante->lire($id);
        if ($condition) {
            require_once '../views/conditions_sante/supprimer.php';
        } else {
            header("Location: index.php?action=lister_conditions&error=Conditions non trouvées");
        }
    }

    // Supprimer des conditions de santé
    public function supprimerCondition($id) {
        if ($this->conditionSante->supprimer($id)) {
            header("Location: index.php?action=lister_conditions&message=Conditions supprimées avec succès");
        } else {
            header("Location: index.php?action=lister_conditions&error=Erreur lors de la suppression");
        }
    }

    // Vérifier l’éligibilité
    public function verifierEligibilite($id) {
        $eligible = $this->conditionSante->estEligible($id);
        $condition = $this->conditionSante->lire($id);
        header("Location: index.php?action=lister_conditions&message=Éligibilité pour ID $id : " . ($eligible ? 'Oui' : 'Non'));
    }
    // Dans ConditionSanteController.php
public function analyserConditions() {
    $db = (new Database())->connect();
    $query = "SELECT 
                SUM(Porteur_HIV) as hiv, 
                SUM(Porteur_HBS) as hbs, 
                SUM(Porteur_HCV) as hcv, 
                SUM(Opere) as opere, 
                SUM(Drepanocytaire) as drepanocytaire, 
                SUM(Diabetique) as diabetique, 
                SUM(Hypertendu) as hypertendu, 
                SUM(Asthmatique) as asthmatique, 
                SUM(Cardiaque) as cardiaque,
                (SELECT COUNT(*) FROM Conditions_Sante WHERE Porteur_HIV = 0 AND Porteur_HBS = 0 AND Porteur_HCV = 0 AND Opere = 0 AND Drepanocytaire = 0 AND Diabetique = 0 AND Hypertendu = 0 AND Cardiaque = 0) as eligibles,
                (SELECT COUNT(*) FROM Conditions_Sante) as total
              FROM Conditions_Sante";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $stats = $stmt->fetch(PDO::FETCH_ASSOC);
    require_once '../views/conditions_sante/analyse.php';
}
}
?>