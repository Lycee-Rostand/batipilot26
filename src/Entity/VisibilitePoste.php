<?php

namespace App\Entity;

use App\Repository\VisibilitePosteRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: VisibilitePosteRepository::class)]
class VisibilitePoste
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(nullable: true)]
    private ?int $archive = null;

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
}
