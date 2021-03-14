<?php

namespace App\Repository;

use App\Entity\Modules;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Modules|null find($id, $lockMode = null, $lockVersion = null)
 * @method Modules|null findOneBy(array $criteria, array $orderBy = null)
 * @method Modules[]    findAll()
 * @method Modules[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ModulesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Modules::class);
    }

    public function getAll(){
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery(
            'SELECT m
                FROM App\Entity\Modules m
                ORDER  BY m.categories'
        );
        return $query->execute();
    }

    // /**
    //  * @return Modules[] Returns an array of Modules objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('m.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Modules
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
