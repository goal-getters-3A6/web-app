<?php

namespace App\Repository;

use App\Entity\Plat;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository

 * @extends ServiceEntityRepository<Plat>
 *
 * @method Plat|null find($id, $lockMode = null, $lockVersion = null)
 * @method Plat|null findOneBy(array $criteria, array $orderBy = null)
 * @method Plat[]    findAll()
 * @method Plat[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */

use Doctrine\Common\Collections\Criteria;

class PlatRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Plat::class);
    }
    
    
    public function search($keyword)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.nomp LIKE :keyword OR p.descp LIKE :keyword OR p.alergiep LIKE :keyword')
            ->setParameter('keyword', '%' . $keyword . '%')
            ->getQuery()
            ->getResult();
    }
    
    
    public function findFavoritedPlatsForUser(User $user): array
    {
        return $this->createQueryBuilder('p')
            ->innerJoin('p.favoritedBy', 'u')
            ->where('u.id = :userId')
            ->setParameter('userId', $user->getId())
            ->getQuery()
            ->getResult();
    }

  
    public function findByCaloriesRange(int $lowerRange, int $upperRange): array
    {
        $criteria = Criteria::create()
            ->where(Criteria::expr()->gte('calories', $lowerRange))
            ->andWhere(Criteria::expr()->lte('calories', $upperRange));

        return $this->matching($criteria)->toArray();
    }
}
