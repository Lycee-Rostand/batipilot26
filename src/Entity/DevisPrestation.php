<?php

namespace App\Entity;

use App\Repository\DevisPrestationRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DevisPrestationRepository::class)]
class DevisPrestation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'devisPrestations')]
    private ?Devis $Devis = null;

    #[ORM\ManyToOne(inversedBy: 'devisPrestations')]
    private ?Prestation $Prestation = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getQuantite(): ?int
    {
        return $this->quantite;
    }

    public function setQuantite(?int $quantite): static
    {
        $this->quantite = $quantite;

        return $this;
    }

    public function getMontantHT(): ?float
    {
        return $this->montantHT;
    }

    public function setMontantHT(?float $montantHT): static
    {
        $this->montantHT = $montantHT;

        return $this;
    }

    public function getDevis(): ?Devis
    {
        return $this->Devis;
    }

    public function setDevis(?Devis $Devis): static
    {
        $this->Devis = $Devis;

        return $this;
    }

    public function getPrestation(): ?Prestation
    {
        return $this->Prestation;
    }

    public function setPrestation(?Prestation $Prestation): static
    {
        $this->Prestation = $Prestation;

        return $this;
    }

}
