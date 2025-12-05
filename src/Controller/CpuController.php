<?php

namespace App\Controller;

use App\Entity\Cpu;
use App\Form\CpuType;
use App\Repository\CpuRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/cpu')]
final class CpuController extends AbstractController
{
    #[Route(name: 'app_cpu_index', methods: ['GET'])]
    public function index(CpuRepository $cpuRepository): Response
    {
        return $this->render('cpu/index.html.twig', [
            'cpus' => $cpuRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_cpu_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $cpu = new Cpu();
        $form = $this->createForm(CpuType::class, $cpu);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($cpu);
            $entityManager->flush();

            return $this->redirectToRoute('app_cpu_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('cpu/new.html.twig', [
            'cpu' => $cpu,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_cpu_show', methods: ['GET'])]
    public function show(Cpu $cpu): Response
    {
        return $this->render('cpu/show.html.twig', [
            'cpu' => $cpu,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_cpu_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Cpu $cpu, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CpuType::class, $cpu);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_cpu_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('cpu/edit.html.twig', [
            'cpu' => $cpu,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_cpu_delete', methods: ['POST'])]
    public function delete(Request $request, Cpu $cpu, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$cpu->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($cpu);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_cpu_index', [], Response::HTTP_SEE_OTHER);
    }
}
