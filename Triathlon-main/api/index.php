<?php
/**
 * FFTRI REST API v1.0
 *
 * Base URL: http://localhost/Triathlon-main/api/
 *
 * Authentification:
 *   1. POST /api/?resource=auth   {"email":"...","password":"..."} -> token Bearer
 *   2. Toutes les autres requêtes: Authorization: Bearer <token>
 *      OU paramètre GET: ?api_key=<token>
 *
 * Endpoints:
 *   GET    ?resource=             Documentation de l'API
 *   POST   ?resource=auth         Connexion (obtenir un token)
 *   GET    ?resource=stats        Statistiques globales
 *   GET    ?resource=types        Types de triathlon
 *
 *   GET    ?resource=clubs                  Liste des clubs
 *   GET    ?resource=clubs&id={id}          Détail d'un club
 *   POST   ?resource=clubs                  Créer un club (admin)
 *   PUT    ?resource=clubs&id={id}          Modifier un club
 *   DELETE ?resource=clubs&id={id}          Supprimer un club (admin)
 *
 *   GET    ?resource=licencies              Liste des licenciés
 *   GET    ?resource=licencies&id={id}      Détail d'un licencié
 *   POST   ?resource=licencies              Créer un licencié
 *   PUT    ?resource=licencies&id={id}      Modifier un licencié
 *   DELETE ?resource=licencies&id={id}      Supprimer un licencié (admin)
 *
 *   GET    ?resource=triathlons             Liste des triathlons
 *   GET    ?resource=triathlons&id={id}     Détail d'un triathlon
 *   POST   ?resource=triathlons             Créer un triathlon (admin)
 *   PUT    ?resource=triathlons&id={id}     Modifier un triathlon (admin)
 *   DELETE ?resource=triathlons&id={id}     Supprimer un triathlon (admin)
 */

error_reporting(E_ALL);
ini_set('display_errors', 0);

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

define('ROOT_PATH', dirname(__DIR__));
require_once ROOT_PATH . '/config/database.php';
require_once ROOT_PATH . '/app/core/Database.php';
require_once ROOT_PATH . '/app/models/Club.php';
require_once ROOT_PATH . '/app/models/Licencie.php';
require_once ROOT_PATH . '/app/models/Triathlon.php';
require_once ROOT_PATH . '/app/models/TypeTriathlon.php';
require_once ROOT_PATH . '/app/models/User.php';

// ============================================================
// HELPERS
// ============================================================

function json_ok($data, int $code = 200): void {
    http_response_code($code);
    echo json_encode(['success' => true, 'data' => $data], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    exit;
}

function json_err(string $msg, int $code = 400, array $extra = []): void {
    http_response_code($code);
    echo json_encode(array_merge(['success' => false, 'error' => $msg], $extra),
        JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    exit;
}

function body(): array {
    $raw = file_get_contents('php://input');
    if (!$raw) return [];
    $d = json_decode($raw, true);
    return is_array($d) ? $d : [];
}

// Support PUT/DELETE via _method override (forms)
$method = strtoupper($_SERVER['REQUEST_METHOD']);
if ($method === 'POST' && isset($_POST['_method'])) {
    $method = strtoupper($_POST['_method']);
}

// Résoudre resource & id
// Support: /api/clubs/5  OU  /api/?resource=clubs&id=5
$requestPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$scriptDir   = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');
$cleanPath   = ltrim(str_replace($scriptDir, '', $requestPath), '/');
$segments    = array_values(array_filter(explode('/', $cleanPath)));

// Si le premier segment est "index.php", ignorer
if (isset($segments[0]) && $segments[0] === 'index.php') {
    array_shift($segments);
}

$resource = $segments[0] ?? $_GET['resource'] ?? '';
$id       = $segments[1] ?? $_GET['id'] ?? null;

// Normaliser la resource (enlever .php éventuel)
$resource = preg_replace('/\.php$/', '', $resource);

// ============================================================
// TOKEN MANAGEMENT
// ============================================================

function get_token(): ?string {
    $auth = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
    if (!$auth && function_exists('apache_request_headers')) {
        $headers = apache_request_headers();
        $auth    = $headers['Authorization'] ?? $headers['authorization'] ?? '';
    }
    if (preg_match('/^Bearer\s+(.+)$/i', $auth, $m)) {
        return trim($m[1]);
    }
    return $_GET['api_key'] ?? null;
}

function verify_auth(): array {
    $token = get_token();
    if (!$token) {
        json_err('Token manquant. POST /api/?resource=auth pour obtenir un token.', 401);
    }

    $decoded = base64_decode($token);
    if (!$decoded || !str_contains($decoded, ':')) {
        json_err('Token invalide.', 401);
    }

    [$userId, $secret] = explode(':', $decoded, 2);
    $userId = (int)$userId;
    if ($userId <= 0) json_err('Token invalide.', 401);

    $db    = Database::getConnection();
    $table = 'users';
    $pk    = 'id';
    try {
        $db->query("SELECT 1 FROM Users LIMIT 1");
        $table = 'Users';
        $pk    = 'id_user';
    } catch (Exception $e) {}

    try {
        $stmt = $db->prepare("SELECT * FROM {$table} WHERE {$pk} = ? LIMIT 1");
        $stmt->execute([$userId]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        json_err('Erreur serveur.', 500);
    }

    if (!$user) json_err('Token invalide.', 401);

    $expected = hash('sha256', ($user['password'] ?? '') . 'fftri_api_2025');
    if (!hash_equals($expected, $secret)) json_err('Token invalide.', 401);

    unset($user['password']);
    return $user;
}

// ============================================================
// ROUTE: POST auth — Pas de token requis
// ============================================================
if ($resource === 'auth' && $method === 'POST') {
    $b        = body();
    $email    = trim($b['email'] ?? '');
    $password = trim($b['password'] ?? '');

    if (!$email || !$password) {
        json_err('email et password sont requis.', 422);
    }

    $uModel = new User();
    $user   = $uModel->authenticate($email, $password);

    if (!$user) {
        json_err('Identifiants incorrects.', 401);
    }

    $db    = Database::getConnection();
    $table = 'users';
    $pk    = 'id';
    try {
        $db->query("SELECT 1 FROM Users LIMIT 1");
        $table = 'Users';
        $pk    = 'id_user';
    } catch (Exception $e) {}

    $userId = $user[$pk] ?? $user['id'] ?? $user['id_user'] ?? null;
    $stmt   = $db->prepare("SELECT password FROM {$table} WHERE {$pk} = ? LIMIT 1");
    $stmt->execute([$userId]);
    $row    = $stmt->fetch(PDO::FETCH_ASSOC);
    $secret = hash('sha256', ($row['password'] ?? '') . 'fftri_api_2025');
    $token  = base64_encode("{$userId}:{$secret}");

    json_ok([
        'token'      => $token,
        'token_type' => 'Bearer',
        'expires_in' => null,
        'user'       => [
            'id'    => $userId,
            'name'  => $user['nom'] ?? $user['name'] ?? '',
            'email' => $user['email'] ?? '',
            'role'  => $user['role'] ?? 'club_manager',
        ],
        'usage' => 'Ajoutez le header: Authorization: Bearer ' . $token,
    ]);
}

// ============================================================
// Toutes les routes suivantes nécessitent un token valide
// ============================================================
$me      = verify_auth();
$isAdmin = ($me['role'] ?? '') === 'admin';
$myClub  = $me['id_club'] ?? $me['club_id'] ?? null;

// ============================================================
// GET / — Documentation
// ============================================================
if ($resource === '' || $resource === null) {
    $base = (isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://'
          . $_SERVER['HTTP_HOST']
          . rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');
    json_ok([
        'api'     => 'FFTRI REST API',
        'version' => '1.0',
        'base'    => $base . '/?resource=',
        'auth'    => [
            'description' => 'POST ?resource=auth {"email":"...","password":"..."} -> token',
            'header'      => 'Authorization: Bearer <token>',
        ],
        'endpoints' => [
            'auth'       => 'POST ?resource=auth',
            'stats'      => 'GET  ?resource=stats',
            'types'      => 'GET  ?resource=types',
            'clubs'      => [
                'list'   => 'GET    ?resource=clubs',
                'get'    => 'GET    ?resource=clubs&id={id}',
                'create' => 'POST   ?resource=clubs',
                'update' => 'PUT    ?resource=clubs&id={id}',
                'delete' => 'DELETE ?resource=clubs&id={id}',
            ],
            'licencies'  => [
                'list'   => 'GET    ?resource=licencies[&club_id=&category=&q=]',
                'get'    => 'GET    ?resource=licencies&id={id}',
                'create' => 'POST   ?resource=licencies',
                'update' => 'PUT    ?resource=licencies&id={id}',
                'delete' => 'DELETE ?resource=licencies&id={id}',
            ],
            'triathlons' => [
                'list'     => 'GET    ?resource=triathlons[&upcoming=1]',
                'get'      => 'GET    ?resource=triathlons&id={id}',
                'create'   => 'POST   ?resource=triathlons',
                'update'   => 'PUT    ?resource=triathlons&id={id}',
                'delete'   => 'DELETE ?resource=triathlons&id={id}',
            ],
        ],
    ]);
}

// ============================================================
// GET stats
// ============================================================
if ($resource === 'stats') {
    $cModel = new Club();
    $lModel = new Licencie();
    $tModel = new Triathlon();
    json_ok([
        'clubs'              => $cModel->getTotalCount(),
        'licencies'          => $lModel->getTotalCount(),
        'triathlons_total'   => $tModel->getTotalCount(),
        'triathlons_upcoming'=> $tModel->getUpcomingCount(),
    ]);
}

// ============================================================
// GET types
// ============================================================
if ($resource === 'types') {
    json_ok((new TypeTriathlon())->getAll());
}

// ============================================================
// /clubs
// ============================================================
if ($resource === 'clubs') {
    $m = new Club();
    switch ($method) {
        case 'GET':
            if ($id) {
                $c = $m->getById($id);
                if (!$c) json_err('Club non trouvé.', 404);
                $c['licencies_count'] = $m->getLicenciesCount($id);
                json_ok($c);
            }
            $list = $m->getAll();
            foreach ($list as &$c) $c['licencies_count'] = $m->getLicenciesCount($c['id']);
            json_ok($list);

        case 'POST':
            if (!$isAdmin) json_err('Réservé aux administrateurs.', 403);
            $b = body();
            if (empty($b['name']) || empty($b['city'])) json_err('name et city sont requis.', 422);
            $newId = $m->create($b);
            if (!$newId) json_err('Erreur de création.', 500);
            json_ok($m->getById($newId), 201);

        case 'PUT':
            if (!$id) json_err('id manquant.', 400);
            if (!$isAdmin && $myClub != $id) json_err('Accès refusé.', 403);
            if (!$m->getById($id)) json_err('Club non trouvé.', 404);
            if (!$m->update($id, body())) json_err('Erreur de mise à jour.', 500);
            json_ok($m->getById($id));

        case 'DELETE':
            if (!$isAdmin) json_err('Réservé aux administrateurs.', 403);
            if (!$id) json_err('id manquant.', 400);
            if (!$m->getById($id)) json_err('Club non trouvé.', 404);
            if (!$m->delete($id)) json_err('Erreur de suppression.', 500);
            json_ok(['deleted' => true, 'id' => (int)$id]);

        default:
            json_err('Méthode non supportée.', 405);
    }
}

// ============================================================
// /licencies
// ============================================================
if ($resource === 'licencies') {
    $m = new Licencie();
    switch ($method) {
        case 'GET':
            if ($id) {
                $l = $m->getById($id);
                if (!$l) json_err('Licencié non trouvé.', 404);
                json_ok($l);
            }
            $filters = [];
            if (!$isAdmin && $myClub) $filters['club_id'] = $myClub;
            elseif (isset($_GET['club_id'])) $filters['club_id'] = $_GET['club_id'];
            if (isset($_GET['category'])) $filters['category'] = $_GET['category'];
            if (isset($_GET['q']))        $filters['q']        = $_GET['q'];
            json_ok($m->getAll($filters));

        case 'POST':
            $b = body();
            foreach (['license_number','first_name','last_name','club_id'] as $req) {
                if (empty($b[$req])) json_err("Champ requis: {$req}.", 422);
            }
            if (!$isAdmin && $b['club_id'] != $myClub) json_err('Vous ne pouvez créer que pour votre club.', 403);
            $newId = $m->create($b);
            if (!$newId) json_err('Erreur de création.', 500);
            json_ok($m->getById($newId), 201);

        case 'PUT':
            if (!$id) json_err('id manquant.', 400);
            $l = $m->getById($id);
            if (!$l) json_err('Licencié non trouvé.', 404);
            if (!$isAdmin && ($l['club_id'] ?? null) != $myClub) json_err('Accès refusé.', 403);
            if (!$m->update($id, body())) json_err('Erreur de mise à jour.', 500);
            json_ok($m->getById($id));

        case 'DELETE':
            if (!$isAdmin) json_err('Réservé aux administrateurs.', 403);
            if (!$id) json_err('id manquant.', 400);
            if (!$m->getById($id)) json_err('Licencié non trouvé.', 404);
            if (!$m->delete($id)) json_err('Erreur de suppression.', 500);
            json_ok(['deleted' => true, 'id' => $id]);

        default:
            json_err('Méthode non supportée.', 405);
    }
}

// ============================================================
// /triathlons
// ============================================================
if ($resource === 'triathlons') {
    $m = new Triathlon();
    switch ($method) {
        case 'GET':
            if ($id) {
                $t = $m->getById($id);
                if (!$t) json_err('Triathlon non trouvé.', 404);
                json_ok($t);
            }
            if (isset($_GET['upcoming'])) json_ok($m->getUpcoming());
            if (!$isAdmin && $myClub) json_ok($m->getAllByClub($myClub));
            json_ok($m->getAll());

        case 'POST':
            if (!$isAdmin) json_err('Réservé aux administrateurs.', 403);
            $b = body();
            foreach (['name','type','location','event_date'] as $req) {
                if (empty($b[$req])) json_err("Champ requis: {$req}.", 422);
            }
            $typeM = new TypeTriathlon();
            if (!$typeM->getByCode($b['type'])) json_err('Type de triathlon inexistant.', 422);
            $newId = $m->create($b);
            if (!$newId) json_err('Erreur de création.', 500);
            json_ok($m->getById($newId), 201);

        case 'PUT':
            if (!$isAdmin) json_err('Réservé aux administrateurs.', 403);
            if (!$id) json_err('id manquant.', 400);
            if (!$m->getById($id)) json_err('Triathlon non trouvé.', 404);
            if (!$m->update($id, body())) json_err('Erreur de mise à jour.', 500);
            json_ok($m->getById($id));

        case 'DELETE':
            if (!$isAdmin) json_err('Réservé aux administrateurs.', 403);
            if (!$id) json_err('id manquant.', 400);
            if (!$m->getById($id)) json_err('Triathlon non trouvé.', 404);
            if (!$m->delete($id)) json_err('Erreur de suppression.', 500);
            json_ok(['deleted' => true, 'id' => (int)$id]);

        default:
            json_err('Méthode non supportée.', 405);
    }
}

// Route non trouvée
json_err("Ressource '{$resource}' inconnue. Consultez GET ?resource= pour la documentation.", 404);
