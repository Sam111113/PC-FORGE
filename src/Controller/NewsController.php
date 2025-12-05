<?php

namespace App\Controller;

use App\Entity\News;
use App\Form\NewsType;
use App\Repository\NewsRepository;
use App\Service\CustomContentSanitizer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/news')]
final class NewsController extends AbstractController
{
    #[Route(name: 'app_news_index', methods: ['GET'])]
    public function index(NewsRepository $newsRepository): Response
    {
        return $this->render('news/index.html.twig', [
            'news' => $newsRepository->findAll(),
        ]);
    }
    #[Route('/adminView', name: 'app_news_adminView', methods: ['GET'])]
    public function adminIndex(NewsRepository $newsRepository): Response
    {
        return $this->render('news/adminView.html.twig', [
            'newss' => $newsRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_news_new', methods: ['GET', 'POST'])]
    public function new(
        Request $request,
        EntityManagerInterface $em,
        SluggerInterface $slugger,
        CustomContentSanitizer $sanitizer
    ) {
        $news = new News();
        $form = $this->createForm(NewsType::class, $news);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Sanitizer du HTML
            $clean = $sanitizer->clean($news->getContent());
            $news->setContent($clean);

            // Slug auto si vide
            if (!$news->getSlug()) {
                $news->setSlug(strtolower($slugger->slug($news->getTitre())));
            }

            // Dates
            $news->setCreatedAt(new \DateTimeImmutable());

            $em->persist($news);
            $em->flush();

            $newsId = $news->getId();

            return $this->redirectToRoute('app_news_show', [
                'slug' => $news->getSlug(),
                'id' => $newsId
            ]);
        }

        return $this->render('news/new.html.twig', [
            'form' => $form,
        ]);
    }

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

    #[Route('/{id}/edit', name: 'app_news_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, News $news, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(NewsType::class, $news);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_news_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('news/edit.html.twig', [
            'news' => $news,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_news_delete', methods: ['POST'])]
    public function delete(Request $request, News $news, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $news->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($news);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_news_index', [], Response::HTTP_SEE_OTHER);
    }
}
