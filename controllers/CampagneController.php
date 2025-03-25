<?php
require_once '../classes/Campagne.php';

class CampagneController {
    private $campagne;

    public function __construct() {
        $this->campagne = new Campagne();
    }

    // Lister toutes les campagnes
    public function listerCampagnes() {
        $campagnes = $this->campagne->lireTous();
        require_once '../views/campagnes/liste.php';
    }

    // Afficher le formulaire d’ajout
    public function afficherAjouter() {
        require_once '../views/campagnes/ajouter.php';
    }

    // Ajouter une campagne
    public function ajouterCampagne($data) {
        $this->campagne->setNomCampagne($data['nom_campagne']);
        $this->campagne->setDateDebut($data['date_debut']);
        $this->campagne->setDateFin($data['date_fin']);
        $this->campagne->setLieu($data['lieu']);
        $this->campagne->setNombreDonneurs($data['nombre_donneurs']);

        if ($this->campagne->ajouter()) {
            header("Location: index.php?action=lister_campagnes&message=Campagne ajoutée avec succès");
        } else {
            header("Location: index.php?action=ajouter_campagne&error=Erreur lors de l’ajout");
        }
    }

    // Afficher le formulaire de modification
    public function afficherModifier($id) {
        $campagne = $this->campagne->lire($id);
        if ($campagne) {
            require_once '../views/campagnes/modifier.php';
        } else {
            header("Location: index.php?action=lister_campagnes&error=Campagne non trouvée");
        }
    }

    // Mettre à jour une campagne
    public function modifierCampagne($id, $data) {
        $this->campagne->setNomCampagne($data['nom_campagne']);
        $this->campagne->setDateDebut($data['date_debut']);
        $this->campagne->setDateFin($data['date_fin']);
        $this->campagne->setLieu($data['lieu']);
        $this->campagne->setNombreDonneurs($data['nombre_donneurs']);

        if ($this->campagne->mettreAJour($id)) {
            header("Location: index.php?action=lister_campagnes&message=Campagne mise à jour avec succès");
        } else {
            header("Location: index.php?action=modifier_campagne&id=$id&error=Erreur lors de la mise à jour");
        }
    }

    // Afficher la confirmation de suppression
    public function afficherSupprimer($id) {
        $campagne = $this->campagne->lire($id);
        if ($campagne) {
            require_once '../views/campagnes/supprimer.php';
        } else {
            header("Location: index.php?action=lister_campagnes&error=Campagne non trouvée");
        }
    }

    // Supprimer une campagne
    public function supprimerCampagne($id) {
        if ($this->campagne->supprimer($id)) {
            header("Location: index.php?action=lister_campagnes&message=Campagne supprimée avec succès");
        } else {
            header("Location: index.php?action=lister_campagnes&error=Erreur lors de la suppression");
        }
    }
}
?>