<?php

namespace App\Controller\Admin;

use App\Entity\Psu;
use App\Form\PsuType;
use App\Repository\PsuRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
#[Route('/admin/psu', name: 'app_admin_psu')]
final class AdminPsuController extends AbstractController
{
    #[Route('/', name: '_index', methods: ['GET'])]
    public function index(PsuRepository $psuRepository): Response
    {
        return $this->render('admin/psu/index.html.twig', [
            'psus' => $psuRepository->findAll(),
        ]);
    }

    #[Route('/new', name: '_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $psu = new Psu();
        $form = $this->createForm(PsuType::class, $psu);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($psu);
            $entityManager->flush();

            return $this->redirectToRoute('app_admin_psu_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/psu/new.html.twig', [
            'psu' => $psu,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/edit', name: '_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Psu $psu, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(PsuType::class, $psu);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_admin_psu_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/psu/edit.html.twig', [
            'psu' => $psu,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: '_delete', methods: ['POST'])]
    public function delete(Request $request, Psu $psu, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $psu->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($psu);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_admin_psu_index', [], Response::HTTP_SEE_OTHER);
    }
}
