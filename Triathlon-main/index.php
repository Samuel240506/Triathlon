<?php
session_start();

// Configuration de base
define('ROOT_PATH', __DIR__);

// Include translation helper
require_once 'app/helpers/translation.php';

// Autoloader simple
spl_autoload_register(function ($className) {
    $paths = [
        'app/controllers/',
        'app/models/',
        'app/core/'
    ];

    foreach ($paths as $path) {
        // construire chemin absolu via ROOT_PATH pour fiabilité
        $file = ROOT_PATH . DIRECTORY_SEPARATOR . $path . $className . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});

// Routage
$action = $_GET['action'] ?? 'index';
$module = $_GET['module'] ?? 'dashboard';
$id = $_GET['id'] ?? null;

// --- Ajout: normalisation & mappage pour tolérer variantes/français/singulier ---
function _normalize_key($str) {
	$s = strtolower((string)$str);
	$s = @iconv('UTF-8', 'ASCII//TRANSLIT', $s) ?: $s;
	$s = preg_replace('/[^a-z0-9]/', '', $s);
	return $s;
}

$actionMap = [
	'ajouter'=>'create','creer'=>'create','create'=>'create',
	'editer'=>'edit','modifier'=>'edit','edit'=>'edit',
	'supprimer'=>'delete','delete'=>'delete','suppr'=>'delete',
	'voir'=>'show','show'=>'show',
	'connexion'=>'login','login'=>'login','authenticate'=>'authenticate',
	'deconnexion'=>'logout','logout'=>'logout'
];

$moduleMap = [
	'licencie'=>'licencies','licencies'=>'licencies','licenci'=>'licencies',
	'licencies'=>'licencies','licenciee'=>'licencies','licenciees'=>'licencies',
	'triathlon'=>'triathlons','triathlons'=>'triathlons',
	'club'=>'clubs','clubs'=>'clubs','dashboard'=>'dashboard',
    'settings'=>'settings'
];

$normAction = _normalize_key($action);
if (isset($actionMap[$normAction])) {
	$action = $actionMap[$normAction];
}

$normModule = _normalize_key($module);
if (isset($moduleMap[$normModule])) {
	$module = $moduleMap[$normModule];
} else {
	// fallback singulier/pluriel (ex: "licencies" vs "licencie")
	if (substr($normModule, -1) === 's') {
		$alt = substr($normModule, 0, -1);
		if (isset($moduleMap[$alt])) $module = $moduleMap[$alt];
	} else {
		$alt = $normModule . 's';
		if (isset($moduleMap[$alt])) $module = $moduleMap[$alt];
	}
}
// --- Fin ajout ---


// Gestion des routes
if ($action === 'login' || $action === 'authenticate') {
    $authController = new AuthController();
    $authController->login();
} elseif ($action === 'logout') {
    $authController = new AuthController();
    $authController->logout();
} else {
    // Vérifier si l'utilisateur est connecté
    if (!isset($_SESSION['user'])) {
        header('Location: index.php?action=login');
        exit;
    }

    // Router vers les contrôleurs
    $data = [];
    $currentPage = $module;

    try {
        switch ($module) {
            case 'dashboard':
                $controller = new DashboardController();
                $data = $controller->index();
                break;

            case 'clubs':
                $controller = new ClubController();
                if ($action === 'show' && $id) {
                    $data = $controller->show($id);
                } elseif ($action === 'create') {
                    $data = $controller->create();
                } elseif ($action === 'edit' && $id) {
                    $data = $controller->edit($id);
                } elseif ($action === 'delete' && $id) {
                    $controller->delete($id);
                    exit;
                } else {
                    $data = $controller->index();
                }
                break;

            case 'licencies':
                $controller = new LicencieController();
                if ($action === 'show' && $id) {
                    $data = $controller->show($id);
                } elseif ($action === 'create') {
                    $data = $controller->create();
                } elseif ($action === 'edit' && $id) {
                    $data = $controller->edit($id);
                } elseif ($action === 'delete' && $id) {
                    $controller->delete($id);
                    exit;
                } else {
                    $data = $controller->index();
                }
                break;

            case 'triathlons':
                $controller = new TriathlonController();
                if ($action === 'show' && $id) {
                    $data = $controller->show($id);
                } elseif ($action === 'create') {
                    $data = $controller->create();
                } elseif ($action === 'edit' && $id) {
                    $data = $controller->edit($id);
                } elseif ($action === 'delete' && $id) {
                    $controller->delete($id);
                    exit;
                } else {
                    $data = $controller->index();
                }
                break;

            case 'settings':
                $controller = new SettingsController();
                if ($action === 'updateProfile') {
                    $data = $controller->updateProfile();
                } elseif ($action === 'changePassword') {
                    $data = $controller->changePassword();
                } elseif ($action === 'updatePreferences') {
                    $data = $controller->updatePreferences();
                }
                 else {
                    $data = $controller->index();
                }
                break;

            default:
                throw new Exception("Module non trouvé");
        }

        // Inclure la vue
        // Sélection de la vue en fonction de l'action (create/show/edit/index)
        $candidateViews = [];
        // action spécifique (ex: create, show, edit)
        if ($action && $action !== 'index') {
            $candidateViews[] = "app/views/$module/$action.php";
        }
        // vue index par défaut du module
        $candidateViews[] = "app/views/$module/index.php";
        // fallback singular/plural index
        $candidateViews[] = "app/views/" . rtrim($module, 's') . "/index.php";
        $candidateViews[] = "app/views/" . ($module . 's') . "/index.php";

        $found = false;
        foreach ($candidateViews as $viewFile) {
            if (file_exists($viewFile)) {
                $content = $viewFile;
                include 'app/views/layouts/main.php';
                $found = true;
                break;
            }
        }
        if (!$found) {
            echo "Vue non trouvée pour module/action : $module / $action";
        }

    } catch (Exception $e) {
        echo "Erreur : " . $e->getMessage();
    }
}
