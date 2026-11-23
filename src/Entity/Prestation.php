<?php

namespace App\Entity;

use App\Repository\PrestationRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: PrestationRepository::class)]
class Prestation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    #[Assert\PositiveOrZero]
    private ?int $ordre = 0;

    #[ORM\Column(length: 30)]
    #[Assert\NotBlank]
    #[Assert\Length(max: 30)]
    private ?string $code = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank]
    private ?string $description = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Assert\NotBlank]
    #[Assert\PositiveOrZero]
    private ?string $montantUnitaire = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Assert\NotNull]
    private ?\DateTimeInterface $dateDebutValidite = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    #[Assert\GreaterThanOrEqual(propertyPath: 'dateDebutValidite', message: 'La date de fin doit être après la date de début.')]
    private ?\DateTimeInterface $dateFinValidite = null;

    #[ORM\Column]
    private bool $archive = false;

    // cif13 : Prestation 1,1 — Poste 0,n
    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull]
    private ?Poste $poste = null;

    // ataux : Prestation 1,1 — Tva 0,n
    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull]
    private ?Tva $tva = null;

    // cif11 : Prestation 1,1 — Unite 0,n
    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull]
    private ?Unite $unite = null;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): static
    {
        $this->code = $code;
        return $this;
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

    public function getMontantUnitaire(): ?string
    {
        return $this->montantUnitaire;
    }

    public function setMontantUnitaire(string $montantUnitaire): static
    {
        $this->montantUnitaire = $montantUnitaire;
        return $this;
    }

    public function getDateDebutValidite(): ?\DateTimeInterface
    {
        return $this->dateDebutValidite;
    }

    public function setDateDebutValidite(\DateTimeInterface $dateDebutValidite): static
    {
        $this->dateDebutValidite = $dateDebutValidite;
        return $this;
    }

    public function getDateFinValidite(): ?\DateTimeInterface
    {
        return $this->dateFinValidite;
    }

    public function setDateFinValidite(?\DateTimeInterface $dateFinValidite): static
    {
        $this->dateFinValidite = $dateFinValidite;
        return $this;
    }

    public function isArchive(): bool
    {
        return $this->archive;
    }

    public function setArchive(bool $archive): static
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

    public function getTva(): ?Tva
    {
        return $this->tva;
    }

    public function setTva(?Tva $tva): static
    {
        $this->tva = $tva;
        return $this;
    }

    public function getUnite(): ?Unite
    {
        return $this->unite;
    }

    public function setUnite(?Unite $unite): static
    {
        $this->unite = $unite;
        return $this;
    }

    /** Vrai si le tarif est utilisable à la date donnée (non archivé + dans la période de validité). */
    public function estValideAu(\DateTimeInterface $date): bool
    {
        return !$this->archive
            && $this->dateDebutValidite <= $date
            && ($this->dateFinValidite === null || $this->dateFinValidite >= $date);
    }

    public function __toString(): string
    {
        return $this->code . ' - ' . mb_strimwidth($this->description ?? '', 0, 60, '…');
    }
}