<?php

namespace App\Entity;

use App\Repository\PosteRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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

    #[ORM\Column(nullable: true)]
    private ?int $ordre = null;

    /**
     * @var Collection<int, Tache>
     */
    #[ORM\OneToMany(targetEntity: Tache::class, mappedBy: 'PosteId')]
    private Collection $taches;

    /**
     * @var Collection<int, VisibilitePoste>
     */
    #[ORM\OneToMany(targetEntity: VisibilitePoste::class, mappedBy: 'poste')]
    private Collection $visibilitePostes;

    public function __construct()
    {
        $this->taches = new ArrayCollection();
        $this->visibilitePostes = new ArrayCollection();
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

    public function getOrdre(): ?int
    {
        return $this->ordre;
    }

    public function setOrdre(?int $ordre): static
    {
        $this->ordre = $ordre;

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
     * @return Collection<int, VisibilitePoste>
     */
    public function getVisibilitePostes(): Collection
    {
        return $this->visibilitePostes;
    }

    public function addVisibilitePoste(VisibilitePoste $visibilitePoste): static
    {
        if (!$this->visibilitePostes->contains($visibilitePoste)) {
            $this->visibilitePostes->add($visibilitePoste);
            $visibilitePoste->setPoste($this);
        }

        return $this;
    }

    public function removeVisibilitePoste(VisibilitePoste $visibilitePoste): static
    {
        if ($this->visibilitePostes->removeElement($visibilitePoste)) {
            // set the owning side to null (unless already changed)
            if ($visibilitePoste->getPoste() === $this) {
                $visibilitePoste->setPoste(null);
            }
        }

        return $this;
    }
}