<?php
// views/donneurs/cartographie.php
if (!isset($_SESSION['utilisateur_id'])) {
    header("Location: index.php?action=login");
    exit;
}

// Calcul des statistiques pour la carte avec vérification
$totalDonneurs = 0;
if (isset($donneesGeographiques) && is_array($donneesGeographiques)) {
    $totalDonneurs = array_sum(array_column($donneesGeographiques, 'nombre_donneurs'));
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
    <title>Cartographie des Donneurs - Yaoundé</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/jsLKmRVo/w5mYbKymI=" crossorigin=""/>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Montserrat:wght@400;600&display=swap" rel="stylesheet">
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

        .menu-item.active-menu {
            background-color: rgba(255, 255, 255, 0.2);
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

        .alert-success {
            background: rgba(16, 185, 129, 0.15);
            border-left: 4px solid var(--success-green);
            color: #ecfdf5;
        }

        .alert-danger {
            background: rgba(239, 68, 68, 0.15);
            border-left: 4px solid var(--danger-red);
            color: #fef2f2;
        }

        .stats-card {
            background: rgba(30, 41, 59, 0.8);
            border-radius: 16px;
            padding: 25px;
            text-align: center;
            color: var(--white);
            box-shadow: var(--box-shadow);
            transition: var(--transition);
            height: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        .stats-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }

        .stats-card h3 {
            font-weight: 600;
            font-size: 1.5rem;
            margin: 15px 0 10px;
            color: var(--gray-100);
        }

        .stats-card p {
            font-size: 2rem;
            margin: 0;
            font-weight: 700;
            color: var(--white);
        }

        .stats-card i {
            font-size: 2.5rem;
            background: rgba(255, 255, 255, 0.1);
            width: 70px;
            height: 70px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            margin-bottom: 15px;
        }

        #map {
            height: 600px;
            border-radius: 16px;
            box-shadow: var(--box-shadow);
            background-color: var(--dark-light);
            position: relative;
            overflow: hidden;
        }

        .leaflet-container {
            background-color: var(--dark-light) !important;
        }

        .leaflet-popup-content-wrapper {
            background: var(--dark-light);
            color: var(--white);
            border-radius: 8px;
            box-shadow: var(--box-shadow);
        }

        .leaflet-popup-tip {
            background: var(--dark-light);
        }

        .leaflet-control-zoom a {
            background-color: var(--dark-bg) !important;
            color: var(--white) !important;
            border: 1px solid rgba(255, 255, 255, 0.2) !important;
        }

        .leaflet-control-zoom a:hover {
            background-color: var(--primary) !important;
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

        .form-select {
            background-color: var(--dark-bg);
            color: var(--white);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 8px;
            padding: 8px 12px;
            transition: var(--transition);
        }

        .form-select:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 0.2rem rgba(217, 83, 79, 0.25);
        }

        .form-label {
            color: var(--white);
            font-weight: 500;
        }

        .card {
            background: rgba(30, 41, 59, 0.8);
            border-radius: 16px;
            padding: 20px;
            box-shadow: var(--box-shadow);
            animation: fadeIn 0.6s ease forwards;
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
        }

        @media (max-width: 768px) {
            .page-header h1 {
                font-size: 1.5rem;
            }
            .stats-card {
                margin-bottom: 20px;
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
                    <div class="menu-item active-menu" id="menuCartographie">
                        <div><i class="fas fa-map-marked-alt"></i> Cartographie</div>
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
                        <a href="index.php?action=deconnecter"><i class="fas fa-sign-out-alt"></i> Déconnexion</a>
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
                        <h1><i class="fas fa-map-marked-alt"></i> Cartographie des Donneurs - Yaoundé</h1>
                    </div>
                    <div class="user-circle" data-bs-toggle="modal" data-bs-target="#userInfoModal">
                        <?php echo $roleLetter; ?>
                    </div>
                </div>

                <?php if (isset($_GET['message'])): ?>
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle me-2"></i><?php echo $_GET['message']; ?>
                    </div>
                <?php endif; ?>

                <?php if (isset($_GET['error'])): ?>
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle me-2"></i><?php echo $_GET['error']; ?>
                    </div>
                <?php endif; ?>

                <!-- Statistiques -->
                <div class="row mb-4">
                    <div class="col-md-3 col-sm-6">
                        <div class="stats-card">
                            <i class="fas fa-users" style="color: var(--primary);"></i>
                            <h3>Total Donneurs</h3>
                            <p><?php echo $totalDonneurs; ?></p>
                        </div>
                    </div>
                    <?php if (isset($statistiquesAvancees)): ?>
                    <div class="col-md-3 col-sm-6">
                        <div class="stats-card">
                            <i class="fas fa-tint" style="color: var(--accent-blue);"></i>
                            <h3>Groupe A+</h3>
                            <p><?php echo $statistiquesAvancees['groupe_A_positif'] ?? 0; ?></p>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <div class="stats-card">
                            <i class="fas fa-tint" style="color: var(--success-green);"></i>
                            <h3>Groupe O+</h3>
                            <p><?php echo $statistiquesAvancees['groupe_O_positif'] ?? 0; ?></p>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <div class="stats-card">
                            <i class="fas fa-calendar-check" style="color: var(--warning-yellow);"></i>
                            <h3>Dernière campagne</h3>
                            <p><?php echo $statistiquesAvancees['derniere_campagne'] ?? 'N/A'; ?></p>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Filtres de carte -->
                <div class="row mb-4">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="d-flex flex-wrap gap-3 align-items-end">
                                <div class="form-group me-3">
                                    <label for="filtreArrondissement" class="form-label">Arrondissement</label>
                                    <select id="filtreArrondissement" class="form-select form-select-sm">
                                        <option value="">Tous</option>
                                        <option value="Yaoundé 1">Yaoundé 1</option>
                                        <option value="Yaoundé 2">Yaoundé 2</option>
                                        <option value="Yaoundé 3">Yaoundé 3</option>
                                        <option value="Yaoundé 4">Yaoundé 4</option>
                                        <option value="Yaoundé 5">Yaoundé 5</option>
                                        <option value="Yaoundé 6">Yaoundé 6</option>
                                        <option value="Yaoundé 7">Yaoundé 7</option>
                                        <?php if (isset($arrondissements) && is_array($arrondissements)): ?>
                                            <?php foreach ($arrondissements as $arrondissement): ?>
                                                <option value="<?php echo htmlspecialchars($arrondissement); ?>"><?php echo htmlspecialchars($arrondissement); ?></option>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>
                                </div>
                                <div class="form-group me-3">
                                    <label for="filtreGroupe" class="form-label">Groupe Sanguin</label>
                                    <select id="filtreGroupe" class="form-select form-select-sm">
                                        <option value="">Tous</option>
                                        <option value="A+">A+</option>
                                        <option value="A-">A-</option>
                                        <option value="B+">B+</option>
                                        <option value="B-">B-</option>
                                        <option value="AB+">AB+</option>
                                        <option value="AB-">AB-</option>
                                        <option value="O+">O+</option>
                                        <option value="O-">O-</option>
                                    </select>
                                </div>
                                <div class="ms-auto">
                                    <button id="resetFiltres" class="btn btn-primary btn-sm">
                                        <i class="fas fa-sync-alt me-2"></i>Réinitialiser
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Carte -->
                <div class="row">
                    <div class="col-md-12">
                        <div id="map"></div>
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.0/jquery.min.js"></script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('mainContent');
            sidebar.classList.add('active');
            mainContent.classList.add('active');
            const icon = document.getElementById('sidebarToggle').querySelector('i');
            icon.classList.remove('fa-bars');
            icon.classList.add('fa-times');

            const statCards = document.querySelectorAll('.stats-card');
            statCards.forEach((card, index) => {
                card.style.opacity = "0";
                card.style.animation = `fadeIn 0.5s ease forwards ${0.2 + (index * 0.1)}s`;
            });

            document.getElementById('sidebarToggle').addEventListener('click', function() {
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

            initMap();
        });

        function initMap() {
            // Centrer la carte sur Yaoundé, Cameroun
            const map = L.map('map', {
                zoomControl: true,
                attributionControl: true,
                scrollWheelZoom: true,
                dragging: true
            }).setView([3.8480, 11.5021], 12); // Coordonnées de Yaoundé

            L.tileLayer('https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png', {
                attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors © <a href="https://carto.com/attributions">CARTO</a>',
                subdomains: 'abcd',
                maxZoom: 20
            }).addTo(map);

            // Coordonnées approximatives des arrondissements de Yaoundé
            const coordonneesYaounde = {
                "Yaoundé 1": { lat: 3.8667, lng: 11.5167 },
                "Yaoundé 2": { lat: 3.8500, lng: 11.5000 },
                "Yaoundé 3": { lat: 3.8333, lng: 11.4833 },
                "Yaoundé 4": { lat: 3.8167, lng: 11.4667 },
                "Yaoundé 5": { lat: 3.8833, lng: 11.5333 },
                "Yaoundé 6": { lat: 3.9000, lng: 11.5500 },
                "Yaoundé 7": { lat: 3.8667, lng: 11.4667 }
            };

            const donneesGeographiques = <?php echo json_encode($donneesGeographiques ?? []); ?>;
            const markersGroup = L.layerGroup().addTo(map);

            function getMarkerColor(count) {
                if (count > 50) return '#ef4444';
                if (count > 25) return '#f59e0b';
                if (count > 10) return '#10b981';
                return '#4682b4';
            }

            function createMarkers(data) {
                markersGroup.clearLayers();

                data.forEach(function(donneur) {
                    if (coordonneesYaounde[donneur.arrondissement]) {
                        const coord = coordonneesYaounde[donneur.arrondissement];
                        const markerColor = getMarkerColor(donneur.nombre_donneurs);

                        const marker = L.circleMarker([coord.lat, coord.lng], {
                            radius: Math.min(donneur.nombre_donneurs / 2, 20),
                            fillColor: markerColor,
                            color: '#fff',
                            weight: 1,
                            opacity: 1,
                            fillOpacity: 0.8
                        });

                        marker.bindPopup(`
                            <div style="text-align: center;">
                                <h6>${donneur.arrondissement}</h6>
                                <p><strong>Nombre de donneurs:</strong> ${donneur.nombre_donneurs}</p>
                                <p><strong>Groupe sanguin majoritaire:</strong> ${donneur.groupe_sanguin_majoritaire || 'Non défini'}</p>
                            </div>
                        `);

                        marker.arrondissement = donneur.arrondissement;
                        marker.groupe_sanguin = donneur.groupe_sanguin_majoritaire;
                        markersGroup.addLayer(marker);
                    }
                });
            }

            // Initialiser les marqueurs avec toutes les données
            createMarkers(donneesGeographiques);

            function applyFilters() {
                const arrondissement = document.getElementById('filtreArrondissement').value;
                const groupe = document.getElementById('filtreGroupe').value;

                let filteredData = donneesGeographiques.filter(donneur => {
                    let matchArrondissement = !arrondissement || donneur.arrondissement === arrondissement;
                    let matchGroupe = !groupe || donneur.groupe_sanguin_majoritaire === groupe;
                    return matchArrondissement && matchGroupe;
                });

                createMarkers(filteredData);
            }

            function resetFilters() {
                document.getElementById('filtreArrondissement').value = '';
                document.getElementById('filtreGroupe').value = '';
                createMarkers(donneesGeographiques);
            }

            document.getElementById('filtreArrondissement').addEventListener('change', applyFilters);
            document.getElementById('filtreGroupe').addEventListener('change', applyFilters);
            document.getElementById('resetFiltres').addEventListener('click', resetFilters);
        }
    </script>
</body>
</html>