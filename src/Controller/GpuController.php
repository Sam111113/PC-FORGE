<?php

namespace App\Controller;

use App\Entity\Gpu;
use App\Form\GpuType;
use App\Repository\GpuRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/gpu')]
final class GpuController extends AbstractController
{
    #[Route(name: 'app_gpu_index', methods: ['GET'])]
    public function index(GpuRepository $gpuRepository): Response
    {
        return $this->render('gpu/index.html.twig', [
            'gpus' => $gpuRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_gpu_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $gpu = new Gpu();
        $form = $this->createForm(GpuType::class, $gpu);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($gpu);
            $entityManager->flush();

            return $this->redirectToRoute('app_gpu_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('gpu/new.html.twig', [
            'gpu' => $gpu,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_gpu_show', methods: ['GET'])]
    public function show(Gpu $gpu): Response
    {
        return $this->render('gpu/show.html.twig', [
            'gpu' => $gpu,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_gpu_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Gpu $gpu, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(GpuType::class, $gpu);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_gpu_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('gpu/edit.html.twig', [
            'gpu' => $gpu,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_gpu_delete', methods: ['POST'])]
    public function delete(Request $request, Gpu $gpu, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$gpu->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($gpu);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_gpu_index', [], Response::HTTP_SEE_OTHER);
    }
}
