<?php

namespace App\Controller;

use App\Entity\Recettes;
use App\Form\RecetteEditType;
use App\Form\RecetteType;
use App\Repository\RecettesRepository;
use App\Service\PaginationService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class AdminRecetteController extends AbstractController
{
    /**
     * Permet d'afficher l'ensemble des recettes
     * @Route("/admin/recettes/{page<\d+>?1}", name="admin_recettes_index")
     * @param RecettesRepository $repo
     * @return Response
     */
    public function index($page, PaginationService $pagination): Response
    {

        $pagination->setEntityClass(Recettes::class)
            ->setPage($page)
            ->setLimit(10)
            ->setRoute('admin_recettes_index');
        /*
   return $this->render('admin/ad/index.html.twig', [
       'ads' => $pagination->getData(),
       'pages' => $pagination->getPages(),
       'page' => $page
   ]);
   */

        return $this->render('admin/recette/index.html.twig', [
            'pagination' => $pagination
        ]);
    }


    /**
     * Permet de modifier une recette
     * @Route("/admin/recettes/{id}/edit", name="admin_recettes_edit")
     * @param Recettes $recette
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */
    public function edit(Recettes $recette, Request $request, EntityManagerInterface $manager)
    {
        $form = $this->createForm(RecetteEditType::class, $recette);
        $currentFile = $recette->getImgRecette();
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $file = $form['imgRecette']->getData();
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

                $recette->setImgRecette($newFilename);
            }else{
                if(!empty($currentFile)){
                    $recette->setImgRecette($currentFile);
                }
            }
            $manager->persist($recette);
            $manager->flush();

            $this->addFlash(
                "success",
                "La recette <strong>{$recette->getTitre()}</strong> a bien été modifiée"
            );

            return $this->redirectToRoute('admin_recettes_index',[
                'id' => $recette->getId()
            ]);
        }


        return $this->render("admin/recette/edit.html.twig",[
            'recette' => $recette,
            'myForm' => $form->createView()
        ]);
    }


    /**
     * Permet de supprimer une recette
     * @Route("admin/recette/{id}/delete", name="admin_recettes_delete")
     * @Security("is_granted('ROLE_ADMIN')")
     * @param Recettes $recette
     * @param EntityManagerInterface $manager
     * @return Response
     */
    public function delete(Recettes $recette, EntityManagerInterface $manager)
    {
        $this->addFlash(
            'success',
            "La recette <strong>{$recette->getTitre()}</strong> a bien été supprimée"
        );
        $manager->remove($recette);
        $manager->flush();
        return $this->redirectToRoute("admin_recettes_index");
    }
}