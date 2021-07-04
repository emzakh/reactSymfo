<?php

namespace App\Controller;



use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\PaginationService;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class AdminUserController extends AbstractController
{
    /**
     * Permet d'afficher les utilisateurs
     * @Route("/admin/users/{page<\d+>?1}", name="admin_users_index")
     * @param $page
     * @param PaginationService $pagination
     * @return Response
     */
    public function index($page, PaginationService $pagination): Response
    {
        $pagination->setEntityClass(User::class)
            ->setPage($page)
            ->setLimit(10)
            ->setRoute('admin_users_index');

        return $this->render('admin/user/index.html.twig', [
            'pagination' => $pagination
        ]);
    }

    /**
     * Permet de supprimer un utilisateur
     * @Route("admin/user/{id}/delete", name="admin_users_delete")
     * @Security("is_granted('ROLE_ADMIN')")
     * @param User $user
     * @param EntityManagerInterface $manager
     * @return Response
     */
    public function delete(User $user, EntityManagerInterface $manager)
    {
        $this->addFlash(
            'success',
            "L'utilisateur <strong>{$user->getFullName()}</strong> a bien été supprimée"
        );
        $manager->remove($user);
        $manager->flush();
        return $this->redirectToRoute("admin_users_index");
    }






}