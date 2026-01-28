<?php

namespace App\Controller;

use App\Repository\FanRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/fan')]
final class FanController extends AbstractController
{
    #[Route('/', name: 'app_fan_index', methods: ['GET'])]
    public function index(FanRepository $fanRepo): Response
    {
        return $this->render('fan/index.html.twig', [
            'fans' => $fanRepo->findAll(),
        ]);
    }
}
