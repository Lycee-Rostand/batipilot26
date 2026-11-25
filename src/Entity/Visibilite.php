<?php

namespace App\Entity;

use App\Repository\VisibiliteRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: VisibiliteRepository::class)]
class Visibilite
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 3, nullable: true)]
    private ?string $code = null;

    #[ORM\Column(length: 255)]
    private ?string $libelle = null;

    /**
     * @var Collection<int, VisibilitePoste>
     */
    #[ORM\OneToMany(targetEntity: VisibilitePoste::class, mappedBy: 'visibilite')]
    private Collection $visibilitePostes;

    public function __construct()
    {
        $this->visibilitePostes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(?string $code): static
    {
        $this->code = $code;

        return $this;
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
            $visibilitePoste->setVisibilite($this);
        }

        return $this;
    }

    public function removeVisibilitePoste(VisibilitePoste $visibilitePoste): static
    {
        if ($this->visibilitePostes->removeElement($visibilitePoste)) {
            // set the owning side to null (unless already changed)
            if ($visibilitePoste->getVisibilite() === $this) {
                $visibilitePoste->setVisibilite(null);
            }
        }

        return $this;
    }
}
