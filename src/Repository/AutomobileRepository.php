<?php

namespace App\Repository;

use App\Entity\Automobile;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Automobile|null find($id, $lockMode = null, $lockVersion = null)
 * @method Automobile|null findOneBy(array $criteria, array $orderBy = null)
 * @method Automobile[]    findAll()
 * @method Automobile[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AutomobileRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Automobile::class);
    }

    // /**
    //  * @return Automobile[] Returns an array of Automobile objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Automobile
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
