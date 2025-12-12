<?php

namespace App\Controller;

use App\Entity\Build;
use App\Form\BuildType;
use App\Repository\BuildRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;


#[Route('/build')]
final class BuildController extends AbstractController
{
    #[IsGranted('ROLE_ADMIN')]
    #[Route(name: 'app_build_index', methods: ['GET'])]
    public function index(BuildRepository $buildRepository): Response
    {
        $build = $buildRepository->findAll();

        return $this->render('build/index.html.twig', [
            'builds' => $build,
        ]);
    }
    #[IsGranted('ROLE_ADMIN')]
    #[Route('/new', name: 'app_build_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $build = new Build();
        $form = $this->createForm(BuildType::class, $build);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($build->getImage() && !$build->getImage()->getFilename()) {
                $build->setImage(null);
            }
            $build->setTotalPrice();
            $entityManager->persist($build);
            $entityManager->flush();

            return $this->redirectToRoute('app_build_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('build/new.html.twig', [
            'build' => $build,
            'form' => $form,
        ]);
    }
    
    #[Route('/{id<\d+>}', name: 'app_build_show', methods: ['GET'])]
    public function show(Build $build): Response
    {
        return $this->render('build/show.html.twig', [
            'build' => $build,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_build_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Build $build, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(BuildType::class, $build);
        $form->handleRequest($request);
        $buildId = $build->getId();
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_build_show', ['id' => $buildId,], Response::HTTP_SEE_OTHER);
        }

        return $this->render('build/edit.html.twig', [
            'build' => $build,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_build_delete', methods: ['POST'])]
    public function delete(Request $request, Build $build, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $build->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($build);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_build_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/pre_builds', name: 'app_prebuilds', methods: ['GET'])]
    public function preBuilds(BuildRepository $buildRepository): Response
    {
        $preBuilds = $buildRepository->findBy(['isPreBuild' => true], ['totalPrice' => 'DESC'], 12);

        $chunks = \array_chunk($preBuilds, 3);
        $labels = ['Space ship', 'High end', 'Mid budget', 'Low cost'];
        $groups = [];

        foreach ($labels as $i => $label) {
            $groups[$label] = $chunks[$i];
        }

        return $this->render('build/pre_builds.html.twig', [
            'groups' => $groups,
        ]);
    }
    #[Route('/communauté', name: 'app_build_comu', methods: ['GET'])]
    public function comuBuild(BuildRepository $buildRepository, Request $request, PaginatorInterface $paginator): Response
    {
        $query = $buildRepository->createQueryBuilder('b')
            ->andWhere('b.isPreBuild = :isPreBuild')
            ->setParameter('isPreBuild', false)
            ->orderBy('b.createdAt', 'DESC');

        $pagination = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            8
        );

        return $this->render('build/comuBuild.html.twig', [
            'pagination' => $pagination,
        ]);
    }
}