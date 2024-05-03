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
    public function findAllNomSeance()
    {
        $result = $this->createQueryBuilder('s')
            ->select('s.nomseance')
            ->distinct()
            ->getQuery()
            ->getResult();

        // Transformation du tableau multidimensionnel en un tableau simple de noms de séance
        $noms = [];
        foreach ($result as $row) {
            $noms[] = $row['nom'];
        }

        return $noms;
    
    }
    public function findAllJourSeance()
    {
        $result = $this->createQueryBuilder('s')
        ->select('s.jourseance')
        ->distinct()
        ->getQuery()
        ->getResult();

    // Transformation du tableau multidimensionnel en un tableau simple de jours de séance
    $jours = [];
    foreach ($result as $row) {
        $jours[] = $row['jourseance'];
    }

    return $jours;
    }
    public function findByNom($nom)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.nom = :nom')
            ->setParameter('nom', $nom)
            ->getQuery()
            ->getResult();
    }

    // Méthode pour rechercher les séances par jour
    public function findByJour($jour)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.jourseance = :jour')
            ->setParameter('jour', $jour)
            ->getQuery()
            ->getResult();
    }
    public function findSeanceByNom($nom)
    {
        $qb = $this->createQueryBuilder('s')
            ->where('s.nom LIKE :nom')
            ->setParameter('nom', '%' . $nom . '%');

        return $qb->getQuery()->getResult();
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
