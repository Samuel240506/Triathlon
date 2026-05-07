<?php
require_once __DIR__ . '/../core/Database.php';
require_once __DIR__ . '/../models/Club.php';
require_once __DIR__ . '/../models/Licencie.php';
require_once __DIR__ . '/../models/Triathlon.php';

class DashboardController {
	private $clubModel;
	private $licencieModel;
	private $triathlonModel;
	private $db;

	public function __construct() {
		$this->clubModel = new Club();
		$this->licencieModel = new Licencie();
		$this->triathlonModel = new Triathlon();
		// Utiliser le singleton Database qui retourne un objet PDO
		$this->db = Database::getConnection();
	}

	public function index() {
		$user = $_SESSION['user'];

		$role = $user['role'] ?? null;
		$clubId = $user['id_club'] ?? ($user['club_id'] ?? null);

		if ($role === 'admin') {
			// Dashboard Admin
			$data = [
				'clubs_count' => $this->clubModel->getTotalCount(),
				'licencies_count' => $this->licencieModel->getTotalCount(),
				'triathlons_count' => $this->triathlonModel->getUpcomingCount(),
				'recent_clubs' => $this->getRecentClubs()
			];
		} else {
			// Dashboard Responsable Club
			$data = [
			'licencies_count' => $this->licencieModel->getCountByClub($clubId),
			'registrations_count' => $this->getRegistrationsCount($clubId),
			//'results_count' => $this->getResultsCount($clubId), // Removed per user request
			'my_licencies' => $this->licencieModel->getByClub($clubId),
			'my_registrations' => $this->getMyRegistrations($clubId)
			];
		}

		return $data;
	}

	private function getRecentClubs() {
		$clubs = $this->clubModel->getAll();
		// Retourner les 5 derniers clubs (simulation)
		$recentClubs = array_slice($clubs, 0, 5);
		// Ajouter le nombre de licenciés pour chaque club
		foreach ($recentClubs as &$club) {
			$club['licencies_count'] = $this->clubModel->getLicenciesCount($club['id']);
		}
		return $recentClubs;
	}

	private function getMyRegistrations($clubId) {
		if (!$clubId) return [];

		// Try new schema: Inscription + Triathlon + Licence_Club + Athlete
		try {
			$sql = "SELECT t.nom AS triathlon_name, t.date_triathlon AS event_date, t.lieu AS location,
						 i.numDossard AS bib, i.forfait AS forfait, i.tempsNat AS tempsNat, i.tempsCycle AS tempsCycle, i.tempsCourse AS tempsCourse, i.classement AS classement, a.numLicence
					FROM Inscription i
					JOIN Triathlon t ON i.id_triathlon = t.id_triathlon
					JOIN Licence_Club lc ON lc.numLicence = i.numLicence
					LEFT JOIN Athlete a ON a.numLicence = i.numLicence
					WHERE lc.id_club = :club_id
					ORDER BY t.date_triathlon DESC";
			$stmt = $this->db->prepare($sql);
			$stmt->execute([':club_id' => $clubId]);
			$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
			$out = [];
			foreach ($rows as $r) {
				$status = 'Inscrit';
				if (!empty($r['forfait'])) $status = 'Forfait';
				elseif (!empty($r['classement'])) $status = 'Terminé';
				$out[] = array_merge($r, ['status' => $status]);
			}
			return $out;
		} catch (Exception $e) {
			// fallback to legacy registrations table
		}

		// Legacy fallback: registrations + triathlons + licencies
		try {
			$sql = "SELECT t.name as triathlon_name, t.event_date, t.location, r.status
					FROM registrations r
					JOIN triathlons t ON r.triathlon_id = t.id
					JOIN licencies l ON r.licencie_id = l.id
					WHERE l.club_id = :club_id
					ORDER BY t.event_date DESC";
			$stmt = $this->db->prepare($sql);
			$stmt->execute([':club_id' => $clubId]);
			$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
			return $rows ?: [];
		} catch (Exception $e) {
			return [];
		}
	}

	private function getRegistrationsCount($clubId) {
		if (!$clubId) return 0;
		// new schema - count only registrations with open status (not forfait, not finished)
		try {
			$sql = "SELECT COUNT(*) as count 
					FROM Inscription i 
					JOIN Licence_Club lc ON lc.numLicence = i.numLicence 
					WHERE lc.id_club = :club_id 
					AND (i.forfait IS NULL OR i.forfait = '') 
					AND i.classement IS NULL";
			$stmt = $this->db->prepare($sql);
			$stmt->execute([':club_id' => $clubId]);
			$r = $stmt->fetch(PDO::FETCH_ASSOC);
			return (int)($r['count'] ?? 0);
		} catch (Exception $e) {
			// fallback
		}
		// legacy
		try {
			$sql = "SELECT COUNT(*) as count 
					FROM registrations r 
					JOIN licencies l ON r.licencie_id = l.id 
					WHERE l.club_id = :club_id 
					AND r.status IN ('pending','confirmed')";
			$stmt = $this->db->prepare($sql);
			$stmt->execute([':club_id' => $clubId]);
			$r = $stmt->fetch(PDO::FETCH_ASSOC);
			return (int)($r['count'] ?? 0);
		} catch (Exception $e) {
			return 0;
		}
	}

	private function getResultsCount($clubId) {
		// Simulation / simple heuristic: count inscriptions with non-null tempsNat (finished)
		if (!$clubId) return 0;
		try {
			$sql = "SELECT COUNT(*) as count FROM Inscription i JOIN Licence_Club lc ON lc.numLicence = i.numLicence WHERE lc.id_club = :club_id AND i.tempsNat IS NOT NULL";
			$stmt = $this->db->prepare($sql);
			$stmt->execute([':club_id' => $clubId]);
			$r = $stmt->fetch(PDO::FETCH_ASSOC);
			return (int)($r['count'] ?? 0);
		} catch (Exception $e) {
			// fallback: fixed value as before
			return 12;
		}
	}
}
?>
