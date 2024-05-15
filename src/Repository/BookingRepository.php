<?php

namespace App\Repository;

use App\Entity\User;
use App\Entity\Booking;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<Booking>
 */
class BookingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Booking::class);
    }

    // Je récupère les réservations payées dont la date de départ est postérieure à aujourd'hui
    public function findFutureBookings(User $user): array
    {
        return $this->createQueryBuilder('b')
            ->where('b.booker = :user')
            ->andWhere('b.status = :status')
            ->andWhere('b.endDateAt > :now')
            ->setParameter('user', $user)
            ->setParameter('status', 'PAID')
            ->setParameter('now', new \DateTime())
            ->getQuery()
            ->getResult();
    }

    // Je récupère les réservations payées dont la date de fin est antérieure à aujourd'hui
    public function findPastBookings(User $user): array
    {
        return $this->createQueryBuilder('b')
            ->where('b.booker = :user')
            ->andWhere('b.status = :status')
            ->andWhere('b.endDateAt < :now')
            ->setParameter('user', $user)
            ->setParameter('status', 'PAID')
            ->setParameter('now', new \DateTime())
            ->getQuery()
            ->getResult();
    }

    //    /**
    //     * @return Booking[] Returns an array of Booking objects
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

    //    public function findOneBySomeField($value): ?Booking
    //    {
    //        return $this->createQueryBuilder('b')
    //            ->andWhere('b.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
