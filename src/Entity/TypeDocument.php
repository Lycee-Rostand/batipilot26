<?php

namespace App\Entity;

use App\Repository\TypeDocumentRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TypeDocumentRepository::class)]
class TypeDocument
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100, unique: true)]
    private ?string $nom = null;

    #[ORM\Column]
    private ?int $archive = null;

    /**
     * @var Collection<int, DevisDocument>
     */
    #[ORM\OneToMany(targetEntity: DevisDocument::class, mappedBy: 'TypeDocument')]
    private Collection $devisDocuments;

    public function __construct()
    {
        $this->devisDocuments = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

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
            $devisDocument->setTypeDocument($this);
        }

        return $this;
    }

    public function removeDevisDocument(DevisDocument $devisDocument): static
    {
        if ($this->devisDocuments->removeElement($devisDocument)) {
            // set the owning side to null (unless already changed)
            if ($devisDocument->getTypeDocument() === $this) {
                $devisDocument->setTypeDocument(null);
            }
        }

        return $this;
    }
}
