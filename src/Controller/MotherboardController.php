<?php

namespace App\Controller;

use App\Repository\MotherboardRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/motherboard')]
final class MotherboardController extends AbstractController
{
    #[Route('/', name: 'app_motherboard_index', methods: ['GET'])]
    public function index(MotherboardRepository $motherboardRepo): Response
    {
        return $this->render('motherboard/index.html.twig', [
            'motherboards' => $motherboardRepo->findAll(),
        ]);
    }
}
