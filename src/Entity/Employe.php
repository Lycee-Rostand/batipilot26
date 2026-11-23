<?php

namespace App\Entity;

use App\Repository\EmployeRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EmployeRepository::class)]
class Employe
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    private ?string $prenom = null;

    #[ORM\Column]
    private ?float $coutHrMo = null;

    #[ORM\Column]
    private ?int $archive = null;

    #[ORM\ManyToOne(inversedBy: 'employes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?TypeCharpente $type_charpente = null;

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

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): static
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getCoutHrMo(): ?float
    {
        return $this->coutHrMo;
    }

    public function setCoutHrMo(float $coutHrMo): static
    {
        $this->coutHrMo = $coutHrMo;

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

    public function getTypeCharpente(): ?TypeCharpente
    {
        return $this->type_charpente;
    }

    public function setTypeCharpente(?TypeCharpente $type_charpente): static
    {
        $this->type_charpente = $type_charpente;

        return $this;
    }
}
