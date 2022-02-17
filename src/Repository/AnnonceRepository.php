<?php

namespace App\Repository;

use App\Entity\Annonce;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Annonce|null find($id, $lockMode = null, $lockVersion = null)
 * @method Annonce|null findOneBy(array $criteria, array $orderBy = null)
 * @method Annonce[]    findAll()
 * @method Annonce[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AnnonceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Annonce::class);
    }

    //Fait maison pour les automobiles
    public function trouverParModele($modele)
    {
        return $this->createQueryBuilder('annonce')
            ->join('annonce.modeleVehicule', 'automobile')
            ->where('automobile.modele = :val')
            ->setParameter('val', $modele)
            ->getQuery()
            ->getResult();
    }

    //Fait maison pour les emplois
    public function trouverEmploiParMot($recherche)
    {
        return $this->createQueryBuilder('annonce')
            ->where('annonce.titre like :val')
            ->setParameter('val', '%' . $recherche . '%')
            ->andWhere("annonce.categorie = :valDeux")
            ->setParameter('valDeux', 'Emploi')
            ->getQuery()
            ->getResult();
    }

    //Fait maison pour les offres immobilières
    public function trouverImmoParMot($recherche)
    {
        return $this->createQueryBuilder('annonce')
            ->where('annonce.titre like :val')
            ->setParameter('val', '%' . $recherche . '%')
            ->andWhere("annonce.categorie = :valDeux")
            ->setParameter('valDeux', 'Immobilier')
            ->getQuery()
            ->getResult();
    }

    // /**
    //  * @return Annonce[] Returns an array of Annonce objects
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
    public function findOneBySomeField($value): ?Annonce
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
