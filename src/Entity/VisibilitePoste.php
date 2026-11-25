<?php

namespace App\Entity;

use App\Repository\VisibilitePosteRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: VisibilitePosteRepository::class)]
#[ORM\UniqueConstraint(columns: ['poste_id', 'visibilite_id'])]
class VisibilitePoste
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(nullable: true)]
    private ?int $archive = null;

    #[ORM\ManyToOne(inversedBy: 'visibilitePostes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Poste $poste = null;

    #[ORM\ManyToOne(inversedBy: 'visibilitePostes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Visibilite $visibilite = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getArchive(): ?int
    {
        return $this->archive;
    }

    public function setArchive(?int $archive): static
    {
        $this->archive = $archive;

        return $this;
    }

    public function getPoste(): ?Poste
    {
        return $this->poste;
    }

    public function setPoste(?Poste $poste): static
    {
        $this->poste = $poste;

        return $this;
    }

    public function getVisibilite(): ?Visibilite
    {
        return $this->visibilite;
    }

    public function setVisibilite(?Visibilite $visibilite): static
    {
        $this->visibilite = $visibilite;

        return $this;
    }
}