<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\BuildRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class ProfileController extends AbstractController
{
    #[Route('/profile', name: 'app_profile')]
    public function index(BuildRepository $buildsRepo): Response
    {
        $user = $this->getUser();

        if (!$user) {
            throw $this->createAccessDeniedException('Vous devez être connecté pour accéder à votre profil.');
        }
        $builds = $buildsRepo->findBy(['User' => $user], ['createdAt' => 'DESC']);
        return $this->render('profile/index.html.twig', [
            'user' => $user,
            'builds' => $builds,
        ]);
    }
}
