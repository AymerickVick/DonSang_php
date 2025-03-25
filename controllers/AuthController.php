<?php
require_once '../classes/Utilisateur.php';

class AuthController {
    private $utilisateur;

    public function __construct() {
        $this->utilisateur = new Utilisateur();
    }

    // Afficher la page de connexion
    public function afficherLogin() {
        require_once '../views/login.php';
    }

    // Gérer la connexion
    public function connecter($data) {
        $email = $data['email'];
        $motDePasse = $data['mot_de_passe'];
        $utilisateur = $this->utilisateur->verifierConnexion($email, $motDePasse);

        if ($utilisateur) {
            session_start();
            $_SESSION['utilisateur_id'] = $utilisateur['ID_Utilisateur'];
            $_SESSION['utilisateur_nom'] = $utilisateur['Nom'];
            $_SESSION['utilisateur_role'] = $utilisateur['Role'];
            header("Location: index.php?action=lister_donneurs");
        } else {
            header("Location: index.php?action=login&error=Email ou mot de passe incorrect");
        }
    }

    // Gérer la déconnexion
    public function deconnecter() {
        session_start();
        session_destroy();
        header("Location: index.php?action=login&message=Vous êtes déconnecté");
    }
}
?>