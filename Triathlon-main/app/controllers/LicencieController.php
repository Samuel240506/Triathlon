<?php
require_once __DIR__ . '/../models/Licencie.php';
require_once __DIR__ . '/../models/Club.php';

require_once __DIR__ . '/../models/Licencie.php';
require_once __DIR__ . '/../models/Club.php';

class LicencieController {
    private $licencieModel;
    private $clubModel;

    public function __construct() {
        $this->licencieModel = new Licencie();
        $this->clubModel = new Club();
    }

    public function index() {
        $filters = [];

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $user = $_SESSION['user'] ?? null;
        $role = $user['role'] ?? null;
        $clubIdUser = $user['id_club'] ?? ($user['club_id'] ?? null);

        // For non-admin users, override filters to only their club
        if ($role !== 'admin' && $clubIdUser) {
            $filters['club_id'] = $clubIdUser;
        } else {
            if (isset($_GET['club_id']) && !empty($_GET['club_id'])) {
                $filters['club_id'] = $_GET['club_id'];
            }
        }

        if (isset($_GET['category']) && !empty($_GET['category'])) {
            $filters['category'] = $_GET['category'];
        }
        if (isset($_GET['license_type']) && !empty($_GET['license_type'])) {
            $filters['license_type'] = $_GET['license_type'];
        }

        $licencies = $this->licencieModel->getAll($filters);

        // For clubs dropdown, show all clubs for admin, otherwise only user's club
        if ($role === 'admin') {
            $clubs = $this->clubModel->getAll();
        } else {
            if ($clubIdUser) {
                $club = $this->clubModel->getById($clubIdUser);
                $clubs = $club ? [$club] : [];
            } else {
                $clubs = [];
            }
        }

        return [
            'licencies' => $licencies,
            'clubs' => $clubs,
            'filters' => $filters
        ];
    }

    public function show($id) {
        $licencie = $this->licencieModel->getById($id);
        if (!$licencie) {
            return ['error' => 'Licencié non trouvé'];
        }

        return ['licencie' => $licencie];
    }

    public function create() {
        $clubs = $this->clubModel->getAll();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Récupérer les champs
            $license_number = trim($_POST['license_number'] ?? '');
            $first = trim($_POST['first_name'] ?? '');
            $last  = trim($_POST['last_name'] ?? '');
            $birth = trim($_POST['birth_date'] ?? $_POST['birthdate'] ?? '');
            $gender = $_POST['gender'] ?? 'M';
            $phone = trim($_POST['phone'] ?? '');
            $club_id_post = $_POST['club_id'] ?? '';
            $club_name_new = trim($_POST['club_name_new'] ?? '');

            $errors = [];

            // validations minimales
            if ($license_number === '') $errors[] = 'Numéro de licence requis';
            if ($first === '') $errors[] = 'Prénom requis';
            if ($last === '') $errors[] = 'Nom requis';
            if ($club_id_post === '' && $club_name_new === '') $errors[] = 'Club requis (sélectionnez ou saisissez un nouveau club)';

            if (!empty($errors)) {
                return ['errors' => $errors, 'old' => $_POST, 'clubs' => $clubs];
            }

            // déterminer club_id : si sélection numérique -> utiliser, sinon créer nouveau club si club_name_new fourni
            $clubId = null;
            if (is_numeric($club_id_post) && (int)$club_id_post > 0) {
                $clubId = (int)$club_id_post;
            } else {
                if ($club_name_new !== '') {
                    $existing = $this->clubModel->getByName($club_name_new);
                    if ($existing) {
                        $clubId = $existing['id'];
                    } else {
                        $clubId = $this->clubModel->create([
                            'name' => $club_name_new,
                            'address' => '',
                            'city' => '',
                            'phone' => ''
                        ]);
                    }
                }
            }

            if (!$clubId) {
                $errors[] = 'Impossible de déterminer le club (erreur interne)';
                return ['errors' => $errors, 'old' => $_POST, 'clubs' => $clubs];
            }

            $newId = $this->licencieModel->create([
                'license_number' => $license_number,
                'first_name' => $first,
                'last_name' => $last,
                'birth_date' => $birth,
                'gender' => $gender,
                'club_id' => $clubId,
                'phone' => $phone
            ]);

            $_SESSION['flash'] = "Licencié ajouté (ID: $newId)";
            header('Location: index.php?module=licencies');
            exit;
        }

        return ['clubs' => $clubs];
    }

    public function edit($id) {
        $licencie = $this->licencieModel->getById($id);
        $clubs = $this->clubModel->getAll();

        if (!$licencie) {
            return ['error' => 'Licencié non trouvé'];
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'license_number' => $_POST['license_number'] ?? '',
                'first_name' => $_POST['first_name'] ?? '',
                'last_name' => $_POST['last_name'] ?? '',
                'birth_date' => $_POST['birth_date'] ?? '',
                'gender' => $_POST['gender'] ?? '',
                'club_id' => $_POST['club_id'] ?? null,
                'phone' => $_POST['phone'] ?? ''
            ];

            if ($this->licencieModel->update($id, $data)) {
                header('Location: index.php?module=licencies');
                exit;
            } else {
                return ['error' => 'Erreur lors de la modification du licencié', 'licencie' => $licencie, 'clubs' => $clubs];
            }
        }

        return ['licencie' => $licencie, 'clubs' => $clubs];
    }

    public function delete($id) {
        if ($this->licencieModel->delete($id)) {
            header('Location: index.php?module=licencies');
            exit;
        } else {
            return ['error' => 'Erreur lors de la suppression du licencié'];
        }
    }
}
?>
 