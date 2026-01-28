<?php

namespace App\Controller\Admin;

use App\Entity\Fan;
use App\Form\FanType;
use App\Repository\FanRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
#[Route('/admin/fan', name: 'app_admin_fan')]
final class AdminFanController extends AbstractController
{
    #[Route('/', name: '_index', methods: ['GET'])]
    public function index(FanRepository $fanRepository): Response
    {
        return $this->render('admin/fan/index.html.twig', [
            'fans' => $fanRepository->findAll(),
        ]);
    }

    #[Route('/new', name: '_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $fan = new Fan();
        $form = $this->createForm(FanType::class, $fan);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($fan);
            $entityManager->flush();

            return $this->redirectToRoute('app_admin_fan_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/fan/new.html.twig', [
            'fan' => $fan,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/edit', name: '_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Fan $fan, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(FanType::class, $fan);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_admin_fan_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/fan/edit.html.twig', [
            'fan' => $fan,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: '_delete', methods: ['POST'])]
    public function delete(Request $request, Fan $fan, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $fan->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($fan);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_admin_fan_index', [], Response::HTTP_SEE_OTHER);
    }
}
