<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api', name: 'api_')]
class AuthController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $em,
        private JWTTokenManagerInterface $jwt,
        private UserPasswordHasherInterface $hasher,
    ) {}

    #[Route('/login', name: 'login', methods: ['POST'])]
    public function login(Request $request): JsonResponse
    {
        $data     = json_decode($request->getContent(), true);
        $email    = trim($data['email'] ?? '');
        $password = trim($data['password'] ?? '');

        if (!$email || !$password) {
            return $this->json(['error' => 'Email et mot de passe requis.'], 400);
        }

        $user = $this->em->getRepository(User::class)->findOneBy(['email' => $email]);

        if (!$user) {
            return $this->json(['error' => 'Identifiants incorrects.'], 401);
        }

        $stored = $user->getPassword();
        $valid  = false;

        if (str_starts_with($stored, '$2y$') || str_starts_with($stored, '$argon')) {
            $valid = $this->hasher->isPasswordValid($user, $password);
        } elseif (strlen($stored) === 32) {
            // legacy MD5
            $valid = md5($password) === $stored;
            if ($valid) {
                // rehash to bcrypt
                $user->setPassword($this->hasher->hashPassword($user, $password));
                $this->em->flush();
            }
        }

        if (!$valid) {
            return $this->json(['error' => 'Identifiants incorrects.'], 401);
        }

        $token = $this->jwt->create($user);

        return $this->json([
            'token'   => $token,
            'user'    => [
                'id'      => $user->getId(),
                'name'    => $user->getName(),
                'email'   => $user->getEmail(),
                'role'    => $user->getRole(),
                'club_id' => $user->getClubId(),
            ],
        ]);
    }

    #[Route('/me', name: 'me', methods: ['GET'])]
    public function me(): JsonResponse
    {
        /** @var User $user */
        $user = $this->getUser();
        if (!$user) {
            return $this->json(['error' => 'Non authentifié.'], 401);
        }

        return $this->json([
            'id'      => $user->getId(),
            'name'    => $user->getName(),
            'email'   => $user->getEmail(),
            'role'    => $user->getRole(),
            'club_id' => $user->getClubId(),
        ]);
    }
}
