<?php

namespace App\Repository;

use App\Entity\Reservation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Reservation>
 *
 * @method Reservation|null find($id, $lockMode = null, $lockVersion = null)
 * @method Reservation|null findOneBy(array $criteria, array $orderBy = null)
 * @method Reservation[]    findAll()
 * @method Reservation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ReservationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Reservation::class);
    }

//    /**
//     * @return Reservation[] Returns an array of Reservation objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('r.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Reservation
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
// ReservationRepository.php


    // ...

    public function searchReservations($userName, $sessionName, $sessionDay)
    {
        $queryBuilder = $this->createQueryBuilder('r')
            ->leftJoin('r.iduser', 'u')
            ->leftJoin('r.ids', 's')
            ->where('u.nom LIKE :userName OR :userName IS NULL')
            ->andWhere('s.nom LIKE :sessionName OR :sessionName IS NULL')
            ->andWhere('s.jourseance = :sessionDay OR :sessionDay IS NULL')
            ->setParameter('userName', $userName)
            ->setParameter('sessionName', $sessionName)
            ->setParameter('sessionDay', $sessionDay);
    
        return $queryBuilder->getQuery()->getResult();
    }
    public function findAllOrderedBySessionDay()
    {
        return $this->createQueryBuilder('r')
            ->leftJoin('r.ids', 's')
            ->orderBy('s.jourseance', 'ASC')
            ->getQuery()
            ->getResult();
    }
    public function getReservationStatisticsBySession()
    {
        $entityManager = $this->getEntityManager();

        $query = $entityManager->createQuery(
            'SELECT s.nomseance, COUNT(r) as totalReservations
            FROM App\Entity\Reservation r
            JOIN r.ids s
            GROUP BY s.nom'
        );

        return $query->getResult();
    }
    public function countReservationsBySessionName()
    {
        /** @var QueryBuilder $qb */
        $qb = $this->createQueryBuilder('r');
        
        return $qb->select('s.nom as session_name', 'COUNT(r.idreservation) as reservation_count')
            ->leftJoin('r.ids', 's')
            ->groupBy('s.nom')
            ->getQuery()
            ->getResult();
    }
    public function getReservationCountForSeance($seanceId)
{
    return $this->createQueryBuilder('r')
        ->select('count(r.idreservation)')
        ->where('r.ids = :seanceId') // Utiliser 'ids' au lieu de 'seance'
        ->setParameter('seanceId', $seanceId)
        ->getQuery()
        ->getSingleScalarResult();
}

}
