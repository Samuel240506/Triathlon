<?php
class Database {
    private static $instance = null;
    private $pdo;

    private function __construct() {
        $host = defined('DB_HOST') ? DB_HOST : '127.0.0.1';
        $db   = defined('DB_NAME') ? DB_NAME : 'triathlon';
        $user = defined('DB_USER') ? DB_USER : 'root';
        $pass = defined('DB_PASS') ? DB_PASS : '';
        $charset = 'utf8mb4';

        $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        try {
            $this->pdo = new PDO($dsn, $user, $pass, $options);
        } catch (PDOException $e) {
            // Tentative de création de la base en dev si elle n'existe pas
            try {
                $tmp = new PDO("mysql:host=$host;charset=$charset", $user, $pass, $options);
                $tmp->exec("CREATE DATABASE IF NOT EXISTS `$db` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
                $this->pdo = new PDO($dsn, $user, $pass, $options);
            } catch (PDOException $e2) {
                die("DB connection failed: " . $e2->getMessage());
            }
        }
    }

    public static function getInstance() {
        // Normaliser : renvoyer la même instance que getConnection()
        return self::getConnection();
    }

    public static function getConnection() {
        if (self::$instance === null) {
            self::$instance = new Database();
        }
        return self::$instance->pdo;
    }
}
