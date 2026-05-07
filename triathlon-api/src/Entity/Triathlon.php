<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
#[ORM\Table(name: 'triathlons')]
class Triathlon
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['triathlon:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    #[Assert\NotBlank]
    #[Groups(['triathlon:read', 'triathlon:write'])]
    private string $name = '';

    #[ORM\Column(length: 10)]
    #[Assert\NotBlank]
    #[Groups(['triathlon:read', 'triathlon:write'])]
    private string $type = '';

    #[ORM\Column(length: 100)]
    #[Assert\NotBlank]
    #[Groups(['triathlon:read', 'triathlon:write'])]
    private string $location = '';

    #[ORM\Column(type: 'date')]
    #[Assert\NotNull]
    #[Groups(['triathlon:read', 'triathlon:write'])]
    private \DateTimeInterface $eventDate;

    #[ORM\Column(type: 'text', nullable: true)]
    #[Groups(['triathlon:read', 'triathlon:write'])]
    private ?string $description = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['triathlon:read', 'triathlon:write'])]
    private ?int $maxParticipants = null;

    #[ORM\Column(type: 'date', nullable: true)]
    #[Groups(['triathlon:read', 'triathlon:write'])]
    private ?\DateTimeInterface $registrationDeadline = null;

    #[ORM\Column]
    #[Groups(['triathlon:read'])]
    private \DateTimeImmutable $createdAt;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->eventDate = new \DateTimeImmutable();
    }

    public function getId(): ?int { return $this->id; }
    public function getName(): string { return $this->name; }
    public function setName(string $n): static { $this->name = $n; return $this; }
    public function getType(): string { return $this->type; }
    public function setType(string $t): static { $this->type = $t; return $this; }
    public function getLocation(): string { return $this->location; }
    public function setLocation(string $l): static { $this->location = $l; return $this; }
    public function getEventDate(): \DateTimeInterface { return $this->eventDate; }
    public function setEventDate(\DateTimeInterface $d): static { $this->eventDate = $d; return $this; }
    public function getDescription(): ?string { return $this->description; }
    public function setDescription(?string $d): static { $this->description = $d; return $this; }
    public function getMaxParticipants(): ?int { return $this->maxParticipants; }
    public function setMaxParticipants(?int $m): static { $this->maxParticipants = $m; return $this; }
    public function getRegistrationDeadline(): ?\DateTimeInterface { return $this->registrationDeadline; }
    public function setRegistrationDeadline(?\DateTimeInterface $d): static { $this->registrationDeadline = $d; return $this; }
    public function getCreatedAt(): \DateTimeImmutable { return $this->createdAt; }
}
