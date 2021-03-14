<?php

namespace App\Repository;

use App\Entity\Programme;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Programme|null find($id, $lockMode = null, $lockVersion = null)
 * @method Programme|null findOneBy(array $criteria, array $orderBy = null)
 * @method Programme[]    findAll()
 * @method Programme[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProgrammeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Programme::class);
    }

    public function getProgramme($id){

        $qb = $this->createQueryBuilder('p')
            ->innerJoin('p.modules', 'm')
            ->andWhere('p.sessions = :id')
            ->setParameter(':id', $id)
            // ->add('orderBy', 'p.modules ASC');
            ->add('orderBy', 'm.categories ASC');
    
        $query = $qb->getQuery();
    
        return $query->execute();
    
        }

        public function verifProgrammeExist($id, $module){

            $qb = $this->createQueryBuilder('p')
                ->where('p.sessions = :id')
                ->andWhere('p.modules = :module')
                ->setParameter(':id', $id)
                ->setParameter(':module', $module);
        
            $query = $qb->getQuery();
        
            return $query->execute();
        
            }

        public function deleteProgrammeByModule($modules) {

            $qb = $this->createQueryBuilder('m')
                ->where('m.modules = :id')
                ->setParameter('id', $modules);

                $query = $qb->getQuery();

                return $query->execute();
        }

        public function getSessionByModule($id){

            $qb = $this->createQueryBuilder('p')
                ->where('p.modules = :id')
                ->setParameter(':id', $id)
                ->add('orderBy', 'p.sessions ASC');
        
            $query = $qb->getQuery();
        
            return $query->execute();
        
            }
    
    // /**
    //  * @return Programme[] Returns an array of Programme objects
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
    public function findOneBySomeField($value): ?Programme
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
