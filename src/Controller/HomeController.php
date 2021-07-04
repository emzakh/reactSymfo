<?php

namespace App\Controller;


use App\Repository\RecettesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="homepage")
     */
    public function index(RecettesRepository $recettesRepo): Response
    {
            return $this->render('home.html.twig', [
                'recettes' => $recettesRepo->findBestRecettes(2)

            ]);
    }

    /**
     * @Route("/hildegarde", name="hildegarde")
     */
    public function show() :Response
    {
        return $this->render('hildegarde.html.twig');
    }


    }