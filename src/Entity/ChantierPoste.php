<?php

namespace App\Entity;

use App\Repository\ChantierPosteRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ChantierPosteRepository::class)]
class ChantierPoste
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    #[Assert\NotNull]
    #[Assert\PositiveOrZero]
    private ?float $montantVenduHT = null;

    #[ORM\Column]
    #[Assert\NotNull]
    #[Assert\PositiveOrZero]
    private ?float $montantFournituresHT = null;

    #[ORM\ManyToOne(inversedBy: 'chantierPostes')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull]
    private ?Chantier $chantier = null;

    #[ORM\ManyToOne(inversedBy: 'chantierPostes')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull]
    private ?Poste $poste = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMontantVenduHT(): ?float
    {
        return $this->montantVenduHT;
    }

    public function setMontantVenduHT(float $montantVenduHT): static
    {
        $this->montantVenduHT = $montantVenduHT;

        return $this;
    }

    public function getMontantFournituresHT(): ?float
    {
        return $this->montantFournituresHT;
    }

    public function setMontantFournituresHT(float $montantFournituresHT): static
    {
        $this->montantFournituresHT = $montantFournituresHT;

        return $this;
    }

    public function getChantier(): ?Chantier
    {
        return $this->chantier;
    }

    public function setChantier(?Chantier $chantier): static
    {
        $this->chantier = $chantier;

        return $this;
    }

    public function getPoste(): ?Poste
    {
        return $this->poste;
    }

    public function setPoste(?Poste $poste): static
    {
        $this->poste = $poste;

        return $this;
    }
}