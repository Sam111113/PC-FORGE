<?php

namespace App\Controller;

use App\Repository\CpuRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Contrôleur public des processeurs (CPU)
 *
 * Ce contrôleur sert de référence pour tous les contrôleurs de composants publics
 * (GPU, RAM, Storage, PSU, Cooler, Boitier, Fan, Motherboard) qui suivent
 * la même structure avec une seule méthode index().
 *
 * Gère l'affichage de la liste des processeurs accessible à tous les visiteurs.
 * L'administration des CPU est gérée par AdminCpuController.
 */
#[Route('/cpu')]
final class CpuController extends AbstractController
{
    /**
     * Affiche la liste de tous les processeurs disponibles
     *
     * @param CpuRepository $cpuRepo Repository pour récupérer les CPU
     * @return Response Page listant tous les processeurs
     */
    #[Route('/', name: 'app_cpu_index', methods: ['GET'])]
    public function index(CpuRepository $cpuRepo): Response
    {
        return $this->render('cpu/index.html.twig', [
            'cpus' => $cpuRepo->findAll(),
        ]);
    }
}
