<?php
require_once '../classes/Donneur.php';

class DonneurController {
    private $donneur;

    public function __construct() {
        $this->donneur = new Donneur();
    }

    // Lister tous les donneurs
    public function listerDonneurs() {
        $donneurs = $this->donneur->lireTous();
        require_once '../views/donneurs/liste.php';
    }

    // Afficher le formulaire d’ajout
    public function afficherAjouter() {
        // Récupérer tous les donneurs pour les graphiques
        $donneurs = $this->donneur->lireTous();
        require_once '../views/donneurs/ajouter.php';
    }

    // Ajouter un donneur
    public function ajouterDonneur($data) {
        $this->donneur->setDateRemplissage($data['date_remplissage']);
        $this->donneur->setDateNaissance($data['date_naissance']);
        $this->donneur->setNiveauEtude($data['niveau_etude']);
        $this->donneur->setGenre($data['genre']);
        $this->donneur->setTaille($data['taille']);
        $this->donneur->setPoids($data['poids']);
        $this->donneur->setSituationMatrimoniale($data['situation_matrimoniale']);
        $this->donneur->setProfession($data['profession']);
        $this->donneur->setArrondissementResidence($data['arrondissement_residence']);
        $this->donneur->setQuartierResidence($data['quartier_residence']);
        $this->donneur->setNom($data['nom']);

        if ($this->donneur->ajouter()) {
            header("Location: index.php?action=lister_donneurs&message=Donneur ajouté avec succès");
        } else {
            header("Location: index.php?action=ajouter_donneur&error=Erreur lors de l’ajout");
        }
    }

    // Afficher le formulaire de modification
    public function afficherModifier($id) {
        $donneur = $this->donneur->lire($id);
        if ($donneur) {
            require_once '../views/donneurs/modifier.php';
        } else {
            header("Location: index.php?action=lister_donneurs&error=Donneur non trouvé");
        }
    }

    // Mettre à jour un donneur
    public function modifierDonneur($id, $data) {
        $this->donneur->setDateRemplissage($data['date_remplissage']);
        $this->donneur->setDateNaissance($data['date_naissance']);
        $this->donneur->setNiveauEtude($data['niveau_etude']);
        $this->donneur->setGenre($data['genre']);
        $this->donneur->setTaille($data['taille']);
        $this->donneur->setPoids($data['poids']);
        $this->donneur->setSituationMatrimoniale($data['situation_matrimoniale']);
        $this->donneur->setProfession($data['profession']);
        $this->donneur->setArrondissementResidence($data['arrondissement_residence']);
        $this->donneur->setQuartierResidence($data['quartier_residence']);
        $this->donneur->setNom($data['nom']);

        if ($this->donneur->mettreAJour($id)) {
            header("Location: index.php?action=lister_donneurs&message=Donneur mis à jour avec succès");
        } else {
            header("Location: index.php?action=modifier_donneur&id=$id&error=Erreur lors de la mise à jour");
        }
    }

    // Afficher la confirmation de suppression
    public function afficherSupprimer($id) {
        $donneur = $this->donneur->lire($id);
        if ($donneur) {
            require_once '../views/donneurs/supprimer.php';
        } else {
            header("Location: index.php?action=lister_donneurs&error=Donneur non trouvé");
        }
    }

    // Supprimer un donneur
    public function supprimerDonneur($id) {
        if ($this->donneur->supprimer($id)) {
            header("Location: index.php?action=lister_donneurs&message=Donneur supprimé avec succès");
        } else {
            header("Location: index.php?action=lister_donneurs&error=Erreur lors de la suppression");
        }
    }

    // Afficher les informations d’un donneur
    public function afficherInfo($id) {
        $result = $this->donneur->lire($id, true);
        if (!$result) {
            header("Location: index.php?action=lister_donneurs&error=Donneur non trouvé");
        }
    }
    // Dans controllers/DonneurController.php, ajoutez cette méthode à la fin de la classe
public function afficherCartographie() {
    $donneesGeographiques = $this->donneur->lireDonneesGeographiques();
    require_once '../views/donneurs/cartographie.php';
}
}
?>