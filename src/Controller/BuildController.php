<?php

namespace App\Controller;

use App\Entity\Build;
use App\Form\BuildUserType;
use App\Repository\BuildRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * Contrôleur public des builds
 *
 * Gère les opérations accessibles aux utilisateurs connectés :
 * - Affichage des détails d'un build
 * - Édition de ses propres builds
 * - Suppression de ses propres builds
 * - Consultation des prebuilds
 * - Consultation des builds communautaires
 *
 * Les opérations d'administration sont dans AdminBuildController
 */
#[Route('/build')]
final class BuildController extends AbstractController
{

    /**
     * Affiche les détails d'un build
     *
     * @param Build $build Build à afficher (injection par ParamConverter)
     * @return Response Page de détail du build avec tous ses composants
     */
    #[Route('/{id<\d+>}', name: 'app_build_show', methods: ['GET'])]
    public function show(Build $build): Response
    {
        return $this->render('build/show.html.twig', [
            'build' => $build,
        ]);
    }

    /**
     * Permet à un utilisateur d'éditer son propre build
     *
     * Vérifie que l'utilisateur connecté est bien le propriétaire du build.
     * Les admins utilisent AdminBuildController pour éditer tous les builds.
     *
     * @param Request $request Requête HTTP
     * @param Build $build Build à éditer (injection par ParamConverter)
     * @param EntityManagerInterface $entityManager Entity manager pour la persistance
     * @return Response Formulaire ou redirection après modification
     */
    #[IsGranted('ROLE_USER')]
    #[Route('/{id}/edit', name: 'app_build_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Build $build, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();

        if ($build->getUser() !== $user) {
            $this->addFlash('error', 'Vous n\'avez pas le droit de modifier ce build.');
            return $this->redirectToRoute('app_home', [], Response::HTTP_SEE_OTHER);
        }
        $form = $this->createForm(BuildUserType::class, $build);
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

    /**
     * Permet à un utilisateur de supprimer son propre build
     *
     * Vérifie que l'utilisateur connecté est le propriétaire et valide le token CSRF.
     * Les admins utilisent AdminBuildController pour supprimer tous les builds.
     *
     * @param Request $request Requête HTTP contenant le token CSRF
     * @param Build $build Build à supprimer (injection par ParamConverter)
     * @param EntityManagerInterface $entityManager Entity manager pour la suppression
     * @return Response Redirection vers la liste des prebuilds
     */
    #[IsGranted('ROLE_USER')]
    #[Route('/{id}', name: 'app_build_delete', methods: ['POST'])]
    public function delete(Request $request, Build $build, EntityManagerInterface $entityManager): Response
    {
        // Seul le propriétaire peut supprimer (les admins utilisent l'interface admin)
        if ($build->getUser() !== $this->getUser()) {
            $this->addFlash('error', 'Vous n\'avez pas le droit de supprimer ce build.');
            return $this->redirectToRoute('app_prebuilds', [], Response::HTTP_SEE_OTHER);
        }
        if ($this->isCsrfTokenValid('delete' . $build->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($build);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_prebuilds', [], Response::HTTP_SEE_OTHER);
    }

    /**
     * Affiche les prebuilds organisés par gamme de prix
     *
     * Récupère les 12 prebuilds triés par prix et les organise en 4 groupes :
     * Space ship, High end, Mid budget, Low cost (3 builds par groupe).
     *
     * @param BuildRepository $buildRepository Repository des builds
     * @return Response Page des prebuilds organisés par gamme
     */
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

    /**
     * Affiche les builds de la communauté avec pagination
     *
     * Liste tous les builds non-prebuilds créés par les utilisateurs,
     * triés par date de création décroissante avec 8 builds par page.
     *
     * @param BuildRepository $buildRepository Repository des builds
     * @param Request $request Requête HTTP pour la pagination
     * @param PaginatorInterface $paginator Service de pagination KnpPaginator
     * @return Response Page des builds communautaires paginée
     */
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