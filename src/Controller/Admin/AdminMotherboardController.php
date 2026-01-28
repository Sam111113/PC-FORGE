<?php

namespace App\Controller\Admin;

use App\Entity\Motherboard;
use App\Form\MotherboardType;
use App\Repository\MotherboardRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
#[Route('/admin/motherboard', name: 'app_admin_motherboard')]
final class AdminMotherboardController extends AbstractController
{
    #[Route('/', name: '_index', methods: ['GET'])]
    public function index(MotherboardRepository $motherboardRepository): Response
    {
        return $this->render('admin/motherboard/index.html.twig', [
            'motherboards' => $motherboardRepository->findAll(),
        ]);
    }

    #[Route('/new', name: '_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $motherboard = new Motherboard();
        $form = $this->createForm(MotherboardType::class, $motherboard);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($motherboard);
            $entityManager->flush();

            return $this->redirectToRoute('app_admin_motherboard_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/motherboard/new.html.twig', [
            'motherboard' => $motherboard,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/edit', name: '_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Motherboard $motherboard, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(MotherboardType::class, $motherboard);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_admin_motherboard_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/motherboard/edit.html.twig', [
            'motherboard' => $motherboard,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: '_delete', methods: ['POST'])]
    public function delete(Request $request, Motherboard $motherboard, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $motherboard->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($motherboard);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_admin_motherboard_index', [], Response::HTTP_SEE_OTHER);
    }
}
