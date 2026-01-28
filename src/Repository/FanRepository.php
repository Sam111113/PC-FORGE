<?php

namespace App\Repository;

use App\Entity\Fan;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Fan>
 */
class FanRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Fan::class);
    }

    //    /**
    //     * @return Fan[] Returns an array of Fan objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('f')
    //            ->andWhere('f.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('f.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Fan
    //    {
    //        return $this->createQueryBuilder('f')
    //            ->andWhere('f.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }

    /**
     * Trouve les ventilateurs compatibles avec un boîtier donné
     *
     * La compatibilité se base sur la taille et le nombre de slots
     *
     * @param int $maxFanSlot Nombre de slots de ventilateur disponibles
     * @param int $maxFanWidth Largeur maximale supportée en mm
     * @return Fan[] Les ventilateurs compatibles, triés par prix décroissant
     */
    public function findCompatibleWithCase(int $maxFanSlot, int $maxFanWidth): array
    {
        return $this->createQueryBuilder('fan')
            ->andWhere('fan.width <= :maxWidth')
            ->andWhere('fan.quantity <= :slot')
            ->setParameter('slot', $maxFanSlot)
            ->setParameter('maxWidth', $maxFanWidth)
            ->orderBy('fan.prix', 'DESC')
            ->getQuery()
            ->getResult();
    }
}

