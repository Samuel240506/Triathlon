<?php
require_once __DIR__ . '/../core/Database.php';

class Triathlon {
    protected $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function getAll() {
        $stmt = $this->db->query(
            "SELECT t.*, tt.libelle as type_label FROM triathlons t LEFT JOIN TypeTriathlon tt ON t.type = tt.codeType ORDER BY t.event_date DESC"
        );
        return $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];
    }

    public function getById($id) {
        $stmt = $this->db->prepare(
            "SELECT t.*, tt.libelle as type_label FROM triathlons t LEFT JOIN TypeTriathlon tt ON t.type = tt.codeType WHERE t.id = ? LIMIT 1"
        );
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function getUpcoming() {
        $stmt = $this->db->prepare(
            "SELECT t.*, tt.libelle as type_label FROM triathlons t LEFT JOIN TypeTriathlon tt ON t.type = tt.codeType WHERE t.event_date >= CURDATE() ORDER BY t.event_date ASC"
        );
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAllByClub($clubId) {
        $stmt = $this->db->prepare(
            "SELECT DISTINCT t.*, tt.libelle as type_label
             FROM triathlons t
             LEFT JOIN TypeTriathlon tt ON t.type = tt.codeType
             JOIN registrations r ON r.triathlon_id = t.id
             JOIN licencies l ON l.id = r.licencie_id
             WHERE l.club_id = :club_id
             ORDER BY t.event_date DESC"
        );
        $stmt->execute([':club_id' => $clubId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create($data) {
        $stmt = $this->db->prepare(
            "INSERT INTO triathlons (name, type, location, event_date, description, max_participants, registration_deadline)
             VALUES (:name, :type, :location, :event_date, :description, :max_participants, :registration_deadline)"
        );
        $ok = $stmt->execute([
            ':name'                  => $data['name'] ?? '',
            ':type'                  => $data['type'] ?? null,
            ':location'              => $data['location'] ?? '',
            ':event_date'            => $data['event_date'] ?? null,
            ':description'           => $data['description'] ?? null,
            ':max_participants'      => $data['max_participants'] ?? null,
            ':registration_deadline' => $data['registration_deadline'] ?? null,
        ]);
        return $ok ? $this->db->lastInsertId() : false;
    }

    public function update($id, $data) {
        $stmt = $this->db->prepare(
            "UPDATE triathlons SET
                name                  = :name,
                type                  = :type,
                location              = :location,
                event_date            = :event_date,
                description           = :description,
                max_participants      = :max_participants,
                registration_deadline = :registration_deadline
             WHERE id = :id"
        );
        return $stmt->execute([
            ':id'                    => $id,
            ':name'                  => $data['name'] ?? '',
            ':type'                  => $data['type'] ?? null,
            ':location'              => $data['location'] ?? '',
            ':event_date'            => $data['event_date'] ?? null,
            ':description'           => $data['description'] ?? null,
            ':max_participants'      => $data['max_participants'] ?? null,
            ':registration_deadline' => $data['registration_deadline'] ?? null,
        ]);
    }

    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM triathlons WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function getTotalCount() {
        $stmt = $this->db->query("SELECT COUNT(*) as count FROM triathlons");
        $r = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int)($r['count'] ?? 0);
    }

    public function getUpcomingCount() {
        $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM triathlons WHERE event_date >= CURDATE()");
        $stmt->execute();
        $r = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int)($r['count'] ?? 0);
    }
}
