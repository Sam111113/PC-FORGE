<?php

namespace App\Repository;

use App\Entity\Cooler;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Cooler>
 */
class CoolerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Cooler::class);
    }

    //    /**
    //     * @return Cooler[] Returns an array of Cooler objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('c.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Cooler
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }

    /**
     * Trouve les refroidisseurs compatibles avec un CPU donné
     *
     * La compatibilité se base sur le socket et le TDP
     *
     * @param string $socket Le socket du CPU (ex: 'AM5', 'LGA1700')
     * @param int $tdp Le TDP du CPU en watts
     * @return Cooler[] Les refroidisseurs compatibles, triés par prix décroissant
     */
    public function findCompatibleWithCpu(string $socket, int $tdp): array
    {
        return $this->createQueryBuilder('cooler')
            ->andWhere('cooler.socket LIKE :socket')
            ->andWhere('cooler.tdp >= :tdp')
            ->setParameter('socket', '%' . $socket . '%')
            ->setParameter('tdp', $tdp)
            ->orderBy('cooler.prix', 'DESC')
            ->getQuery()
            ->getResult();
    }
}

