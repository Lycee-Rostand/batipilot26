<?php

namespace App\Entity;

use App\Repository\DevisModifRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DevisModifRepository::class)]
class DevisModif
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(nullable: true)]
    private ?int $version = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTime $dateModif = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $objet = null;

    #[ORM\ManyToOne]
    private ?Devis $Devis = null;

    #[ORM\ManyToOne(inversedBy: 'devis_modif_id')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Devis $devis = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getVersion(): ?int
    {
        return $this->version;
    }

    public function setVersion(?int $version): static
    {
        $this->version = $version;

        return $this;
    }

    public function getDateModif(): ?\DateTime
    {
        return $this->dateModif;
    }

    public function setDateModif(?\DateTime $dateModif): static
    {
        $this->dateModif = $dateModif;

        return $this;
    }

    public function getObjet(): ?string
    {
        return $this->objet;
    }

    public function setObjet(?string $objet): static
    {
        $this->objet = $objet;

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
}
