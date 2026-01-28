<?php

namespace App\Controller;

use App\Repository\BuildRepository;
use App\Repository\NewsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Contrôleur de la page d'accueil
 *
 * Gère l'affichage de la page principale du site PC Forge
 * avec le build du mois et la dernière actualité.
 */
final class DefaultController extends AbstractController
{
    /**
     * Affiche la page d'accueil du site
     *
     * Récupère et affiche :
     * - Le build du mois (build marqué isMonthBuild = true)
     * - La dernière actualité publiée
     *
     * @param NewsRepository $newsRepo Repository pour récupérer les actualités
     * @param BuildRepository $buildRepo Repository pour récupérer les builds
     * @return Response Page d'accueil avec le build du mois et la dernière news
     */
    #[Route('/Pc-Forge', name: 'app_home')]
    public function index(NewsRepository $newsRepo, BuildRepository $buildRepo): Response
    {
        $monthBuild = $buildRepo->findOneBy(['isMonthBuild'=> true]);
        $lastNews = $newsRepo->findOneby([], ['created_at' => 'DESC']);
        return $this->render('default/index.html.twig', [
            'news' => $lastNews,
            'monthBuild' => $monthBuild,
        ]);
    }
}
