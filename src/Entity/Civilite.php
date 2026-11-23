<?php

namespace App\Entity;

use App\Repository\CiviliteRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CiviliteRepository::class)]
class Civilite
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $nom_court = null;

    #[ORM\Column(length: 50)]
    private ?string $nom_long = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomCourt(): ?string
    {
        return $this->nom_court;
    }

    public function setNomCourt(string $nom_court): static
    {
        $this->nom_court = $nom_court;

        return $this;
    }

    public function getNomLong(): ?string
    {
        return $this->nom_long;
    }

    public function setNomLong(string $nom_long): static
    {
        $this->nom_long = $nom_long;

        return $this;
    }
}
