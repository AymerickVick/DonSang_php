<?php
// Vérifier si l'utilisateur est connecté, sinon rediriger vers la page de connexion
if (!isset($_SESSION['utilisateur_id'])) {
    header("Location: index.php?action=login");
    exit;
}

// Déterminer la lettre à afficher selon le rôle
$roleLetter = '';
switch ($_SESSION['utilisateur_role'] ?? 'inconnu') {
    case 'admin':
        $roleLetter = 'A';
        break;
    case 'organisateur':
        $roleLetter = 'O';
        break;
    case 'analyste':
        $roleLetter = 'AN';
        break;
    default:
        $roleLetter = '?'; // Par défaut si rôle inconnu
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Supprimer une Campagne</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Montserrat:wght@400;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #d9534f;
            --primary-dark: #c9302c;
            --primary-light: #f2dede;
            --dark-bg: #1e293b;
            --dark-light: #334155;
            --table-bg: #1e293b;
            --white: #f8fafc;
            --gray-light: #e2e8f0;
            --accent-blue: #4682b4;
            --success-green: #10b981;
            --warning-yellow: #f59e0b;
            --danger-red: #ef4444;
            --gray-50: #f9fafb;
            --gray-100: #f3f4f6;
            --gray-200: #e5e7eb;
            --box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        body {
            background: linear-gradient(135deg, var(--dark-bg) 0%, var(--dark-light) 100%);
            color: var(--white);
            font-family: 'Poppins', sans-serif;
            min-height: 100vh;
        }

        /* Sidebar */
        .sidebar {
            height: 100vh;
            width: 280px;
            position: fixed;
            left: -280px;
            top: 0;
            background: linear-gradient(180deg, var(--primary-dark) 0%, var(--primary) 100%);
            transition: var(--transition);
            z-index: 999;
            padding-top: 20px;
            box-shadow: var(--box-shadow);
            overflow-y: auto;
            scrollbar-width: thin;
            scrollbar-color: var(--primary-light) transparent;
        }

        .sidebar.active {
            left: 0;
        }

        .main-content {
            margin-left: 0;
            transition: var(--transition);
            padding: 30px;
            width: 100%;
        }

        .main-content.active {
            margin-left: 280px;
            width: calc(100% - 280px);
        }

        .sidebar-header {
            padding: 20px;
            text-align: center;
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
            margin-bottom: 15px;
        }

        .sidebar-logo {
            color: var(--white);
            font-size: 28px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .sidebar-menu {
            padding: 10px 0;
        }

        .menu-item {
            padding: 14px 25px;
            cursor: pointer;
            color: var(--white);
            transition: var(--transition);
            font-weight: 500;
            border-left: 3px solid transparent;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .menu-item:hover {
            background-color: rgba(255, 255, 255, 0.1);
            border-left-color: var(--white);
        }

        .menu-item i.menu-icon {
            margin-right: 10px;
            width: 20px;
            text-align: center;
        }

        .sub-menu {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease;
            background: rgba(0, 0, 0, 0.2);
        }

        .sub-menu.active {
            max-height: 120px;
        }

        .sub-menu-item a {
            padding: 12px 25px 12px 50px;
            color: var(--white);
            text-decoration: none;
            transition: var(--transition);
            display: block;
            font-size: 0.9rem;
            opacity: 0.9;
        }

        .sub-menu-item a:hover {
            background-color: rgba(255, 255, 255, 0.05);
            opacity: 1;
        }

        .sub-menu-item a i {
            margin-right: 10px;
            width: 18px;
            text-align: center;
        }

        /* Boutons */
        .btn {
            border-radius: 8px;
            padding: 10px 20px;
            font-weight: 500;
            text-transform: uppercase;
            transition: var(--transition);
            font-size: 0.85rem;
            letter-spacing: 0.5px;
            border: none;
        }

        .btn-danger {
            background: var(--danger-red);
            color: var(--white);
        }

        .btn-danger:hover {
            background: #c82333;
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(220, 53, 69, 0.4);
        }

        .btn-secondary {
            background: var(--accent-blue);
            color: var(--white);
        }

        .btn-secondary:hover {
            background: #3b72a5;
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(70, 130, 180, 0.3);
        }

        /* En-tête de page */
        .page-header {
            padding: 20px 0;
            margin-bottom: 40px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            animation: fadeIn 0.5s ease forwards;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .page-header h1 {
            font-weight: 700;
            font-size: 2rem;
            color: var(--white);
            margin: 0;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .page-header h1 i {
            color: var(--primary);
        }

        #sidebarToggle {
            background: var(--primary);
            color: var(--white);
            border: none;
            border-radius: 10px;
            width: 45px;
            height: 45px;
            transition: var(--transition);
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 20px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        #sidebarToggle:hover {
            background: var(--primary-dark);
            transform: scale(1.05);
            box-shadow: 0 5px 15px rgba(169, 68, 66, 0.3);
        }

        /* Cercle utilisateur */
        .user-circle {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: var(--white);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            font-weight: bold;
            cursor: pointer;
            transition: var(--transition);
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .user-circle:hover {
            transform: scale(1.05);
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.15);
        }

        /* Animations */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Conteneur de formulaire */
        .form-container {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: calc(100vh - 140px);
            margin: 0 20px;
        }

        .form-frame {
            background: rgba(30, 41, 59, 0.8);
            border-radius: 16px;
            padding: 30px;
            box-shadow: var(--box-shadow);
            max-width: 500px;
            width: 100%;
        }

        /* Liste */
        .list-group {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 8px;
            box-shadow: var(--box-shadow);
            margin-bottom: 20px;
        }

        .list-group-item {
            background: transparent;
            color: var(--white);
            border: 1px solid rgba(255, 255, 255, 0.1);
            padding: 12px 15px;
            font-size: 0.9rem;
        }

        /* Déconnexion */
        .logout-item {
            padding: 14px 25px;
            cursor: pointer;
            color: var(--white);
            transition: var(--transition);
            font-weight: 500;
            margin-top: 20px;
            display: flex;
            align-items: center;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }

        .logout-item a {
            color: var(--white);
            text-decoration: none;
            display: flex;
            align-items: center;
            width: 100%;
        }

        .logout-item:hover {
            background-color: var(--danger-red);
        }

        .logout-item i {
            margin-right: 10px;
            width: 20px;
            text-align: center;
        }

        /* Modal */
        .modal-content {
            background: var(--dark-light);
            color: var(--white);
            border: none;
            border-radius: 16px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            overflow: hidden;
        }

        .modal-header {
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            padding: 15px 20px;
            background: rgba(217, 83, 79, 0.1);
        }

        .modal-footer {
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            padding: 15px 20px;
        }

        .modal-title {
            color: var(--primary);
            font-weight: 600;
        }

        .btn-close-white {
            filter: brightness(0) invert(1);
            opacity: 0.7;
        }

        .btn-close-white:hover {
            opacity: 1;
        }

        /* Responsive */
        @media (max-width: 992px) {
            .main-content.active {
                margin-left: 0;
                width: 100%;
            }

            .sidebar.active {
                left: 0;
                width: 280px;
            }

            .sidebar {
                width: 0;
            }
        }

        @media (max-width: 768px) {
            .page-header h1 {
                font-size: 1.5rem;
            }

            .form-frame {
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="sidebar" id="sidebar">
                <div class="sidebar-header">
                    <div class="sidebar-logo">
                        <i class="fas fa-tint"></i> Don de Sang
                    </div>
                </div>
                <div class="sidebar-menu">
                    <div class="menu-item" id="menuDonneur">
                        <div>
                            <i class="fas fa-user-md menu-icon"></i> Donneur
                        </div>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="sub-menu" id="subMenuDonneur">
                        <div class="sub-menu-item">
                            <a href="index.php?action=ajouter_donneur">
                                <i class="fas fa-plus-circle"></i> Ajouter
                            </a>
                        </div>
                        <div class="sub-menu-item">
                            <a href="index.php?action=lister_donneurs">
                                <i class="fas fa-list"></i> Lister
                            </a>
                        </div>
                    </div>
                    <div class="menu-item" id="menuConditionSanter">
                        <div>
                            <i class="fas fa-stethoscope menu-icon"></i> Condition Santé
                        </div>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="sub-menu" id="subMenuConditionSanter">
                        <div class="sub-menu-item">
                            <a href="index.php?action=ajouter_condition">
                                <i class="fas fa-plus-circle"></i> Ajouter
                            </a>
                        </div>
                        <div class="sub-menu-item">
                            <a href="index.php?action=lister_conditions">
                                <i class="fas fa-list"></i> Lister
                            </a>
                        </div>
                    </div>
                    <div class="menu-item" id="menuCampagne">
                        <div>
                            <i class="fas fa-bullhorn menu-icon"></i> Campagne
                        </div>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="sub-menu" id="subMenuCampagne">
                        <div class="sub-menu-item">
                            <a href="index.php?action=ajouter_campagne">
                                <i class="fas fa-plus-circle"></i> Ajouter
                            </a>
                        </div>
                        <div class="sub-menu-item">
                            <a href="index.php?action=lister_campagnes">
                                <i class="fas fa-list"></i> Lister
                            </a>
                        </div>
                    </div>
                    <?php if (isset($_SESSION['utilisateur_role']) && $_SESSION['utilisateur_role'] === 'admin'): ?>
                        <div class="menu-item" id="menuUtilisateur">
                            <div>
                                <i class="fas fa-users menu-icon"></i> Utilisateur
                            </div>
                            <i class="fas fa-chevron-down"></i>
                        </div>
                        <div class="sub-menu" id="subMenuUtilisateur">
                            <div class="sub-menu-item">
                                <a href="index.php?action=ajouter_utilisateur">
                                    <i class="fas fa-plus-circle"></i> Ajouter
                                </a>
                            </div>
                            <div class="sub-menu-item">
                                <a href="index.php?action=lister_utilisateurs">
                                    <i class="fas fa-list"></i> Lister
                                </a>
                            </div>
                        </div>
                    <?php endif; ?>
                    <div class="logout-item">
                        <a href="index.php?action=deconnecter">
                            <i class="fas fa-sign-out-alt"></i> Déconnexion
                        </a>
                    </div>
                </div>
            </div>

            <!-- Main Content -->
            <div class="main-content" id="mainContent">
                <div class="page-header">
                    <div style="display: flex; align-items: center;">
                        <button id="sidebarToggle" aria-label="Toggle Sidebar">
                            <i class="fas fa-bars"></i>
                        </button>
                        <h1><i class="fas fa-trash-alt"></i> Supprimer une Campagne</h1>
                    </div>
                    <div class="user-circle" data-bs-toggle="modal" data-bs-target="#userInfoModal">
                        <?php echo $roleLetter; ?>
                    </div>
                </div>

                <!-- Contenu de suppression -->
                <div class="form-container">
                    <div class="form-frame">
                        <p class="text-center mb-4">Êtes-vous sûr de vouloir supprimer la campagne suivante ?</p>
                        <ul class="list-group mb-4">
                            <li class="list-group-item">ID : <?php echo $campagne['ID_Campagne']; ?></li>
                            <li class="list-group-item">Nom : <?php echo $campagne['Nom_Campagne']; ?></li>
                            <li class="list-group-item">Date Début : <?php echo $campagne['Date_Debut']; ?></li>
                            <li class="list-group-item">Date Fin : <?php echo $campagne['Date_Fin']; ?></li>
                            <li class="list-group-item">Lieu : <?php echo $campagne['Lieu']; ?></li>
                            <li class="list-group-item">Nombre de Donneurs : <?php echo $campagne['Nombre_Donneurs']; ?></li>
                        </ul>
                        <form action="index.php?action=supprimer_campagne&id=<?php echo $campagne['ID_Campagne']; ?>" method="POST" class="text-center">
                            <button type="submit" class="btn btn-danger"><i class="fas fa-trash me-2"></i>Oui, supprimer</button>
                            <a href="index.php?action=lister_campagnes" class="btn btn-secondary"><i class="fas fa-times me-2"></i>Non, annuler</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal pour les informations de l'utilisateur -->
    <div class="modal fade" id="userInfoModal" tabindex="-1" aria-labelledby="userInfoModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="userInfoModalLabel">Informations Utilisateur</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p><strong>Nom :</strong> <?php echo htmlspecialchars($_SESSION['utilisateur_nom'] ?? 'Inconnu'); ?></p>
                    <p><strong>Email :</strong> <?php echo htmlspecialchars($_SESSION['utilisateur_email'] ?? 'Non défini'); ?></p>
                    <p><strong>Rôle :</strong>
                        <?php
                        $role = $_SESSION['utilisateur_role'] ?? 'inconnu';
                        switch ($role) {
                            case 'admin': echo 'Administrateur'; break;
                            case 'organisateur': echo 'Organisateur'; break;
                            case 'analyste': echo 'Analyste'; break;
                            default: echo 'Inconnu';
                        }
                        ?>
                    </p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script>
        // Sidebar par défaut ouverte
        window.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('mainContent');
            sidebar.classList.add('active');
            mainContent.classList.add('active');
            const icon = document.getElementById('sidebarToggle').querySelector('i');
            icon.classList.remove('fa-bars');
            icon.classList.add('fa-times');

            // Activer le sous-menu de la campagne par défaut
            document.getElementById('subMenuCampagne').classList.add('active');
        });

        // Toggle Sidebar
        document.getElementById('sidebarToggle').addEventListener('click', function() {
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('mainContent');
            sidebar.classList.toggle('active');
            mainContent.classList.toggle('active');

            const icon = this.querySelector('i');
            if (sidebar.classList.contains('active')) {
                icon.classList.remove('fa-bars');
                icon.classList.add('fa-times');
            } else {
                icon.classList.remove('fa-times');
                icon.classList.add('fa-bars');
            }
        });

        // Sous-menus
        document.getElementById('menuDonneur').addEventListener('click', function() {
            document.getElementById('subMenuDonneur').classList.toggle('active');
        });

        document.getElementById('menuConditionSanter').addEventListener('click', function() {
            document.getElementById('subMenuConditionSanter').classList.toggle('active');
        });

        document.getElementById('menuCampagne').addEventListener('click', function() {
            document.getElementById('subMenuCampagne').classList.toggle('active');
        });

        const menuUtilisateur = document.getElementById('menuUtilisateur');
        if (menuUtilisateur) {
            menuUtilisateur.addEventListener('click', function() {
                document.getElementById('subMenuUtilisateur').classList.toggle('active');
            });
        }
    </script>
</body>
</html>