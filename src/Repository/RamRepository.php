<?php

namespace App\Repository;

use App\Entity\Ram;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Ram>
 */
class RamRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Ram::class);
    }

    //    /**
    //     * @return Ram[] Returns an array of Ram objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('r')
    //            ->andWhere('r.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('r.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Ram
    //    {
    //        return $this->createQueryBuilder('r')
    //            ->andWhere('r.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }

    /**
     * Trouve les RAM compatibles avec une carte mère donnée
     *
     * La compatibilité se base sur le type de mémoire et la capacité maximale
     *
     * @param string $memoryType Le type de mémoire supporté par la carte mère (ex: 'DDR4', 'DDR5')
     * @param int $memoryMax La capacité maximale de RAM supportée (en Go)
     * @return Ram[] Les RAM compatibles, triées par prix décroissant
     */
    public function findCompatibleWithMotherboard(string $memoryType, int $memoryMax): array
    {
        return $this->createQueryBuilder('ram')
            ->andWhere('ram.type LIKE :memoryType')
            ->andWhere('ram.total <= :memoryMax')
            ->setParameter('memoryType', $memoryType)
            ->setParameter('memoryMax', $memoryMax)
            ->orderBy('ram.prix', 'DESC')
            ->getQuery()
            ->getResult();
    }
}

