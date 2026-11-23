<?php

namespace App\Repository;

use App\Entity\Poste;
use App\Entity\Prestation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Prestation>
 */
class PrestationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Prestation::class);
    }

    /** Prestations utilisables à une date donnée (aujourd'hui par défaut) pour un poste. */
    public function findValidesPourPoste(Poste $poste, ?\DateTimeInterface $date = null): array
    {
        $date ??= new \DateTime();

        return $this->createQueryBuilder('p')
            ->andWhere('p.poste = :poste')
            ->andWhere('p.archive = false')
            ->andWhere('p.dateDebutValidite <= :date')
            ->andWhere('p.dateFinValidite IS NULL OR p.dateFinValidite >= :date')
            ->setParameter('poste', $poste)
            ->setParameter('date', $date)
            ->orderBy('p.ordre', 'ASC')
            ->getQuery()
            ->getResult();
    }
    /**
     * Prestations non archivées, triées par poste puis par ordre.
     *
     * @return Prestation[]
     */
    public function findPourListe(): array
    {
        return $this->createQueryBuilder('p')
            ->leftJoin('p.poste', 'po')->addSelect('po')
            ->leftJoin('p.tva', 't')->addSelect('t')
            ->leftJoin('p.unite', 'u')->addSelect('u')
            ->where('p.archive = false')
            ->orderBy('po.ordre', 'ASC')
            ->addOrderBy('po.libelle', 'ASC')
            ->addOrderBy('p.ordre', 'ASC')
            ->addOrderBy('p.id', 'ASC')
            ->getQuery()
            ->getResult();
    }
}