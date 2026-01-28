<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Security\AppAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Contrôleur d'inscription des utilisateurs
 *
 * Gère le processus d'inscription d'un nouvel utilisateur :
 * - Affichage du formulaire d'inscription
 * - Validation des données (email, pseudo, mot de passe)
 * - Création du compte avec hashage du mot de passe
 * - Connexion automatique après inscription réussie
 */
class RegistrationController extends AbstractController
{
    /**
     * Gère l'inscription d'un nouvel utilisateur
     *
     * Processus :
     * 1. Affiche le formulaire d'inscription
     * 2. Valide que les mots de passe correspondent
     * 3. Hash le mot de passe et crée l'utilisateur avec ROLE_USER
     * 4. Connecte automatiquement l'utilisateur après inscription
     *
     * @param Request $request Requête HTTP
     * @param UserPasswordHasherInterface $userPasswordHasher Service de hashage
     * @param Security $security Service de sécurité pour la connexion auto
     * @param EntityManagerInterface $entityManager Gestionnaire d'entités
     * @return Response Formulaire ou redirection après inscription
     */
    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, Security $security, EntityManagerInterface $entityManager): Response
    {
        
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $plainPassword = $form->get('plainPassword')->getData();

            $confirmedPassword = $form->get('confirmedPassword')->getData();

            if ($plainPassword !== $confirmedPassword) {
                $this->addFlash('error', 'Les mots de passe ne correspondent pas.');

                return $this->render('registration/register.html.twig', [
                    'registrationForm' => $form,
                ]);
            }

            $user->setPassword($userPasswordHasher->hashPassword($user, $plainPassword));
            $user->setRoles(['ROLE_USER']);
            $entityManager->persist($user);
            $entityManager->flush();

            return $security->login($user, AppAuthenticator::class, 'main');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form,
        ]);
    }
}
