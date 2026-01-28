<?php

namespace App\Controller;

use App\Repository\CoolerRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/cooler')]
final class CoolerController extends AbstractController
{
    #[Route('/', name: 'app_cooler_index', methods: ['GET'])]
    public function index(CoolerRepository $coolerRepo): Response
    {
        return $this->render('cooler/index.html.twig', [
            'coolers' => $coolerRepo->findAll(),
        ]);
    }
}
