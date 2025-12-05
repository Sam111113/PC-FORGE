<?php

namespace App\Controller;

use App\Entity\Psu;
use App\Form\PsuType;
use App\Repository\PsuRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/psu')]
final class PsuController extends AbstractController
{
    #[Route(name: 'app_psu_index', methods: ['GET'])]
    public function index(PsuRepository $psuRepository): Response
    {
        return $this->render('psu/index.html.twig', [
            'psus' => $psuRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_psu_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $psu = new Psu();
        $form = $this->createForm(PsuType::class, $psu);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($psu);
            $entityManager->flush();

            return $this->redirectToRoute('app_psu_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('psu/new.html.twig', [
            'psu' => $psu,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_psu_show', methods: ['GET'])]
    public function show(Psu $psu): Response
    {
        return $this->render('psu/show.html.twig', [
            'psu' => $psu,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_psu_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Psu $psu, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(PsuType::class, $psu);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_psu_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('psu/edit.html.twig', [
            'psu' => $psu,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_psu_delete', methods: ['POST'])]
    public function delete(Request $request, Psu $psu, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$psu->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($psu);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_psu_index', [], Response::HTTP_SEE_OTHER);
    }
}
