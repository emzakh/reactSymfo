<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;

class StatsService{
    private $manager;

    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    public function getUsersCount(){
        return $this->manager->createQuery('SELECT COUNT(u) FROM App\Entity\User u')->getSingleScalarResult();
    }

    public function getRecettesCount()
    {
        return $this->manager->createQuery('SELECT COUNT(r) FROM App\Entity\Recettes r')->getSingleScalarResult();
    }

    public function getProduitsCount()
    {
        return $this->manager->createQuery('SELECT COUNT(p) FROM App\Entity\Produits p')->getSingleScalarResult();
    }

    public function getCommentairesCount()
    {
        return $this->manager->createQuery('SELECT COUNT(c) FROM App\Entity\Commentaires c')->getSingleScalarResult();
    }

    public function getRecettesStats($direction)
    {
        return $this->manager->createQuery(
            'SELECT AVG(c.rating) as note, r.titre, r.id, u.firstName, u.lastName
            FROM App\Entity\Commentaires c
            JOIN c.recette r
            JOIN r.author u
            GROUP BY r
            ORDER BY note '. $direction
        )
            ->setMaxResults(5)
            ->getResult();
    }

}