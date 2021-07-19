<?php

namespace App\Repository;

use App\Entity\Sortie;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Sortie|null find($id, $lockMode = null, $lockVersion = null)
 * @method Sortie|null findOneBy(array $criteria, array $orderBy = null)
 * @method Sortie[]    findAll()
 * @method Sortie[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SortieRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Sortie::class);
    }

    public function sortiePlusRecent()
    {
        return $this->createQueryBuilder('s')
            ->orderBy('s.dateHeureDebut','ASC')
            ->getQuery()
            ->getResult()
            ;
    }

    public function mesSortie($id)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.organisateur = :id')
            ->setParameter('id',$id)
            ->orderBy('s.datePublication','DESC')
            ->getQuery()
            ->getResult()
            ;
    }
    public function sortieTrieeParMot($value): array
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.nom LIKE :value')
            ->setParameter('value', '%'.$value.'%')
            ->addOrderBy('s.nom')
            ->getQuery()
            ->getResult()
            ;
    }

    public function sortieTrieeParCampus($value): array
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.campusOrganisateur = :value')
            ->setParameter('value', $value)
            ->getQuery()
            ->getResult()
            ;
    }

    // /**
    //  * @return Sortie[] Returns an array of Sortie objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Sortie
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
