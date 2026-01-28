<?php

namespace App\Controller;

use App\Repository\BoitierRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/boitier')]
final class BoitierController extends AbstractController
{
    #[Route('/', name: 'app_boitier_index', methods: ['GET'])]
    public function index(BoitierRepository $boitierRepo): Response
    {
        return $this->render('boitier/index.html.twig', [
            'boitiers' => $boitierRepo->findAll(),
        ]);
    }
}
