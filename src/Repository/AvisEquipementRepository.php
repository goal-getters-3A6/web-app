<?php

namespace App\Repository;

use App\Entity\Avisequipement;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Avisequipement>
 *
 * @method Avisequipement|null find($id, $lockMode = null, $lockVersion = null)
 * @method Avisequipement|null findOneBy(array $criteria, array $orderBy = null)
 * @method Avisequipement[]    findAll()
 * @method Avisequipement[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AvisEquipementRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Avisequipement::class);
    }

  
// Dans AvisEquipementRepository.php

public function findByCriteria($criteria)
{
    $queryBuilder = $this->createQueryBuilder('ae');

    // Si le critère `avisequipement.commaeq` est spécifié
    if (!empty($criteria['commaeq'])) {
        $queryBuilder
            ->andWhere('ae.commaeq = :commaeq')
            ->setParameter('commaeq', $criteria['commaeq']);
    }

    // Si le critère `avisequipement.idEq.nomeq` est spécifié
    if (!empty($criteria['nomeq'])) {
        $queryBuilder
            ->andWhere('ae.idEq.nomeq = :nomeq')
            ->setParameter('nomeq', $criteria['nomeq']);
    }

    // Si le critère `avisequipement.idUs.nom` est spécifié
    if (!empty($criteria['nom'])) {
        $queryBuilder
            ->andWhere('ae.idUs.nom = :nom')
            ->setParameter('nom', $criteria['nom']);
    }

    // Si le critère `avisequipement.idUs.prenom` est spécifié
    if (!empty($criteria['prenom'])) {
        $queryBuilder
            ->andWhere('ae.idUs.prenom = :prenom')
            ->setParameter('prenom', $criteria['prenom']);
    }

    return $queryBuilder->getQuery()->getResult();
}


//    /**
//     * @return Avisequipement[] Returns an array of Avisequipement objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('a.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Avisequipement
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
