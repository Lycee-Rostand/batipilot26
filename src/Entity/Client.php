<?php

namespace App\Entity;

use App\Repository\ClientRepository;
use BcMath\Number;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ClientRepository::class)]
class Client
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(name: 'nom1', length: 50)]
    private ?string $nom = null;

    #[ORM\Column(name: 'prenom1', length: 50)]
    private ?string $prenom = null;

    #[ORM\Column(name: 'lieuNaissance1', length: 150)]
    private ?string $lieu_naissance = null;

    #[ORM\Column(length: 50)]
    private ?string $telephone1 = null;

    #[ORM\Column(length: 50)]
    private ?string $mail1 = null;

    
    #[ORM\Column(length: 50, nullable: true)]
    private ?string $nom2 = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $prenom2 = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTime $dateNaissance1 = null;

    #[ORM\ManyToOne]
    private ?Civilite $civilite1 = null;

    #[ORM\ManyToOne]
    private ?Civilite $civilite2 = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): static
    {
        $this->id = $id;

        return $this;
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

    public function getLieuNaissance(): ?string
    {
        return $this->lieu_naissance;
    }

    public function setLieuNaissance(string $lieu_naissance): static
    {
        $this->lieu_naissance = $lieu_naissance;

        return $this;
    }

    public function getTelephone1(): ?string
    {
        return $this->telephone1;
    }

    public function setTelephone1(string $telephone1): static
    {
        $this->telephone1 = $telephone1;

        return $this;
    }

    public function getMail1(): ?string
    {
        return $this->mail1;
    }

    public function setMail1(string $mail1): static
    {
        $this->mail1 = $mail1;

        return $this;
    }

    public function getNom2(): ?string
    {
        return $this->nom2;
    }

    public function setNom2(?string $nom2): static
    {
        $this->nom2 = $nom2;

        return $this;
    }

    public function getPrenom2(): ?string
    {
        return $this->prenom2;
    }

    public function setPrenom2(?string $prenom2): static
    {
        $this->prenom2 = $prenom2;

        return $this;
    }

    public function getDateNaissance1(): ?\DateTime
    {
        return $this->dateNaissance1;
    }

    public function setDateNaissance1(\DateTime $dateNaissance1): static
    {
        $this->dateNaissance1 = $dateNaissance1;

        return $this;
    }

    public function getCivilite1(): ?Civilite
    {
        return $this->civilite1;
    }

    public function setCivilite1(?Civilite $civilite1): static
    {
        $this->civilite1 = $civilite1;

        return $this;
    }

    public function getCivilite2(): ?Civilite
    {
        return $this->civilite2;
    }

    public function setCivilite2(?Civilite $civilite2): static
    {
        $this->civilite2 = $civilite2;

        return $this;
    }
}
