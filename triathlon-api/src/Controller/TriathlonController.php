<?php

namespace App\Controller;

use App\Entity\Triathlon;
use App\Entity\TypeTriathlon;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api/triathlons', name: 'api_triathlons_')]
class TriathlonController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $em,
        private ValidatorInterface $validator,
    ) {}

    #[Route('', name: 'list', methods: ['GET'])]
    public function list(Request $request): JsonResponse
    {
        $qb = $this->em->getRepository(Triathlon::class)->createQueryBuilder('t');

        if ($request->query->get('upcoming')) {
            $qb->andWhere('t.eventDate >= :today')->setParameter('today', new \DateTime('today'));
        }

        if ($type = $request->query->get('type')) {
            $qb->andWhere('t.type = :type')->setParameter('type', $type);
        }

        $triathlons = $qb->orderBy('t.eventDate', 'ASC')->getQuery()->getResult();

        return $this->json($triathlons, 200, [], ['groups' => ['triathlon:read']]);
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(int $id): JsonResponse
    {
        $triathlon = $this->em->getRepository(Triathlon::class)->find($id);
        if (!$triathlon) {
            return $this->json(['error' => 'Triathlon introuvable.'], 404);
        }

        return $this->json($triathlon, 200, [], ['groups' => ['triathlon:read']]);
    }

    #[Route('', name: 'create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $triathlon = $this->hydrate(new Triathlon(), json_decode($request->getContent(), true) ?? []);

        $errors = $this->validator->validate($triathlon);
        if (count($errors) > 0) {
            $messages = [];
            foreach ($errors as $e) $messages[] = $e->getMessage();
            return $this->json(['errors' => $messages], 422);
        }

        $this->em->persist($triathlon);
        $this->em->flush();

        return $this->json($triathlon, 201, [], ['groups' => ['triathlon:read']]);
    }

    #[Route('/{id}', name: 'update', methods: ['PUT', 'PATCH'])]
    public function update(int $id, Request $request): JsonResponse
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $triathlon = $this->em->getRepository(Triathlon::class)->find($id);
        if (!$triathlon) {
            return $this->json(['error' => 'Triathlon introuvable.'], 404);
        }

        $this->hydrate($triathlon, json_decode($request->getContent(), true) ?? []);
        $this->em->flush();

        return $this->json($triathlon, 200, [], ['groups' => ['triathlon:read']]);
    }

    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(int $id): JsonResponse
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $triathlon = $this->em->getRepository(Triathlon::class)->find($id);
        if (!$triathlon) {
            return $this->json(['error' => 'Triathlon introuvable.'], 404);
        }

        $this->em->remove($triathlon);
        $this->em->flush();

        return $this->json(null, 204);
    }

    #[Route('/types', name: 'types', methods: ['GET'])]
    public function types(): JsonResponse
    {
        $types = $this->em->getRepository(TypeTriathlon::class)->findAll();
        return $this->json($types, 200, [], ['groups' => ['type:read']]);
    }

    private function hydrate(Triathlon $t, array $data): Triathlon
    {
        if (isset($data['name']))                  $t->setName($data['name']);
        if (isset($data['type']))                  $t->setType($data['type']);
        if (isset($data['location']))              $t->setLocation($data['location']);
        if (isset($data['event_date']))            $t->setEventDate(new \DateTime($data['event_date']));
        if (isset($data['description']))           $t->setDescription($data['description']);
        if (isset($data['max_participants']))      $t->setMaxParticipants($data['max_participants'] ? (int)$data['max_participants'] : null);
        if (isset($data['registration_deadline'])) $t->setRegistrationDeadline($data['registration_deadline'] ? new \DateTime($data['registration_deadline']) : null);
        return $t;
    }
}
