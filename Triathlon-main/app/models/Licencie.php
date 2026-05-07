<?php
require_once __DIR__ . '/../core/Database.php';

class Licencie {
    protected $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function getAll($filters = []) {
        $sql = "SELECT l.*, c.name as club_name
                FROM licencies l
                LEFT JOIN clubs c ON l.club_id = c.id";
        $params = [];
        $where  = [];

        if (!empty($filters['club_id'])) {
            $where[] = "l.club_id = :club_id";
            $params[':club_id'] = $filters['club_id'];
        }
        if (!empty($filters['category'])) {
            $where[] = "l.category = :category";
            $params[':category'] = $filters['category'];
        }
        if (!empty($filters['license_type'])) {
            $where[] = "l.license_type = :license_type";
            $params[':license_type'] = $filters['license_type'];
        }
        if (!empty($filters['q'])) {
            $where[] = "(l.first_name LIKE :q OR l.last_name LIKE :q OR l.license_number LIKE :q OR l.email LIKE :q)";
            $params[':q'] = '%' . $filters['q'] . '%';
        }

        if ($where) $sql .= " WHERE " . implode(' AND ', $where);
        $sql .= " ORDER BY l.last_name, l.first_name";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id) {
        $stmt = $this->db->prepare(
            "SELECT l.*, c.name as club_name FROM licencies l LEFT JOIN clubs c ON l.club_id = c.id WHERE l.id = ? LIMIT 1"
        );
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function getByClub($clubId) {
        $stmt = $this->db->prepare(
            "SELECT l.*, c.name as club_name FROM licencies l LEFT JOIN clubs c ON l.club_id = c.id WHERE l.club_id = ? ORDER BY l.last_name, l.first_name"
        );
        $stmt->execute([$clubId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create(array $data) {
        $stmt = $this->db->prepare(
            "INSERT INTO licencies (license_number, first_name, last_name, birth_date, gender, category, license_type, club_id, email, phone)
             VALUES (:license_number, :first_name, :last_name, :birth_date, :gender, :category, :license_type, :club_id, :email, :phone)"
        );
        $stmt->execute([
            ':license_number' => $data['license_number'] ?? null,
            ':first_name'     => $data['first_name'] ?? null,
            ':last_name'      => $data['last_name'] ?? null,
            ':birth_date'     => $data['birth_date'] ?? null,
            ':gender'         => $data['gender'] ?? 'M',
            ':category'       => $data['category'] ?? 'Senior',
            ':license_type'   => $data['license_type'] ?? 'Loisir',
            ':club_id'        => $data['club_id'] ?? null,
            ':email'          => $data['email'] ?? null,
            ':phone'          => $data['phone'] ?? null,
        ]);
        return $this->db->lastInsertId();
    }

    public function update($id, $data) {
        $stmt = $this->db->prepare(
            "UPDATE licencies SET
                license_number = :license_number,
                first_name     = :first_name,
                last_name      = :last_name,
                birth_date     = :birth_date,
                gender         = :gender,
                category       = :category,
                license_type   = :license_type,
                club_id        = :club_id,
                email          = :email,
                phone          = :phone
             WHERE id = :id"
        );
        return $stmt->execute([
            ':id'             => $id,
            ':license_number' => $data['license_number'] ?? null,
            ':first_name'     => $data['first_name'] ?? null,
            ':last_name'      => $data['last_name'] ?? null,
            ':birth_date'     => $data['birth_date'] ?? null,
            ':gender'         => $data['gender'] ?? 'M',
            ':category'       => $data['category'] ?? 'Senior',
            ':license_type'   => $data['license_type'] ?? 'Loisir',
            ':club_id'        => $data['club_id'] ?? null,
            ':email'          => $data['email'] ?? null,
            ':phone'          => $data['phone'] ?? null,
        ]);
    }

    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM licencies WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function getTotalCount() {
        $stmt = $this->db->query("SELECT COUNT(*) as count FROM licencies");
        $r = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int)($r['count'] ?? 0);
    }

    public function getCountByClub($clubId) {
        $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM licencies WHERE club_id = ?");
        $stmt->execute([$clubId]);
        $r = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int)($r['count'] ?? 0);
    }
}
