<?php
// Pas besoin de session_start() ici, il est dans index.php

// Vérifier si l'utilisateur est connecté, sinon rediriger vers la page de connexion
if (!isset($_SESSION['utilisateur_id'])) {
    header("Location: index.php?action=login");
    exit;
}

// Initialiser $donneurs comme tableau vide si non défini (sécurité supplémentaire)
$donneurs = $donneurs ?? [];

// Calcul des données pour les graphiques
$donneursParJour = [];
$donneursParSemaine = [];
$donneursParMois = [];

foreach ($donneurs as $donneur) {
    $date = new DateTime($donneur['Date_Remplissage']);
    
    // Par jour
    $jour = $date->format('Y-m-d');
    $donneursParJour[$jour] = ($donneursParJour[$jour] ?? 0) + 1;
    
    // Par semaine (numéro de semaine dans l'année)
    $semaine = $date->format('Y-W');
    $donneursParSemaine[$semaine] = ($donneursParSemaine[$semaine] ?? 0) + 1;
    
    // Par mois
    $mois = $date->format('Y-m');
    $donneursParMois[$mois] = ($donneursParMois[$mois] ?? 0) + 1;
}

// Conversion en format JSON pour JavaScript
$joursLabels = json_encode(array_keys($donneursParJour));
$joursData = json_encode(array_values($donneursParJour));
$semainesLabels = json_encode(array_keys($donneursParSemaine));
$semainesData = json_encode(array_values($donneursParSemaine));
$moisLabels = json_encode(array_keys($donneursParMois));
$moisData = json_encode(array_values($donneursParMois));

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
    <title>Ajouter un Donneur</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Montserrat:wght@400;600&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        :root {
            --primary: #d9534f;
            --primary-dark: #c9302c;
            --primary-light: #f2dede;
            --dark-bg: #1e293b;
            --dark-light: #334155;
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

        .sub-menu {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease;
            background: rgba(0, 0, 0, 0.2);
        }

        .sub-menu.active {
            max-height: 160px;
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

        .form-container {
            margin: 0 20px;
        }

        .form-frame {
            background: rgba(30, 41, 59, 0.8);
            border-radius: 16px;
            padding: 30px;
            box-shadow: var(--box-shadow);
            animation: fadeIn 0.6s ease forwards;
            max-width: 900px;
            width: 100%;
            margin: 0 auto;
        }

        .form-row {
            display: flex;
            gap: 15px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }

        .form-row > div {
            flex: 1;
            min-width: 200px;
        }

        .form-control, .form-select {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: var(--white);
            border-radius: 8px;
            transition: var(--transition);
            padding: 10px;
        }

        .form-control:focus, .form-select:focus {
            background: rgba(255, 255, 255, 0.1);
            border-color: var(--primary);
            box-shadow: 0 0 10px rgba(217, 83, 79, 0.3);
            color: var(--white);
        }

        .form-label {
            font-weight: 500;
            color: var(--white);
            margin-bottom: 8px;
        }

        .btn-primary {
            background: var(--primary);
            color: var(--white);
            border-radius: 8px;
            padding: 10px 20px;
            font-weight: 500;
            text-transform: uppercase;
            transition: var(--transition);
            border: none;
        }

        .btn-primary:hover {
            background: var(--primary-dark);
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(169, 68, 66, 0.4);
        }

        .btn-secondary {
            background: var(--accent-blue);
            color: var(--white);
            border-radius: 8px;
            padding: 10px 20px;
            font-weight: 500;
            text-transform: uppercase;
            transition: var(--transition);
            border: none;
        }

        .btn-secondary:hover {
            background: #3b72a5;
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(70, 130, 180, 0.4);
        }

        .charts-row {
            display: flex;
            gap: 20px;
            flex-wrap: nowrap;
            margin-bottom: 40px;
            overflow-x: auto;
        }

        .chart-container {
            background: rgba(30, 41, 59, 0.8);
            border-radius: 16px;
            padding: 20px;
            box-shadow: var(--box-shadow);
            flex: 1;
            min-width: 300px;
            animation: fadeIn 0.6s ease forwards;
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

        .logout-item:hover {
            background-color: var(--danger-red);
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
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
            .charts-row {
                flex-wrap: wrap;
            }
        }

        @media (max-width: 768px) {
            .page-header h1 {
                font-size: 1.5rem;
            }
            .form-row > div {
                min-width: 100%;
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
                        <div><i class="fas fa-user-md"></i> Donneur</div>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="sub-menu" id="subMenuDonneur">
                        <div class="sub-menu-item"><a href="index.php?action=ajouter_donneur"><i class="fas fa-plus-circle"></i> Ajouter</a></div>
                        <div class="sub-menu-item"><a href="index.php?action=lister_donneurs"><i class="fas fa-list"></i> Lister</a></div>
                    </div>
                    <div class="menu-item" id="menuConditionSanter">
                        <div><i class="fas fa-stethoscope"></i> Condition Santé</div>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="sub-menu" id="subMenuConditionSanter">
                        <div class="sub-menu-item"><a href="index.php?action=ajouter_condition"><i class="fas fa-plus-circle"></i> Ajouter</a></div>
                        <div class="sub-menu-item"><a href="index.php?action=lister_conditions"><i class="fas fa-list"></i> Lister</a></div>
                        <div class="sub-menu-item"><a href="index.php?action=analyser_conditions"><i class="fas fa-chart-bar"></i> Analyser</a></div>
                    </div>
                    <div class="menu-item" id="menuCampagne">
                        <div><i class="fas fa-bullhorn"></i> Campagne</div>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="sub-menu" id="subMenuCampagne">
                        <div class="sub-menu-item"><a href="index.php?action=ajouter_campagne"><i class="fas fa-plus-circle"></i> Ajouter</a></div>
                        <div class="sub-menu-item"><a href="index.php?action=lister_campagnes"><i class="fas fa-list"></i> Lister</a></div>
                    </div>
                    <div class="menu-item" id="menuCartographie">
                        <div>
                            <a href="index.php?action=cartographie_donneurs" style="color: var(--white); text-decoration: none;">
                                <i class="fas fa-map-marked-alt"></i> Cartographie
                            </a>
                        </div>
                    </div>
                    <?php if (isset($_SESSION['utilisateur_role']) && $_SESSION['utilisateur_role'] === 'admin'): ?>
                        <div class="menu-item" id="menuUtilisateur">
                            <div><i class="fas fa-users"></i> Utilisateur</div>
                            <i class="fas fa-chevron-down"></i>
                        </div>
                        <div class="sub-menu" id="subMenuUtilisateur">
                            <div class="sub-menu-item"><a href="index.php?action=ajouter_utilisateur"><i class="fas fa-plus-circle"></i> Ajouter</a></div>
                            <div class="sub-menu-item"><a href="index.php?action=lister_utilisateurs"><i class="fas fa-list"></i> Lister</a></div>
                        </div>
                    <?php endif; ?>
                    <div class="logout-item">
                        <a href="index.php?action=deconnecter" style="color: white; text-decoration: none"><i class="fas fa-sign-out-alt"></i> Déconnexion</a>
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
                        <h1><i class="fas fa-user-plus"></i> Ajouter un Donneur</h1>
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

                <!-- Graphiques -->
                <div class="charts-row">
                    <div class="chart-container">
                        <canvas id="chartParJour"></canvas>
                    </div>
                    <div class="chart-container">
                        <canvas id="chartParSemaine"></canvas>
                    </div>
                    <div class="chart-container">
                        <canvas id="chartParMois"></canvas>
                    </div>
                </div>

                <!-- Formulaire -->
                <div class="form-container">
                    <div class="form-frame">
                        <form action="index.php?action=ajouter_donneur" method="POST" class="w-100">
                            <div class="form-row">
                                <div>
                                    <label for="date_remplissage" class="form-label">Date de Remplissage</label>
                                    <input type="date" class="form-control" id="date_remplissage" name="date_remplissage" required readonly>
                                </div>
                                <div>
                                    <label for="date_naissance" class="form-label">Date de Naissance</label>
                                    <input type="date" class="form-control" id="date_naissance" name="date_naissance" required>
                                </div>
                            </div>
                            <div class="form-row">
                                <div>
                                    <label for="nom" class="form-label">Nom du Donneur</label>
                                    <input type="text" class="form-control" id="nom" name="nom">
                                </div>
                                <div>
                                    <label for="niveau_etude" class="form-label">Niveau d'Étude</label>
                                    <input type="text" class="form-control" id="niveau_etude" name="niveau_etude">
                                </div>
                                <div>
                                    <label for="genre" class="form-label">Genre</label>
                                    <select class="form-select" id="genre" name="genre" required>
                                        <option value="M">M</option>
                                        <option value="F">F</option>
                                        <option value="Autre">Autre</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-row">
                                <div>
                                    <label for="taille" class="form-label">Taille (m)</label>
                                    <input type="number" step="0.01" class="form-control" id="taille" name="taille">
                                </div>
                                <div>
                                    <label for="poids" class="form-label">Poids (kg)</label>
                                    <input type="number" step="0.1" class="form-control" id="poids" name="poids">
                                </div>
                            </div>
                            <div class="form-row">
                                <div>
                                    <label for="situation_matrimoniale" class="form-label">Situation Matrimoniale</label>
                                    <input type="text" class="form-control" id="situation_matrimoniale" name="situation_matrimoniale">
                                </div>
                                <div>
                                    <label for="profession" class="form-label">Profession</label>
                                    <input type="text" class="form-control" id="profession" name="profession">
                                </div>
                            </div>
                            <div class="form-row">
                                <div>
                                    <label for="arrondissement_residence" class="form-label">Arrondissement de Résidence</label>
                                    <input type="text" class="form-control" id="arrondissement_residence" name="arrondissement_residence">
                                </div>
                                <div>
                                    <label for="quartier_residence" class="form-label">Quartier de Résidence</label>
                                    <input type="text" class="form-control" id="quartier_residence" name="quartier_residence">
                                </div>
                            </div>
                            <div class="text-center">
                                <button type="submit" class="btn btn-primary"><i class="fas fa-plus me-2"></i>Ajouter</button>
                                <a href="index.php?action=lister_donneurs" class="btn btn-secondary"><i class="fas fa-arrow-left me-2"></i>Retour</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Utilisateur -->
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
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Fermer</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script>
        window.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('mainContent');
            sidebar.classList.add('active');
            mainContent.classList.add('active');
            const icon = document.getElementById('sidebarToggle').querySelector('i');
            icon.classList.remove('fa-bars');
            icon.classList.add('fa-times');
            document.getElementById('subMenuDonneur').classList.add('active');

            const chartContainers = document.querySelectorAll('.chart-container');
            chartContainers.forEach((container, index) => {
                container.style.opacity = "0";
                container.style.animation = `fadeIn 0.5s ease forwards ${0.2 + (index * 0.1)}s`;
            });

            const today = new Date();
            const formattedDate = `${today.getFullYear()}-${(today.getMonth() + 1).toString().padStart(2, '0')}-${today.getDate().toString().padStart(2, '0')}`;
            document.getElementById('date_remplissage').value = formattedDate;

            const maxDate = new Date(today.getFullYear() - 21, today.getMonth(), today.getDate());
            const formattedMaxDate = `${maxDate.getFullYear()}-${(maxDate.getMonth() + 1).toString().padStart(2, '0')}-${maxDate.getDate().toString().padStart(2, '0')}`;
            const dateNaissanceInput = document.getElementById('date_naissance');
            dateNaissanceInput.max = formattedMaxDate;
            dateNaissanceInput.min = '1900-01-01';
        });

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

        const ctxJour = document.getElementById('chartParJour').getContext('2d');
        new Chart(ctxJour, {
            type: 'line',
            data: {
                labels: <?php echo $joursLabels; ?>,
                datasets: [{
                    label: 'Donneurs par Jour',
                    data: <?php echo $joursData; ?>,
                    borderColor: 'rgba(217, 83, 79, 1)',
                    backgroundColor: 'rgba(217, 83, 79, 0.2)',
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                scales: {
                    y: { beginAtZero: true, title: { display: true, text: 'Nombre de Donneurs', color: '#f8fafc' } },
                    x: { title: { display: true, text: 'Date', color: '#f8fafc' } }
                },
                plugins: { legend: { labels: { color: '#f8fafc' } } }
            }
        });

        const ctxSemaine = document.getElementById('chartParSemaine').getContext('2d');
        new Chart(ctxSemaine, {
            type: 'line',
            data: {
                labels: <?php echo $semainesLabels; ?>,
                datasets: [{
                    label: 'Donneurs par Semaine',
                    data: <?php echo $semainesData; ?>,
                    borderColor: 'rgba(70, 130, 180, 1)',
                    backgroundColor: 'rgba(70, 130, 180, 0.2)',
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                scales: {
                    y: { beginAtZero: true, title: { display: true, text: 'Nombre de Donneurs', color: '#f8fafc' } },
                    x: { title: { display: true, text: 'Semaine (Année-Semaine)', color: '#f8fafc' } }
                },
                plugins: { legend: { labels: { color: '#f8fafc' } } }
            }
        });

        const ctxMois = document.getElementById('chartParMois').getContext('2d');
        new Chart(ctxMois, {
            type: 'line',
            data: {
                labels: <?php echo $moisLabels; ?>,
                datasets: [{
                    label: 'Donneurs par Mois',
                    data: <?php echo $moisData; ?>,
                    borderColor: 'rgba(16, 185, 129, 1)',
                    backgroundColor: 'rgba(16, 185, 129, 0.2)',
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                scales: {
                    y: { beginAtZero: true, title: { display: true, text: 'Nombre de Donneurs', color: '#f8fafc' } },
                    x: { title: { display: true, text: 'Mois (Année-Mois)', color: '#f8fafc' } }
                },
                plugins: { legend: { labels: { color: '#f8fafc' } } }
            }
        });
    </script>
</body>
</html>