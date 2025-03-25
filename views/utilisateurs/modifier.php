<?php
// Pas besoin de session_start() ici, il est dans index.php

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
        $roleLetter = '?';
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier un Utilisateur</title>
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

        .btn-primary {
            background: var(--primary);
            color: var(--white);
        }

        .btn-primary:hover {
            background: var(--primary-dark);
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(169, 68, 66, 0.4);
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

        .alert {
            border-radius: 10px;
            padding: 16px;
            margin-bottom: 25px;
            animation: slideDown 0.5s ease forwards;
            font-weight: 500;
            border: none;
            box-shadow: var(--box-shadow);
        }

        .alert-danger {
            background: rgba(239, 68, 68, 0.15);
            border-left: 4px solid var(--danger-red);
            color: #fef2f2;
        }

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

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .form-container {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: calc(100vh - 140px);
            margin: 0 20px;
        }

        .form-box {
            background: var(--table-bg);
            padding: 30px;
            border-radius: 16px;
            box-shadow: var(--box-shadow);
            width: 100%;
            max-width: 600px;
            animation: fadeIn 0.6s ease forwards;
        }

        .form-control, .form-select {
            background: var(--dark-light);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: var(--white);
            border-radius: 8px;
            transition: var(--transition);
        }

        .form-control:focus, .form-select:focus {
            background: var(--dark-light);
            border-color: var(--primary);
            box-shadow: 0 0 10px rgba(217, 83, 79, 0.3);
            color: var(--white);
        }

        .form-label {
            font-weight: 600;
            color: var(--white);
        }

        .form-grid {
            display: grid;
            gap: 20px;
            margin-bottom: 20px;
        }

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
                        <h1><i class="fas fa-user-edit"></i> Modifier un Utilisateur</h1>
                    </div>
                    <div class="user-circle" data-bs-toggle="modal" data-bs-target="#userInfoModal">
                        <?php echo $roleLetter; ?>
                    </div>
                </div>

                <?php if (isset($_GET['error'])): ?>
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle me-2"></i><?php echo $_GET['error']; ?>
                    </div>
                <?php endif; ?>

                <div class="form-container">
                    <div class="form-box">
                        <form action="index.php?action=modifier_utilisateur&id=<?php echo $utilisateur['ID_Utilisateur']; ?>" method="POST">
                            <div class="form-grid">
                                <div>
                                    <label for="nom" class="form-label">Nom</label>
                                    <input type="text" class="form-control" id="nom" name="nom" value="<?php echo $utilisateur['Nom']; ?>" required>
                                </div>
                                <div>
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="email" name="email" value="<?php echo $utilisateur['Email']; ?>" required>
                                </div>
                                <div>
                                    <label for="mot_de_passe" class="form-label">Nouveau Mot de Passe (laisser vide pour ne pas changer)</label>
                                    <input type="password" class="form-control" id="mot_de_passe" name="mot_de_passe">
                                </div>
                                <div>
                                    <label for="role" class="form-label">Rôle</label>
                                    <select class="form-select" id="role" name="role" required>
                                        <option value="admin" <?php echo $utilisateur['Role'] == 'admin' ? 'selected' : ''; ?>>Administrateur</option>
                                        <option value="organisateur" <?php echo $utilisateur['Role'] == 'organisateur' ? 'selected' : ''; ?>>Organisateur</option>
                                        <option value="analyste" <?php echo $utilisateur['Role'] == 'analyste' ? 'selected' : ''; ?>>Analyste</option>
                                    </select>
                                </div>
                            </div>
                            <div class="text-center">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i> Modifier
                                </button>
                                <a href="index.php?action=lister_utilisateurs" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left me-2"></i> Retour
                                </a>
                            </div>
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
        // Ajouter les classes 'active' par défaut au chargement de la page
        window.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('mainContent');
            sidebar.classList.add('active');
            mainContent.classList.add('active');
            const icon = document.getElementById('sidebarToggle').querySelector('i');
            icon.classList.remove('fa-bars');
            icon.classList.add('fa-times');

            // Activer le sous-menu Utilisateur par défaut si présent
            const subMenuUtilisateur = document.getElementById('subMenuUtilisateur');
            if (subMenuUtilisateur) {
                subMenuUtilisateur.classList.add('active');
            }
        });

        // Fonction pour le toggle du sidebar
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

        // Fonctions pour les sous-menus
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

        // Auto-fermeture des alertes après 5 secondes
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(alert => {
            setTimeout(() => {
                alert.style.opacity = '0';
                alert.style.transition = 'opacity 0.5s ease';
                setTimeout(() => {
                    alert.style.display = 'none';
                }, 500);
            }, 5000);
        });
    </script>
</body>
</html>