<?php

namespace App\Entity;

use App\Repository\TarifStructureRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TarifStructureRepository::class)]
class TarifStructure
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 15, scale: 2)]
    private ?string $surfaceMin = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 15, scale: 2)]
    private ?string $surfaceMax = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 15, scale: 2)]
    private ?string $montantHT = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSurfaceMin(): ?string
    {
        return $this->surfaceMin;
    }

    public function setSurfaceMin(string $surfaceMin): static
    {
        $this->surfaceMin = $surfaceMin;

        return $this;
    }

    public function getSurfaceMax(): ?string
    {
        return $this->surfaceMax;
    }

    public function setSurfaceMax(string $surfaceMax): static
    {
        $this->surfaceMax = $surfaceMax;

        return $this;
    }

    public function getMontantHT(): ?string
    {
        return $this->montantHT;
    }

    public function setMontantHT(string $montantHT): static
    {
        $this->montantHT = $montantHT;

        return $this;
    }
}
