<?php

namespace App\Controller\Admin;

use App\Entity\Cooler;
use App\Form\CoolerType;
use App\Repository\CoolerRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
#[Route('/admin/cooler', name: 'app_admin_cooler')]
final class AdminCoolerController extends AbstractController
{
    #[Route('/', name: '_index', methods: ['GET'])]
    public function index(CoolerRepository $coolerRepository): Response
    {
        return $this->render('admin/cooler/index.html.twig', [
            'coolers' => $coolerRepository->findAll(),
        ]);
    }

    #[Route('/new', name: '_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $cooler = new Cooler();
        $form = $this->createForm(CoolerType::class, $cooler);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($cooler);
            $entityManager->flush();

            return $this->redirectToRoute('app_admin_cooler_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/cooler/new.html.twig', [
            'cooler' => $cooler,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/edit', name: '_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Cooler $cooler, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CoolerType::class, $cooler);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_admin_cooler_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/cooler/edit.html.twig', [
            'cooler' => $cooler,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: '_delete', methods: ['POST'])]
    public function delete(Request $request, Cooler $cooler, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $cooler->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($cooler);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_admin_cooler_index', [], Response::HTTP_SEE_OTHER);
    }
}
