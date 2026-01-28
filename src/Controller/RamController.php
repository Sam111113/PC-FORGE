<?php

namespace App\Controller;

use App\Repository\RamRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/ram')]
final class RamController extends AbstractController
{
    #[Route('/', name: 'app_ram_index', methods: ['GET'])]
    public function index(RamRepository $ramRepo): Response
    {
        return $this->render('ram/index.html.twig', [
            'rams' => $ramRepo->findAll(),
        ]);
    }
}
