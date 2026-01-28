<?php

namespace App\Repository;

use App\Entity\Motherboard;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Motherboard>
 */
class MotherboardRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Motherboard::class);
    }

    //    /**
    //     * @return Motherboard[] Returns an array of Motherboard objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('m')
    //            ->andWhere('m.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('m.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Motherboard
    //    {
    //        return $this->createQueryBuilder('m')
    //            ->andWhere('m.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }

    /**
     * Trouve les cartes mères compatibles avec un CPU donné
     *
     * La compatibilité se base sur le socket du processeur
     *
     * @param string $socket Le socket du CPU (ex: 'AM5', 'LGA1700')
     * @return Motherboard[] Les cartes mères compatibles, triées par ID décroissant
     */
    public function findCompatibleWithCpu(string $socket): array
    {
        return $this->createQueryBuilder('mb')
            ->andWhere('mb.socket = :socket')
            ->setParameter('socket', $socket)
            ->orderBy('mb.id', 'DESC')
            ->getQuery()
            ->getResult();
    }
}

