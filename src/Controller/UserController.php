<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * Contrôleur public des utilisateurs
 *
 * Gère les opérations accessibles aux utilisateurs connectés :
 * - Édition de son propre profil
 *
 * Les opérations d'administration (liste, gestion des rôles, suppression)
 * sont dans AdminUserController
 */
#[Route('/user')]
final class UserController extends AbstractController
{
    /**
     * Permet à un utilisateur d'éditer son propre profil
     *
     * Vérifie que l'utilisateur connecté est bien le propriétaire du profil.
     * Les admins utilisent AdminUserController pour gérer tous les utilisateurs.
     *
     * @param Request $request Requête HTTP
     * @param User $user Utilisateur à éditer (injection par ParamConverter)
     * @param EntityManagerInterface $entityManager Entity manager pour la persistance
     * @return Response Formulaire ou redirection après modification
     */
    #[IsGranted('ROLE_USER')]
    #[Route('/{id}/edit', name: 'app_user_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        $current = $this->getUser();

        if ($current !== $user) {
            throw $this->createAccessDeniedException('Vous ne pouvez pas modifier ce profil.');
        }

        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Profil modifié avec succès');
            return $this->redirectToRoute('app_profile', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('user/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }
}
