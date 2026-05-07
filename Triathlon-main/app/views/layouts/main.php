<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FFTRI - Gestion des Triathlons</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --fftri-blue: #0033A0;
            --fftri-blue-dark: #00257a;
            --fftri-blue-light: #1a4db5;
            --fftri-red: #D50032;
            --fftri-red-dark: #b3002b;
            --white: #FFFFFF;
            --light-gray: #F4F6FA;
            --medium-gray: #E2E8F0;
            --dark-gray: #64748B;
            --text-color: #1E293B;
            --text-muted: #64748B;
            --success: #10B981;
            --warning: #F59E0B;
            --info: #3B82F6;
            --sidebar-width: 260px;
            --header-height: 64px;
            --shadow-sm: 0 1px 3px rgba(0,0,0,0.08);
            --shadow-md: 0 4px 12px rgba(0,0,0,0.1);
            --shadow-lg: 0 8px 24px rgba(0,0,0,0.12);
            --radius-sm: 6px;
            --radius-md: 10px;
            --radius-lg: 16px;
            --transition: all 0.2s ease;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Inter', sans-serif;
            color: var(--text-color);
            background-color: var(--light-gray);
            line-height: 1.6;
        }

        h1, h2, h3, h4, h5, h6 {
            font-family: 'Montserrat', sans-serif;
            font-weight: 700;
        }

        /* ========================
           LAYOUT
           ======================== */
        .layout {
            display: flex;
            min-height: 100vh;
        }

        /* ========================
           SIDEBAR
           ======================== */
        .sidebar {
            width: var(--sidebar-width);
            background: linear-gradient(165deg, #0033A0 0%, #001f6b 60%, #001040 100%);
            color: var(--white);
            display: flex;
            flex-direction: column;
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            z-index: 100;
            box-shadow: 4px 0 20px rgba(0, 51, 160, 0.3);
            overflow-y: auto;
        }

        .sidebar-logo {
            padding: 24px 20px;
            border-bottom: 1px solid rgba(255,255,255,0.12);
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
            color: white;
        }

        .sidebar-logo-icon {
            width: 44px;
            height: 44px;
            background: rgba(255,255,255,0.15);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.3rem;
            flex-shrink: 0;
            border: 1px solid rgba(255,255,255,0.2);
        }

        .sidebar-logo-text h2 {
            font-size: 0.95rem;
            font-weight: 700;
            line-height: 1.2;
            letter-spacing: 0.02em;
        }

        .sidebar-logo-text p {
            font-size: 0.7rem;
            opacity: 0.65;
            font-family: 'Inter', sans-serif;
            font-weight: 400;
        }

        .sidebar-section {
            padding: 20px 12px 8px;
        }

        .sidebar-section-label {
            font-size: 0.65rem;
            font-weight: 700;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            color: rgba(255,255,255,0.4);
            padding: 0 10px;
            margin-bottom: 6px;
        }

        .nav-links {
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: 2px;
        }

        .nav-links li a {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 11px 14px;
            border-radius: var(--radius-sm);
            color: rgba(255,255,255,0.75);
            text-decoration: none;
            font-size: 0.875rem;
            font-weight: 500;
            transition: var(--transition);
            position: relative;
        }

        .nav-links li a:hover {
            background: rgba(255,255,255,0.1);
            color: var(--white);
        }

        .nav-links li.active a {
            background: rgba(255,255,255,0.18);
            color: var(--white);
            font-weight: 600;
        }

        .nav-links li.active a::before {
            content: '';
            position: absolute;
            left: 0;
            top: 50%;
            transform: translateY(-50%);
            width: 3px;
            height: 24px;
            background: var(--fftri-red);
            border-radius: 0 3px 3px 0;
        }

        .nav-links li a .nav-icon {
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            background: rgba(255,255,255,0.08);
            font-size: 0.85rem;
            flex-shrink: 0;
            transition: var(--transition);
        }

        .nav-links li.active a .nav-icon,
        .nav-links li a:hover .nav-icon {
            background: rgba(255,255,255,0.2);
        }

        .sidebar-footer {
            margin-top: auto;
            padding: 16px 12px;
            border-top: 1px solid rgba(255,255,255,0.1);
        }

        .sidebar-footer a {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 14px;
            border-radius: var(--radius-sm);
            color: rgba(255,255,255,0.6);
            text-decoration: none;
            font-size: 0.85rem;
            font-weight: 500;
            transition: var(--transition);
        }

        .sidebar-footer a:hover {
            background: rgba(213,0,50,0.2);
            color: #ff6b8a;
        }

        /* ========================
           MAIN CONTENT
           ======================== */
        .main-content {
            flex: 1;
            margin-left: var(--sidebar-width);
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        /* ========================
           HEADER
           ======================== */
        .header {
            background: var(--white);
            height: var(--header-height);
            padding: 0 28px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: var(--shadow-sm);
            position: sticky;
            top: 0;
            z-index: 90;
            border-bottom: 1px solid var(--medium-gray);
        }

        .header-left h3 {
            font-size: 1rem;
            font-weight: 600;
            color: var(--text-color);
        }

        .header-left p {
            font-size: 0.75rem;
            color: var(--text-muted);
            font-family: 'Inter', sans-serif;
        }

        .header-right {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .user-badge {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 6px 14px 6px 6px;
            background: var(--light-gray);
            border-radius: 50px;
            border: 1px solid var(--medium-gray);
        }

        .user-avatar {
            width: 34px;
            height: 34px;
            background: linear-gradient(135deg, var(--fftri-blue), var(--fftri-blue-light));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 0.8rem;
            font-weight: 700;
        }

        .user-badge-text {
            line-height: 1.2;
        }

        .user-badge-name {
            font-size: 0.82rem;
            font-weight: 600;
            color: var(--text-color);
        }

        .user-badge-role {
            font-size: 0.7rem;
            color: var(--text-muted);
        }

        /* ========================
           PAGE CONTENT
           ======================== */
        .content {
            padding: 28px;
            flex: 1;
        }

        /* ========================
           PAGE TITLE
           ======================== */
        .page-title {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
        }

        .page-title h1 {
            font-size: 1.6rem;
            color: var(--text-color);
            font-weight: 700;
        }

        .page-title-sub {
            font-size: 0.82rem;
            color: var(--text-muted);
            font-family: 'Inter', sans-serif;
            font-weight: 400;
            margin-top: 2px;
        }

        /* ========================
           BUTTONS
           ======================== */
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 9px 18px;
            background: var(--fftri-blue);
            color: var(--white);
            border: none;
            border-radius: var(--radius-sm);
            cursor: pointer;
            font-size: 0.875rem;
            font-weight: 600;
            font-family: 'Inter', sans-serif;
            text-decoration: none;
            transition: var(--transition);
            white-space: nowrap;
            box-shadow: 0 2px 6px rgba(0,51,160,0.25);
        }

        .btn:hover {
            background: var(--fftri-blue-dark);
            box-shadow: 0 4px 12px rgba(0,51,160,0.35);
            transform: translateY(-1px);
        }

        .btn:active { transform: translateY(0); }

        .btn-danger {
            background: var(--fftri-red);
            box-shadow: 0 2px 6px rgba(213,0,50,0.25);
        }

        .btn-danger:hover {
            background: var(--fftri-red-dark);
            box-shadow: 0 4px 12px rgba(213,0,50,0.35);
        }

        .btn-secondary {
            background: var(--white);
            color: var(--text-color);
            border: 1px solid var(--medium-gray);
            box-shadow: var(--shadow-sm);
        }

        .btn-secondary:hover {
            background: var(--light-gray);
            box-shadow: var(--shadow-md);
        }

        .btn-success {
            background: var(--success);
            box-shadow: 0 2px 6px rgba(16,185,129,0.25);
        }

        .btn-success:hover { background: #059669; }

        /* ========================
           STATS CARDS
           ======================== */
        .stats-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 20px;
            margin-bottom: 28px;
        }

        .stat-card {
            background: var(--white);
            border-radius: var(--radius-md);
            padding: 22px;
            box-shadow: var(--shadow-sm);
            border: 1px solid var(--medium-gray);
            display: flex;
            align-items: center;
            gap: 18px;
            transition: var(--transition);
        }

        .stat-card:hover {
            box-shadow: var(--shadow-md);
            transform: translateY(-2px);
        }

        .stat-icon {
            width: 54px;
            height: 54px;
            border-radius: var(--radius-md);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.4rem;
            flex-shrink: 0;
        }

        .stat-icon.blue  { background: rgba(0,51,160,0.1); color: var(--fftri-blue); }
        .stat-icon.red   { background: rgba(213,0,50,0.1); color: var(--fftri-red); }
        .stat-icon.green { background: rgba(16,185,129,0.1); color: var(--success); }
        .stat-icon.amber { background: rgba(245,158,11,0.1); color: var(--warning); }

        .stat-content { flex: 1; }

        .stat-card h3 {
            font-size: 1.8rem;
            color: var(--text-color);
            line-height: 1;
            margin-bottom: 4px;
        }

        .stat-card p {
            font-size: 0.8rem;
            color: var(--text-muted);
            font-family: 'Inter', sans-serif;
        }

        /* ========================
           TABLE
           ======================== */
        .table-container {
            background: var(--white);
            border-radius: var(--radius-md);
            box-shadow: var(--shadow-sm);
            border: 1px solid var(--medium-gray);
            overflow: hidden;
        }

        .table-header {
            padding: 18px 22px;
            border-bottom: 1px solid var(--medium-gray);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .table-header h3 {
            font-size: 1rem;
            font-weight: 700;
            color: var(--text-color);
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        thead th {
            background: var(--light-gray);
            color: var(--text-muted);
            font-size: 0.72rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            padding: 12px 18px;
            text-align: left;
            border-bottom: 1px solid var(--medium-gray);
            font-family: 'Inter', sans-serif;
        }

        tbody td {
            padding: 13px 18px;
            font-size: 0.875rem;
            border-bottom: 1px solid var(--medium-gray);
            color: var(--text-color);
            vertical-align: middle;
        }

        tbody tr:last-child td { border-bottom: none; }

        tbody tr:hover { background: rgba(0,51,160,0.02); }

        /* ========================
           ACTIONS
           ======================== */
        .actions {
            display: flex;
            gap: 6px;
        }

        .action-btn {
            width: 32px;
            height: 32px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: var(--radius-sm);
            text-decoration: none;
            font-size: 0.8rem;
            transition: var(--transition);
            border: none;
            background: none;
            cursor: pointer;
        }

        .action-btn.edit {
            background: rgba(0,51,160,0.08);
            color: var(--fftri-blue);
        }

        .action-btn.edit:hover {
            background: rgba(0,51,160,0.18);
        }

        .action-btn.delete {
            background: rgba(213,0,50,0.08);
            color: var(--fftri-red);
        }

        .action-btn.delete:hover {
            background: rgba(213,0,50,0.18);
        }

        /* ========================
           BADGES
           ======================== */
        .badge {
            display: inline-flex;
            align-items: center;
            padding: 3px 10px;
            border-radius: 50px;
            font-size: 0.72rem;
            font-weight: 600;
            font-family: 'Inter', sans-serif;
        }

        .badge-blue   { background: rgba(0,51,160,0.1); color: var(--fftri-blue); }
        .badge-red    { background: rgba(213,0,50,0.1); color: var(--fftri-red); }
        .badge-green  { background: rgba(16,185,129,0.1); color: #059669; }
        .badge-amber  { background: rgba(245,158,11,0.1); color: #B45309; }
        .badge-gray   { background: var(--medium-gray); color: var(--dark-gray); }

        /* ========================
           ALERTS
           ======================== */
        .alert {
            padding: 12px 16px;
            border-radius: var(--radius-sm);
            margin-bottom: 20px;
            font-size: 0.875rem;
            display: flex;
            align-items: flex-start;
            gap: 10px;
        }

        .alert-error   { background: rgba(213,0,50,0.08); color: #9B1C1C; border: 1px solid rgba(213,0,50,0.2); }
        .alert-success { background: rgba(16,185,129,0.08); color: #065F46; border: 1px solid rgba(16,185,129,0.2); }
        .alert-info    { background: rgba(59,130,246,0.08); color: #1E40AF; border: 1px solid rgba(59,130,246,0.2); }

        /* ========================
           FILTERS
           ======================== */
        .filters {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            align-items: flex-end;
            background: var(--white);
            padding: 16px 20px;
            border-radius: var(--radius-md);
            border: 1px solid var(--medium-gray);
            margin-bottom: 20px;
            box-shadow: var(--shadow-sm);
        }

        .filter-group {
            display: flex;
            flex-direction: column;
            gap: 5px;
            min-width: 160px;
            flex: 1;
        }

        .filter-group label {
            font-size: 0.72rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: var(--text-muted);
            font-family: 'Inter', sans-serif;
        }

        .filter-group .btn {
            align-self: flex-end;
            padding: 9px 20px;
        }

        /* ========================
           FORM ELEMENTS
           ======================== */
        .form-control {
            width: 100%;
            padding: 10px 14px;
            border: 1.5px solid var(--medium-gray);
            border-radius: var(--radius-sm);
            font-size: 0.875rem;
            font-family: 'Inter', sans-serif;
            color: var(--text-color);
            background: var(--white);
            transition: var(--transition);
        }

        .form-control:focus {
            border-color: var(--fftri-blue);
            outline: none;
            box-shadow: 0 0 0 3px rgba(0,51,160,0.1);
        }

        .form-control[readonly] {
            background: var(--light-gray);
            color: var(--text-muted);
        }

        .form-container {
            background: var(--white);
            border-radius: var(--radius-md);
            padding: 28px;
            box-shadow: var(--shadow-sm);
            border: 1px solid var(--medium-gray);
            max-width: 900px;
            margin: 0 auto;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            gap: 20px;
            margin-bottom: 24px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .form-group label {
            font-size: 0.8rem;
            font-weight: 600;
            color: var(--text-color);
            font-family: 'Inter', sans-serif;
        }

        .form-group.full-width {
            grid-column: 1 / -1;
        }

        .form-actions {
            display: flex;
            gap: 12px;
            padding-top: 8px;
            border-top: 1px solid var(--medium-gray);
        }

        /* ========================
           FLASH MESSAGES (TOAST)
           ======================== */
        .toast-container {
            position: fixed;
            top: 80px;
            right: 20px;
            z-index: 999;
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .toast {
            background: var(--white);
            border-radius: var(--radius-md);
            padding: 14px 18px;
            box-shadow: var(--shadow-lg);
            border-left: 4px solid var(--success);
            display: flex;
            align-items: center;
            gap: 12px;
            min-width: 280px;
            max-width: 380px;
            animation: slideIn 0.3s ease, fadeOut 0.5s ease 3.5s forwards;
            font-size: 0.875rem;
            font-weight: 500;
        }

        .toast.error { border-color: var(--fftri-red); }

        @keyframes slideIn {
            from { transform: translateX(100%); opacity: 0; }
            to   { transform: translateX(0); opacity: 1; }
        }

        @keyframes fadeOut {
            from { opacity: 1; transform: translateX(0); }
            to   { opacity: 0; transform: translateX(100%); }
        }

        /* ========================
           RESPONSIVE
           ======================== */
        @media (max-width: 1024px) {
            .sidebar { width: 220px; }
            .main-content { margin-left: 220px; }
        }

        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
                transition: transform 0.3s ease;
            }
            .sidebar.open { transform: translateX(0); }
            .main-content { margin-left: 0; }
            .content { padding: 16px; }
            .stats-container { grid-template-columns: 1fr 1fr; }
            .filters { flex-direction: column; }
            .filter-group { min-width: 100%; }
            .page-title { flex-direction: column; align-items: flex-start; gap: 12px; }
        }

        @media (max-width: 480px) {
            .stats-container { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
<?php
$userName = $_SESSION['user']['name'] ?? $_SESSION['user']['nom'] ?? 'Utilisateur';
$userRole = ($_SESSION['user']['role'] ?? '') === 'admin' ? 'Administrateur' : 'Responsable Club';
$userInitial = strtoupper(substr($userName, 0, 1));
?>

<!-- Toast flash messages -->
<?php if (isset($_SESSION['flash'])): ?>
<div class="toast-container">
    <div class="toast">
        <i class="fas fa-check-circle" style="color: var(--success);"></i>
        <span><?= htmlspecialchars($_SESSION['flash']) ?></span>
    </div>
</div>
<?php unset($_SESSION['flash']); ?>
<?php endif; ?>

<?php if (isset($_SESSION['flash_error'])): ?>
<div class="toast-container">
    <div class="toast error">
        <i class="fas fa-exclamation-circle" style="color: var(--fftri-red);"></i>
        <span><?= htmlspecialchars($_SESSION['flash_error']) ?></span>
    </div>
</div>
<?php unset($_SESSION['flash_error']); ?>
<?php endif; ?>

<div class="layout">
    <!-- Sidebar -->
    <aside class="sidebar" id="sidebar">
        <a href="index.php?module=dashboard" class="sidebar-logo">
            <div class="sidebar-logo-icon">
                <i class="fas fa-swimmer"></i>
            </div>
            <div class="sidebar-logo-text">
                <h2>FFTRI</h2>
                <p>Gestion Triathlons</p>
            </div>
        </a>

        <div class="sidebar-section">
            <div class="sidebar-section-label">Navigation</div>
            <ul class="nav-links">
                <li class="<?= $currentPage === 'dashboard' ? 'active' : '' ?>">
                    <a href="index.php?module=dashboard">
                        <span class="nav-icon"><i class="fas fa-home"></i></span>
                        Tableau de bord
                    </a>
                </li>
                <li class="<?= $currentPage === 'clubs' ? 'active' : '' ?>">
                    <a href="index.php?module=clubs">
                        <span class="nav-icon"><i class="fas fa-shield-halved"></i></span>
                        Clubs
                    </a>
                </li>
                <li class="<?= $currentPage === 'licencies' ? 'active' : '' ?>">
                    <a href="index.php?module=licencies">
                        <span class="nav-icon"><i class="fas fa-id-card"></i></span>
                        Licenciés
                    </a>
                </li>
                <li class="<?= $currentPage === 'triathlons' ? 'active' : '' ?>">
                    <a href="index.php?module=triathlons">
                        <span class="nav-icon"><i class="fas fa-trophy"></i></span>
                        Triathlons
                    </a>
                </li>
            </ul>
        </div>

        <div class="sidebar-section">
            <div class="sidebar-section-label">Compte</div>
            <ul class="nav-links">
                <li class="<?= $currentPage === 'settings' ? 'active' : '' ?>">
                    <a href="index.php?module=settings">
                        <span class="nav-icon"><i class="fas fa-gear"></i></span>
                        Paramètres
                    </a>
                </li>
            </ul>
        </div>

        <div class="sidebar-footer">
            <a href="index.php?action=logout" onclick="return confirm('Se déconnecter ?')">
                <i class="fas fa-arrow-right-from-bracket"></i>
                Déconnexion
            </a>
        </div>
    </aside>

    <!-- Main Content -->
    <div class="main-content">
        <header class="header">
            <div class="header-left">
                <h3>Bonjour, <?= htmlspecialchars($userName) ?> 👋</h3>
                <p><?= date('l d F Y') ?></p>
            </div>
            <div class="header-right">
                <a href="index.php?module=settings" class="user-badge" style="text-decoration:none;">
                    <div class="user-avatar"><?= $userInitial ?></div>
                    <div class="user-badge-text">
                        <div class="user-badge-name"><?= htmlspecialchars($userName) ?></div>
                        <div class="user-badge-role"><?= $userRole ?></div>
                    </div>
                </a>
            </div>
        </header>

        <main class="content">
            <?php include $content; ?>
        </main>
    </div>
</div>

<script>
// Auto-hide toast after animation
setTimeout(() => {
    document.querySelectorAll('.toast').forEach(t => t.remove());
}, 4200);
</script>
</body>
</html>
