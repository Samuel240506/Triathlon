<?php
require_once __DIR__ . '/../models/Club.php';

class ClubController {
    private $clubModel;

    public function __construct() {
        $this->clubModel = new Club();
    }

    public function index() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $user = $_SESSION['user'] ?? null;
        $role = $user['role'] ?? null;
        $clubId = $user['id_club'] ?? ($user['club_id'] ?? null);

        if ($role === 'admin') {
            $clubs = $this->clubModel->getAll();
        } else {
            // For non-admin, return only their club if clubId exists
            if ($clubId) {
                $club = $this->clubModel->getById($clubId);
                $clubs = $club ? [$club] : [];
            } else {
                $clubs = [];
            }
        }

        // Ajouter le nombre de licenciés pour chaque club
        foreach ($clubs as &$club) {
            $club['licencies_count'] = $this->clubModel->getLicenciesCount($club['id']);
        }

        return ['clubs' => $clubs];
    }

    public function show($id) {
        $club = $this->clubModel->getById($id);
        if (!$club) {
            return ['error' => 'Club non trouvé'];
        }

        $club['licencies_count'] = $this->clubModel->getLicenciesCount($id);

        return ['club' => $club];
    }

    public function create() {

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'name' => $_POST['name'] ?? '',
                'address' => $_POST['address'] ?? '',
                'city' => $_POST['city'] ?? '',
                'phone' => $_POST['phone'] ?? '',
                'email' => $_POST['email'] ?? ''
            ];

            if ($this->clubModel->create($data)) {
                header('Location: index.php?module=clubs');
                exit;
            } else {
                return ['error' => 'Erreur lors de la création du club'];
            }
        }

    }

    public function edit($id) {
        $club = $this->clubModel->getById($id);
        if (!$club) {
            return ['error' => 'Club non trouvé'];
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'name' => $_POST['name'] ?? '',
                'address' => $_POST['address'] ?? '',
                'city' => $_POST['city'] ?? '',
                'phone' => $_POST['phone'] ?? '',
                'email' => $_POST['email'] ?? ''
            ];

            if ($this->clubModel->update($id, $data)) {
                header('Location: index.php?module=clubs');
                exit;
            } else {
                return ['error' => 'Erreur lors de la mise à jour du club'];
            }
        }

        return ['club' => $club];
    }

    public function delete($id) {
        if ($this->clubModel->delete($id)) {
            header('Location: index.php?module=clubs');
            exit;
        } else {
            return ['error' => 'Erreur lors de la suppression du club'];
        }
    }
}
?>