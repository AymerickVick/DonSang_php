<?php
session_start();

require_once '../controllers/DonneurController.php';
require_once '../controllers/ConditionSanteController.php';
require_once '../controllers/CampagneController.php';
require_once '../controllers/UtilisateurController.php';
require_once '../controllers/AuthController.php';

$donneurController = new DonneurController();
$conditionSanteController = new ConditionSanteController();
$campagneController = new CampagneController();
$utilisateurController = new UtilisateurController();
$authController = new AuthController();

$action = $_GET['action'] ?? 'login';
$id = $_GET['id'] ?? null;

$actionsPubliques = ['login', 'connecter'];
if (!isset($_SESSION['utilisateur_id']) && !in_array($action, $actionsPubliques)) {
    $authController->afficherLogin();
    exit;
}

// Ajout d’une protection supplémentaire pour les actions utilisateurs
$actionsUtilisateurs = ['lister_utilisateurs', 'ajouter_utilisateur', 'modifier_utilisateur', 'supprimer_utilisateur'];
if (in_array($action, $actionsUtilisateurs) && $_SESSION['utilisateur_role'] !== 'admin') {
    header("Location: index.php?action=lister_donneurs&error=Accès réservé aux administrateurs");
    exit;
}

switch ($action) {
    case 'login':
        $authController->afficherLogin();
        break;
    case 'connecter':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $authController->connecter($_POST);
        } else {
            $authController->afficherLogin();
        }
        break;
    case 'deconnecter':
        $authController->deconnecter();
        break;

    case 'lister_donneurs':
        $donneurController->listerDonneurs();
        break;
    case 'ajouter_donneur':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $donneurController->ajouterDonneur($_POST);
        } else {
            $donneurController->afficherAjouter();
        }
        break;
    case 'modifier_donneur':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $donneurController->modifierDonneur($id, $_POST);
        } else {
            $donneurController->afficherModifier($id);
        }
        break;
    case 'supprimer_donneur':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $donneurController->supprimerDonneur($id);
        } else {
            $donneurController->afficherSupprimer($id);
        }
        break;
    case 'info_donneur':
        $donneurController->afficherInfo($id);
        break;

    case 'lister_conditions':
        $conditionSanteController->listerConditions();
        break;
    case 'ajouter_condition':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $conditionSanteController->ajouterCondition($_POST);
        } else {
            $conditionSanteController->afficherAjouter();
        }
        break;
    case 'modifier_condition':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $conditionSanteController->modifierCondition($id, $_POST);
        } else {
            $conditionSanteController->afficherModifier($id);
        }
        break;
    case 'supprimer_condition':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $conditionSanteController->supprimerCondition($id);
        } else {
            $conditionSanteController->afficherSupprimer($id);
        }
        break;
    case 'verifier_eligibilite':
        $conditionSanteController->verifierEligibilite($id);
        break;

    case 'lister_campagnes':
        $campagneController->listerCampagnes();
        break;
    case 'ajouter_campagne':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $campagneController->ajouterCampagne($_POST);
        } else {
            $campagneController->afficherAjouter();
        }
        break;
    case 'modifier_campagne':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $campagneController->modifierCampagne($id, $_POST);
        } else {
            $campagneController->afficherModifier($id);
        }
        break;
    case 'supprimer_campagne':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $campagneController->supprimerCampagne($id);
        } else {
            $campagneController->afficherSupprimer($id);
        }
        break;

    case 'lister_utilisateurs':
        $utilisateurController->listerUtilisateurs();
        break;
    case 'ajouter_utilisateur':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $utilisateurController->ajouterUtilisateur($_POST);
        } else {
            $utilisateurController->afficherAjouter();
        }
        break;
    case 'modifier_utilisateur':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $utilisateurController->modifierUtilisateur($id, $_POST);
        } else {
            $utilisateurController->afficherModifier($id);
        }
        break;
        // Dans index.php, ajoutez ce cas dans le switch après 'info_donneur'
case 'cartographie_donneurs':
    $donneurController->afficherCartographie();
    break;
    // Dans index.php, dans le switch
case 'analyser_conditions':
    $conditionSanteController->analyserConditions();
    break;
    case 'supprimer_utilisateur':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $utilisateurController->supprimerUtilisateur($id);
        } else {
            $utilisateurController->afficherSupprimer($id);
        }
        break;

    default:
        $authController->afficherLogin();
}
?>