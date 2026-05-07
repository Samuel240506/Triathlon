<?php
require_once __DIR__ . '/../core/Database.php';

class TypeTriathlon {
    private $db;
    private $table = 'typetriathlon';

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function getAll() {
        $sql = "SELECT codeType, libelle FROM {$this->table} ORDER BY libelle";
        $stmt = $this->db->query($sql);
        return $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];
    }

    public function getByCode($code) {
        $sql = "SELECT codeType, libelle FROM {$this->table} WHERE codeType = :code LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':code' => $code]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>