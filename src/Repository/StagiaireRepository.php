<?php

namespace App\Repository;

use App\Entity\Stagiaire;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Stagiaire|null find($id, $lockMode = null, $lockVersion = null)
 * @method Stagiaire|null findOneBy(array $criteria, array $orderBy = null)
 * @method Stagiaire[]    findAll()
 * @method Stagiaire[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StagiaireRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Stagiaire::class);
    }

    public function getAll() {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery(
            'SELECT s
                FROM App\Entity\Stagiaire s
                ORDER BY s.nom'
        );
        return $query->execute();
    }

    public function getAllOrder() {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery(
            'SELECT s
                FROM App\Entity\Stagiaire s
                ORDER BY s.id DESC'
        )
        ->setMaxResults(5);

        return $query->execute();
    }


    public function specificStagiaire($id){

        $qb = $this->createQueryBuilder('s')
            ->where('s.id = :id')
            ->setParameter(':id', $id);
    
        $query = $qb->getQuery();
    
        return $query->execute();
    
        }
    // /**
    //  * @return Stagiaire[] Returns an array of Stagiaire objects
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
    public function findOneBySomeField($value): ?Stagiaire
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
