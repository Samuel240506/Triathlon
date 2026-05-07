<?php
require_once __DIR__ . '/../models/Triathlon.php';
require_once __DIR__ . '/../models/TypeTriathlon.php';

require_once __DIR__ . '/../models/Licencie.php';

class TriathlonController {
    private $triathlonModel;
    private $typeTriathlonModel;
    private $licencieModel;

    public function __construct() {
        $this->triathlonModel = new Triathlon();
        $this->typeTriathlonModel = new TypeTriathlon();
        $this->licencieModel = new Licencie();
    }

    public function index() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $user = $_SESSION['user'] ?? null;
        $role = $user['role'] ?? null;
        $clubIdUser = $user['id_club'] ?? ($user['club_id'] ?? null);

        if ($role === 'admin') {
            $triathlons = $this->triathlonModel->getAll();
        } else if ($clubIdUser) {
            $triathlons = $this->triathlonModel->getAllByClub($clubIdUser);
        } else {
            $triathlons = [];
        }



        return ['triathlons' => $triathlons];
    }

    public function show($id) {
        $triathlon = $this->triathlonModel->getById($id);
        if (!$triathlon) {
            return ['error' => 'Triathlon non trouvé'];
        }


        return ['triathlon' => $triathlon];
    }

    public function create() {
        // Récupérer les types pour l'affichage du formulaire
        $types = $this->typeTriathlonModel->getAll();
        
        // Si c'est une requête POST, traiter le formulaire
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'name' => trim($_POST['name'] ?? ''),
                'type' => trim($_POST['type'] ?? ''),
                'location' => trim($_POST['location'] ?? ''),
                'event_date' => $_POST['event_date'] ?? '',
                'max_participants' => !empty($_POST['max_participants']) ? (int)$_POST['max_participants'] : null
            ];

            // Validation basique
            if (empty($data['name']) || empty($data['type']) || empty($data['location']) || empty($data['event_date'])) {
                return [
                    'error' => 'Tous les champs obligatoires doivent être remplis.',
                    'types' => $types
                ];
            }

            // Vérifier que le type existe
            $typeExists = $this->typeTriathlonModel->getByCode($data['type']);
            if (!$typeExists) {
                return [
                    'error' => 'Le type de triathlon sélectionné n\'existe pas.',
                    'types' => $types
                ];
            }

            if ($this->triathlonModel->create($data)) {
                header('Location: index.php?module=triathlons');
                exit;
            } else {
                return [
                    'error' => 'Erreur lors de la création du triathlon',
                    'types' => $types
                ];
            }
        }

        // Si c'est une requête GET, retourner juste les types
        return ['types' => $types];
    }

    public function edit($id) {
        $triathlon = $this->triathlonModel->getById($id);
        if (!$triathlon) {
            return ['error' => 'Triathlon non trouvé'];
        }

        // Provide default empty values for missing keys to avoid undefined index warnings in view
        $triathlon['max_participants'] = $triathlon['max_participants'] ?? '';

        $types = $this->typeTriathlonModel->getAll();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'name' => trim($_POST['name'] ?? ''),
                'type' => trim($_POST['type'] ?? ''),
                'location' => trim($_POST['location'] ?? ''),
                'event_date' => $_POST['event_date'] ?? '',
                'max_participants' => !empty($_POST['max_participants']) ? (int)$_POST['max_participants'] : null
            ];

            // Validation
            if (empty($data['name']) || empty($data['type']) || empty($data['location']) || empty($data['event_date'])) {
                return [
                    'error' => 'Tous les champs obligatoires doivent être remplis.',
                    'triathlon' => $triathlon,
                    'types' => $types
                ];
            }

            // Vérifier que le type existe
            $typeExists = $this->typeTriathlonModel->getByCode($data['type']);
            if (!$typeExists) {
                return [
                    'error' => 'Le type de triathlon sélectionné n\'existe pas.',
                    'triathlon' => $triathlon,
                    'types' => $types
                ];
            }

            if ($this->triathlonModel->update($id, $data)) {
                header('Location: index.php?module=triathlons');
                exit;
            } else {
                return [
                    'error' => 'Erreur lors de la modification du triathlon', 
                    'triathlon' => $triathlon,
                    'types' => $types
                ];
            }
        }

        return ['triathlon' => $triathlon, 'types' => $types];
    }

    public function delete($id) {
        if ($this->triathlonModel->delete($id)) {
            header('Location: index.php?module=triathlons');
            exit;
        } else {
            return ['error' => 'Erreur lors de la suppression du triathlon'];
        }
    }

}
?>
