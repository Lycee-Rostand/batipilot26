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
    private ?\DateTime $dateCreation = null;

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

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTime $dateRDVClient = null;

    #[ORM\ManyToOne(inversedBy: 'devis')]
    #[ORM\JoinColumn(nullable: false)]
    private ?EtatDevis $etatDevis = null;

    #[ORM\ManyToOne(inversedBy: 'devis')]
    #[ORM\JoinColumn(nullable: false)]
    private ?TarifStructure $tarifStructure = null;

    #[ORM\ManyToOne(inversedBy: 'devis')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Maison $maison = null;

    #[ORM\ManyToOne(inversedBy: 'devis')]
<<<<<<< HEAD
    private ?LigneDevisSupplementaire $ligneDevisSupplementaire = null;
=======
    #[ORM\JoinColumn(nullable: false)]
    private ?Prime $prime = null;
>>>>>>> bba5f8a (feat(chantier): Creation entity,crud Prime + relations Prime vers Devis)

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

    public function getDateCreation(): ?\DateTime
    {
        return $this->dateCreation;
    }

    public function setDateCreation(\DateTime $dateCreation): static
    {
        $this->dateCreation = $dateCreation;

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

    public function getDateRDVClient(): ?\DateTime
    {
        return $this->dateRDVClient;
    }

    public function setDateRDVClient(?\DateTime $dateRDVClient): static
    {
        $this->dateRDVClient = $dateRDVClient;

        return $this;
    }

    public function getEtatDevis(): ?EtatDevis
    {
        return $this->etatDevis;
    }

    public function setEtatDevis(?EtatDevis $etatDevis): static
    {
        $this->etatDevis = $etatDevis;

        return $this;
    }

    public function getTarifStructure(): ?TarifStructure
    {
        return $this->tarifStructure;
    }

    public function setTarifStructure(?TarifStructure $tarifStructure): static
    {
        $this->tarifStructure = $tarifStructure;

        return $this;
    }

    public function getMaison(): ?Maison
    {
        return $this->maison;
    }

    public function setMaison(?Maison $maison): static
    {
        $this->maison = $maison;

        return $this;
    }

<<<<<<< HEAD
    public function getLigneDevisSupplementaire(): ?LigneDevisSupplementaire
    {
        return $this->ligneDevisSupplementaire;
    }

    public function setLigneDevisSupplementaire(?LigneDevisSupplementaire $ligneDevisSupplementaire): static
    {
        $this->ligneDevisSupplementaire = $ligneDevisSupplementaire;
=======
    public function getPrime(): ?Prime
    {
        return $this->prime;
    }

    public function setPrime(?Prime $prime): static
    {
        $this->prime = $prime;
>>>>>>> bba5f8a (feat(chantier): Creation entity,crud Prime + relations Prime vers Devis)

        return $this;
    }
}
