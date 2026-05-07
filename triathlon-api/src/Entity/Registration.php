<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
#[ORM\Table(name: 'registrations')]
#[ORM\UniqueConstraint(name: 'uk_registration', columns: ['triathlon_id', 'licencie_id'])]
class Registration
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['registration:read'])]
    private ?int $id = null;

    #[ORM\Column]
    #[Assert\NotNull]
    #[Groups(['registration:read', 'registration:write'])]
    private int $triathlonId = 0;

    #[ORM\Column]
    #[Assert\NotNull]
    #[Groups(['registration:read', 'registration:write'])]
    private int $licencieId = 0;

    #[ORM\Column(length: 20)]
    #[Groups(['registration:read', 'registration:write'])]
    private string $status = 'pending';

    #[ORM\Column]
    #[Groups(['registration:read'])]
    private \DateTimeImmutable $registrationDate;

    public function __construct()
    {
        $this->registrationDate = new \DateTimeImmutable();
    }

    public function getId(): ?int { return $this->id; }
    public function getTriathlonId(): int { return $this->triathlonId; }
    public function setTriathlonId(int $id): static { $this->triathlonId = $id; return $this; }
    public function getLicencieId(): int { return $this->licencieId; }
    public function setLicencieId(int $id): static { $this->licencieId = $id; return $this; }
    public function getStatus(): string { return $this->status; }
    public function setStatus(string $s): static { $this->status = $s; return $this; }
    public function getRegistrationDate(): \DateTimeImmutable { return $this->registrationDate; }
}
