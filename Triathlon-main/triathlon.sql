-- Script SQL pour créer la base de données triathlon
-- À exécuter dans phpMyAdmin ou via la ligne de commande MySQL

CREATE DATABASE IF NOT EXISTS triathlon CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE triathlon;

-- Table des utilisateurs (admin et responsables de club)
CREATE TABLE IF NOT EXISTS users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'club_manager') NOT NULL DEFAULT 'club_manager',
    club_id INT NULL,
    preferences JSON NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Table des clubs
CREATE TABLE IF NOT EXISTS clubs (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    address VARCHAR(255),
    city VARCHAR(100) NOT NULL,
    phone VARCHAR(20),
    email VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Contrainte clé étrangère users -> clubs
ALTER TABLE users ADD CONSTRAINT fk_users_club FOREIGN KEY (club_id) REFERENCES clubs(id) ON DELETE SET NULL;

-- Table des licenciés
CREATE TABLE IF NOT EXISTS licencies (
    id INT PRIMARY KEY AUTO_INCREMENT,
    license_number VARCHAR(20) UNIQUE NOT NULL,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    birth_date DATE,
    gender ENUM('M', 'F') NOT NULL,
    category ENUM('Junior', 'Senior', 'Vétéran') NOT NULL DEFAULT 'Senior',
    license_type ENUM('Compétition', 'Loisir') NOT NULL DEFAULT 'Loisir',
    club_id INT NOT NULL,
    email VARCHAR(100),
    phone VARCHAR(20),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (club_id) REFERENCES clubs(id) ON DELETE CASCADE
);

-- Table des types de triathlon
CREATE TABLE IF NOT EXISTS TypeTriathlon (
    codeType VARCHAR(10) PRIMARY KEY,
    libelle VARCHAR(100) NOT NULL
);

-- Table des triathlons
CREATE TABLE IF NOT EXISTS triathlons (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    type VARCHAR(10) NOT NULL,
    location VARCHAR(100) NOT NULL,
    event_date DATE NOT NULL,
    description TEXT,
    max_participants INT,
    registration_deadline DATE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (type) REFERENCES TypeTriathlon(codeType)
);

-- Table des inscriptions aux triathlons
CREATE TABLE IF NOT EXISTS registrations (
    id INT PRIMARY KEY AUTO_INCREMENT,
    triathlon_id INT NOT NULL,
    licencie_id INT NOT NULL,
    registration_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('pending', 'confirmed', 'cancelled') DEFAULT 'pending',
    FOREIGN KEY (triathlon_id) REFERENCES triathlons(id) ON DELETE CASCADE,
    FOREIGN KEY (licencie_id) REFERENCES licencies(id) ON DELETE CASCADE,
    UNIQUE KEY unique_registration (triathlon_id, licencie_id)
);

-- ============================================================
-- Données de test
-- ============================================================

-- Clubs
INSERT IGNORE INTO clubs (name, address, city, phone, email) VALUES
('Triathlon Club Orléans', '123 Rue de la Loire', 'Orléans', '02 38 42 00 00', 'contact@tco.fr'),
('AS Triathlon Tours', '456 Avenue de la République', 'Tours', '02 47 20 00 00', 'info@asttours.fr'),
('Triathlon Club Blois', '789 Boulevard de la Liberté', 'Blois', '02 54 78 00 00', 'club@tc-blois.fr'),
('US Triathlon Chartres', '321 Rue des Sports', 'Chartres', '02 37 30 00 00', 'contact@ust-chartres.fr');

-- Utilisateurs (mot de passe: password)
INSERT IGNORE INTO users (name, email, password, role, club_id) VALUES
('Admin FFTRI', 'admin@fftri.com', MD5('password'), 'admin', NULL),
('Responsable Orléans', 'resp.orleans@fftri.com', MD5('password'), 'club_manager', 1),
('Responsable Tours', 'resp.tours@fftri.com', MD5('password'), 'club_manager', 2);

-- Types de triathlon
INSERT IGNORE INTO TypeTriathlon (codeType, libelle) VALUES
('XS',   'Triathlon XS (Super Sprint)'),
('S',    'Triathlon S (Sprint)'),
('M',    'Triathlon M (Olympique)'),
('TROP', 'Triathlon Olympique'),
('L',    'Triathlon L (Half Iron)'),
('XL',   'Triathlon XL (Iron Man)'),
('DU',   'Duathlon'),
('AQ',   'Aquathlon');

-- Licenciés
INSERT IGNORE INTO licencies (license_number, first_name, last_name, birth_date, gender, category, license_type, club_id, email, phone) VALUES
('FFTRI2024001', 'Pierre', 'Martin', '1990-05-15', 'M', 'Senior', 'Compétition', 1, 'pierre.martin@email.com', '06 12 34 56 78'),
('FFTRI2024002', 'Marie', 'Dubois', '1985-08-22', 'F', 'Vétéran', 'Loisir', 2, 'marie.dubois@email.com', '06 23 45 67 89'),
('FFTRI2024003', 'Jean', 'Moreau', '2005-03-10', 'M', 'Junior', 'Compétition', 3, 'jean.moreau@email.com', '06 34 56 78 90'),
('FFTRI2024004', 'Sophie', 'Bernard', '1992-11-30', 'F', 'Senior', 'Compétition', 4, 'sophie.bernard@email.com', '06 45 67 89 01'),
('FFTRI2024005', 'Lucas', 'Petit', '1998-07-14', 'M', 'Senior', 'Loisir', 1, 'lucas.petit@email.com', '06 56 78 90 12'),
('FFTRI2024006', 'Emma', 'Leroy', '2003-12-01', 'F', 'Junior', 'Compétition', 2, 'emma.leroy@email.com', '06 67 89 01 23');

-- Triathlons
INSERT IGNORE INTO triathlons (name, type, location, event_date, description, max_participants, registration_deadline) VALUES
('Triathlon d\'Orléans', 'TROP', 'Orléans', '2026-06-26', 'Triathlon Olympique - Distance complète', 200, '2026-06-15'),
('Triathlon de Tours', 'XS', 'Tours', '2026-07-15', 'Format court - Idéal pour débutants', 100, '2026-07-01'),
('Triathlon du Val de Loire', 'S', 'Blois', '2026-08-05', 'Distance sprint - Paysages magnifiques', 150, '2026-07-20'),
('Triathlon de Chartres', 'M', 'Chartres', '2026-08-25', 'Distance moyenne - Challenge technique', 120, '2026-08-10'),
('Aquathlon de Printemps', 'AQ', 'Orléans', '2026-04-12', 'Aquathlon printemps - Natation + Course', 80, '2026-04-01');

-- Inscriptions
INSERT IGNORE INTO registrations (triathlon_id, licencie_id, status) VALUES
(1, 1, 'confirmed'),
(1, 2, 'confirmed'),
(2, 3, 'pending'),
(3, 4, 'confirmed'),
(4, 5, 'pending'),
(1, 6, 'confirmed');
