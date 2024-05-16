<?php

namespace App\Repository;

use App\Entity\Reclamation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Reclamation>
 *
 * @method Reclamation|null find($id, $lockMode = null, $lockVersion = null)
 * @method Reclamation|null findOneBy(array $criteria, array $orderBy = null)
 * @method Reclamation[]    findAll()
 * @method Reclamation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ReclamationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Reclamation::class);
    }

//    /**
//     * @return Reclamation[] Returns an array of Reclamation objects
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

//    public function findOneBySomeField($value): ?Reclamation
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }


public function findByCriteria(array $criteria): array
{
    $queryBuilder = $this->createQueryBuilder('r');

   //filtre par catégory
    if (!empty($criteria['categorierec'])) {
        $queryBuilder
            ->andWhere('r.categorierec = :categorierec')
            ->setParameter('categorierec', $criteria['categorierec']);
    }

    if (!empty($criteria['descriptionrec'])) {
        $queryBuilder
            ->andWhere('r.descriptionrec LIKE :descriptionrec')
            ->setParameter('descriptionrec', '%' . $criteria['descriptionrec'] . '%');
    }

    
    if (!empty($criteria['servicerec'])) {
        $queryBuilder
            ->andWhere('r.servicerec = :servicerec')
            ->setParameter('servicerec', $criteria['servicerec']);
    }

    return $queryBuilder->getQuery()->getResult();
}

public function getStatsByCategory(): array
{
    //On peut y accéder directement via le repository en utilisant la méthode createQueryBuilder
    $queryBuilder = $this->createQueryBuilder('r')
        ->select('r.categorierec, COUNT(r.idrec) as total')
        ->groupBy('r.categorierec');

    $result = $queryBuilder->getQuery()->getResult();

    $stats = [];
    foreach ($result as $row) {
        $stats[$row['categorierec']] = $row['total'];
    }

    return $stats;
}


}
