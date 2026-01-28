<?php

namespace App\Controller;

use App\Repository\GpuRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/gpu')]
final class GpuController extends AbstractController
{
    #[Route('/', name: 'app_gpu_index', methods: ['GET'])]
    public function index(GpuRepository $gpuRepo): Response
    {
        return $this->render('gpu/index.html.twig', [
            'gpus' => $gpuRepo->findAll(),
        ]);
    }
}
