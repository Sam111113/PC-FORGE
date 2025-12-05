<?php

namespace App\Controller;

use App\Entity\Fan;
use App\Form\FanType;
use App\Repository\FanRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/fan')]
final class FanController extends AbstractController
{
    #[Route(name: 'app_fan_index', methods: ['GET'])]
    public function index(FanRepository $fanRepository): Response
    {
        return $this->render('fan/index.html.twig', [
            'fans' => $fanRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_fan_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $fan = new Fan();
        $form = $this->createForm(FanType::class, $fan);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($fan);
            $entityManager->flush();

            return $this->redirectToRoute('app_fan_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('fan/new.html.twig', [
            'fan' => $fan,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_fan_show', methods: ['GET'])]
    public function show(Fan $fan): Response
    {
        return $this->render('fan/show.html.twig', [
            'fan' => $fan,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_fan_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Fan $fan, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(FanType::class, $fan);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_fan_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('fan/edit.html.twig', [
            'fan' => $fan,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_fan_delete', methods: ['POST'])]
    public function delete(Request $request, Fan $fan, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$fan->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($fan);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_fan_index', [], Response::HTTP_SEE_OTHER);
    }
}
