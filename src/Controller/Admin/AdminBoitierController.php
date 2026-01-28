<?php

namespace App\Controller\Admin;

use App\Entity\Boitier;
use App\Form\BoitierType;
use App\Repository\BoitierRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
#[Route('/admin/boitier', name: 'app_admin_boitier')]
final class AdminBoitierController extends AbstractController
{
    #[Route('/', name: '_index', methods: ['GET'])]
    public function index(BoitierRepository $boitierRepository): Response
    {
        return $this->render('admin/boitier/index.html.twig', [
            'boitiers' => $boitierRepository->findAll(),
        ]);
    }

    #[Route('/new', name: '_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $boitier = new Boitier();
        $form = $this->createForm(BoitierType::class, $boitier);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($boitier);
            $entityManager->flush();

            return $this->redirectToRoute('app_admin_boitier_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/boitier/new.html.twig', [
            'boitier' => $boitier,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/edit', name: '_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Boitier $boitier, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(BoitierType::class, $boitier);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_admin_boitier_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/boitier/edit.html.twig', [
            'boitier' => $boitier,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: '_delete', methods: ['POST'])]
    public function delete(Request $request, Boitier $boitier, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $boitier->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($boitier);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_admin_boitier_index', [], Response::HTTP_SEE_OTHER);
    }
}
