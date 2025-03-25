<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - Don de Sang</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Montserrat:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --blood-red: #e63946;
            --blood-red-dark: #d00000;
            --blood-red-light: #ffccd5;
            --dark-blue: #1d3557;
            --medium-blue: #457b9d;
            --light-blue: #a8dadc;
            --off-white: #f1faee;
            --pure-white: #ffffff;
            --box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            --transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }

        body {
            background: linear-gradient(135deg, var(--dark-blue) 0%, var(--medium-blue) 100%);
            color: var(--off-white);
            font-family: 'Poppins', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            overflow-x: hidden;
            position: relative;
        }

        /* Animation d'arrière-plan */
        body::before {
            content: "";
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: 
                radial-gradient(circle at 10% 10%, rgba(230, 57, 70, 0.1) 0%, transparent 50%),
                radial-gradient(circle at 90% 90%, rgba(168, 218, 220, 0.1) 0%, transparent 50%);
            z-index: -1;
            animation: backgroundPulse 15s ease-in-out infinite alternate;
        }

        @keyframes backgroundPulse {
            0% {
                opacity: 0.8;
                transform: scale(1);
            }
            100% {
                opacity: 1;
                transform: scale(1.1);
            }
        }

        /* Particules de cellules sanguines */
        .blood-particles {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: -1;
        }

        .particle {
            position: absolute;
            background-color: var(--blood-red-light);
            border-radius: 50%;
            opacity: 0.4;
            animation: float 15s infinite ease-in-out;
        }

        @keyframes float {
            0% {
                transform: translateY(0) translateX(0) rotate(0deg);
                opacity: 0;
            }
            20% {
                opacity: 0.4;
            }
            80% {
                opacity: 0.4;
            }
            100% {
                transform: translateY(-100vh) translateX(20px) rotate(360deg);
                opacity: 0;
            }
        }

        .login-container {
            background: rgba(255, 255, 255, 0.1);
            padding: 40px;
            border-radius: 20px;
            box-shadow: var(--box-shadow);
            width: 100%;
            max-width: 450px;
            margin: 20px;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            transform: translateY(20px);
            opacity: 0;
            animation: fadeUp 0.8s forwards 0.3s;
            overflow: hidden;
            position: relative;
        }

        /* Effet de goutte d'eau/sang */
        .login-container::before {
            content: "";
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle at center, rgba(230, 57, 70, 0.1) 0%, transparent 60%);
            transform: scale(0);
            transition: transform 1.5s;
            z-index: -1;
        }

        .login-container:hover::before {
            transform: scale(1);
        }

        @keyframes fadeUp {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .login-header {
            text-align: center;
            margin-bottom: 40px;
            position: relative;
        }

        .login-logo {
            margin-bottom: 15px;
        }

        .blood-drop {
            font-size: 50px;
            color: var(--blood-red);
            animation: pulse 2s infinite;
            margin: 0 auto;
            display: block;
            position: relative;
            width: 80px;
            height: 80px;
        }

        .blood-drop svg {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            fill: var(--blood-red);
        }

        .blood-drop-shadow {
            position: absolute;
            bottom: -5px;
            left: 50%;
            transform: translateX(-50%);
            width: 40px;
            height: 10px;
            background: rgba(0, 0, 0, 0.1);
            border-radius: 50%;
            animation: shadowPulse 2s infinite alternate;
        }

        @keyframes pulse {
            0% {
                transform: scale(1);
            }
            50% {
                transform: scale(1.1);
            }
            100% {
                transform: scale(1);
            }
        }

        @keyframes shadowPulse {
            0% {
                transform: translateX(-50%) scale(1);
                opacity: 0.6;
            }
            100% {
                transform: translateX(-50%) scale(1.2);
                opacity: 0.4;
            }
        }

        .login-header h2 {
            font-family: 'Montserrat', sans-serif;
            font-weight: 700;
            font-size: 28px;
            color: var(--pure-white);
            margin-top: 15px;
            letter-spacing: 0.5px;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        }

        .form-group {
            margin-bottom: 25px;
            position: relative;
            opacity: 0;
            transform: translateX(-20px);
        }

        .form-group:nth-child(1) {
            animation: slideIn 0.5s forwards 0.6s;
        }

        .form-group:nth-child(2) {
            animation: slideIn 0.5s forwards 0.8s;
        }

        @keyframes slideIn {
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .form-label {
            color: var(--off-white);
            font-weight: 500;
            display: block;
            margin-bottom: 8px;
            font-size: 15px;
            transition: var(--transition);
        }

        .input-with-icon {
            position: relative;
        }

        .input-with-icon .form-control {
            padding-left: 45px;
            height: 50px;
        }

        .input-icon {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--blood-red);
            font-size: 18px;
            transition: var(--transition);
        }

        .form-control {
            background: rgba(255, 255, 255, 0.1);
            border: 2px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            color: var(--pure-white);
            font-size: 16px;
            padding: 12px 20px;
            transition: var(--transition);
            font-family: 'Poppins', sans-serif;
        }

        .form-control:focus {
            background: rgba(255, 255, 255, 0.15);
            border-color: var(--blood-red);
            box-shadow: 0 0 0 4px rgba(230, 57, 70, 0.15);
            color: var(--pure-white);
        }

        .form-control:focus + .input-icon {
            color: var(--blood-red-light);
            transform: translateY(-50%) scale(1.1);
        }

        .password-toggle {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--off-white);
            font-size: 16px;
            cursor: pointer;
            transition: var(--transition);
            opacity: 0.7;
        }

        .password-toggle:hover {
            opacity: 1;
        }

        .btn {
            border-radius: 12px;
            font-family: 'Montserrat', sans-serif;
            font-weight: 600;
            text-transform: uppercase;
            transition: var(--transition);
            letter-spacing: 0.5px;
            position: relative;
            overflow: hidden;
            padding: 12px 28px;
            font-size: 16px;
            z-index: 1;
        }

        .btn-login {
            background: var(--blood-red);
            border: none;
            color: var(--pure-white);
            box-shadow: 0 4px 15px rgba(230, 57, 70, 0.4);
            opacity: 0;
            transform: translateY(20px);
            animation: fadeUp 0.5s forwards 1s;
            height: 50px;
        }

        .btn-login::before {
            content: "";
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: 0.5s;
            z-index: -1;
        }

        .btn-login:hover {
            background: var(--blood-red-dark);
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(230, 57, 70, 0.5);
        }

        .btn-login:hover::before {
            left: 100%;
            transition: 0.5s;
        }

        .btn-login:active {
            transform: translateY(-1px);
        }

        /* Loading animation */
        .btn-loading {
            display: none;
            align-items: center;
            justify-content: center;
        }

        .loading-spinner {
            width: 20px;
            height: 20px;
            border: 3px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            border-top-color: var(--pure-white);
            animation: spin 1s ease-in-out infinite;
            margin-right: 10px;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        .alert {
            border-radius: 12px;
            margin-bottom: 25px;
            padding: 15px;
            display: flex;
            align-items: center;
            border: none;
            animation: alertSlideDown 0.5s forwards;
        }

        @keyframes alertSlideDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .alert-danger {
            background: rgba(230, 57, 70, 0.15);
            border-left: 4px solid var(--blood-red);
            color: var(--off-white);
        }

        .alert-success {
            background: rgba(168, 218, 220, 0.15);
            border-left: 4px solid var(--light-blue);
            color: var(--off-white);
        }

        .alert i {
            margin-right: 10px;
            font-size: 20px;
        }

        /* Animation de pulsation cardiaque pour le logo */
        .heartbeat {
            animation: heartbeat 1.5s ease-in-out infinite;
        }

        @keyframes heartbeat {
            0% { transform: scale(1); }
            14% { transform: scale(1.1); }
            28% { transform: scale(1); }
            42% { transform: scale(1.15); }
            70% { transform: scale(1); }
        }

        /* Footer text */
        .login-footer {
            text-align: center;
            margin-top: 30px;
            font-size: 14px;
            color: rgba(255, 255, 255, 0.6);
            opacity: 0;
            animation: fadeIn 1s forwards 1.2s;
        }

        @keyframes fadeIn {
            to {
                opacity: 1;
            }
        }

        /* Responsive design */
        @media (max-width: 576px) {
            .login-container {
                padding: 30px 20px;
                margin: 15px;
            }

            .login-header h2 {
                font-size: 24px;
            }

            .blood-drop {
                font-size: 40px;
            }
        }
    </style>
</head>
<body>
    <!-- Particules de cellules sanguines -->
    <div class="blood-particles" id="bloodParticles"></div>

    <div class="login-container">
        <div class="login-header">
            <div class="login-logo">
                <div class="blood-drop">
                    <svg viewBox="0 0 384 512" class="heartbeat">
                        <path d="M272 464h-160C76.8 464 48 435.2 48 400v-224.6c0-12 4-23.7 11.4-33.2L194.1 8.9c10.5-13.5 33.3-13.5 43.8 0l134.7 133.3c7.4 9.5 11.4 21.2 11.4 33.2V400c0 35.2-28.8 64-64 64zm-5.3-118.3l-58.9-66.2c-3.2-3.6-7.9-5.5-12.8-5.5s-9.6 2-12.8 5.5l-58.9 66.2c-10.9 12.2-1.5 30.3 16.8 30.3h110c18.3 0 27.6-18.2 16.6-30.3z"></path>
                    </svg>
                    <div class="blood-drop-shadow"></div>
                </div>
            </div>
            <h2>Don de Sang - Connexion</h2>
        </div>
        
        <?php if (isset($_GET['error'])): ?>
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle"></i>
                <?php echo $_GET['error']; ?>
            </div>
        <?php endif; ?>
        
        <?php if (isset($_GET['message'])): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i>
                <?php echo $_GET['message']; ?>
            </div>
        <?php endif; ?>

        <form id="loginForm" action="index.php?action=connecter" method="POST">
            <div class="form-group">
                <label for="email" class="form-label">Adresse Email</label>
                <div class="input-with-icon">
                    <input type="email" class="form-control" id="email" name="email" required autocomplete="email">
                    <span class="input-icon">
                        <i class="fas fa-envelope"></i>
                    </span>
                </div>
            </div>
            
            <div class="form-group">
                <label for="mot_de_passe" class="form-label">Mot de Passe</label>
                <div class="input-with-icon">
                    <input type="password" class="form-control" id="mot_de_passe" name="mot_de_passe" required autocomplete="current-password">
                    <span class="input-icon">
                        <i class="fas fa-lock"></i>
                    </span>
                    <span class="password-toggle" id="passwordToggle">
                        <i class="fas fa-eye"></i>
                    </span>
                </div>
            </div>
            
            <button type="submit" class="btn btn-login w-100" id="loginButton">
                <span class="btn-text">Se connecter</span>
                <span class="btn-loading">
                    <span class="loading-spinner"></span>
                    Connexion...
                </span>
            </button>
        </form>

        <div class="login-footer">
            <p>Merci de votre engagement pour le don de sang</p>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script>
        // Création des particules représentant des cellules sanguines
        document.addEventListener('DOMContentLoaded', function() {
            // Création des particules de sang
            createBloodParticles();
            
            // Toggle de visibilité du mot de passe
            const passwordToggle = document.getElementById('passwordToggle');
            const passwordInput = document.getElementById('mot_de_passe');
            
            passwordToggle.addEventListener('click', function() {
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);
                
                // Changer l'icône
                const icon = passwordToggle.querySelector('i');
                if (type === 'password') {
                    icon.classList.remove('fa-eye-slash');
                    icon.classList.add('fa-eye');
                } else {
                    icon.classList.remove('fa-eye');
                    icon.classList.add('fa-eye-slash');
                }
            });
            
            // Animation de chargement lors de la soumission
            const loginForm = document.getElementById('loginForm');
            const loginButton = document.getElementById('loginButton');
            
            loginForm.addEventListener('submit', function(e) {
                // Afficher l'animation de chargement
                const btnText = loginButton.querySelector('.btn-text');
                const btnLoading = loginButton.querySelector('.btn-loading');
                
                btnText.style.display = 'none';
                btnLoading.style.display = 'flex';
                
                // Simulating a slight delay for the animation 
                // (you can remove this in production if you want)
                setTimeout(() => {
                    // Le formulaire va se soumettre normalement
                }, 800);
            });
            
            // Auto-fermeture des alertes après 5 secondes
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                setTimeout(() => {
                    alert.style.opacity = '0';
                    alert.style.height = alert.offsetHeight + 'px';
                    alert.style.marginBottom = '25px';
                    alert.style.transition = 'all 0.5s ease';
                    
                    setTimeout(() => {
                        alert.style.height = '0';
                        alert.style.marginBottom = '0';
                        alert.style.padding = '0';
                        
                        setTimeout(() => {
                            alert.style.display = 'none';
                        }, 500);
                    }, 500);
                }, 5000);
            });
        });
        
        // Fonction pour créer les particules de sang
        function createBloodParticles() {
            const container = document.getElementById('bloodParticles');
            const numParticles = 50; // Nombre de particules
            
            for (let i = 0; i < numParticles; i++) {
                const particle = document.createElement('div');
                particle.className = 'particle';
                
                // Taille aléatoire
                const size = Math.random() * 14 + 4;
                particle.style.width = `${size}px`;
                particle.style.height = `${size}px`;
                
                // Position aléatoire
                const posX = Math.random() * 100;
                const posY = Math.random() * 100 + 100; // Commencer en-dessous de l'écran
                particle.style.left = `${posX}%`;
                particle.style.top = `${posY}%`;
                
                // Vitesse et délai aléatoires
                const duration = Math.random() * 15 + 10;
                const delay = Math.random() * 10;
                particle.style.animation = `float ${duration}s ${delay}s infinite`;
                
                container.appendChild(particle);
            }
        }
        
        // Animations sur les champs de formulaire
        const inputs = document.querySelectorAll('.form-control');
        inputs.forEach(input => {
            input.addEventListener('focus', () => {
                input.parentElement.classList.add('focused');
            });
            
            input.addEventListener('blur', () => {
                input.parentElement.classList.remove('focused');
            });
        });
    </script>
</body>
</html>