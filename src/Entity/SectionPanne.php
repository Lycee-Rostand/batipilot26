<?php

namespace App\Entity;

use App\Repository\SectionPanneRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SectionPanneRepository::class)]
class SectionPanne
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $section = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 7, scale: 2)]
    private ?string $longueur = null;

    #[ORM\ManyToOne(inversedBy: 'sectionPanne')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Maison $maison = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSection(): ?string
    {
        return $this->section;
    }

    public function setSection(string $section): static
    {
        $this->section = $section;

        return $this;
    }

    public function getLongueur(): ?string
    {
        return $this->longueur;
    }

    public function setLongueur(string $longueur): static
    {
        $this->longueur = $longueur;

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
}
