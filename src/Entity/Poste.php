<?php

namespace App\Entity;

use App\Repository\PosteRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PosteRepository::class)]
class Poste
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $libelle = null;

    #[ORM\Column(type: Types::FLOAT, nullable: true)]
    private ?float $tva = null;

    #[ORM\Column(nullable: true)]
    private ?int $ordre = null;

    #[ORM\Column(nullable: true)]
    private ?int $archive = null;

    /**
     * @var Collection<int, Tache>
     */
    #[ORM\OneToMany(targetEntity: Tache::class, mappedBy: 'PosteId')]
    private Collection $taches;

    /**
     * @var Collection<int, ChantierPoste>
     */
    #[ORM\OneToMany(targetEntity: ChantierPoste::class, mappedBy: 'poste')]
    private Collection $chantierPostes;

    public function __construct()
    {
        $this->taches = new ArrayCollection();
        $this->chantierPostes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getLibelle(): ?string
    {
        return $this->libelle;
    }

    public function setLibelle(?string $libelle): static
    {
        $this->libelle = $libelle;

        return $this;
    }

    public function getTva(): ?float
    {
        return $this->tva;
    }

    public function setTva(?float $tva): static
    {
        $this->tva = $tva;

        return $this;
    }

    public function getOrdre(): ?int
    {
        return $this->ordre;
    }

    public function setOrdre(?int $ordre): static
    {
        $this->ordre = $ordre;

        return $this;
    }

    public function isArchive(): ?bool
    {
        return $this->archive;
    }

    public function setArchive(?bool $archive): static
    {
        $this->archive = $archive;

        return $this;
    }

    /**
     * @return Collection<int, Tache>
     */
    public function getTaches(): Collection
    {
        return $this->taches;
    }

    public function addTach(Tache $tach): static
    {
        if (!$this->taches->contains($tach)) {
            $this->taches->add($tach);
            $tach->setPosteId($this);
        }

        return $this;
    }

    public function removeTach(Tache $tach): static
    {
        if ($this->taches->removeElement($tach)) {
            // set the owning side to null (unless already changed)
            if ($tach->getPosteId() === $this) {
                $tach->setPosteId(null);
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
            $chantierPoste->setPoste($this);
        }

        return $this;
    }

    public function removeChantierPoste(ChantierPoste $chantierPoste): static
    {
        if ($this->chantierPostes->removeElement($chantierPoste)) {
            if ($chantierPoste->getPoste() === $this) {
                $chantierPoste->setPoste(null);
            }
        }

        return $this;
    }
}