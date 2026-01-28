<?php

namespace App\Controller\Admin;

use App\Entity\Ram;
use App\Form\RamType;
use App\Repository\RamRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
#[Route('/admin/ram', name: 'app_admin_ram')]
final class AdminRamController extends AbstractController
{
    #[Route('/', name: '_index', methods: ['GET'])]
    public function index(RamRepository $ramRepository): Response
    {
        return $this->render('admin/ram/index.html.twig', [
            'rams' => $ramRepository->findAll(),
        ]);
    }

    #[Route('/new', name: '_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $ram = new Ram();
        $form = $this->createForm(RamType::class, $ram);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($ram);
            $entityManager->flush();

            return $this->redirectToRoute('app_admin_ram_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/ram/new.html.twig', [
            'ram' => $ram,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/edit', name: '_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Ram $ram, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(RamType::class, $ram);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_admin_ram_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/ram/edit.html.twig', [
            'ram' => $ram,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: '_delete', methods: ['POST'])]
    public function delete(Request $request, Ram $ram, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $ram->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($ram);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_admin_ram_index', [], Response::HTTP_SEE_OTHER);
    }
}
