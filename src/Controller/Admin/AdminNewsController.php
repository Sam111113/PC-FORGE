<?php

namespace App\Controller\Admin;

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
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * Contrôleur d'administration des actualités
 *
 * Gère les opérations réservées aux rédacteurs et administrateurs :
 * - Liste de toutes les news pour gestion
 * - Création de nouvelles actualités
 * - Modification des actualités existantes
 * - Suppression des actualités
 */
#[IsGranted(attribute: 'ROLE_REDACTEUR')]
#[Route('/admin/news')]
final class AdminNewsController extends AbstractController
{
    /**
     * [ADMIN] Affiche la liste de gestion de toutes les actualités
     *
     * @param NewsRepository $newsRepository
     * @return Response Page d'administration des news
     */
    #[Route(name: 'app_admin_news_index', methods: ['GET'])]
    public function index(NewsRepository $newsRepository): Response
    {
        return $this->render('admin/news/index.html.twig', [
            'newss' => $newsRepository->findAll(),
        ]);
    }

    /**
     * [ADMIN] Crée une nouvelle actualité
     *
     * @param Request $request
     * @param EntityManagerInterface $em
     * @param SluggerInterface $slugger
     * @param CustomContentSanitizer $sanitizer
     * @return Response Formulaire de création ou redirection après succès
     */
    #[Route('/new', name: 'app_admin_news_new', methods: ['GET', 'POST'])]
    public function new(
        Request $request,
        EntityManagerInterface $em,
        SluggerInterface $slugger,
        CustomContentSanitizer $sanitizer
    ): Response {
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

            $this->addFlash('success', 'Actualité créée avec succès');
            return $this->redirectToRoute('app_admin_news_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/news/new.html.twig', [
            'form' => $form,
        ]);
    }

    /**
     * [ADMIN] Modifie une actualité existante
     *
     * @param Request $request
     * @param News $news
     * @param EntityManagerInterface $entityManager
     * @param CustomContentSanitizer $sanitizer
     * @return Response Formulaire d'édition ou redirection après succès
     */
    #[Route('/{id}/edit', name: 'app_admin_news_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request,
        News $news,
        EntityManagerInterface $entityManager,
        CustomContentSanitizer $sanitizer
    ): Response {
        $form = $this->createForm(NewsType::class, $news);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $clean = $sanitizer->clean($news->getContent());
            $news->setContent($clean);

            $entityManager->flush();

            $this->addFlash('success', 'Actualité modifiée avec succès');
            return $this->redirectToRoute('app_admin_news_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/news/edit.html.twig', [
            'news' => $news,
            'form' => $form,
        ]);
    }

    /**
     * [ADMIN] Supprime une actualité
     *
     * @param Request $request
     * @param News $news
     * @param EntityManagerInterface $entityManager
     * @return Response Redirection vers la liste
     */
    #[Route('/{id}', name: 'app_admin_news_delete', methods: ['POST'])]
    public function delete(Request $request, News $news, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $news->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($news);
            $entityManager->flush();

            $this->addFlash('success', 'Actualité supprimée avec succès');
        }

        return $this->redirectToRoute('app_admin_news_index', [], Response::HTTP_SEE_OTHER);
    }
}
