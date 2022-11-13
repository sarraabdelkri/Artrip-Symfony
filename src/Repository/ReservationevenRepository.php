<?php

namespace App\Repository;

use App\Entity\Reservationeven;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Reservationeven|null find($id, $lockMode = null, $lockVersion = null)
 * @method Reservationeven|null findOneBy(array $criteria, array $orderBy = null)
 * @method Reservationeven[]    findAll()
 * @method Reservationeven[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ReservationevenRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Reservationeven::class);
    }
    public function orderByDate()
    {
        return $this->createQueryBuilder('r')
            ->orderBy('r.date', 'desc')
            ->getQuery()
            ->getResult();
    }

}

    // /**
    //  * @return Reservationeven[] Returns an array of Reservationeven objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('r.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Reservationeven
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }


