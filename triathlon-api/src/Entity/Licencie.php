<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
#[ORM\Table(name: 'licencies')]
class Licencie
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['licencie:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 20, unique: true)]
    #[Assert\NotBlank]
    #[Groups(['licencie:read', 'licencie:write'])]
    private string $licenseNumber = '';

    #[ORM\Column(length: 50)]
    #[Assert\NotBlank]
    #[Groups(['licencie:read', 'licencie:write'])]
    private string $firstName = '';

    #[ORM\Column(length: 50)]
    #[Assert\NotBlank]
    #[Groups(['licencie:read', 'licencie:write'])]
    private string $lastName = '';

    #[ORM\Column(type: 'date', nullable: true)]
    #[Groups(['licencie:read', 'licencie:write'])]
    private ?\DateTimeInterface $birthDate = null;

    #[ORM\Column(length: 1)]
    #[Groups(['licencie:read', 'licencie:write'])]
    private string $gender = 'M';

    #[ORM\Column(length: 20)]
    #[Groups(['licencie:read', 'licencie:write'])]
    private string $category = 'Senior';

    #[ORM\Column(length: 20)]
    #[Groups(['licencie:read', 'licencie:write'])]
    private string $licenseType = 'Loisir';

    #[ORM\Column]
    #[Groups(['licencie:read', 'licencie:write'])]
    private int $clubId = 0;

    #[ORM\Column(length: 100, nullable: true)]
    #[Groups(['licencie:read', 'licencie:write'])]
    private ?string $email = null;

    #[ORM\Column(length: 20, nullable: true)]
    #[Groups(['licencie:read', 'licencie:write'])]
    private ?string $phone = null;

    public function getId(): ?int { return $this->id; }
    public function getLicenseNumber(): string { return $this->licenseNumber; }
    public function setLicenseNumber(string $n): static { $this->licenseNumber = $n; return $this; }
    public function getFirstName(): string { return $this->firstName; }
    public function setFirstName(string $n): static { $this->firstName = $n; return $this; }
    public function getLastName(): string { return $this->lastName; }
    public function setLastName(string $n): static { $this->lastName = $n; return $this; }
    public function getBirthDate(): ?\DateTimeInterface { return $this->birthDate; }
    public function setBirthDate(?\DateTimeInterface $d): static { $this->birthDate = $d; return $this; }
    public function getGender(): string { return $this->gender; }
    public function setGender(string $g): static { $this->gender = $g; return $this; }
    public function getCategory(): string { return $this->category; }
    public function setCategory(string $c): static { $this->category = $c; return $this; }
    public function getLicenseType(): string { return $this->licenseType; }
    public function setLicenseType(string $t): static { $this->licenseType = $t; return $this; }
    public function getClubId(): int { return $this->clubId; }
    public function setClubId(int $id): static { $this->clubId = $id; return $this; }
    public function getEmail(): ?string { return $this->email; }
    public function setEmail(?string $e): static { $this->email = $e; return $this; }
    public function getPhone(): ?string { return $this->phone; }
    public function setPhone(?string $p): static { $this->phone = $p; return $this; }
}
