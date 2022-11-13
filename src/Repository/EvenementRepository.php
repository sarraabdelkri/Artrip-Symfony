<?php

namespace App\Repository;

use App\Entity\Evenement;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;

/**
 * @method Evenement|null find($id, $lockMode = null, $lockVersion = null)
 * @method Evenement|null findOneBy(array $criteria, array $orderBy = null)
 * @method Evenement[]    findAll()
 * @method Evenement[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EvenementRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Evenement::class);
    }

    public function orderById()
    {
        $qb = $this->createQueryBuilder('s')
            ->orderBy('s.id', 'desc')
            ->setMaxResults(30);
        return $qb->getQuery()
            ->getResult();
    }
    public function listEvenemetByTitre()
    {
        return $this->createQueryBuilder('I')
            ->where('I.titre = 1')
            ->getQuery()
            ->getResult();
    }
    public function orderByDate()
    {
        return $this->createQueryBuilder('s')
            ->orderBy('s.date', 'desc')
            ->setMaxResults(4)
            ->getQuery()
            ->getResult();
    }

    public function listEvenemetByidtrier($id)
    {
        return $this->createQueryBuilder('I')
            ->join('I.evenement', 'T')
            ->addSelect('T')
            ->where('T.id=:id')
            ->setParameter('id',$id)
            ->orderBy('I.date', 'ASC')
            ->getQuery()
            ->getResult();
    }


    /**
     * @return Query
     */

    public function findAllVisibleQuery(): Query
    {
        return $this->createQueryBuilder('I')
            ->getQuery();

    }
    /**
     * @return \Doctrine\ORM\Query
     */

    public function findAllVisibleQuerybytitre(): \Doctrine\ORM\Query
    {
        return $this->createQueryBuilder('I')
            ->where('I.titre = 1')
            ->getQuery();

    }
    public function searchEvenement($evenement)
    {
        return $this->createQueryBuilder('e')
            ->andWhere('c.titre LIKE :titre')
            ->setParameter('titre', '%'.$evenement.'%')
            ->getQuery()
            ->execute();
    }


}

    /**
    //  * @return Evenement[] Returns an array of Evenement objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('e.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Evenement
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

}
