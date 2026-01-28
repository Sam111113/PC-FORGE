<?php

namespace App\Repository;

use App\Entity\Psu;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Psu>
 */
class PsuRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Psu::class);
    }

    //    /**
    //     * @return Psu[] Returns an array of Psu objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('p.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Psu
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }

    /**
     * Trouve les alimentations compatibles avec un build donné
     *
     * La compatibilité se base sur la puissance requise (TDP CPU + GPU + 150W marge)
     *
     * @param int $totalTdp Le TDP total requis en watts
     * @return Psu[] Les alimentations compatibles, triées par prix décroissant
     */
    public function findByMinimumWattage(int $totalTdp): array
    {
        return $this->createQueryBuilder('psu')
            ->andWhere('psu.wattage >= :tdp')
            ->setParameter('tdp', $totalTdp)
            ->orderBy('psu.prix', 'DESC')
            ->getQuery()
            ->getResult();
    }
}

