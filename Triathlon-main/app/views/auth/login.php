<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FFTRI - Connexion</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@600;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            display: flex;
            background: #f0f4ff;
        }

        /* Left panel */
        .login-panel-left {
            flex: 1;
            background: linear-gradient(155deg, #0033A0 0%, #001f6b 55%, #D50032 100%);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 60px 40px;
            position: relative;
            overflow: hidden;
        }

        .login-panel-left::before {
            content: '';
            position: absolute;
            top: -80px; right: -80px;
            width: 320px; height: 320px;
            background: rgba(255,255,255,0.05);
            border-radius: 50%;
        }

        .login-panel-left::after {
            content: '';
            position: absolute;
            bottom: -60px; left: -60px;
            width: 240px; height: 240px;
            background: rgba(255,255,255,0.05);
            border-radius: 50%;
        }

        .left-content { z-index: 1; text-align: center; color: white; max-width: 380px; }

        .brand-logo {
            width: 80px; height: 80px;
            background: rgba(255,255,255,0.15);
            border-radius: 20px;
            display: flex; align-items: center; justify-content: center;
            font-size: 2.2rem;
            margin: 0 auto 24px;
            border: 2px solid rgba(255,255,255,0.25);
        }

        .left-content h1 {
            font-family: 'Montserrat', sans-serif;
            font-size: 2rem;
            font-weight: 800;
            margin-bottom: 12px;
            letter-spacing: -0.02em;
        }

        .left-content p {
            font-size: 0.95rem;
            opacity: 0.75;
            line-height: 1.7;
        }

        .features {
            margin-top: 40px;
            display: flex;
            flex-direction: column;
            gap: 14px;
            text-align: left;
        }

        .feature-item {
            display: flex;
            align-items: center;
            gap: 14px;
            font-size: 0.875rem;
            opacity: 0.85;
        }

        .feature-icon {
            width: 36px; height: 36px;
            background: rgba(255,255,255,0.12);
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
        }

        /* Right panel */
        .login-panel-right {
            width: 480px;
            background: white;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 48px 44px;
        }

        .login-form-wrapper { width: 100%; max-width: 360px; }

        .login-form-header { margin-bottom: 32px; }

        .login-form-header h2 {
            font-family: 'Montserrat', sans-serif;
            font-size: 1.6rem;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 6px;
        }

        .login-form-header p {
            font-size: 0.875rem;
            color: #64748b;
        }

        .form-group {
            margin-bottom: 18px;
        }

        .form-group label {
            display: block;
            font-size: 0.8rem;
            font-weight: 600;
            color: #374151;
            margin-bottom: 6px;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }

        .input-wrapper {
            position: relative;
        }

        .input-icon {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: #9ca3af;
            font-size: 0.85rem;
        }

        .form-control {
            width: 100%;
            padding: 11px 14px 11px 38px;
            border: 1.5px solid #e2e8f0;
            border-radius: 8px;
            font-size: 0.9rem;
            font-family: 'Inter', sans-serif;
            color: #1e293b;
            transition: all 0.2s;
            background: #fafafa;
        }

        .form-control:focus {
            border-color: #0033A0;
            outline: none;
            box-shadow: 0 0 0 3px rgba(0,51,160,0.1);
            background: white;
        }

        .btn-login {
            display: block;
            width: 100%;
            padding: 13px;
            background: linear-gradient(135deg, #0033A0, #001f6b);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 0.95rem;
            font-weight: 700;
            font-family: 'Inter', sans-serif;
            cursor: pointer;
            transition: all 0.2s;
            box-shadow: 0 4px 14px rgba(0,51,160,0.3);
            margin-top: 8px;
        }

        .btn-login:hover {
            box-shadow: 0 6px 20px rgba(0,51,160,0.4);
            transform: translateY(-1px);
        }

        .btn-login:active { transform: translateY(0); }

        .alert-error {
            background: rgba(213,0,50,0.08);
            color: #9B1C1C;
            border: 1px solid rgba(213,0,50,0.2);
            border-radius: 8px;
            padding: 11px 14px;
            font-size: 0.85rem;
            margin-bottom: 18px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .demo-box {
            margin-top: 28px;
            padding: 14px 16px;
            background: #f0f4ff;
            border-radius: 10px;
            border: 1px solid #c7d2fe;
        }

        .demo-box p {
            font-size: 0.78rem;
            color: #3730a3;
            font-weight: 600;
            margin-bottom: 6px;
        }

        .demo-box .demo-cred {
            font-size: 0.8rem;
            color: #4338ca;
            line-height: 1.8;
        }

        .demo-box .demo-cred strong { color: #1e1b4b; }

        @media (max-width: 900px) {
            .login-panel-left { display: none; }
            .login-panel-right { width: 100%; }
        }
    </style>
</head>
<body>
    <div class="login-panel-left">
        <div class="left-content">
            <div class="brand-logo"><i class="fas fa-swimmer"></i></div>
            <h1>FFTRI</h1>
            <p>Plateforme de gestion des clubs, licenciés et épreuves de triathlon en France.</p>

            <div class="features">
                <div class="feature-item">
                    <div class="feature-icon"><i class="fas fa-shield-halved"></i></div>
                    <span>Gestion complète des clubs affiliés</span>
                </div>
                <div class="feature-item">
                    <div class="feature-icon"><i class="fas fa-id-card"></i></div>
                    <span>Suivi des licenciés et catégories</span>
                </div>
                <div class="feature-item">
                    <div class="feature-icon"><i class="fas fa-trophy"></i></div>
                    <span>Organisation des épreuves & inscriptions</span>
                </div>
                <div class="feature-item">
                    <div class="feature-icon"><i class="fas fa-chart-line"></i></div>
                    <span>Tableaux de bord et statistiques</span>
                </div>
            </div>
        </div>
    </div>

    <div class="login-panel-right">
        <div class="login-form-wrapper">
            <div class="login-form-header">
                <h2>Connexion</h2>
                <p>Accédez à votre espace de gestion FFTRI</p>
            </div>

            <?php if (isset($error)): ?>
                <div class="alert-error">
                    <i class="fas fa-exclamation-circle"></i>
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="index.php?action=authenticate">
                <div class="form-group">
                    <label>Adresse email</label>
                    <div class="input-wrapper">
                        <i class="fas fa-envelope input-icon"></i>
                        <input type="email" name="email" class="form-control"
                               placeholder="votre@email.com" required
                               value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                    </div>
                </div>

                <div class="form-group">
                    <label>Mot de passe</label>
                    <div class="input-wrapper">
                        <i class="fas fa-lock input-icon"></i>
                        <input type="password" name="password" class="form-control"
                               placeholder="••••••••" required>
                    </div>
                </div>

                <button type="submit" class="btn-login">
                    <i class="fas fa-arrow-right-to-bracket"></i>&nbsp; Se connecter
                </button>
            </form>

            <div class="demo-box">
                <p><i class="fas fa-info-circle"></i> Comptes de démonstration</p>
                <div class="demo-cred">
                    <strong>Admin :</strong> admin@fftri.com / password<br>
                    <strong>Responsable :</strong> resp.orleans@fftri.com / password
                </div>
            </div>
        </div>
    </div>
</body>
</html>
