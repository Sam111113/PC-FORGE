<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * Contrôleur d'administration des utilisateurs
 *
 * Gère les opérations réservées aux administrateurs :
 * - Liste et recherche des utilisateurs
 * - Gestion des rôles utilisateurs
 * - Suppression des comptes utilisateurs
 */
#[IsGranted('ROLE_ADMIN')]
#[Route('/admin/user')]
final class AdminUserController extends AbstractController
{
    /**
     * [ADMIN] Affiche la liste de tous les utilisateurs avec recherche
     *
     * @param UserRepository $userRepository
     * @param Request $request
     * @return Response Page d'administration des utilisateurs
     */
    #[Route(name: 'app_admin_user_index', methods: ['GET'])]
    public function index(UserRepository $userRepository, Request $request): Response
    {
        $keywords = (string) $request->query->get('keywords', '');

        if ($keywords !== '') {
            $users = $userRepository->createQueryBuilder('u')
                ->where('u.email LIKE :k OR u.pseudo LIKE :k')
                ->setParameter('k', "%{$keywords}%")
                ->getQuery()
                ->getResult();
        } else {
            $users = $userRepository->findAll();
        }

        return $this->render('admin/user/index.html.twig', [
            'users' => $users,
            'keywords' => $keywords,
        ]);
    }

    /**
     * [ADMIN] Met à jour les rôles d'un utilisateur
     *
     * @param User $user
     * @param Request $request
     * @param EntityManagerInterface $em
     * @return Response Redirection vers la liste
     */
    #[Route('/{id}/update-roles', name: 'app_admin_user_update_roles', methods: ['POST'])]
    public function updateRoles(User $user, Request $request, EntityManagerInterface $em): Response
    {
        if (!$this->isCsrfTokenValid('update_roles' . $user->getId(), $request->request->get('_token'))) {
            throw $this->createAccessDeniedException('Token CSRF invalide');
        }

        $roles = $request->request->get('role');

        $user->setRoles([$roles]);
        $em->flush();

        $this->addFlash('success', 'Rôle mis à jour avec succès');
        return $this->redirectToRoute('app_admin_user_index');
    }

    /**
     * [ADMIN] Supprime un compte utilisateur
     *
     * @param Request $request
     * @param User $user
     * @param EntityManagerInterface $entityManager
     * @return Response Redirection vers la liste
     */
    #[Route('/{id}', name: 'app_admin_user_delete', methods: ['POST'])]
    public function delete(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $user->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($user);
            $entityManager->flush();

            $this->addFlash('success', 'Utilisateur supprimé avec succès');
        }

        return $this->redirectToRoute('app_admin_user_index', [], Response::HTTP_SEE_OTHER);
    }
}
