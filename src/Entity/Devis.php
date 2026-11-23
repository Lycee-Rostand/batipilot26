<?php

namespace App\Entity;

use App\Repository\DevisRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DevisRepository::class)]
class Devis
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTime $dateEmission = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTime $dateValidite = null;

    #[ORM\Column(length: 15)]
    private ?string $refInterne = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTime $dateSignature = null;

    /**
     * @var Collection<int, DevisDocument>
     */
    #[ORM\OneToMany(targetEntity: DevisDocument::class, mappedBy: 'Devis')]
    private Collection $devisDocuments;

    public function __construct()
    {
        $this->devisDocuments = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getDateEmission(): ?\DateTime
    {
        return $this->dateEmission;
    }

    public function setDateEmission(\DateTime $dateEmission): static
    {
        $this->dateEmission = $dateEmission;

        return $this;
    }

    public function getDateValidite(): ?\DateTime
    {
        return $this->dateValidite;
    }

    public function setDateValidite(\DateTime $dateValidite): static
    {
        $this->dateValidite = $dateValidite;

        return $this;
    }

    public function getRefInterne(): ?string
    {
        return $this->refInterne;
    }

    public function setRefInterne(string $refInterne): static
    {
        $this->refInterne = $refInterne;

        return $this;
    }

    public function getDateSignature(): ?\DateTime
    {
        return $this->dateSignature;
    }

    public function setDateSignature(?\DateTime $dateSignature): static
    {
        $this->dateSignature = $dateSignature;

        return $this;
    }

    /**
     * @return Collection<int, DevisDocument>
     */
    public function getDevisDocuments(): Collection
    {
        return $this->devisDocuments;
    }

    public function addDevisDocument(DevisDocument $devisDocument): static
    {
        if (!$this->devisDocuments->contains($devisDocument)) {
            $this->devisDocuments->add($devisDocument);
            $devisDocument->setDevis($this);
        }

        return $this;
    }

    public function removeDevisDocument(DevisDocument $devisDocument): static
    {
        if ($this->devisDocuments->removeElement($devisDocument)) {
            // set the owning side to null (unless already changed)
            if ($devisDocument->getDevis() === $this) {
                $devisDocument->setDevis(null);
            }
        }

        return $this;
    }
}
