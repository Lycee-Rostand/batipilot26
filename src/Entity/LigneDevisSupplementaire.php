<?php

namespace App\Entity;

use App\Repository\LigneDevisSupplementaireRepository;
use BcMath\Number;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LigneDevisSupplementaireRepository::class)]
class LigneDevisSupplementaire
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $description = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 15, scale: 2)]
    private ?string $montant = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 15, scale: 2)]
    private ?string $montantTva = null;

    #[ORM\Column]
    private ?int $ordre = null;

    /**
     * @var Collection<int, Poste>
     */
    #[ORM\OneToMany(targetEntity: Poste::class, mappedBy: 'ligneDevisSupplementaire')]
    private Collection $posteTemp;

    /**
     * @var Collection<int, Devis>
     */
    #[ORM\OneToMany(targetEntity: Devis::class, mappedBy: 'ligneDevisSupplementaire')]
    private Collection $devis;



    public function __construct()
    {
        $this->posteTemp = new ArrayCollection();
        $this->devis = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getMontant(): ?string
    {
        return $this->montant;
    }

    public function setMontant(string $montant): static
    {
        $this->montant = $montant;

        return $this;
    }

    public function getMontantTva(): ?string
    {
        return $this->montantTva;
    }

    public function setMontantTva(string $montantTva): static
    {
        $this->montantTva = $montantTva;

        return $this;
    }

    public function getOrdre(): ?int
    {
        return $this->ordre;
    }

    public function setOrdre(int $ordre): static
    {
        $this->ordre = $ordre;

        return $this;
    }

    /**
     * @return Collection<int, Poste>
     */
    public function getPosteTemp(): Collection
    {
        return $this->posteTemp;
    }

    public function addPosteTemp(Poste $posteTemp): static
    {
        if (!$this->posteTemp->contains($posteTemp)) {
            $this->posteTemp->add($posteTemp);
            $posteTemp->setLigneDevisSupplementaire($this);
        }

        return $this;
    }

    public function removePosteTemp(Poste $posteTemp): static
    {
        if ($this->posteTemp->removeElement($posteTemp)) {
            // set the owning side to null (unless already changed)
            if ($posteTemp->getLigneDevisSupplementaire() === $this) {
                $posteTemp->setLigneDevisSupplementaire(null);
            }
        }

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
            $devi->setLigneDevisSupplementaire($this);
        }

        return $this;
    }

    public function removeDevi(Devis $devi): static
    {
        if ($this->devis->removeElement($devi)) {
            // set the owning side to null (unless already changed)
            if ($devi->getLigneDevisSupplementaire() === $this) {
                $devi->setLigneDevisSupplementaire(null);
            }
        }

        return $this;
    }


}
