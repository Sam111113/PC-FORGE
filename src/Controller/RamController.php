<?php

namespace App\Controller;

use App\Entity\Ram;
use App\Form\RamType;
use App\Repository\RamRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/ram')]
final class RamController extends AbstractController
{
    #[Route(name: 'app_ram_index', methods: ['GET'])]
    public function index(RamRepository $ramRepository): Response
    {
        return $this->render('ram/index.html.twig', [
            'rams' => $ramRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_ram_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $ram = new Ram();
        $form = $this->createForm(RamType::class, $ram);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($ram);
            $entityManager->flush();

            return $this->redirectToRoute('app_ram_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('ram/new.html.twig', [
            'ram' => $ram,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_ram_show', methods: ['GET'])]
    public function show(Ram $ram): Response
    {
        return $this->render('ram/show.html.twig', [
            'ram' => $ram,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_ram_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Ram $ram, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(RamType::class, $ram);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_ram_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('ram/edit.html.twig', [
            'ram' => $ram,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_ram_delete', methods: ['POST'])]
    public function delete(Request $request, Ram $ram, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$ram->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($ram);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_ram_index', [], Response::HTTP_SEE_OTHER);
    }
}
