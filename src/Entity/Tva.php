<?php

namespace App\Entity;

use App\Repository\TvaRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TvaRepository::class)]
class Tva
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $Nom = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 5, scale: 2)]
    private ?string $taux = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTime $dataValiditeDebut = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTime $dataValiditeFin = null;

    #[ORM\Column]
    private ?int $archive = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->Nom;
    }

    public function setNom(string $Nom): static
    {
        $this->Nom = $Nom;

        return $this;
    }

    public function getTaux(): ?string
    {
        return $this->taux;
    }

    public function setTaux(string $taux): static
    {
        $this->taux = $taux;

        return $this;
    }

    public function getDataValiditeDebut(): ?\DateTime
    {
        return $this->dataValiditeDebut;
    }

    public function setDataValiditeDebut(\DateTime $dataValiditeDebut): static
    {
        $this->dataValiditeDebut = $dataValiditeDebut;

        return $this;
    }

    public function getDataValiditeFin(): ?\DateTime
    {
        return $this->dataValiditeFin;
    }

    public function setDataValiditeFin(\DateTime $dataValiditeFin): static
    {
        $this->dataValiditeFin = $dataValiditeFin;

        return $this;
    }

    public function getArchive(): ?int
    {
        return $this->archive;
    }

    public function setArchive(int $archive): static
    {
        $this->archive = $archive;

        return $this;
    }
}
