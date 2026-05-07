<?php

namespace App\Controller;

use App\Entity\Club;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api/clubs', name: 'api_clubs_')]
class ClubController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $em,
        private SerializerInterface $serializer,
        private ValidatorInterface $validator,
    ) {}

    #[Route('', name: 'list', methods: ['GET'])]
    public function list(): JsonResponse
    {
        /** @var User $user */
        $user  = $this->getUser();
        $repo  = $this->em->getRepository(Club::class);

        if ($user->getRole() === 'admin') {
            $clubs = $repo->findAll();
        } else {
            $clubs = $user->getClubId() ? [$repo->find($user->getClubId())] : [];
            $clubs = array_filter($clubs);
        }

        return $this->json($clubs, 200, [], ['groups' => ['club:read']]);
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(int $id): JsonResponse
    {
        $club = $this->em->getRepository(Club::class)->find($id);
        if (!$club) {
            return $this->json(['error' => 'Club introuvable.'], 404);
        }

        $this->denyAccessUnlessGranted('club_view', $club);

        return $this->json($club, 200, [], ['groups' => ['club:read']]);
    }

    #[Route('', name: 'create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $club = $this->serializer->deserialize($request->getContent(), Club::class, 'json', ['groups' => ['club:write']]);

        $errors = $this->validator->validate($club);
        if (count($errors) > 0) {
            $messages = [];
            foreach ($errors as $e) $messages[] = $e->getMessage();
            return $this->json(['errors' => $messages], 422);
        }

        $this->em->persist($club);
        $this->em->flush();

        return $this->json($club, 201, [], ['groups' => ['club:read']]);
    }

    #[Route('/{id}', name: 'update', methods: ['PUT', 'PATCH'])]
    public function update(int $id, Request $request): JsonResponse
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $club = $this->em->getRepository(Club::class)->find($id);
        if (!$club) {
            return $this->json(['error' => 'Club introuvable.'], 404);
        }

        $data = json_decode($request->getContent(), true) ?? [];
        if (isset($data['name']))    $club->setName($data['name']);
        if (isset($data['address'])) $club->setAddress($data['address']);
        if (isset($data['city']))    $club->setCity($data['city']);
        if (isset($data['phone']))   $club->setPhone($data['phone']);
        if (isset($data['email']))   $club->setEmail($data['email']);

        $this->em->flush();

        return $this->json($club, 200, [], ['groups' => ['club:read']]);
    }

    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(int $id): JsonResponse
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $club = $this->em->getRepository(Club::class)->find($id);
        if (!$club) {
            return $this->json(['error' => 'Club introuvable.'], 404);
        }

        $this->em->remove($club);
        $this->em->flush();

        return $this->json(null, 204);
    }
}
