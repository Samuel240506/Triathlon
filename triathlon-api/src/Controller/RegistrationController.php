<?php

namespace App\Controller;

use App\Entity\Licencie;
use App\Entity\Registration;
use App\Entity\Triathlon;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/registrations', name: 'api_registrations_')]
class RegistrationController extends AbstractController
{
    public function __construct(private EntityManagerInterface $em) {}

    #[Route('', name: 'list', methods: ['GET'])]
    public function list(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $this->getUser();
        $qb   = $this->em->getRepository(Registration::class)->createQueryBuilder('r');

        if ($user->getRole() !== 'admin') {
            $licencieIds = $this->em->getRepository(Licencie::class)
                ->createQueryBuilder('l')
                ->select('l.id')
                ->where('l.clubId = :club')
                ->setParameter('club', $user->getClubId())
                ->getQuery()->getSingleColumnResult();

            if (empty($licencieIds)) return $this->json([]);
            $qb->andWhere('r.licencieId IN (:ids)')->setParameter('ids', $licencieIds);
        }

        if ($triathlonId = $request->query->get('triathlon_id')) {
            $qb->andWhere('r.triathlonId = :tid')->setParameter('tid', $triathlonId);
        }
        if ($licencieId = $request->query->get('licencie_id')) {
            $qb->andWhere('r.licencieId = :lid')->setParameter('lid', $licencieId);
        }
        if ($status = $request->query->get('status')) {
            $qb->andWhere('r.status = :s')->setParameter('s', $status);
        }

        $rows = $qb->getQuery()->getResult();
        return $this->json($this->enrichRegistrations($rows));
    }

    #[Route('', name: 'create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true) ?? [];

        $triathlonId = (int)($data['triathlon_id'] ?? 0);
        $licencieId  = (int)($data['licencie_id'] ?? 0);

        if (!$triathlonId || !$licencieId) {
            return $this->json(['error' => 'triathlon_id et licencie_id sont requis.'], 400);
        }

        $triathlon = $this->em->getRepository(Triathlon::class)->find($triathlonId);
        if (!$triathlon) return $this->json(['error' => 'Triathlon introuvable.'], 404);

        $licencie = $this->em->getRepository(Licencie::class)->find($licencieId);
        if (!$licencie) return $this->json(['error' => 'Licencié introuvable.'], 404);

        /** @var User $user */
        $user = $this->getUser();
        if ($user->getRole() !== 'admin' && $licencie->getClubId() !== $user->getClubId()) {
            return $this->json(['error' => 'Accès refusé.'], 403);
        }

        $existing = $this->em->getRepository(Registration::class)
            ->findOneBy(['triathlonId' => $triathlonId, 'licencieId' => $licencieId]);
        if ($existing) return $this->json(['error' => 'Inscription déjà existante.'], 409);

        $registration = new Registration();
        $registration->setTriathlonId($triathlonId)->setLicencieId($licencieId)->setStatus('pending');

        $this->em->persist($registration);
        $this->em->flush();

        return $this->json($this->enrichRegistration($registration), 201);
    }

    #[Route('/{id}/status', name: 'status', methods: ['PATCH'])]
    public function updateStatus(int $id, Request $request): JsonResponse
    {
        $registration = $this->em->getRepository(Registration::class)->find($id);
        if (!$registration) return $this->json(['error' => 'Inscription introuvable.'], 404);

        $data   = json_decode($request->getContent(), true) ?? [];
        $status = $data['status'] ?? '';
        if (!in_array($status, ['pending', 'confirmed', 'cancelled'])) {
            return $this->json(['error' => 'Statut invalide.'], 400);
        }

        $registration->setStatus($status);
        $this->em->flush();

        return $this->json($this->enrichRegistration($registration));
    }

    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(int $id): JsonResponse
    {
        $registration = $this->em->getRepository(Registration::class)->find($id);
        if (!$registration) return $this->json(['error' => 'Inscription introuvable.'], 404);

        $this->em->remove($registration);
        $this->em->flush();

        return $this->json(null, 204);
    }

    // ── helpers ─────────────────────────────────────────────────────────────

    private function enrichRegistration(Registration $r): array
    {
        $triathlon = $this->em->getRepository(Triathlon::class)->find($r->getTriathlonId());
        $licencie  = $this->em->getRepository(Licencie::class)->find($r->getLicencieId());

        return [
            'id'                => $r->getId(),
            'status'            => $r->getStatus(),
            'registration_date' => $r->getRegistrationDate()->format('Y-m-d H:i:s'),
            'triathlon' => $triathlon ? [
                'id'         => $triathlon->getId(),
                'name'       => $triathlon->getName(),
                'type'       => $triathlon->getType(),
                'location'   => $triathlon->getLocation(),
                'event_date' => $triathlon->getEventDate()->format('Y-m-d'),
            ] : null,
            'licencie' => $licencie ? [
                'id'             => $licencie->getId(),
                'license_number' => $licencie->getLicenseNumber(),
                'first_name'     => $licencie->getFirstName(),
                'last_name'      => $licencie->getLastName(),
            ] : null,
        ];
    }

    private function enrichRegistrations(array $registrations): array
    {
        return array_map(fn($r) => $this->enrichRegistration($r), $registrations);
    }
}
