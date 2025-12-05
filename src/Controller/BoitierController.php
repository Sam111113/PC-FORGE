<?php

namespace App\Controller;

use App\Entity\Boitier;
use App\Form\BoitierType;
use App\Repository\BoitierRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/boitier')]
final class BoitierController extends AbstractController
{
    #[Route(name: 'app_boitier_index', methods: ['GET'])]
    public function index(BoitierRepository $boitierRepository): Response
    {
        return $this->render('boitier/index.html.twig', [
            'boitiers' => $boitierRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_boitier_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $boitier = new Boitier();
        $form = $this->createForm(BoitierType::class, $boitier);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($boitier);
            $entityManager->flush();

            return $this->redirectToRoute('app_boitier_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('boitier/new.html.twig', [
            'boitier' => $boitier,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_boitier_show', methods: ['GET'])]
    public function show(Boitier $boitier): Response
    {
        return $this->render('boitier/show.html.twig', [
            'boitier' => $boitier,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_boitier_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Boitier $boitier, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(BoitierType::class, $boitier);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_boitier_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('boitier/edit.html.twig', [
            'boitier' => $boitier,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_boitier_delete', methods: ['POST'])]
    public function delete(Request $request, Boitier $boitier, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$boitier->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($boitier);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_boitier_index', [], Response::HTTP_SEE_OTHER);
    }
}
