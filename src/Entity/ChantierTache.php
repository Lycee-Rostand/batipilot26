<?php

namespace App\Entity;

use App\Repository\ChantierTacheRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ChantierTacheRepository::class)]
class ChantierTache
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(nullable: true)]
    private ?bool $valBoolean = null;

    #[ORM\Column(nullable: true)]
    private ?int $valEntier = null;

    #[ORM\Column(nullable: true)]
    private ?float $valFloat = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $valTexte = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTime $valDate = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTime $valDateHeure = null;

    #[ORM\ManyToOne(inversedBy: 'chantierTaches')]
    private ?Tache $tache = null;

    #[ORM\ManyToOne(inversedBy: 'chantierTaches')]
    private ?Chantier $chantier = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function isValBoolean(): ?bool
    {
        return $this->valBoolean;
    }

    public function setValBoolean(?bool $valBoolean): static
    {
        $this->valBoolean = $valBoolean;

        return $this;
    }

    public function getValEntier(): ?int
    {
        return $this->valEntier;
    }

    public function setValEntier(?int $valEntier): static
    {
        $this->valEntier = $valEntier;

        return $this;
    }

    public function getValFloat(): ?float
    {
        return $this->valFloat;
    }

    public function setValFloat(?float $valFloat): static
    {
        $this->valFloat = $valFloat;

        return $this;
    }

    public function getValTexte(): ?string
    {
        return $this->valTexte;
    }

    public function setValTexte(?string $valTexte): static
    {
        $this->valTexte = $valTexte;

        return $this;
    }

    public function getValDate(): ?\DateTime
    {
        return $this->valDate;
    }

    public function setValDate(?\DateTime $valDate): static
    {
        $this->valDate = $valDate;

        return $this;
    }

    public function getValDateHeure(): ?\DateTime
    {
        return $this->valDateHeure;
    }

    public function setValDateHeure(?\DateTime $valDateHeure): static
    {
        $this->valDateHeure = $valDateHeure;

        return $this;
    }

    public function getTache(): ?Tache
    {
        return $this->tache;
    }

    public function setTache(?Tache $tache): static
    {
        $this->tache = $tache;

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
}
