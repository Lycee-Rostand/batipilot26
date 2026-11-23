<?php

namespace App\Entity;

use App\Repository\TarifStructureRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TarifStructureRepository::class)]
class TarifStructure
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 15, scale: 2)]
    private ?string $surfaceMin = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 15, scale: 2)]
    private ?string $surfaceMax = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 15, scale: 2)]
    private ?string $montantHT = null;

    /**
     * @var Collection<int, Devis>
     */
    #[ORM\OneToMany(targetEntity: Devis::class, mappedBy: 'tarifStructure')]
    private Collection $devis;

    public function __construct()
    {
        $this->devis = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSurfaceMin(): ?string
    {
        return $this->surfaceMin;
    }

    public function setSurfaceMin(string $surfaceMin): static
    {
        $this->surfaceMin = $surfaceMin;

        return $this;
    }

    public function getSurfaceMax(): ?string
    {
        return $this->surfaceMax;
    }

    public function setSurfaceMax(string $surfaceMax): static
    {
        $this->surfaceMax = $surfaceMax;

        return $this;
    }

    public function getMontantHT(): ?string
    {
        return $this->montantHT;
    }

    public function setMontantHT(string $montantHT): static
    {
        $this->montantHT = $montantHT;

        return $this;
    }

    /**
     * @return Collection<int, Devis>
     */
    public function getDevis(): Collection
    {
        return $this->devis;
    }

    public function addDevi(Devis $devi): static
    {
        if (!$this->devis->contains($devi)) {
            $this->devis->add($devi);
            $devi->setTarifStructure($this);
        }

        return $this;
    }

    public function removeDevi(Devis $devi): static
    {
        if ($this->devis->removeElement($devi)) {
            // set the owning side to null (unless already changed)
            if ($devi->getTarifStructure() === $this) {
                $devi->setTarifStructure(null);
            }
        }

        return $this;
    }
}
