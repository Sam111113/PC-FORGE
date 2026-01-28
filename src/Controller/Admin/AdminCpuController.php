<?php

namespace App\Controller\Admin;

use App\Entity\Cpu;
use App\Form\CpuType;
use App\Repository\CpuRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * Contrôleur d'administration des processeurs (CPU)
 *
 * Ce contrôleur sert de référence pour tous les contrôleurs admin de composants
 * (AdminGpuController, AdminRamController, AdminStorageController, AdminPsuController,
 * AdminCoolerController, AdminBoitierController, AdminFanController, AdminMotherboardController)
 * qui suivent la même structure CRUD.
 *
 * Gère les opérations CRUD (Create, Read, Update, Delete) sur les processeurs.
 * Accessible uniquement aux administrateurs (ROLE_ADMIN).
 */
#[IsGranted('ROLE_ADMIN')]
#[Route('/admin/cpu', name: 'app_admin_cpu')]
final class AdminCpuController extends AbstractController
{
    /**
     * Affiche la liste de tous les processeurs
     *
     * @param CpuRepository $cpuRepository Repository des CPU
     * @return Response Page listant tous les CPU avec actions d'édition/suppression
     */
    #[Route('/', name: '_index', methods: ['GET'])]
    public function index(CpuRepository $cpuRepository): Response
    {
        return $this->render('admin/cpu/index.html.twig', [
            'cpus' => $cpuRepository->findAll(),
        ]);
    }

    /**
     * Affiche et traite le formulaire de création d'un nouveau CPU
     *
     * @param Request $request Requête HTTP
     * @param EntityManagerInterface $entityManager Entity manager pour la persistance
     * @return Response Formulaire ou redirection après création
     */
    #[Route('/new', name: '_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $cpu = new Cpu();
        $form = $this->createForm(CpuType::class, $cpu);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($cpu);
            $entityManager->flush();

            return $this->redirectToRoute('app_admin_cpu_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/cpu/new.html.twig', [
            'cpu' => $cpu,
            'form' => $form,
        ]);
    }

    /**
     * Affiche et traite le formulaire d'édition d'un CPU existant
     *
     * @param Request $request Requête HTTP
     * @param Cpu $cpu CPU à éditer (injection par ParamConverter)
     * @param EntityManagerInterface $entityManager Entity manager pour la persistance
     * @return Response Formulaire ou redirection après modification
     */
    #[Route('/{id}/edit', name: '_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Cpu $cpu, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CpuType::class, $cpu);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_admin_cpu_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/cpu/edit.html.twig', [
            'cpu' => $cpu,
            'form' => $form,
        ]);
    }

    /**
     * Supprime un CPU après validation du token CSRF
     *
     * @param Request $request Requête HTTP contenant le token CSRF
     * @param Cpu $cpu CPU à supprimer (injection par ParamConverter)
     * @param EntityManagerInterface $entityManager Entity manager pour la suppression
     * @return Response Redirection vers la liste des CPU
     */
    #[Route('/{id}', name: '_delete', methods: ['POST'])]
    public function delete(Request $request, Cpu $cpu, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $cpu->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($cpu);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_admin_cpu_index', [], Response::HTTP_SEE_OTHER);
    }
}
