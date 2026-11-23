<?php

namespace App\Entity;

use App\Repository\MaisonRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MaisonRepository::class)]
class Maison
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private ?string $rue = null;

    #[ORM\Column(length: 100)]
    private ?string $ville = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?TypeCharpente $typeCharpente = null;

    #[ORM\Column(length: 5)]
    private ?string $copos = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $anneeConstruction = null;

    #[ORM\Column(length: 100)]
    private ?string $sectionCadastrale = null;

    #[ORM\Column(nullable: true)]
    private ?int $superficieSectionCadastrale = null;

    #[ORM\Column(nullable: true)]
    private ?bool $maisonPhenix = null;

    #[ORM\Column]
    private ?int $distanceDepot = null;

    #[ORM\Column(nullable: true)]
    private ?int $tempsTrajet = null;

    #[ORM\Column]
    private ?int $surfaceHabitable = null;

    #[ORM\Column]
    private ?int $surfaceComblesCree = null;

    #[ORM\Column(length: 255)]
    private ?string $remarques = null;

    #[ORM\ManyToOne(inversedBy: 'maisons')]
    #[ORM\JoinColumn(nullable: true)]
    private ?Client $client = null;

    /**
     * @var Collection<int, Devis>
     */
    #[ORM\OneToMany(targetEntity: Devis::class, mappedBy: 'maison')]
    private Collection $devis;

    public function __construct()
    {
        $this->devis = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRue(): ?string
    {
        return $this->rue;
    }

    public function setRue(string $rue): static
    {
        $this->rue = $rue;
        return $this;
    }

    public function getVille(): ?string
    {
        return $this->ville;
    }

    public function setVille(string $ville): static
    {
        $this->ville = $ville;
        return $this;
    }

    public function getTypeCharpente(): ?TypeCharpente
    {
        return $this->typeCharpente;
    }

    public function setTypeCharpente(?TypeCharpente $typeCharpente): static
    {
        $this->typeCharpente = $typeCharpente;
        return $this;
    }

    public function getCopos(): ?string
    {
        return $this->copos;
    }

    public function setCopos(string $copos): static
    {
        $this->copos = $copos;
        return $this;
    }

    public function getAnneeConstruction(): ?\DateTimeInterface
    {
        return $this->anneeConstruction;
    }

    public function setAnneeConstruction(\DateTimeInterface $anneeConstruction): static
    {
        $this->anneeConstruction = $anneeConstruction;
        return $this;
    }

    public function getSectionCadastrale(): ?string
    {
        return $this->sectionCadastrale;
    }

    public function setSectionCadastrale(string $sectionCadastrale): static
    {
        $this->sectionCadastrale = $sectionCadastrale;
        return $this;
    }

    public function getSuperficieSectionCadastrale(): ?int
    {
        return $this->superficieSectionCadastrale;
    }

    public function setSuperficieSectionCadastrale(?int $superficieSectionCadastrale): static
    {
        $this->superficieSectionCadastrale = $superficieSectionCadastrale;
        return $this;
    }

    public function isMaisonPhenix(): ?bool
    {
        return $this->maisonPhenix;
    }

    public function setMaisonPhenix(?bool $maisonPhenix): static
    {
        $this->maisonPhenix = $maisonPhenix;
        return $this;
    }

    public function getDistanceDepot(): ?int
    {
        return $this->distanceDepot;
    }

    public function setDistanceDepot(int $distanceDepot): static
    {
        $this->distanceDepot = $distanceDepot;
        return $this;
    }

    public function getTempsTrajet(): ?int
    {
        return $this->tempsTrajet;
    }

    public function setTempsTrajet(?int $tempsTrajet): static
    {
        $this->tempsTrajet = $tempsTrajet;
        return $this;
    }

    public function getSurfaceHabitable(): ?int
    {
        return $this->surfaceHabitable;
    }

    public function setSurfaceHabitable(int $surfaceHabitable): static
    {
        $this->surfaceHabitable = $surfaceHabitable;
        return $this;
    }

    public function getSurfaceComblesCree(): ?int
    {
        return $this->surfaceComblesCree;
    }

    public function setSurfaceComblesCree(int $surfaceComblesCree): static
    {
        $this->surfaceComblesCree = $surfaceComblesCree;
        return $this;
    }

    public function getRemarques(): ?string
    {
        return $this->remarques;
    }

    public function setRemarques(string $remarques): static
    {
        $this->remarques = $remarques;
        return $this;
    }

    public function getClient(): ?Client
    {
        return $this->client;
    }

    public function setClient(?Client $client): static
    {
        $this->client = $client;
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
            $devi->setMaison($this);
        }

        return $this;
    }

    public function removeDevi(Devis $devi): static
    {
        if ($this->devis->removeElement($devi)) {
            // set the owning side to null (unless already changed)
            if ($devi->getMaison() === $this) {
                $devi->setMaison(null);
            }
        }

        return $this;
    }
}