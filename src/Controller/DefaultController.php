<?php

namespace App\Controller;

use App\Repository\BuildRepository;
use App\Repository\NewsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class DefaultController extends AbstractController
{
    #[Route('/Pc Forge', name: 'app_home')]
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
