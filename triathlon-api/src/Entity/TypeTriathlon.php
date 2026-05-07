<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity]
#[ORM\Table(name: 'TypeTriathlon')]
class TypeTriathlon
{
    #[ORM\Id]
    #[ORM\Column(length: 10)]
    #[Groups(['type:read'])]
    private string $codeType = '';

    #[ORM\Column(length: 150)]
    #[Groups(['type:read'])]
    private string $libelle = '';

    public function getCodeType(): string { return $this->codeType; }
    public function setCodeType(string $c): static { $this->codeType = $c; return $this; }
    public function getLibelle(): string { return $this->libelle; }
    public function setLibelle(string $l): static { $this->libelle = $l; return $this; }
}
