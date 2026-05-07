<?php

namespace App\Controller;

use App\Entity\Licencie;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api/licencies', name: 'api_licencies_')]
class LicencieController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $em,
        private SerializerInterface $serializer,
        private ValidatorInterface $validator,
    ) {}

    #[Route('', name: 'list', methods: ['GET'])]
    public function list(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $this->getUser();
        $repo = $this->em->getRepository(Licencie::class);
        $qb   = $repo->createQueryBuilder('l');

        if ($user->getRole() !== 'admin') {
            $qb->andWhere('l.clubId = :club')->setParameter('club', $user->getClubId());
        } elseif ($clubId = $request->query->get('club_id')) {
            $qb->andWhere('l.clubId = :club')->setParameter('club', $clubId);
        }

        if ($category = $request->query->get('category')) {
            $qb->andWhere('l.category = :cat')->setParameter('cat', $category);
        }

        if ($q = $request->query->get('q')) {
            $qb->andWhere('l.firstName LIKE :q OR l.lastName LIKE :q OR l.licenseNumber LIKE :q')
               ->setParameter('q', "%$q%");
        }

        $licencies = $qb->orderBy('l.lastName')->getQuery()->getResult();

        return $this->json($licencies, 200, [], ['groups' => ['licencie:read']]);
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(int $id): JsonResponse
    {
        $licencie = $this->em->getRepository(Licencie::class)->find($id);
        if (!$licencie) {
            return $this->json(['error' => 'Licencié introuvable.'], 404);
        }

        /** @var User $user */
        $user = $this->getUser();
        if ($user->getRole() !== 'admin' && $licencie->getClubId() !== $user->getClubId()) {
            return $this->json(['error' => 'Accès refusé.'], 403);
        }

        return $this->json($licencie, 200, [], ['groups' => ['licencie:read']]);
    }

    #[Route('', name: 'create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $data     = json_decode($request->getContent(), true) ?? [];
        $licencie = new Licencie();

        $this->hydrate($licencie, $data);

        /** @var User $user */
        $user = $this->getUser();
        if ($user->getRole() !== 'admin') {
            $licencie->setClubId($user->getClubId() ?? 0);
        }

        $errors = $this->validator->validate($licencie);
        if (count($errors) > 0) {
            $messages = [];
            foreach ($errors as $e) $messages[] = $e->getMessage();
            return $this->json(['errors' => $messages], 422);
        }

        $this->em->persist($licencie);
        $this->em->flush();

        return $this->json($licencie, 201, [], ['groups' => ['licencie:read']]);
    }

    #[Route('/{id}', name: 'update', methods: ['PUT', 'PATCH'])]
    public function update(int $id, Request $request): JsonResponse
    {
        $licencie = $this->em->getRepository(Licencie::class)->find($id);
        if (!$licencie) {
            return $this->json(['error' => 'Licencié introuvable.'], 404);
        }

        /** @var User $user */
        $user = $this->getUser();
        if ($user->getRole() !== 'admin' && $licencie->getClubId() !== $user->getClubId()) {
            return $this->json(['error' => 'Accès refusé.'], 403);
        }

        $this->hydrate($licencie, json_decode($request->getContent(), true) ?? []);
        $this->em->flush();

        return $this->json($licencie, 200, [], ['groups' => ['licencie:read']]);
    }

    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(int $id): JsonResponse
    {
        $licencie = $this->em->getRepository(Licencie::class)->find($id);
        if (!$licencie) {
            return $this->json(['error' => 'Licencié introuvable.'], 404);
        }

        /** @var User $user */
        $user = $this->getUser();
        if ($user->getRole() !== 'admin' && $licencie->getClubId() !== $user->getClubId()) {
            return $this->json(['error' => 'Accès refusé.'], 403);
        }

        $this->em->remove($licencie);
        $this->em->flush();

        return $this->json(null, 204);
    }

    private function hydrate(Licencie $l, array $data): void
    {
        if (isset($data['license_number'])) $l->setLicenseNumber($data['license_number']);
        if (isset($data['first_name']))     $l->setFirstName($data['first_name']);
        if (isset($data['last_name']))      $l->setLastName($data['last_name']);
        if (isset($data['birth_date']))     $l->setBirthDate($data['birth_date'] ? new \DateTime($data['birth_date']) : null);
        if (isset($data['gender']))         $l->setGender($data['gender']);
        if (isset($data['category']))       $l->setCategory($data['category']);
        if (isset($data['license_type']))   $l->setLicenseType($data['license_type']);
        if (isset($data['club_id']))        $l->setClubId((int)$data['club_id']);
        if (isset($data['email']))          $l->setEmail($data['email']);
        if (isset($data['phone']))          $l->setPhone($data['phone']);
    }
}
