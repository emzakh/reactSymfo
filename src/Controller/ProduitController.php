<?php

namespace App\Controller;

use App\Entity\PropertySearch;
use App\Entity\Recettes;
use App\Form\ProduitEditType;
use App\Form\ProduitType;
use App\Entity\Produits;
use App\Form\PropertySearchType;
use App\Repository\ProduitsRepository;
use App\Service\PaginationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ProduitController extends AbstractController
{
    /**
     * @Route("/produits/{categorie}/{page<\d+>?1}", name="produits_index")
     * @param $page
     * @param PaginationService $pagination
     * @return Response
     */
    public function index($page, PaginationService $pagination, $categorie="all"): Response
    {
        if($categorie == "all"){
            $pagination->setEntityClass(Produits::class)
                ->setPage($page)
                ->setLimit(6);
        }else{
            $pagination->setEntityClass(Produits::class)
                ->setPage($page)
                ->setCategory($categorie)
                ->setLimit(6);
        }



        return $this->render('produit/index.html.twig', [
            'pagination' => $pagination,

        ]);
    }
        // dump($produits);




    /**
     * Permet de créer un produit
     * @Route("/produit/new", name="produit_create")
     * @param EntityManagerInterface $manager
     * @param Request $request
     * @return Response
     */

    public function create(EntityManagerInterface $manager, Request $request)
    {
        $produit = new Produits();
        $form = $this ->createForm(ProduitType::class, $produit);

        $form->handleRequest($request);


            if($form->isSubmitted() && $form->isValid()){


                // on ajoute l'auteur mais attention maintenant il y a un risque de bug si on n'est pas connecté (à corriger)
             //   $produit->setAuthor($this->getUser());
                $file = $form['image']->getData();
                if(!empty($file)){
                    $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                    $safeFilename = $originalFilename.'-'.rand();
                    $newFilename = $safeFilename.'-'.uniqid().'.'.$file->guessExtension();
                    try{
                        $file->move(
                            $this->getParameter('uploads_directory'),
                            $newFilename
                        );
                    }
                    catch(FileException $e)
                    {
                        return $e->getMessage();
                    }

                    $produit->setImage($newFilename);
                }

                $manager->persist($produit);
                $manager->flush();
                $this->addFlash(
                    'success',
                    "Le produit <strong>{$produit->getNom()}</strong> a bien été enregistrée"
                );


            return $this->redirectToRoute('produit_show',[
                'slug' => $produit->getSlug()
            ]);

        }

        return $this->render('produit/new.html.twig',[
            'myForm' => $form->createView()
        ]);
    }


    /**
     * Permet de modifier un produit
     * @Route("/produit/{slug}/edit", name="produit_edit")
     *
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @param Produits $produit
     * @return Response
     */
    public function edit(Request $request, EntityManagerInterface $manager, Produits $produit)
    {
        $form = $this->createForm(ProduitEditType::class, $produit);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $file = $form['image']->getData();
            if(!empty($file)){
                $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $originalFilename.'-'.rand();
                $newFilename = $safeFilename.'-'.uniqid().'.'.$file->guessExtension();
                try{
                    $file->move(
                        $this->getParameter('uploads_directory'),
                        $newFilename
                    );
                }
                catch(FileException $e)
                {
                    return $e->getMessage();
                }

                $produit->setImage($newFilename);
            }
            $produit->setSlug(''); // pour que initialize slug

     //       foreach($produit->getImages() as $image){
       //         $image->setAd($produit);
         //       $manager->persist($image);
           // }

            $manager->persist($produit);
            $manager->flush();

            $this->addFlash(
                'success',
                "Le produit <strong>{$produit->getNom()}</strong> a bien été modifiée"
            );

            return $this->redirectToRoute('produit_show',[
                'slug' => $produit->getSlug()
            ]);
        }


        return $this->render("produit/edit.html.twig",[
            "produit" => $produit,
            "myForm" => $form->createView()
        ]);

    }

    /**
     * Permet d'afficher un seul produit
     * @Route("/produit/{slug}", name="produit_show")
     * @param Produits $produit
     *
     * @return Response
     */
    public function show(Produits $produit)
    {
        //$repo = $this->getDoctrine()->getRepository(Ad::class);
        //$ad = $repo->findOneBySlug($slug);


        //dump($ad);

        return $this->render('produit/show.html.twig',[
            'produit' => $produit,

        ]);

    }

    /**
     * Page recherche
     * @Route("/search", name="produit_search")
     * @param Request $request
     * @return Response
     */
    public function search(Request $request)
    {
        $propertySearch = new PropertySearch();
        $form = $this->createForm(PropertySearchType::class, $propertySearch);
        $form->handleRequest($request);
        //initialement le tableau des produits est vide,
        //c.a.d on affiche les articles que lorsque l'utilisateur clique sur le bouton rechercher
        $produitsSearch = [];

        if ($form->isSubmitted() && $form->isValid()) {
            //on récupère le nom de produit tapé dans le formulaire
            $nom = $propertySearch->getNom();

            if ($nom != "" )
                //si on a fourni un nom de produit on affiche tous les produits qui match
                $produitsSearch = $this->getDoctrine()->getRepository(Produits::class)->findBy([
                    'nom' => $nom,
                ]);
            else
                //si si aucun nom n'est fourni on affiche tous les produits
                $produitsSearch = $this->getDoctrine()->getRepository(Produits::class)->findAll();
        }
        return $this->render('search/search.html.twig',[

            'form' => $form->createView(),
            'produits' => $produitsSearch
        ]);
    }






}

