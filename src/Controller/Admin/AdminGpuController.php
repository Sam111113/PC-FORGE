<?php

namespace App\Controller\Admin;

use App\Entity\Gpu;
use App\Form\GpuType;
use App\Repository\GpuRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
#[Route('/admin/gpu', name: 'app_admin_gpu')]
final class AdminGpuController extends AbstractController
{
    #[Route('/', name: '_index', methods: ['GET'])]
    public function index(GpuRepository $gpuRepository): Response
    {
        return $this->render('admin/gpu/index.html.twig', [
            'gpus' => $gpuRepository->findAll(),
        ]);
    }

    #[Route('/new', name: '_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $gpu = new Gpu();
        $form = $this->createForm(GpuType::class, $gpu);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($gpu);
            $entityManager->flush();

            return $this->redirectToRoute('app_admin_gpu_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/gpu/new.html.twig', [
            'gpu' => $gpu,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/edit', name: '_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Gpu $gpu, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(GpuType::class, $gpu);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_admin_gpu_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/gpu/edit.html.twig', [
            'gpu' => $gpu,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: '_delete', methods: ['POST'])]
    public function delete(Request $request, Gpu $gpu, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $gpu->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($gpu);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_admin_gpu_index', [], Response::HTTP_SEE_OTHER);
    }
}
