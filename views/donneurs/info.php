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
    <title>Informations du Donneur</title>
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

        .sidebar::-webkit-scrollbar {
            width: 5px;
        }

        .sidebar::-webkit-scrollbar-track {
            background: transparent;
        }

        .sidebar::-webkit-scrollbar-thumb {
            background-color: var(--primary-light);
            border-radius: 10px;
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
            max-height: 200px;
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

        .document-container {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: calc(100vh - 140px);
            margin: 0 20px;
        }

        .document-card {
            background: rgba(30, 41, 59, 0.8);
            border: none;
            border-radius: 16px;
            box-shadow: var(--box-shadow);
            width: 100%;
            max-width: 800px;
            padding: 30px;
            animation: fadeIn 0.6s ease forwards;
        }

        .document-header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid var(--primary);
            padding-bottom: 15px;
            position: relative;
        }

        .document-header h2 {
            font-family: 'Poppins', sans-serif;
            color: var(--primary);
            font-size: 28px;
            font-weight: 600;
            margin: 0;
        }
        
        .document-header::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 50%;
            transform: translateX(-50%);
            width: 100px;
            height: 4px;
            background: var(--accent-blue);
            border-radius: 2px;
        }

        .document-section {
            margin-bottom: 30px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 12px;
            padding: 20px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        
        .document-section:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.2);
            background: rgba(255, 255, 255, 0.08);
        }

        .document-section h3 {
            color: var(--white);
            font-family: 'Poppins', sans-serif;
            font-weight: 600;
            font-size: 20px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
            padding-bottom: 10px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
        }
        
        .document-section h3 i {
            color: var(--accent-blue);
            margin-right: 10px;
            font-size: 22px;
        }

        .document-item {
            display: flex;
            justify-content: space-between;
            padding: 12px 15px;
            border-bottom: 1px dashed rgba(255, 255, 255, 0.1);
            transition: all 0.2s ease;
            border-radius: 6px;
        }

        .document-item:last-child {
            border-bottom: none;
        }
        
        .document-item:hover {
            background: rgba(255, 255, 255, 0.05);
        }

        .document-label {
            font-weight: 500;
            color: var(--gray-light);
            flex-basis: 45%;
            display: flex;
            align-items: center;
        }
        
        .document-label i {
            margin-right: 8px;
            color: var(--accent-blue);
            width: 20px;
            text-align: center;
        }

        .document-value {
            color: var(--white);
            font-weight: 500;
            flex-basis: 55%;
            text-align: right;
        }
        
        .blood-type {
            display: inline-block;
            padding: 5px 12px;
            border-radius: 20px;
            background: var(--primary);
            font-weight: 600;
            letter-spacing: 0.5px;
        }
        
        .status-badge {
            display: inline-block;
            padding: 5px 12px;
            border-radius: 20px;
            font-weight: 600;
            letter-spacing: 0.5px;
        }
        
        .status-active {
            background: var(--success-green);
            color: white;
        }
        
        .status-inactive {
            background: var(--gray-light);
            color: var(--dark-bg);
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
        
        .donor-actions {
            display: flex;
            justify-content: center;
            gap: 15px;
            margin-top: 30px;
        }
        
        .donor-actions a {
            padding: 10px 20px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            gap: 8px;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.3s ease;
        }
        
        .action-edit {
            background: var(--accent-blue);
            color: white;
        }
        
        .action-edit:hover {
            background: #3a6d96;
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(70, 130, 180, 0.3);
            color: white;
        }
        
        .action-return {
            background: rgba(255, 255, 255, 0.1);
            color: white;
        }
        
        .action-return:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(255, 255, 255, 0.1);
            color: white;
        }
        
        .donor-avatar {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--accent-blue) 0%, #2c5282 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
            border: 3px solid rgba(255, 255, 255, 0.1);
        }
        
        .donor-avatar i {
            font-size: 40px;
            color: white;
        }

        .modal-content {
            background: var(--dark-light);
            color: var(--white);
            border: none;
            border-radius: 16px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        }

        .modal-header {
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            padding: 15px 20px;
            background: rgba(217, 83, 79, 0.1);
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

            .document-card {
                padding: 20px;
            }

            .document-header h2 {
                font-size: 24px;
            }

            .document-section h3 {
                font-size: 18px;
            }
            
            .document-item {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .document-value {
                text-align: left;
                margin-top: 5px;
                margin-left: 28px;
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
                    <div class="menu-item" id="menuCartographie">
                        <div>
                            <a href="index.php?action=cartographie_donneurs" style="color: var(--white); text-decoration: none;">
                                <i class="fas fa-map-marked-alt menu-icon"></i> Cartographie
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
                        <h1><i class="fas fa-info-circle"></i> Informations du Donneur</h1>
                    </div>
                    <div class="user-circle" data-bs-toggle="modal" data-bs-target="#userInfoModal">
                        <?php echo htmlspecialchars($roleLetter); ?>
                    </div>
                </div>

                <?php if (isset($donneur) && is_array($donneur) && !empty($donneur)): ?>
                    <div class="document-container">
                        <div class="document-card">
                            <div class="donor-avatar">
                                <i class="<?php echo htmlspecialchars($donneur['Genre'] ?? '') == 'M' ? 'fas fa-male' : 'fas fa-female'; ?>"></i>
                            </div>
                            <div class="document-header">
                                <h2>
                                    Fiche d'Information du Donneur - ID: <?php echo htmlspecialchars($donneur['ID'] ?? 'N/A'); ?>
                                    <?php if (isset($donneur['Statut']) && $donneur['Statut'] == 'Actif'): ?>
                                        <span class="status-badge status-active ms-2"><i class="fas fa-check-circle me-1"></i> Actif</span>
                                    <?php elseif (isset($donneur['Statut'])): ?>
                                        <span class="status-badge status-inactive ms-2"><i class="fas fa-times-circle me-1"></i> Inactif</span>
                                    <?php endif; ?>
                                </h2>
                            </div>
                            
                            <div class="document-section">
                                <h3><i class="fas fa-user"></i> Informations Personnelles</h3>
                                <div class="document-item">
                                    <span class="document-label"><i class="fas fa-calendar-alt"></i> Date d'enregistrement :</span>
                                    <span class="document-value"><?php echo htmlspecialchars($donneur['Date_Remplissage'] ?? 'Non spécifié'); ?></span>
                                </div>
                                <div class="document-item">
                                    <span class="document-label"><i class="fas fa-id-card"></i> Nom complet :</span>
                                    <span class="document-value"><?php echo htmlspecialchars($donneur['nom'] ?? 'Non spécifié'); ?></span>
                                </div>
                                <div class="document-item">
                                    <span class="document-label"><i class="fas fa-birthday-cake"></i> Date de naissance :</span>
                                    <span class="document-value"><?php echo htmlspecialchars($donneur['Date_Naissance'] ?? 'Non spécifié'); ?></span>
                                </div>
                                <div class="document-item">
                                    <span class="document-label"><i class="fas fa-hourglass-half"></i> Âge :</span>
                                    <span class="document-value"><?php echo htmlspecialchars($donneur['Age'] ?? 'Non spécifié') . ' ans'; ?></span>
                                </div>
                                <div class="document-item">
                                    <span class="document-label"><i class="fas fa-venus-mars"></i> Genre :</span>
                                    <span class="document-value">
                                        <?php echo htmlspecialchars($donneur['Genre'] ?? '') == 'M' ? 
                                            '<span class="status-badge" style="background-color: #3b82f6;"><i class="fas fa-male me-1"></i> Masculin</span>' : 
                                            '<span class="status-badge" style="background-color: #ec4899;"><i class="fas fa-female me-1"></i> Féminin</span>'; 
                                        ?>
                                    </span>
                                </div>
                                <div class="document-item">
                                    <span class="document-label"><i class="fas fa-heart"></i> Situation Matrimoniale :</span>
                                    <span class="document-value"><?php echo htmlspecialchars($donneur['Situation_Matrimoniale'] ?? 'Non spécifié'); ?></span>
                                </div>
                                <?php if (isset($donneur['Groupe_Sanguin'])): ?>
                                <div class="document-item">
                                    <span class="document-label"><i class="fas fa-tint"></i> Groupe Sanguin :</span>
                                    <span class="document-value">
                                        <span class="blood-type"><?php echo htmlspecialchars($donneur['Groupe_Sanguin']); ?></span>
                                    </span>
                                </div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="document-section">
                                <h3><i class="fas fa-briefcase"></i> Informations Professionnelles</h3>
                                <div class="document-item">
                                    <span class="document-label"><i class="fas fa-graduation-cap"></i> Niveau d'étude :</span>
                                    <span class="document-value"><?php echo htmlspecialchars($donneur['Niveau_Etude'] ?? 'Non spécifié'); ?></span>
                                </div>
                                <div class="document-item">
                                    <span class="document-label"><i class="fas fa-user-tie"></i> Profession :</span>
                                    <span class="document-value"><?php echo htmlspecialchars($donneur['Profession'] ?? 'Non spécifié'); ?></span>
                                </div>
                                <?php if (isset($donneur['Lieu_Travail'])): ?>
                                <div class="document-item">
                                    <span class="document-label"><i class="fas fa-building"></i> Lieu de travail :</span>
                                    <span class="document-value"><?php echo htmlspecialchars($donneur['Lieu_Travail']); ?></span>
                                </div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="document-section">
                                <h3><i class="fas fa-home"></i> Coordonnées & Résidence</h3>
                                <div class="document-item">
                                    <span class="document-label"><i class="fas fa-map-marker-alt"></i> Arrondissement :</span>
                                    <span class="document-value"><?php echo htmlspecialchars($donneur['Arrondissement_Residence'] ?? 'Non spécifié'); ?></span>
                                </div>
                                <div class="document-item">
                                    <span class="document-label"><i class="fas fa-street-view"></i> Quartier :</span>
                                    <span class="document-value"><?php echo htmlspecialchars($donneur['Quartier_Residence'] ?? 'Non spécifié'); ?></span>
                                </div>
                                <?php if (isset($donneur['Telephone'])): ?>
                                <div class="document-item">
                                    <span class="document-label"><i class="fas fa-phone"></i> Téléphone :</span>
                                    <span class="document-value"><?php echo htmlspecialchars($donneur['Telephone']); ?></span>
                                </div>
                                <?php endif; ?>
                                <?php if (isset($donneur['Email'])): ?>
                                <div class="document-item">
                                    <span class="document-label"><i class="fas fa-envelope"></i> Email :</span>
                                    <span class="document-value"><?php echo htmlspecialchars($donneur['Email']); ?></span>
                                </div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="document-section">
                                <h3><i class="fas fa-weight"></i> Caractéristiques Physiques</h3>
                                <div class="document-item">
                                    <span class="document-label"><i class="fas fa-ruler-vertical"></i> Taille :</span>
                                    <span class="document-value"><?php echo isset($donneur['Taille']) ? htmlspecialchars($donneur['Taille']) . ' m' : 'Non spécifié'; ?></span>
                                </div>
                                <div class="document-item">
                                    <span class="document-label"><i class="fas fa-balance-scale"></i> Poids :</span>
                                    <span class="document-value"><?php echo isset($donneur['Poids']) ? htmlspecialchars($donneur['Poids']) . ' kg' : 'Non spécifié'; ?></span>
                                </div>
                                <?php if (isset($donneur['Tension_Arterielle'])): ?>
                                <div class="document-item">
                                    <span class="document-label"><i class="fas fa-heartbeat"></i> Tension artérielle :</span>
                                    <span class="document-value"><?php echo htmlspecialchars($donneur['Tension_Arterielle']); ?></span>
                                </div>
                                <?php endif; ?>
                            </div>
                            
                            <?php if (isset($donneur['Derniere_Date_Don'])): ?>
                            <div class="document-section">
                                <h3><i class="fas fa-history"></i> Historique des Dons</h3>
                                <div class="document-item">
                                    <span class="document-label"><i class="fas fa-calendar-check"></i> Dernier don :</span>
                                    <span class="document-value"><?php echo htmlspecialchars($donneur['Derniere_Date_Don']); ?></span>
                                </div>
                                <div class="document-item">
                                    <span class="document-label"><i class="fas fa-calculator"></i> Nombre total de dons :</span>
                                    <span class="document-value"><?php echo htmlspecialchars($donneur['Nombre_Dons'] ?? '0'); ?></span>
                                </div>
                            </div>
                            <?php endif; ?>
                            
                            <?php if (isset($donneur['Observations']) && !empty($donneur['Observations'])): ?>
                            <div class="document-section">
                                <h3><i class="fas fa-comment-medical"></i> Observations</h3>
                                <div class="document-item">
                                    <span class="document-value"><?php echo nl2br(htmlspecialchars($donneur['Observations'])); ?></span>
                                </div>
                            </div>
                            <?php endif; ?>
                            
                            <div class="donor-actions">
                                <a href="index.php?action=modifier_donneur&id=<?php echo htmlspecialchars($donneur['ID'] ?? ''); ?>" class="action-edit">
                                    <i class="fas fa-edit"></i> Modifier
                                </a>
                                <a href="index.php?action=lister_donneurs" class="action-return">
                                    <i class="fas fa-arrow-left"></i> Retour à la liste
                                </a>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle me-2"></i> Le donneur demandé n'existe pas ou a été supprimé.
                    </div>
                    <div class="text-center mt-4">
                        <a href="index.php?action=lister_donneurs" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-2"></i> Retour à la liste des donneurs
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- User Info Modal -->
    <div class="modal fade" id="userInfoModal" tabindex="-1" aria-labelledby="userInfoModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="userInfoModalLabel">Informations Utilisateur</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="text-center mb-4">
                        <div class="user-circle mx-auto mb-3" style="width: 80px; height: 80px; font-size: 30px;">
                            <?php echo htmlspecialchars($roleLetter); ?>
                        </div>
                        <h5><?php echo htmlspecialchars($_SESSION['utilisateur_nom'] ?? 'Utilisateur'); ?></h5>
                        <span class="badge bg-primary"><?php echo htmlspecialchars(ucfirst($_SESSION['utilisateur_role'] ?? 'Inconnu')); ?></span>
                    </div>
                    <div class="list-group list-group-flush bg-transparent">
                        <div class="list-group-item bg-transparent text-white border-secondary d-flex justify-content-between">
                            <span><i class="fas fa-id-badge me-2"></i> ID :</span>
                            <span><?php echo htmlspecialchars($_SESSION['utilisateur_id'] ?? 'N/A'); ?></span>
                        </div>
                        <div class="list-group-item bg-transparent text-white border-secondary d-flex justify-content-between">
                            <span><i class="fas fa-envelope me-2"></i> Email :</span>
                            <span><?php echo htmlspecialchars($_SESSION['utilisateur_email'] ?? 'N/A'); ?></span>
                        </div>
                        <div class="list-group-item bg-transparent text-white border-secondary d-flex justify-content-between">
                            <span><i class="fas fa-clock me-2"></i> Dernière connexion :</span>
                            <span><?php echo htmlspecialchars($_SESSION['derniere_connexion'] ?? date('Y-m-d H:i:s')); ?></span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <a href="index.php?action=profil" class="btn btn-outline-light btn-sm">
                        <i class="fas fa-user-edit me-2"></i> Modifier Profil
                    </a>
                    <a href="index.php?action=deconnecter" class="btn btn-danger btn-sm">
                        <i class="fas fa-sign-out-alt me-2"></i> Déconnexion
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('mainContent');
            const sidebarToggle = document.getElementById('sidebarToggle');
            const icon = sidebarToggle.querySelector('i');

            // Initial state
            sidebar.classList.add('active');
            mainContent.classList.add('active');
            icon.classList.remove('fa-bars');
            icon.classList.add('fa-times');

            // Sidebar toggle
            sidebarToggle.addEventListener('click', function() {
                sidebar.classList.toggle('active');
                mainContent.classList.toggle('active');
                if (sidebar.classList.contains('active')) {
                    icon.classList.remove('fa-bars');
                    icon.classList.add('fa-times');
                } else {
                    icon.classList.remove('fa-times');
                    icon.classList.add('fa-bars');
                }
            });
            
            // Menu toggle functionality
            const menuItems = document.querySelectorAll('.menu-item');
            menuItems.forEach(item => {
                item.addEventListener('click', function(e) {
                    const submenuId = this.id.replace('menu', 'subMenu');
                    const submenu = document.getElementById(submenuId);
                    if (submenu) {
                        e.preventDefault();
                        submenu.classList.toggle('active');
                        document.querySelectorAll('.sub-menu').forEach(menu => {
                            if (menu.id !== submenuId) {
                                menu.classList.remove('active');
                            }
                        });
                    }
                });
            });
            
            // Handle screen size changes
            function handleResize() {
                if (window.innerWidth >= 992) {
                    sidebar.classList.add('active');
                    mainContent.classList.add('active');
                    icon.classList.remove('fa-bars');
                    icon.classList.add('fa-times');
                } else {
                    sidebar.classList.remove('active');
                    mainContent.classList.remove('active');
                    icon.classList.remove('fa-times');
                    icon.classList.add('fa-bars');
                }
            }
            
            // Initial call and listen for resize
            handleResize();
            window.addEventListener('resize', handleResize);

            // Auto-dismiss alerts
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                setTimeout(() => {
                    alert.style.opacity = '0';
                    alert.style.transition = 'opacity 0.5s ease';
                    setTimeout(() => alert.remove(), 500);
                }, 5000);
            });
        });
    </script>
</body>
</html>