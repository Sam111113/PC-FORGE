<?php

namespace App\Controller;

use App\Entity\News;
use App\Repository\NewsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Contrôleur public des actualités
 *
 * Gère les opérations accessibles à tous les visiteurs :
 * - Affichage de la liste des actualités
 * - Affichage du détail d'une actualité
 *
 * Les opérations d'administration (création, modification, suppression)
 * sont dans AdminNewsController (ROLE_REDACTEUR requis)
 */
#[Route('/news')]
final class NewsController extends AbstractController
{
    /**
     * Affiche la liste de toutes les actualités
     *
     * @param NewsRepository $newsRepository Repository des actualités
     * @return Response Page listant toutes les actualités
     */
    #[Route(name: 'app_news_index', methods: ['GET'])]
    public function index(NewsRepository $newsRepository): Response
    {
        return $this->render('news/index.html.twig', [
            'news' => $newsRepository->findAll(),
        ]);
    }

    /**
     * Affiche le détail d'une actualité avec les dernières news associées
     *
     * @param News $news Actualité à afficher (injection par ParamConverter)
     * @param NewsRepository $newsRepo Repository pour récupérer les actualités récentes
     * @return Response Page de détail de l'actualité
     */
    #[Route('/{id}', name: 'app_news_show', methods: ['GET'])]
    public function show(News $news, NewsRepository $newsRepo): Response
    {
        $latestNews = $newsRepo->createQueryBuilder('n')
            ->where('n.id != :currentId')
            ->setParameter('currentId', $news->getId())
            ->orderBy('n.created_at', 'DESC')
            ->setMaxResults(6)
            ->getQuery()
            ->getResult();

        return $this->render('news/show.html.twig', [
            'news' => $news,
            'latestNews' => $latestNews,
        ]);
    }
}
