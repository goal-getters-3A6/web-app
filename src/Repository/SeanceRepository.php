<?php

namespace App\Repository;

use App\Entity\Seance;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Seance>
 *
 * @method Seance|null find($id, $lockMode = null, $lockVersion = null)
 * @method Seance|null findOneBy(array $criteria, array $orderBy = null)
 * @method Seance[]    findAll()
 * @method Seance[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SeanceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Seance::class);
    }
    public function findAllImages()
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.imageseance IS NOT NULL') // Filtrer uniquement les enregistrements avec des images non nulles
            ->getQuery()
            ->getResult();
    }
    public function findByCriteria($criteria)
    {
        $query = $this->createQueryBuilder('s');

        if (!empty($criteria['nom'])) {
            $query->andWhere('s.nom = :nom')
                  ->setParameter('nom', $criteria['nom']);
        }

        if (!empty($criteria['jourseance'])) {
            $query->andWhere('s.jourseance = :jourseance')
                  ->setParameter('jourseance', $criteria['jourseance']);
        }

        if (!empty($criteria['numesalle'])) {
            $query->andWhere('s.numesalle = :numesalle')
                  ->setParameter('numesalle', $criteria['numesalle']);
        }

        return $query->getQuery()->getResult();
    }
    public function getSeanceStatsByDayOfWeek()
    {
        return $this->createQueryBuilder('s')
            ->select('s.nom AS typeSeance, COUNT(s.idseance) AS nombreSeances')
            ->groupBy('s.nom')
            ->getQuery()
            ->getResult();
    }
//    /**
//     * @return Seance[] Returns an array of Seance objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('s.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Seance
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
