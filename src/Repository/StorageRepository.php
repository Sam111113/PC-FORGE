<?php

namespace App\Repository;

use App\Entity\Storage;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Storage>
 */
class StorageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Storage::class);
    }

    //    /**
    //     * @return Storage[] Returns an array of Storage objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('s.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Storage
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }

    /**
     * Trouve les stockages compatibles avec une carte mère donnée
     *
     * La compatibilité se base sur les ports disponibles (M.2 ou SATA)
     *
     * @param int $m2Slots Nombre de slots M.2 disponibles
     * @param int $sataPorts Nombre de ports SATA disponibles
     * @return Storage[] Les stockages compatibles, triés par prix décroissant
     */
    public function findCompatibleWithMotherboard(int $m2Slots, int $sataPorts): array
    {
        $qb = $this->createQueryBuilder('storage');

        if ($m2Slots > 0) {
            // Priorité aux stockages PCIe/M.2
            $qb->andWhere('storage.interface LIKE :pcie')
               ->setParameter('pcie', '%PCie%');
        } elseif ($sataPorts > 0) {
            // Sinon stockages SATA
            $qb->andWhere('storage.interface LIKE :sata')
               ->setParameter('sata', '%SATA%');
        }

        return $qb->orderBy('storage.prix', 'DESC')
            ->getQuery()
            ->getResult();
    }
}

