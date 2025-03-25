<?php
require_once '../classes/Utilisateur.php';

class UtilisateurController {
    private $utilisateur;

    public function __construct() {
        $this->utilisateur = new Utilisateur();
    }

    // Lister tous les utilisateurs
    public function listerUtilisateurs() {
        $utilisateurs = $this->utilisateur->lireTous();
        require_once '../views/utilisateurs/liste.php';
    }

    // Afficher le formulaire d’ajout
    public function afficherAjouter() {
        require_once '../views/utilisateurs/ajouter.php';
    }

    // Ajouter un utilisateur
    public function ajouterUtilisateur($data) {
        $this->utilisateur->setNom($data['nom']);
        $this->utilisateur->setEmail($data['email']);
        $this->utilisateur->setMotDePasse($data['mot_de_passe']);
        $this->utilisateur->setRole($data['role']);

        if ($this->utilisateur->ajouter()) {
            header("Location: index.php?action=lister_utilisateurs&message=Utilisateur ajouté avec succès");
        } else {
            header("Location: index.php?action=ajouter_utilisateur&error=Erreur lors de l’ajout");
        }
    }

    // Afficher le formulaire de modification
    public function afficherModifier($id) {
        $utilisateur = $this->utilisateur->lire($id);
        if ($utilisateur) {
            require_once '../views/utilisateurs/modifier.php';
        } else {
            header("Location: index.php?action=lister_utilisateurs&error=Utilisateur non trouvé");
        }
    }

    // Mettre à jour un utilisateur
    public function modifierUtilisateur($id, $data) {
        $this->utilisateur->setNom($data['nom']);
        $this->utilisateur->setEmail($data['email']);
        $this->utilisateur->setRole($data['role']);

        if ($this->utilisateur->mettreAJour($id)) {
            // Si un nouveau mot de passe est fourni, le mettre à jour séparément
            if (!empty($data['mot_de_passe'])) {
                $this->utilisateur->setMotDePasse($data['mot_de_passe']);
                $this->utilisateur->mettreAJourMotDePasse($id);
            }
            header("Location: index.php?action=lister_utilisateurs&message=Utilisateur mis à jour avec succès");
        } else {
            header("Location: index.php?action=modifier_utilisateur&id=$id&error=Erreur lors de la mise à jour");
        }
    }

    // Afficher la confirmation de suppression
    public function afficherSupprimer($id) {
        $utilisateur = $this->utilisateur->lire($id);
        if ($utilisateur) {
            require_once '../views/utilisateurs/supprimer.php';
        } else {
            header("Location: index.php?action=lister_utilisateurs&error=Utilisateur non trouvé");
        }
    }

    // Supprimer un utilisateur
    public function supprimerUtilisateur($id) {
        if ($this->utilisateur->supprimer($id)) {
            header("Location: index.php?action=lister_utilisateurs&message=Utilisateur supprimé avec succès");
        } else {
            header("Location: index.php?action=lister_utilisateurs&error=Erreur lors de la suppression");
        }
    }
}
?>