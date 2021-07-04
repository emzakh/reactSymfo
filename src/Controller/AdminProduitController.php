<?php

namespace App\Controller;

use App\Entity\Produits;
use App\Form\ProduitEditType;
use App\Form\ProduitType;
use App\Repository\ProduitsRepository;
use App\Service\PaginationService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class AdminProduitController extends AbstractController
{
    /**
     * Permet d'afficher l'ensemble des produits
     * @Route("/admin/produits/{page<\d+>?1}", name="admin_produits_index")
     * @param ProduitsRepository $repo
     * @return Response
     */
    public function index($page, PaginationService $pagination): Response
    {
        $pagination->setEntityClass(Produits::class)
            ->setPage($page)
            ->setLimit(10);

        return $this->render('admin/produit/index.html.twig', [
            'pagination' => $pagination
        ]);
    }


    /**
     * Permet de modifier un produit
     * @Route("/produit/{id}/edit", name="admin_produits_edit")
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @param Produits $produit
     * @return Response
     */
    public function edit(Request $request, EntityManagerInterface $manager, Produits $produit)
    {
        $form = $this->createForm(ProduitEditType::class, $produit);
        $currentFile = $produit->getImage();

        $form->handleRequest($request);


        if($form->isSubmitted() && $form->isValid()){

            $produit->setSlug(''); // pour que initialize slug
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
            }else{
                 if(!empty($currentFile)){
                     $produit->setImage($currentFile);
                 }
            }

            $manager->persist($produit);
            $manager->flush();

            $this->addFlash(
                'success',
                "Le produit <strong>{$produit->getNom()}</strong> a bien été modifiée"
            );

            return $this->redirectToRoute('admin_produits_index',[
                'id' => $produit->getId()
            ]);
        }


        return $this->render("admin/produit/edit.html.twig",[
            "produit" => $produit,
            "myForm" => $form->createView()
        ]);

    }

    /**
     * Permet de supprimer une recette
     * @Route("admin/produit/{id}/delete", name="admin_produits_delete")
     * @Security("is_granted('ROLE_ADMIN')")
     * @param Produits $produit
     * @param EntityManagerInterface $manager
     * @return Response
     */
    public function delete(Produits $produit, EntityManagerInterface $manager)
    {
        $this->addFlash(
            'success',
            "Le produit <strong>{$produit->getNom()}</strong> a bien été supprimée"
        );
        $manager->remove($produit);
        $manager->flush();
        return $this->redirectToRoute("admin_produits_index");
    }



}