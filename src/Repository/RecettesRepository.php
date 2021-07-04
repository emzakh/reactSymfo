<?php

namespace App\Repository;

use App\Entity\Recettes;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Recettes|null find($id, $lockMode = null, $lockVersion = null)
 * @method Recettes|null findOneBy(array $criteria, array $orderBy = null)
 * @method Recettes[]    findAll()
 * @method Recettes[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RecettesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Recettes::class);
    }

    public function findBestRecettes($limit)
    {
        return $this->createQueryBuilder('r')
            ->select('r as recette, AVG(c.rating) as avgRatings')
            ->join('r.commentaires','c')
            ->groupBy('r')
            ->orderBy('avgRatings','DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult()
            ;
    }
}
