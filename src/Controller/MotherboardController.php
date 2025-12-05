<?php

namespace App\Controller;

use App\Entity\Motherboard;
use App\Form\MotherboardType;
use App\Repository\MotherboardRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/motherboard')]
final class MotherboardController extends AbstractController
{
    #[Route(name: 'app_motherboard_index', methods: ['GET'])]
    public function index(MotherboardRepository $motherboardRepository): Response
    {
        return $this->render('motherboard/index.html.twig', [
            'motherboards' => $motherboardRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_motherboard_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $motherboard = new Motherboard();
        $form = $this->createForm(MotherboardType::class, $motherboard);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($motherboard);
            $entityManager->flush();

            return $this->redirectToRoute('app_motherboard_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('motherboard/new.html.twig', [
            'motherboard' => $motherboard,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_motherboard_show', methods: ['GET'])]
    public function show(Motherboard $motherboard): Response
    {
        return $this->render('motherboard/show.html.twig', [
            'motherboard' => $motherboard,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_motherboard_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Motherboard $motherboard, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(MotherboardType::class, $motherboard);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_motherboard_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('motherboard/edit.html.twig', [
            'motherboard' => $motherboard,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_motherboard_delete', methods: ['POST'])]
    public function delete(Request $request, Motherboard $motherboard, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$motherboard->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($motherboard);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_motherboard_index', [], Response::HTTP_SEE_OTHER);
    }
}
