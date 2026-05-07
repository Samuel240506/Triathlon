<?php
require_once __DIR__ . '/../core/Database.php';

class Club {
    private $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function getAll() {
        $stmt = $this->db->query("SELECT id, name, address, city, phone, email FROM clubs ORDER BY name");
        return $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];
    }

    public function getById($id) {
        $stmt = $this->db->prepare("SELECT id, name, address, city, phone, email FROM clubs WHERE id = ? LIMIT 1");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function getByName($name) {
        $stmt = $this->db->prepare("SELECT id, name FROM clubs WHERE name = ? LIMIT 1");
        $stmt->execute([$name]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function getLicenciesCount($clubId) {
        $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM licencies WHERE club_id = ?");
        $stmt->execute([$clubId]);
        $r = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int)($r['count'] ?? 0);
    }

    public function create($data) {
        $stmt = $this->db->prepare(
            "INSERT INTO clubs (name, address, city, phone, email) VALUES (:name, :address, :city, :phone, :email)"
        );
        $ok = $stmt->execute([
            ':name'    => $data['name'] ?? '',
            ':address' => $data['address'] ?? null,
            ':city'    => $data['city'] ?? null,
            ':phone'   => $data['phone'] ?? null,
            ':email'   => $data['email'] ?? null,
        ]);
        return $ok ? $this->db->lastInsertId() : false;
    }

    public function update($id, $data) {
        $stmt = $this->db->prepare(
            "UPDATE clubs SET name = :name, address = :address, city = :city, phone = :phone, email = :email WHERE id = :id"
        );
        return $stmt->execute([
            ':id'      => $id,
            ':name'    => $data['name'] ?? '',
            ':address' => $data['address'] ?? null,
            ':city'    => $data['city'] ?? null,
            ':phone'   => $data['phone'] ?? null,
            ':email'   => $data['email'] ?? null,
        ]);
    }

    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM clubs WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function getTotalCount() {
        $stmt = $this->db->query("SELECT COUNT(*) as count FROM clubs");
        $r = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int)($r['count'] ?? 0);
    }
}
