<?php

namespace App\Controller;


use App\Entity\Produits;
use App\Repository\ProduitsRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class CategorieController extends AbstractController
{


    /**
     * Affichage par catégorie
     * @Route("/categorie/{categorie}", name="produit_categorie")
     * @return Response
     */

    public function show(ProduitsRepository $repo, $categorie)
    {

        $produits = $repo->findBy(
            ['categorie'=>$categorie]
        );

        $tableau = array('jardin', 'epices', 'potager');

        if(in_array($categorie,$tableau)){

            return $this->render('produit/categorie.html.twig',[
                'controller_name' => 'ProduitController',
                'produits'=>$produits
            ]);
        }else{
           throw $this->createNotFoundException('cette catégorie n\'existe pas');
        }

    }

}

