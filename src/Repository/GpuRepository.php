<?php

namespace App\Repository;

use App\Entity\Gpu;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Gpu>
 */
class GpuRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Gpu::class);
    }

    //    /**
    //     * @return Gpu[] Returns an array of Gpu objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('g')
    //            ->andWhere('g.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('g.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Gpu
    //    {
    //        return $this->createQueryBuilder('g')
    //            ->andWhere('g.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }

    /**
     * Trouve les GPU compatibles avec une carte mère donnée
     *
     * La compatibilité se base sur le module PCIe
     *
     * @param string $pcieModule Le module PCIe de la carte mère (ex: 'PCIe 4.0')
     * @return Gpu[] Les GPU compatibles, triés par ID décroissant
     */
    public function findCompatibleWithMotherboard(string $pcieModule): array
    {
        return $this->createQueryBuilder('gpu')
            ->andWhere('gpu.pcieModule = :pcieModule')
            ->setParameter('pcieModule', $pcieModule)
            ->orderBy('gpu.id', 'DESC')
            ->getQuery()
            ->getResult();
    }
}

