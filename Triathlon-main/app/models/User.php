<?php
require_once __DIR__ . '/../core/Database.php';

class User {
    private $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function authenticate(string $email, string $password) {
        $password = trim($password);
        try {
            $stmt = $this->db->prepare("SELECT * FROM users WHERE email = ? LIMIT 1");
            $stmt->execute([$email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return false;
        }

        if (!$user) return false;

        $stored = $user['password'] ?? '';

        if (!empty($stored) && password_get_info($stored)['algo'] !== 0 && password_verify($password, $stored)) {
            unset($user['password']);
            return $user;
        }

        if (!empty($stored) && md5($password) === $stored) {
            try {
                $newHash = password_hash($password, PASSWORD_DEFAULT);
                $upd = $this->db->prepare("UPDATE users SET password = ? WHERE id = ?");
                $upd->execute([$newHash, $user['id']]);
            } catch (Exception $e) {}
            unset($user['password']);
            return $user;
        }

        if (!empty($stored) && $password === $stored) {
            unset($user['password']);
            return $user;
        }

        return false;
    }

    public function getById($id) {
        $stmt = $this->db->prepare("SELECT id, name, email, role, club_id, preferences, created_at FROM users WHERE id = ? LIMIT 1");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getAll() {
        $stmt = $this->db->query("SELECT id, name, email, role, club_id, created_at FROM users ORDER BY name");
        return $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];
    }

    public function create($data) {
        $stmt = $this->db->prepare(
            "INSERT INTO users (name, email, password, role, club_id) VALUES (:name, :email, :password, :role, :club_id)"
        );
        return $stmt->execute([
            ':name'     => $data['name'] ?? '',
            ':email'    => $data['email'] ?? '',
            ':password' => password_hash($data['password'] ?? '', PASSWORD_DEFAULT),
            ':role'     => $data['role'] ?? 'club_manager',
            ':club_id'  => $data['club_id'] ?? null,
        ]);
    }

    public function update($id, $data) {
        $sets   = ['name = :name', 'email = :email', 'role = :role', 'club_id = :club_id'];
        $params = [
            ':id'      => $id,
            ':name'    => $data['name'] ?? '',
            ':email'   => $data['email'] ?? '',
            ':role'    => $data['role'] ?? 'club_manager',
            ':club_id' => $data['club_id'] ?? null,
        ];

        if (!empty($data['password'])) {
            $sets[]              = 'password = :password';
            $params[':password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        }

        $stmt = $this->db->prepare("UPDATE users SET " . implode(', ', $sets) . " WHERE id = :id");
        return $stmt->execute($params);
    }

    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM users WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
