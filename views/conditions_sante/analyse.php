<?php
// Vérifier si l'utilisateur est connecté
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
    <title>Analyse des Conditions de Santé</title>
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

        .dashboard-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            margin-bottom: 30px;
        }

        .chart-container {
            background: rgba(30, 41, 59, 0.8);
            padding: 20px;
            border-radius: 16px;
            box-shadow: var(--box-shadow);
            margin-bottom: 30px;
        }

        .chart-container h3 {
            margin-bottom: 20px;
            font-size: 1.3rem;
            color: var(--white);
        }

        .chart-wrapper {
            position: relative;
            height: 350px;
            width: 100%;
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

        .stat-cards {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: rgba(30, 41, 59, 0.8);
            border-radius: 16px;
            padding: 20px;
            display: flex;
            align-items: center;
            box-shadow: var(--box-shadow);
            transition: var(--transition);
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.15);
        }

        .stat-icon {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            font-size: 1.5rem;
        }

        .stat-icon.warning {
            background: rgba(245, 158, 11, 0.2);
            color: var(--warning-yellow);
        }

        .stat-icon.success {
            background: rgba(16, 185, 129, 0.2);
            color: var(--success-green);
        }

        .stat-icon.danger {
            background: rgba(239, 68, 68, 0.2);
            color: var(--danger-red);
        }

        .stat-info h4 {
            margin: 0;
            font-size: 1.8rem;
        }

        .stat-info p {
            margin: 5px 0 0;
            opacity: 0.7;
            font-size: 0.9rem;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @media (max-width: 1200px) {
            .dashboard-grid {
                grid-template-columns: 1fr;
            }
            .stat-cards {
                grid-template-columns: repeat(2, 1fr);
            }
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
            .stat-cards {
                grid-template-columns: 1fr;
            }
            .chart-wrapper {
                height: 300px;
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
            <i class="fas fa-map-marked-alt menu-icon"></i> Cartographie
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
                        <h1><i class="fas fa-chart-bar"></i> Analyse des Conditions de Santé</h1>
                    </div>
                    <div class="user-circle" data-bs-toggle="modal" data-bs-target="#userInfoModal">
                        <?php echo $roleLetter; ?>
                    </div>
                </div>

                <!-- Statistiques résumées -->
                <div class="stat-cards">
                    <div class="stat-card">
                        <div class="stat-icon warning">
                            <i class="fas fa-users"></i>
                        </div>
                        <div class="stat-info">
                            <h4><?php echo $stats['total'] ?? 0; ?></h4>
                            <p>Total donneurs</p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon success">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <div class="stat-info">
                            <h4><?php echo $stats['eligibles'] ?? 0; ?></h4>
                            <p>Donneurs éligibles</p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon danger">
                            <i class="fas fa-times-circle"></i>
                        </div>
                        <div class="stat-info">
                            <h4><?php echo ($stats['total'] ?? 0) - ($stats['eligibles'] ?? 0); ?></h4>
                            <p>Donneurs non éligibles</p>
                        </div>
                    </div>
                </div>

                <!-- Graphiques -->
                <div class="dashboard-grid">
                    <div class="chart-container">
                        <h3><i class="fas fa-chart-bar"></i> Impact des Conditions de Santé</h3>
                        <div class="chart-wrapper">
                            <canvas id="conditionsChart"></canvas>
                        </div>
                    </div>
                    <div class="chart-container">
                        <h3><i class="fas fa-chart-pie"></i> Éligibilité des Donneurs</h3>
                        <div class="chart-wrapper">
                            <canvas id="eligibilityChart"></canvas>
                        </div>
                    </div>
                </div>

                <div class="chart-container">
                    <h3><i class="fas fa-chart-line"></i> Évolution des Conditions de Santé</h3>
                    <div class="chart-wrapper">
                        <canvas id="trendChart"></canvas>
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
        // Sidebar Toggle
        window.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('mainContent');
            sidebar.classList.add('active');
            mainContent.classList.add('active');
            const icon = document.getElementById('sidebarToggle').querySelector('i');
            icon.classList.remove('fa-bars');
            icon.classList.add('fa-times');
            document.getElementById('subMenuConditionSanter').classList.add('active');
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

        // Gestion des menus
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

        // Configuration des graphiques
        Chart.defaults.color = '#f8fafc';
        Chart.defaults.font.family = "'Poppins', sans-serif";

        // Graphique des conditions de santé
        const conditionsCtx = document.getElementById('conditionsChart').getContext('2d');
        const conditionsChart = new Chart(conditionsCtx, {
            type: 'bar',
            data: {
                labels: ['VIH', 'HBS', 'HCV', 'Opéré', 'Drépanocytaire', 'Diabète', 'Hypertension', 'Asthme', 'Cardiaque'],
                datasets: [{
                    label: 'Nombre de donneurs affectés',
                    data: [
                        <?php echo $stats['hiv'] ?? 0; ?>,
                        <?php echo $stats['hbs'] ?? 0; ?>,
                        <?php echo $stats['hcv'] ?? 0; ?>,
                        <?php echo $stats['opere'] ?? 0; ?>,
                        <?php echo $stats['drepanocytaire'] ?? 0; ?>,
                        <?php echo $stats['diabetique'] ?? 0; ?>,
                        <?php echo $stats['hypertendu'] ?? 0; ?>,
                        <?php echo $stats['asthmatique'] ?? 0; ?>,
                        <?php echo $stats['cardiaque'] ?? 0; ?>
                    ],
                    backgroundColor: 'rgba(217, 83, 79, 0.8)',
                    borderColor: 'rgba(217, 83, 79, 1)',
                    borderWidth: 1,
                    borderRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: { beginAtZero: true }
                },
                plugins: {
                    legend: { display: false }
                }
            }
        });

        // Graphique d'éligibilité
        const eligibilityCtx = document.getElementById('eligibilityChart').getContext('2d');
        const eligibilityChart = new Chart(eligibilityCtx, {
            type: 'doughnut',
            data: {
                labels: ['Éligibles', 'Non éligibles'],
                datasets: [{
                    data: [
                        <?php echo $stats['eligibles'] ?? 0; ?>,
                        <?php echo ($stats['total'] ?? 0) - ($stats['eligibles'] ?? 0); ?>
                    ],
                    backgroundColor: ['rgba(16, 185, 129, 0.8)', 'rgba(239, 68, 68, 0.8)'],
                    borderColor: ['rgba(16, 185, 129, 1)', 'rgba(239, 68, 68, 1)'],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '70%',
                plugins: {
                    legend: { position: 'bottom' }
                }
            }
        });

        // Graphique d'évolution (exemple statique, à adapter avec des données réelles)
        const trendCtx = document.getElementById('trendChart').getContext('2d');
        const trendChart = new Chart(trendCtx, {
            type: 'line',
            data: {
                labels: ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Juin', 'Juil', 'Août', 'Sep', 'Oct', 'Nov', 'Déc'],
                datasets: [
                    {
                        label: 'VIH',
                        data: [12, 14, 15, 13, 16, 18, 17, 19, 18, 16, 15, 14],
                        borderColor: '#ef4444',
                        tension: 0.4,
                        fill: false
                    },
                    {
                        label: 'Diabète',
                        data: [25, 28, 27, 29, 32, 30, 33, 35, 34, 32, 30, 28],
                        borderColor: '#4682b4',
                        tension: 0.4,
                        fill: false
                    },
                    {
                        label: 'Hypertension',
                        data: [18, 20, 22, 24, 23, 25, 24, 26, 28, 25, 24, 22],
                        borderColor: '#f59e0b',
                        tension: 0.4,
                        fill: false
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });
    </script>
</body>
</html>