<?php

namespace App\Controller\Admin;

use App\Entity\Build;
use App\Form\BuildType;
use App\Repository\BuildRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * Contrôleur d'administration des builds
 *
 * Gère les opérations réservées aux administrateurs :
 * - Liste complète de tous les builds (utilisateurs + prebuilds)
 * - Création de nouveaux builds (notamment les prebuilds)
 * - Modification de tous les builds
 * - Suppression de tous les builds
 */
#[IsGranted('ROLE_ADMIN')]
#[Route('/admin/build')]
final class AdminBuildController extends AbstractController
{
    /**
     * [ADMIN] Affiche la liste complète de tous les builds pour gestion
     *
     * @param BuildRepository $buildRepository
     * @return Response Page d'administration avec tous les builds
     */
    #[Route(name: 'app_admin_build_index', methods: ['GET'])]
    public function index(BuildRepository $buildRepository): Response
    {
        $builds = $buildRepository->findAll();

        return $this->render('admin/build/index.html.twig', [
            'builds' => $builds,
        ]);
    }

    /**
     * [ADMIN] Crée un nouveau build (notamment pour les prebuilds)
     *
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return Response Formulaire de création ou redirection après succès
     */
    #[Route('/new', name: 'app_admin_build_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $build = new Build();
        $form = $this->createForm(BuildType::class, $build);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($build->getImage() && !$build->getImage()->getFilename()) {
                $build->setImage(null);
            }
            // Le prix total sera calculé automatiquement via le hook PrePersist
            $entityManager->persist($build);
            $entityManager->flush();

            $this->addFlash('success', 'Build créé avec succès');
            return $this->redirectToRoute('app_admin_build_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/build/new.html.twig', [
            'build' => $build,
            'form' => $form,
        ]);
    }

    /**
     * [ADMIN] Modifie n'importe quel build
     *
     * @param Request $request
     * @param Build $build
     * @param EntityManagerInterface $entityManager
     * @return Response Formulaire d'édition ou redirection après succès
     */
    #[Route('/{id}/edit', name: 'app_admin_build_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Build $build, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(BuildType::class, $build);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($build->getImage() && !$build->getImage()->getFilename()) {
                $build->setImage(null);
            }
            $entityManager->flush();

            $this->addFlash('success', 'Build modifié avec succès');
            return $this->redirectToRoute('app_admin_build_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/build/edit.html.twig', [
            'build' => $build,
            'form' => $form,
        ]);
    }

    /**
     * [ADMIN] Supprime n'importe quel build
     *
     * @param Request $request
     * @param Build $build
     * @param EntityManagerInterface $entityManager
     * @return Response Redirection vers la liste
     */
    #[Route('/{id}', name: 'app_admin_build_delete', methods: ['POST'])]
    public function delete(Request $request, Build $build, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $build->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($build);
            $entityManager->flush();

            $this->addFlash('success', 'Build supprimé avec succès');
        }

        return $this->redirectToRoute('app_admin_build_index', [], Response::HTTP_SEE_OTHER);
    }
}
