<?php
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/Club.php';
require_once __DIR__ . '/../core/Database.php';

class SettingsController {
    private $userModel;
    private $db;

    public function __construct() {
        $this->userModel = new User();
        $this->db = Database::getConnection();
    }

    public function index() {
        $data = [];
        $user = $_SESSION['user'] ?? null;
        if ($user) {
            $data['role'] = $user['role'] ?? null;
            $data['name'] = $user['name'] ?? null;

            if (($user['role'] ?? '') === 'club_manager' && !empty($user['id_club'])) {
                $clubModel = new Club();
                $club = $clubModel->getById($user['id_club']);
                if ($club) {
                    $data['club_name'] = $club['name'] ?? null;
                }
            }
        }
        return $data;
    }

    public function updateProfile() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->index();
        }

        $userId = $_SESSION['user']['id'] ?? null;
        if (!$userId) {
            return ['error' => 'Vous n\'êtes pas connecté.'];
        }

        $name  = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');

        if ($name === '' || $email === '') {
            return ['error' => 'Le nom et l\'email sont obligatoires.'];
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ['error' => 'Adresse email invalide.'];
        }

        $user = $this->userModel->getById($userId);
        if (!$user) {
            return ['error' => 'Utilisateur introuvable.'];
        }

        $updateData = [
            'name'    => $name,
            'email'   => $email,
            'role'    => $user['role'] ?? $_SESSION['user']['role'] ?? 'club_manager',
            'club_id' => $user['club_id'] ?? $user['id_club'] ?? $_SESSION['user']['id_club'] ?? null
        ];

        $success = $this->userModel->update($userId, $updateData);

        if ($success) {
            $_SESSION['user']['name']  = $name;
            $_SESSION['user']['email'] = $email;
            $_SESSION['flash'] = 'Profil mis à jour avec succès.';
            header('Location: index.php?module=settings');
            exit;
        }

        return ['error' => 'Erreur lors de la mise à jour du profil.'];
    }

    public function changePassword() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->index();
        }

        $userId = $_SESSION['user']['id'] ?? null;
        if (!$userId) {
            return ['error' => 'Vous n\'êtes pas connecté.'];
        }

        $current = $_POST['current_password'] ?? '';
        $new     = $_POST['new_password'] ?? '';
        $confirm = $_POST['confirm_password'] ?? '';

        if ($new !== $confirm) {
            return ['error' => 'Les nouveaux mots de passe ne correspondent pas.'];
        }

        if (strlen($new) < 8) {
            return ['error' => 'Le nouveau mot de passe doit contenir au moins 8 caractères.'];
        }

        // Récupérer le hash du mot de passe directement en DB
        $table = 'users';
        $pk    = 'id';
        try { $this->db->query("SELECT 1 FROM Users LIMIT 1"); $table = 'Users'; $pk = 'id_user'; } catch (Exception $e) {}

        $stmt = $this->db->prepare("SELECT password FROM {$table} WHERE {$pk} = ? LIMIT 1");
        $stmt->execute([$userId]);
        $row  = $stmt->fetch(PDO::FETCH_ASSOC);
        $hash = $row['password'] ?? '';

        // Vérifier le mot de passe actuel (bcrypt ou MD5)
        $valid = false;
        if (!empty($hash)) {
            if (password_get_info($hash)['algo'] !== 0) {
                $valid = password_verify($current, $hash);
            } else {
                $valid = (md5($current) === $hash);
            }
        }

        if (!$valid) {
            return ['error' => 'Mot de passe actuel incorrect.'];
        }

        $newHash = password_hash($new, PASSWORD_DEFAULT);
        $upd     = $this->db->prepare("UPDATE {$table} SET password = ? WHERE {$pk} = ?");
        $ok      = $upd->execute([$newHash, $userId]);

        if ($ok) {
            $_SESSION['flash'] = 'Mot de passe modifié avec succès.';
            header('Location: index.php?module=settings');
            exit;
        }

        return ['error' => 'Erreur lors du changement de mot de passe.'];
    }

    public function updatePreferences() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->index();
        }

        $theme = in_array($_POST['theme'] ?? '', ['light', 'dark']) ? $_POST['theme'] : 'light';

        $prefs                           = $_SESSION['user']['preferences'] ?? [];
        $prefs['theme']                  = $theme;
        $_SESSION['user']['preferences'] = $prefs;

        $_SESSION['flash'] = 'Préférences enregistrées.';
        header('Location: index.php?module=settings');
        exit;
    }
}
?>
