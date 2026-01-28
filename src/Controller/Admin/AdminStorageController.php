<?php

namespace App\Controller\Admin;

use App\Entity\Storage;
use App\Form\StorageType;
use App\Repository\StorageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
#[Route('/admin/storage', name: 'app_admin_storage')]
final class AdminStorageController extends AbstractController
{
    #[Route('/', name: '_index', methods: ['GET'])]
    public function index(StorageRepository $storageRepository): Response
    {
        return $this->render('admin/storage/index.html.twig', [
            'storages' => $storageRepository->findAll(),
        ]);
    }

    #[Route('/new', name: '_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $storage = new Storage();
        $form = $this->createForm(StorageType::class, $storage);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($storage);
            $entityManager->flush();

            return $this->redirectToRoute('app_admin_storage_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/storage/new.html.twig', [
            'storage' => $storage,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/edit', name: '_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Storage $storage, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(StorageType::class, $storage);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_admin_storage_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/storage/edit.html.twig', [
            'storage' => $storage,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: '_delete', methods: ['POST'])]
    public function delete(Request $request, Storage $storage, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $storage->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($storage);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_admin_storage_index', [], Response::HTTP_SEE_OTHER);
    }
}
