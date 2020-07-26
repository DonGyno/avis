<?php

namespace App\Repository;

use App\Entity\Avis;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Avis|null find($id, $lockMode = null, $lockVersion = null)
 * @method Avis|null findOneBy(array $criteria, array $orderBy = null)
 * @method Avis[]    findAll()
 * @method Avis[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AvisRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $managerRegistry)
    {
        parent::__construct($managerRegistry, Avis::class);
    }

    /*public function findAllAvecEntreprise()
    {
        return $this->createQueryBuilder('a')
            ->select('a','entrepriseConcernee.nom')
            ->leftJoin('a.entreprise_concernee','entrepriseConcernee')
            ->orderBy('a.id','ASC')
            ->getQuery()
            ->getResult()
            ;
    }*/

    public function exportAvis($array_id)
    {
        $qb = $this->createQueryBuilder('a');
         $qb->select('a')
            ->add('where', $qb->expr()->in('a.id',':array_id'))
            ->andWhere('a.statut_avis = :statut')
            ->setParameter('array_id', $array_id)
            ->setParameter('statut', "Réponse enregistrée")
            ;
         return $qb->getQuery()->getResult();
    }

    // /**
    //  * @return Avis[] Returns an array of Avis objects
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
    public function findOneBySomeField($value): ?Avis
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
