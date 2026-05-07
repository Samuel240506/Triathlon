<?php
require_once __DIR__ . '/../models/User.php';

class AuthController {
    private $userModel;

    public function __construct() {
        $this->userModel = new User();
    }

    public function login() {
        $error = null;
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email'] ?? '');
            $password = trim($_POST['password'] ?? '');

            $userModel = new User();
            $user = $userModel->authenticate($email, $password);

            if ($user) {
                // Load full user data including preferences
                $fullUser = $userModel->getById($user['id_user'] ?? ($user['id'] ?? null));
                $preferences = json_decode($fullUser['preferences'] ?? '{}', true) ?: [
                    'theme' => 'light',
                    'language' => 'fr',
                    'date_format' => 'd/m/Y'
                ];

                $_SESSION['user'] = [
                    'id'          => $fullUser['id'] ?? $user['id'] ?? null,
                    'name'        => $fullUser['name'] ?? $user['name'] ?? null,
                    'email'       => $fullUser['email'] ?? $user['email'] ?? null,
                    'role'        => $fullUser['role'] ?? $user['role'] ?? 'club_manager',
                    'id_club'     => $fullUser['club_id'] ?? $user['club_id'] ?? null,
                    'preferences' => $preferences
                ];
                header('Location: index.php');
                exit;
            }

            // Si on arrive ici, l'authentification a échoué
            $error = 'Identifiants incorrects';
        }

        // Inclure la vue (la vue doit afficher la variable $error si présente)
        include __DIR__ . '/../views/auth/login.php';
    }

    public function logout() {
        session_unset();
        session_destroy();
        header('Location: index.php?action=login');
        exit;
    }
}
?>
