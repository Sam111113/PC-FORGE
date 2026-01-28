<?php

namespace App\Controller;

use App\Repository\PsuRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/psu')]
final class PsuController extends AbstractController
{
    #[Route('/', name: 'app_psu_index', methods: ['GET'])]
    public function index(PsuRepository $psuRepo): Response
    {
        return $this->render('psu/index.html.twig', [
            'psus' => $psuRepo->findAll(),
        ]);
    }
}
