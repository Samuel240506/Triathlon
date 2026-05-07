-- ============================================================
--  FFTRI - Script de creation de la base de donnees
--  Federation Francaise de Triathlon
--  Version : 1.1
--
--  Import phpMyAdmin : Importer > Choisir ce fichier > Executer
--  Import ligne de commande : mysql -u root -p < triathlon_create.sql
--
--  ATTENTION : supprime et recree toutes les tables existantes.
-- ============================================================

SET NAMES utf8mb4;
SET CHARACTER SET utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ───────────────────────────────────────────────
-- BASE DE DONNEES
-- ───────────────────────────────────────────────

DROP DATABASE IF EXISTS triathlon;

CREATE DATABASE triathlon
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE triathlon;

-- ───────────────────────────────────────────────
-- SUPPRESSION DES TABLES (ordre inverse des FK)
-- ───────────────────────────────────────────────

DROP TABLE IF EXISTS registrations;
DROP TABLE IF EXISTS triathlons;
DROP TABLE IF EXISTS TypeTriathlon;
DROP TABLE IF EXISTS licencies;
DROP TABLE IF EXISTS users;
DROP TABLE IF EXISTS clubs;

-- ───────────────────────────────────────────────
-- TABLE : clubs
-- ───────────────────────────────────────────────

CREATE TABLE clubs (
    id         INT          NOT NULL AUTO_INCREMENT,
    name       VARCHAR(100) NOT NULL,
    address    VARCHAR(255)     NULL,
    city       VARCHAR(100) NOT NULL,
    phone      VARCHAR(20)      NULL,
    email      VARCHAR(100)     NULL,
    created_at TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    PRIMARY KEY (id),
    UNIQUE KEY uk_clubs_name (name)

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ───────────────────────────────────────────────
-- TABLE : users
-- ───────────────────────────────────────────────

CREATE TABLE users (
    id          INT          NOT NULL AUTO_INCREMENT,
    name        VARCHAR(100) NOT NULL,
    email       VARCHAR(100) NOT NULL,
    password    VARCHAR(255) NOT NULL,
    role        ENUM('admin','club_manager') NOT NULL DEFAULT 'club_manager',
    club_id     INT              NULL,
    preferences JSON             NULL,
    created_at  TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at  TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    PRIMARY KEY (id),
    UNIQUE KEY uk_users_email (email),
    KEY        idx_users_club  (club_id),

    CONSTRAINT fk_users_club
        FOREIGN KEY (club_id) REFERENCES clubs(id)
        ON DELETE SET NULL
        ON UPDATE CASCADE

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ───────────────────────────────────────────────
-- TABLE : TypeTriathlon
-- ───────────────────────────────────────────────

CREATE TABLE TypeTriathlon (
    codeType VARCHAR(10)  NOT NULL,
    libelle  VARCHAR(150) NOT NULL,

    PRIMARY KEY (codeType)

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ───────────────────────────────────────────────
-- TABLE : licencies
-- ───────────────────────────────────────────────

CREATE TABLE licencies (
    id             INT          NOT NULL AUTO_INCREMENT,
    license_number VARCHAR(20)  NOT NULL,
    first_name     VARCHAR(50)  NOT NULL,
    last_name      VARCHAR(50)  NOT NULL,
    birth_date     DATE             NULL,
    gender         ENUM('M','F') NOT NULL DEFAULT 'M',
    category       ENUM('Junior','Senior','Vétéran') NOT NULL DEFAULT 'Senior',
    license_type   ENUM('Compétition','Loisir')      NOT NULL DEFAULT 'Loisir',
    club_id        INT          NOT NULL,
    email          VARCHAR(100)     NULL,
    phone          VARCHAR(20)      NULL,
    created_at     TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at     TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    PRIMARY KEY (id),
    UNIQUE KEY uk_license_number (license_number),
    KEY        idx_licencies_club (club_id),
    KEY        idx_licencies_cat  (category),

    CONSTRAINT fk_licencies_club
        FOREIGN KEY (club_id) REFERENCES clubs(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ───────────────────────────────────────────────
-- TABLE : triathlons
-- ───────────────────────────────────────────────

CREATE TABLE triathlons (
    id                    INT          NOT NULL AUTO_INCREMENT,
    name                  VARCHAR(100) NOT NULL,
    type                  VARCHAR(10)  NOT NULL,
    location              VARCHAR(100) NOT NULL,
    event_date            DATE         NOT NULL,
    description           TEXT             NULL,
    max_participants      INT UNSIGNED     NULL,
    registration_deadline DATE             NULL,
    created_at            TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at            TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    PRIMARY KEY (id),
    KEY idx_triathlons_date (event_date),
    KEY idx_triathlons_type (type),

    CONSTRAINT fk_triathlons_type
        FOREIGN KEY (type) REFERENCES TypeTriathlon(codeType)
        ON DELETE RESTRICT
        ON UPDATE CASCADE

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ───────────────────────────────────────────────
-- TABLE : registrations
-- ───────────────────────────────────────────────

CREATE TABLE registrations (
    id                INT       NOT NULL AUTO_INCREMENT,
    triathlon_id      INT       NOT NULL,
    licencie_id       INT       NOT NULL,
    registration_date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    status            ENUM('pending','confirmed','cancelled') NOT NULL DEFAULT 'pending',

    PRIMARY KEY (id),
    UNIQUE KEY uk_registration          (triathlon_id, licencie_id),
    KEY        idx_reg_triathlon        (triathlon_id),
    KEY        idx_reg_licencie         (licencie_id),
    KEY        idx_reg_status           (status),

    CONSTRAINT fk_reg_triathlon
        FOREIGN KEY (triathlon_id) REFERENCES triathlons(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE,

    CONSTRAINT fk_reg_licencie
        FOREIGN KEY (licencie_id) REFERENCES licencies(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;

-- ============================================================
-- DONNEES DE REFERENCE : TypeTriathlon
-- ============================================================

INSERT INTO TypeTriathlon (codeType, libelle) VALUES
('XS',   'Super Sprint - Nat. 0,25 km / Velo 6,5 km / Course 1,6 km'),
('S',    'Sprint - Nat. 0,75 km / Velo 20 km / Course 5 km'),
('M',    'Olympique - Nat. 1,5 km / Velo 40 km / Course 10 km'),
('TROP', 'Triathlon Olympique FFTRI'),
('L',    'Half Iron - Nat. 1,9 km / Velo 90 km / Course 21,1 km'),
('XL',   'Iron Man - Nat. 3,8 km / Velo 180 km / Course 42,2 km'),
('DU',   'Duathlon - Course + Velo + Course'),
('AQ',   'Aquathlon - Natation + Course a pied');

-- ============================================================
-- DONNEES DE TEST : Clubs (id 1 a 5)
-- ============================================================

INSERT INTO clubs (name, address, city, phone, email) VALUES
('Triathlon Club Orleans',  '123 Rue de la Loire',         'Orleans',  '02 38 42 00 00', 'contact@tco.fr'),
('AS Triathlon Tours',      '456 Avenue de la Republique', 'Tours',    '02 47 20 00 00', 'info@asttours.fr'),
('Triathlon Club Blois',    '789 Boulevard de la Liberte', 'Blois',    '02 54 78 00 00', 'club@tc-blois.fr'),
('US Triathlon Chartres',   '321 Rue des Sports',          'Chartres', '02 37 30 00 00', 'contact@ust-chartres.fr'),
('Triathlon Bourges',       '15 Allee du Stade',           'Bourges',  '02 48 65 00 00', 'tri.bourges@email.fr');

-- ============================================================
-- DONNEES DE TEST : Utilisateurs
--   Mot de passe de tous les comptes : password
--   Le hash MD5 est converti en bcrypt lors de la 1ere connexion.
-- ============================================================

INSERT INTO users (name, email, password, role, club_id) VALUES
('Admin FFTRI',          'admin@fftri.com',          MD5('password'), 'admin',        NULL),
('Responsable Orleans',  'resp.orleans@fftri.com',   MD5('password'), 'club_manager', 1),
('Responsable Tours',    'resp.tours@fftri.com',     MD5('password'), 'club_manager', 2),
('Responsable Blois',    'resp.blois@fftri.com',     MD5('password'), 'club_manager', 3),
('Responsable Chartres', 'resp.chartres@fftri.com',  MD5('password'), 'club_manager', 4);

-- ============================================================
-- DONNEES DE TEST : Licencies (id 1 a 12)
--   category  : Junior | Senior | Veteran
--   license_type : Competition | Loisir
-- ============================================================

INSERT INTO licencies
    (license_number, first_name, last_name, birth_date,   gender, category,  license_type,  club_id, email,                       phone)
VALUES
-- Club 1 : Orleans
('FFTRI2024001', 'Pierre',  'Martin',   '1990-05-15', 'M', 'Senior',  'Compétition', 1, 'pierre.martin@email.com',   '06 12 34 56 78'),
('FFTRI2024002', 'Lucas',   'Petit',    '1998-07-14', 'M', 'Senior',  'Loisir',      1, 'lucas.petit@email.com',     '06 56 78 90 12'),
('FFTRI2024003', 'Camille', 'Renard',   '2001-03-22', 'F', 'Senior',  'Compétition', 1, 'camille.renard@email.com',  '06 11 22 33 44'),
-- Club 2 : Tours
('FFTRI2024004', 'Marie',   'Dubois',   '1985-08-22', 'F', 'Vétéran', 'Loisir',      2, 'marie.dubois@email.com',    '06 23 45 67 89'),
('FFTRI2024005', 'Emma',    'Leroy',    '2003-12-01', 'F', 'Junior',  'Compétition', 2, 'emma.leroy@email.com',      '06 67 89 01 23'),
('FFTRI2024006', 'Thomas',  'Girard',   '1995-09-10', 'M', 'Senior',  'Compétition', 2, 'thomas.girard@email.com',   '06 34 56 12 78'),
-- Club 3 : Blois
('FFTRI2024007', 'Jean',    'Moreau',   '2005-03-10', 'M', 'Junior',  'Compétition', 3, 'jean.moreau@email.com',     '06 34 56 78 90'),
('FFTRI2024008', 'Lucie',   'Simon',    '1988-11-05', 'F', 'Vétéran', 'Loisir',      3, 'lucie.simon@email.com',     '06 45 23 67 89'),
-- Club 4 : Chartres
('FFTRI2024009', 'Sophie',  'Bernard',  '1992-11-30', 'F', 'Senior',  'Compétition', 4, 'sophie.bernard@email.com',  '06 45 67 89 01'),
('FFTRI2024010', 'Antoine', 'Lambert',  '2004-06-18', 'M', 'Junior',  'Loisir',      4, 'antoine.lambert@email.com', '06 78 90 12 34'),
-- Club 5 : Bourges
('FFTRI2024011', 'Chloe',   'Fontaine', '1997-02-14', 'F', 'Senior',  'Compétition', 5, 'chloe.fontaine@email.com',  '06 89 01 23 45'),
('FFTRI2024012', 'Maxime',  'Dupont',   '2006-08-30', 'M', 'Junior',  'Compétition', 5, 'maxime.dupont@email.com',   '06 90 12 34 56');

-- ============================================================
-- DONNEES DE TEST : Triathlons (saison 2026, id 1 a 8)
-- ============================================================

INSERT INTO triathlons
    (name, type, location, event_date, description, max_participants, registration_deadline)
VALUES
('Triathlon Orleans',           'TROP', 'Orleans',  '2026-06-26', 'Triathlon Olympique sur la Loire',           200, '2026-06-12'),
('Triathlon Sprint Tours',      'S',    'Tours',    '2026-07-15', 'Format sprint en bord de Cher',              150, '2026-07-01'),
('Triathlon Val de Loire',      'S',    'Blois',    '2026-08-05', 'Sprint panoramique Chateaux de la Loire',    150, '2026-07-22'),
('Triathlon Chartres',          'M',    'Chartres', '2026-08-25', 'Distance olympique - challenge technique',   120, '2026-08-11'),
('Aquathlon Printemps Orleans', 'AQ',   'Orleans',  '2026-04-19', 'Natation + Course - ouverture de saison',    80, '2026-04-05'),
('Super Sprint Blois',          'XS',   'Blois',    '2026-05-10', 'Ideal pour les debutants et familles',       60, '2026-04-26'),
('Duathlon Bourges',            'DU',   'Bourges',  '2026-09-13', 'Course + Velo + Course - cloture saison',   100, '2026-08-30'),
('Half Iron de la Loire',       'L',    'Orleans',  '2026-10-04', 'La grande epreuve de la region Centre',      80, '2026-09-20');

-- ============================================================
-- DONNEES DE TEST : Inscriptions
-- ============================================================

INSERT INTO registrations (triathlon_id, licencie_id, status) VALUES
-- Triathlon Orleans (id=1)
(1,  1,  'confirmed'),
(1,  4,  'confirmed'),
(1,  11, 'confirmed'),
(1,  5,  'pending'),
-- Sprint Tours (id=2)
(2,  6,  'confirmed'),
(2,  3,  'confirmed'),
(2,  10, 'pending'),
-- Val de Loire (id=3)
(3,  9,  'confirmed'),
(3,  7,  'confirmed'),
-- Chartres (id=4)
(4,  2,  'pending'),
(4,  12, 'pending'),
-- Aquathlon Orleans (id=5)
(5,  1,  'confirmed'),
(5,  3,  'confirmed'),
-- Super Sprint Blois (id=6)
(6,  8,  'confirmed'),
(6,  10, 'confirmed'),
-- Half Iron (id=8)
(8,  1,  'pending'),
(8,  11, 'pending');

-- ============================================================
-- VERIFICATION FINALE
-- ============================================================

SELECT 'clubs'         AS table_name, COUNT(*) AS nb_lignes FROM clubs
UNION ALL
SELECT 'users',                        COUNT(*)              FROM users
UNION ALL
SELECT 'TypeTriathlon',                COUNT(*)              FROM TypeTriathlon
UNION ALL
SELECT 'licencies',                    COUNT(*)              FROM licencies
UNION ALL
SELECT 'triathlons',                   COUNT(*)              FROM triathlons
UNION ALL
SELECT 'registrations',                COUNT(*)              FROM registrations;
