<?php

namespace App\Controller;

use App\Repository\StorageRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/storage')]
final class StorageController extends AbstractController
{
    #[Route('/', name: 'app_storage_index', methods: ['GET'])]
    public function index(StorageRepository $storageRepo): Response
    {
        return $this->render('storage/index.html.twig', [
            'storages' => $storageRepo->findAll(),
        ]);
    }
}
