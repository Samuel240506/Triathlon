<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
#[ORM\Table(name: 'clubs')]
class Club
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['club:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    #[Assert\NotBlank]
    #[Groups(['club:read', 'club:write'])]
    private string $name = '';

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['club:read', 'club:write'])]
    private ?string $address = null;

    #[ORM\Column(length: 100)]
    #[Assert\NotBlank]
    #[Groups(['club:read', 'club:write'])]
    private string $city = '';

    #[ORM\Column(length: 20, nullable: true)]
    #[Groups(['club:read', 'club:write'])]
    private ?string $phone = null;

    #[ORM\Column(length: 100, nullable: true)]
    #[Groups(['club:read', 'club:write'])]
    private ?string $email = null;

    #[ORM\Column]
    #[Groups(['club:read'])]
    private \DateTimeImmutable $createdAt;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?int { return $this->id; }
    public function getName(): string { return $this->name; }
    public function setName(string $name): static { $this->name = $name; return $this; }
    public function getAddress(): ?string { return $this->address; }
    public function setAddress(?string $address): static { $this->address = $address; return $this; }
    public function getCity(): string { return $this->city; }
    public function setCity(string $city): static { $this->city = $city; return $this; }
    public function getPhone(): ?string { return $this->phone; }
    public function setPhone(?string $phone): static { $this->phone = $phone; return $this; }
    public function getEmail(): ?string { return $this->email; }
    public function setEmail(?string $email): static { $this->email = $email; return $this; }
    public function getCreatedAt(): \DateTimeImmutable { return $this->createdAt; }
}
