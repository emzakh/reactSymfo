<?php

namespace App\Controller;


use App\Entity\Commentaires;
use App\Entity\Recettes;
use App\Form\CommentType;
use App\Form\RecetteEditType;
use App\Form\RecetteType;
use App\Repository\RecettesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\Date;

class RecetteController extends AbstractController
{
    /**
     * @Route("/recettes", name="recettes_index")
     * @param RecettesRepository $repo
     * @return Response
     */
    public function index(RecettesRepository $repo): Response
    {

        $recettes = $repo->findAll();


        return $this->render('recette/index.html.twig', [
            'recettes' =>$recettes

        ]);
    }

    /**
     * Permet de créer une recette
     * @Route("/recette/new", name="recette_create")
     * @IsGranted("ROLE_USER")
     * @param EntityManagerInterface $manager
     * @param Request $request
     * @return Response
     */
    public function create(EntityManagerInterface $manager, Request $request)
    {
        $recette = new Recettes();
        $form = $this ->createForm(RecetteType::class, $recette);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

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
            }
            $recette->setAuthor($this->getUser());
            $recette->setDate(new \DateTime());
            $manager->persist($recette);
            $manager->flush();

            $this->addFlash(
                'success',
                "La recette <strong>{$recette->getTitre()}</strong> a bien été enregistrée"
            );

            return $this->redirectToRoute('recettes_index'//,[

            );

        }

        return $this->render('recette/new.html.twig',[
            'myForm' => $form->createView()
        ]);
    }

    /**
     * Permet de modifier une recette
     * @Route("/recette/{slug}/edit", name="recette_edit")
     * @Security("(is_granted('ROLE_USER') and user === recette.getAuthor()) or is_granted('ROLE_ADMIN')", message="Cette recette ne vous appartient pas, vous ne pouvez pas la modifier")
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @param Recettes $recette
     * @return Response
     */
    public function edit(Request $request, EntityManagerInterface $manager, Recettes $recette)
    {
        $form = $this->createForm(RecetteEditType::class, $recette);
        $currentFile = $recette->getImgRecette();

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

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
            $recette->setSlug(''); // pour que initialize slug


            $manager->persist($recette);
            $manager->flush();

            $this->addFlash(
                'success',
                "La recette <strong>{$recette->getTitre()}</strong> a bien été modifiée"
            );

            return $this->redirectToRoute('recette_show',[
                'slug' => $recette->getSlug()
            ]);
        }


        return $this->render("recette/edit.html.twig",[
            "recette" => $recette,
            "myForm" => $form->createView()
        ]);

    }
    /**
     * Permet de supprimer une recette
     * @Route("/recette/{slug}/delete", name="recette_delete")
     * @Security("is_granted('ROLE_USER') and user === recette.getAuthor()", message="Vous n'avez pas le droit d'accèder à cette ressource")
     * @param Recettes $recette
     * @param EntityManagerInterface $manager
     * @return Response
     */
    public function delete(Recettes $recette, EntityManagerInterface $manager)
    {
        $this->addFlash(
            'success',
            "L'annonce <strong>{$recette->getTitre()}</strong> a bien été supprimée"
        );
        $manager->remove($recette);
        $manager->flush();
        return $this->redirectToRoute("recettes_index");
    }

    /**
     * Permet d'afficher une seule recette
     * @Route("/recette/{slug}", name="recette_show")

     * @param Recettes $recette
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */
    public function show(Recettes $recette, Request $request, EntityManagerInterface $manager)

    {
        $comment = new Commentaires();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $comment -> setRecette($recette->addComment($comment));
            $comment ->setAuthor($this->getUser());
            $manager->persist($comment);
            $manager->flush();

            $this->addFlash(
                'success',
                'Votre commentaire a bien été pris en compte!'
            );
        }

        return $this->render('recette/show.html.twig',[
            'recette' => $recette,
            'myForm' => $form->createView()
        ]);

    }




}
