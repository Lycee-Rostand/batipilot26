<?php

namespace App\Entity;

use App\Repository\TacheRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TacheRepository::class)]
class Tache
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $libelle = null;

    #[ORM\ManyToOne(inversedBy: 'taches')]
    private ?TacheFormat $TacheFormat = null;

    #[ORM\ManyToOne(inversedBy: 'taches')]
    private ?Poste $PosteId = null;

    /**
     * @var Collection<int, ChantierTache>
     */
    #[ORM\OneToMany(targetEntity: ChantierTache::class, mappedBy: 'tache')]
    private Collection $chantierTaches;

    public function __construct()
    {
        $this->chantierTaches = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLibelle(): ?string
    {
        return $this->libelle;
    }

    public function setLibelle(string $libelle): static
    {
        $this->libelle = $libelle;

        return $this;
    }

    public function getTacheFormat(): ?TacheFormat
    {
        return $this->TacheFormat;
    }

    public function setTacheFormat(?TacheFormat $TacheFormat): static
    {
        $this->TacheFormat = $TacheFormat;

        return $this;
    }

    public function getPosteId(): ?Poste
    {
        return $this->PosteId;
    }

    public function setPosteId(?Poste $PosteId): static
    {
        $this->PosteId = $PosteId;

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
            $chantierTach->setTache($this);
        }

        return $this;
    }

    public function removeChantierTach(ChantierTache $chantierTach): static
    {
        if ($this->chantierTaches->removeElement($chantierTach)) {
            // set the owning side to null (unless already changed)
            if ($chantierTach->getTache() === $this) {
                $chantierTach->setTache(null);
            }
        }

        return $this;
    }
}
