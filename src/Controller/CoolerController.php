<?php

namespace App\Controller;

use App\Entity\Cooler;
use App\Form\CoolerType;
use App\Repository\CoolerRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/cooler')]
final class CoolerController extends AbstractController
{
    #[Route(name: 'app_cooler_index', methods: ['GET'])]
    public function index(CoolerRepository $coolerRepository): Response
    {
        return $this->render('cooler/index.html.twig', [
            'coolers' => $coolerRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_cooler_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $cooler = new Cooler();
        $form = $this->createForm(CoolerType::class, $cooler);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($cooler);
            $entityManager->flush();

            return $this->redirectToRoute('app_cooler_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('cooler/new.html.twig', [
            'cooler' => $cooler,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_cooler_show', methods: ['GET'])]
    public function show(Cooler $cooler): Response
    {
        return $this->render('cooler/show.html.twig', [
            'cooler' => $cooler,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_cooler_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Cooler $cooler, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CoolerType::class, $cooler);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_cooler_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('cooler/edit.html.twig', [
            'cooler' => $cooler,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_cooler_delete', methods: ['POST'])]
    public function delete(Request $request, Cooler $cooler, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$cooler->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($cooler);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_cooler_index', [], Response::HTTP_SEE_OTHER);
    }
}
