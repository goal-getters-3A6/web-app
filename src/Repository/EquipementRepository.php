<?php

namespace App\Repository;

use App\Entity\Equipement;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Equipement>
 *
 * @method Equipement|null find($id, $lockMode = null, $lockVersion = null)
 * @method Equipement|null findOneBy(array $criteria, array $orderBy = null)
 * @method Equipement[]    findAll()
 * @method Equipement[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EquipementRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Equipement::class);
    }

   // EquipementRepository.php

   // Méthode pour récupérer tous les équipements triés par date de maintenance
   /* public function findAllSortedByMaintenanceDate($sortDirection = 'ASC')
    {
        return $this->createQueryBuilder('e')
            ->orderBy('e.datepremainte', $sortDirection)
            ->getQuery()
            ->getResult();
    }
   */
  public function countByCategory(string $category): int
    {
        return $this->createQueryBuilder('e')
            ->select('COUNT(e.idEq)')
            ->andWhere('e.categeq = :category')
            ->setParameter('category', $category)
            ->getQuery()
            ->getSingleScalarResult();
    }

   public function findByCriteria($criteria)
{
    $queryBuilder = $this->createQueryBuilder('ae');

    // Si le critère `equipement.nomeq` est spécifié
    if (!empty($criteria['nomeq'])) {
        $queryBuilder
            ->andWhere('ae.nomeq = :nomeq')
            ->setParameter('nomeq', $criteria['nomeq']);
    }

    // Si le critère `equipement.categeq` est spécifié
    if (!empty($criteria['categeq'])) {
        $queryBuilder
            ->andWhere('ae.categeq = :categeq')
            ->setParameter('categeq', $criteria['categeq']);
    }

    

    return $queryBuilder->getQuery()->getResult();
}


//    /**
//     * @return Equipement[] Returns an array of Equipement objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('e.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Equipement
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
