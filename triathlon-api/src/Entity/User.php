<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity]
#[ORM\Table(name: 'users')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['user:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    #[Groups(['user:read'])]
    private string $name = '';

    #[ORM\Column(length: 100, unique: true)]
    #[Groups(['user:read'])]
    private string $email = '';

    #[ORM\Column(length: 255)]
    private string $password = '';

    #[ORM\Column(length: 20, enumType: null)]
    #[Groups(['user:read'])]
    private string $role = 'club_manager';

    #[ORM\Column(nullable: true)]
    #[Groups(['user:read'])]
    private ?int $clubId = null;

    #[ORM\Column(type: 'json', nullable: true)]
    private ?array $preferences = null;

    public function getId(): ?int { return $this->id; }
    public function getName(): string { return $this->name; }
    public function setName(string $name): static { $this->name = $name; return $this; }
    public function getEmail(): string { return $this->email; }
    public function setEmail(string $email): static { $this->email = $email; return $this; }
    public function getPassword(): string { return $this->password; }
    public function setPassword(string $password): static { $this->password = $password; return $this; }
    public function getRole(): string { return $this->role; }
    public function setRole(string $role): static { $this->role = $role; return $this; }
    public function getClubId(): ?int { return $this->clubId; }
    public function setClubId(?int $clubId): static { $this->clubId = $clubId; return $this; }
    public function getPreferences(): ?array { return $this->preferences; }
    public function setPreferences(?array $preferences): static { $this->preferences = $preferences; return $this; }

    public function getRoles(): array { return ['ROLE_USER', $this->role === 'admin' ? 'ROLE_ADMIN' : 'ROLE_CLUB_MANAGER']; }
    public function getUserIdentifier(): string { return $this->email; }
    public function eraseCredentials(): void {}
}
