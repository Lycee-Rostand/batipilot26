<?php

namespace App\Entity;

use App\Repository\DevisDocumentRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DevisDocumentRepository::class)]
class DevisDocument
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTime $dateEnregistrement = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTime $dateModification = null;

    #[ORM\ManyToOne(inversedBy: 'devisDocuments')]
    private ?TypeDocument $TypeDocument = null;

    #[ORM\ManyToOne(inversedBy: 'devisDocuments')]
    private ?Devis $Devis = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateEnregistrement(): ?\DateTime
    {
        return $this->dateEnregistrement;
    }

    public function setDateEnregistrement(\DateTime $dateEnregistrement): static
    {
        $this->dateEnregistrement = $dateEnregistrement;

        return $this;
    }

    public function getDateModification(): ?\DateTime
    {
        return $this->dateModification;
    }

    public function setDateModification(\DateTime $dateModification): static
    {
        $this->dateModification = $dateModification;

        return $this;
    }

    public function getTypeDocument(): ?TypeDocument
    {
        return $this->TypeDocument;
    }

    public function setTypeDocument(?TypeDocument $TypeDocument): static
    {
        $this->TypeDocument = $TypeDocument;

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
