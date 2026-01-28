<?php

namespace App\Repository;

use App\Entity\Boitier;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Boitier>
 */
class BoitierRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Boitier::class);
    }

    //    /**
    //     * @return Boitier[] Returns an array of Boitier objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('b')
    //            ->andWhere('b.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('b.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Boitier
    //    {
    //        return $this->createQueryBuilder('b')
    //            ->andWhere('b.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }

    /**
     * Trouve les boîtiers compatibles sans refroidissement
     *
     * @param string $mbFormFactor Le format de la carte mère (ex: 'ATX', 'Micro-ATX')
     * @param int $gpuLength La longueur du GPU en mm
     * @return Boitier[] Les boîtiers compatibles, triés par prix décroissant
     */
    public function findCompatibleWithoutCooler(string $mbFormFactor, int $gpuLength): array
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.gpuMaxL >= :length')
            ->andWhere('c.mbFormFactor LIKE :formFactor')
            ->setParameter('length', $gpuLength)
            ->setParameter('formFactor', '%' . $mbFormFactor . '%')
            ->orderBy('c.prix', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Trouve les boîtiers compatibles avec un refroidissement à air
     *
     * @param string $mbFormFactor Le format de la carte mère
     * @param int $gpuLength La longueur du GPU en mm
     * @param int $coolerHeight La hauteur du refroidisseur en mm
     * @return Boitier[] Les boîtiers compatibles, triés par prix décroissant
     */
    public function findCompatibleWithAirCooler(string $mbFormFactor, int $gpuLength, int $coolerHeight): array
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.gpuMaxL >= :length')
            ->andWhere('c.mbFormFactor LIKE :formFactor')
            ->andWhere('c.coolerMaxHeight >= :coolerHeight')
            ->setParameter('length', $gpuLength)
            ->setParameter('formFactor', '%' . $mbFormFactor . '%')
            ->setParameter('coolerHeight', $coolerHeight)
            ->orderBy('c.prix', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Trouve les boîtiers compatibles avec un refroidissement AIO (watercooling)
     *
     * @param string $mbFormFactor Le format de la carte mère
     * @param int $gpuLength La longueur du GPU en mm
     * @param int $coolerFans Nombre de ventilateurs du AIO
     * @return Boitier[] Les boîtiers compatibles, triés par prix décroissant
     */
    public function findCompatibleWithAioCooler(string $mbFormFactor, int $gpuLength, int $coolerFans): array
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.gpuMaxL >= :length')
            ->andWhere('c.mbFormFactor LIKE :formFactor')
            ->andWhere('c.fanSlot >= :fan')
            ->setParameter('fan', $coolerFans)
            ->setParameter('length', $gpuLength)
            ->setParameter('formFactor', '%' . $mbFormFactor . '%')
            ->orderBy('c.prix', 'DESC')
            ->getQuery()
            ->getResult();
    }
}

