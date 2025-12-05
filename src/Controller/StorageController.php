<?php

namespace App\Controller;

use App\Entity\Storage;
use App\Form\StorageType;
use App\Repository\StorageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/storage')]
final class StorageController extends AbstractController
{
    #[Route(name: 'app_storage_index', methods: ['GET'])]
    public function index(StorageRepository $storageRepository): Response
    {
        return $this->render('storage/index.html.twig', [
            'storages' => $storageRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_storage_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $storage = new Storage();
        $form = $this->createForm(StorageType::class, $storage);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($storage);
            $entityManager->flush();

            return $this->redirectToRoute('app_storage_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('storage/new.html.twig', [
            'storage' => $storage,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_storage_show', methods: ['GET'])]
    public function show(Storage $storage): Response
    {
        return $this->render('storage/show.html.twig', [
            'storage' => $storage,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_storage_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Storage $storage, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(StorageType::class, $storage);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_storage_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('storage/edit.html.twig', [
            'storage' => $storage,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_storage_delete', methods: ['POST'])]
    public function delete(Request $request, Storage $storage, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$storage->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($storage);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_storage_index', [], Response::HTTP_SEE_OTHER);
    }
}
