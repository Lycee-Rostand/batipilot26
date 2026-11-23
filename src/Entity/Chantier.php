<?php

namespace App\Entity;

use App\Repository\ChantierRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ChantierRepository::class)]
class Chantier
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $reference = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTime $dateDemarrage = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTime $dateFinPrevue = null;

    #[ORM\Column]
    private ?int $archive = null;

    #[ORM\ManyToOne(inversedBy: 'chantiers')]
    private ?StatutChantier $statut = null;

    /**
     * @var Collection<int, ChantierTache>
     */
    #[ORM\OneToMany(targetEntity: ChantierTache::class, mappedBy: 'chantier')]
    private Collection $chantierTaches;

    /**
     * @var Collection<int, ChantierPoste>
     */
    #[ORM\OneToMany(targetEntity: ChantierPoste::class, mappedBy: 'chantier', orphanRemoval: true)]
    private Collection $chantierPostes;

    public function __construct()
    {
        $this->chantierTaches = new ArrayCollection();
        $this->chantierPostes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getReference(): ?string
    {
        return $this->reference;
    }

    public function setReference(string $reference): static
    {
        $this->reference = $reference;

        return $this;
    }

    public function getDateDemarrage(): ?\DateTime
    {
        return $this->dateDemarrage;
    }

    public function setDateDemarrage(\DateTime $dateDemarrage): static
    {
        $this->dateDemarrage = $dateDemarrage;

        return $this;
    }

    public function getDateFinPrevue(): ?\DateTime
    {
        return $this->dateFinPrevue;
    }

    public function setDateFinPrevue(\DateTime $dateFinPrevue): static
    {
        $this->dateFinPrevue = $dateFinPrevue;

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

    public function getStatut(): ?StatutChantier
    {
        return $this->statut;
    }

    public function setStatut(?StatutChantier $statut): static
    {
        $this->statut = $statut;

        return $this;
    }

    /**
     * @return Collection<int, ChantierTache>
     */
    public function getChantierTaches(): Collection
    {
        return $this->chantierTaches;
    }

    public function addChantierTach(ChantierTache $chantierTach): static
    {
        if (!$this->chantierTaches->contains($chantierTach)) {
            $this->chantierTaches->add($chantierTach);
            $chantierTach->setChantier($this);
        }

        return $this;
    }

    public function removeChantierTach(ChantierTache $chantierTach): static
    {
        if ($this->chantierTaches->removeElement($chantierTach)) {
            // set the owning side to null (unless already changed)
            if ($chantierTach->getChantier() === $this) {
                $chantierTach->setChantier(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, ChantierPoste>
     */
    public function getChantierPostes(): Collection
    {
        return $this->chantierPostes;
    }

    public function addChantierPoste(ChantierPoste $chantierPoste): static
    {
        if (!$this->chantierPostes->contains($chantierPoste)) {
            $this->chantierPostes->add($chantierPoste);
            $chantierPoste->setChantier($this);
        }

        return $this;
    }

    public function removeChantierPoste(ChantierPoste $chantierPoste): static
    {
        if ($this->chantierPostes->removeElement($chantierPoste)) {
            // set the owning side to null (unless already changed)
            if ($chantierPoste->getChantier() === $this) {
                $chantierPoste->setChantier(null);
            }
        }

        return $this;
    }
}