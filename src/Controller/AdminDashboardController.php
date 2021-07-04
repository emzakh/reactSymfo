<?php

namespace App\Controller;

use App\Service\StatsService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminDashboardController extends AbstractController
{
    /**
     * @Route("/admin", name="admin_dashboard_index")
     */
    public function index(StatsService $statsService): Response
    {

        $users = $statsService->getUsersCount();
        $recettes = $statsService->getRecettesCount();
        $produits = $statsService->getProduitsCount();
        $commentaires = $statsService->getCommentairesCount();

        $bestRecettes = $statsService->getRecettesStats('DESC');
        $worstRecettes = $statsService->getRecettesStats('ASC');

        // 'stats' => compact('users','ads','bookings','comments')

        return $this->render('admin/dashboard/index.html.twig', [
            'stats' => [
                'users' => $users,
                'produits' => $produits,
                'recettes' => $recettes,
                'commentaires' => $commentaires
            ],
            'bestRecettes' => $bestRecettes,
            'worstRecettes' => $worstRecettes
        ]);
    }
}