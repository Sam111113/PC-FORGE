<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Build;
use App\Repository\BoitierRepository;
use App\Repository\CoolerRepository;
use App\Repository\CpuRepository;
use App\Repository\FanRepository;
use App\Repository\GpuRepository;
use App\Repository\MotherboardRepository;
use App\Repository\PsuRepository;
use App\Repository\RamRepository;
use App\Repository\StorageRepository;
use App\Service\BuildSessionManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * Contrôleur principal du Builder PC (Forge)
 *
 * Gère tout le processus de création d'un build PC étape par étape :
 * - Sélection séquentielle des composants (CPU → Motherboard → GPU → RAM → Storage → Cooler → PSU → Boitier → Fan)
 * - Vérification de compatibilité entre composants à chaque étape
 * - Stockage temporaire en session via BuildSessionManager
 * - Sauvegarde finale du build en base de données
 *
 * Chaque composant ne peut être sélectionné qu'après les composants prérequis.
 * Le cooler et le fan sont optionnels.
 *
 * Accessible uniquement aux utilisateurs authentifiés.
 */
#[IsGranted('IS_AUTHENTICATED_FULLY')]
final class ForgeController extends AbstractController
{
    /**
     * @param BuildSessionManager $buildSession Service de gestion du build en session
     */
    public function __construct(
        private readonly BuildSessionManager $buildSession
    ) {
    }

    /**
     * Affiche la page principale du builder avec les composants sélectionnés
     *
     * Récupère tous les composants stockés en session et les charge depuis la BDD
     * pour afficher l'état actuel du build en cours de création.
     *
     * @return Response Page du builder avec les composants sélectionnés
     */
    #[Route('/forge', name: 'app_forge')]
    public function index( CpuRepository $cpuRepo, MotherboardRepository $mbRepo, GpuRepository $gpuRepo, RamRepository $ramRepo, StorageRepository $storageRepo, CoolerRepository $coolerRepo, PsuRepository $psuRepo, BoitierRepository $boitierRepo, FanRepository $fanRepo ): Response {

        $selected = [
            'cpu' => null,
            'motherboard' => null,
            'gpu' => null,
            'ram' => null,
            'storage' => null,
            'cooler' => null,
            'psu' => null,
            'boitier' => null,
            'fan' => null,
        ];
        if ($this->buildSession->hasComponent('cpuId')) {
            $selected['cpu'] = $cpuRepo->find($this->buildSession->getComponent('cpuId'));
        }

        if ($this->buildSession->hasComponent('mbId')) {
            $selected['motherboard'] = $mbRepo->find($this->buildSession->getComponent('mbId'));
        }

        if ($this->buildSession->hasComponent('gpuId')) {
            $selected['gpu'] = $gpuRepo->find($this->buildSession->getComponent('gpuId'));
        }

        if ($this->buildSession->hasComponent('ramId')) {
            $selected['ram'] = $ramRepo->find($this->buildSession->getComponent('ramId'));
        }

        if ($this->buildSession->hasComponent('storageId')) {
            $selected['storage'] = $storageRepo->find($this->buildSession->getComponent('storageId'));
        }

        if ($this->buildSession->hasComponent('coolerId')) {
            $selected['cooler'] = $coolerRepo->find($this->buildSession->getComponent('coolerId'));
        }

        if ($this->buildSession->hasComponent('psuId')) {
            $selected['psu'] = $psuRepo->find($this->buildSession->getComponent('psuId'));
        }

        if ($this->buildSession->hasComponent('boitierId')) {
            $selected['boitier'] = $boitierRepo->find($this->buildSession->getComponent('boitierId'));
        }

        if ($this->buildSession->hasComponent('fanId')) {
            $selected['fan'] = $fanRepo->find($this->buildSession->getComponent('fanId'));
        }

        return $this->render('forge/index.html.twig', [
            'selected' => $selected,
        ]);
    }
    /**
     * Affiche la liste des CPU disponibles pour sélection
     *
     * Première étape du builder. Si un CPU est déjà sélectionné,
     * redirige vers la forge avec un message d'avertissement.
     *
     * @param CpuRepository $cpuRepo Repository des processeurs
     * @return Response Liste des CPU ou redirection si déjà sélectionné
     */
    #[Route('/forge/select/cpu', name: 'forge_select_cpu')]
    public function selectCpu(CpuRepository $cpuRepo): Response
    {
        if ($this->buildSession->hasComponent('cpuId')) {
            $this->addFlash('warning', 'Tu as déjà sélectionné un processeur.');
            return $this->redirectToRoute('app_forge');
        }
        $cpus = $cpuRepo->findBy([], ['prix' => 'DESC']);
        return $this->render('cpu/index.html.twig', [
            'cpus' => $cpus,
            'build' => $this->buildSession->getBuild(),
        ]);
    }

    /**
     * Ajoute un CPU au build en session
     *
     * Valide le token CSRF, vérifie l'existence du CPU en BDD,
     * puis stocke l'ID en session.
     *
     * @param int $id ID du CPU à ajouter
     * @param Request $request Requête HTTP contenant le token CSRF
     * @param CpuRepository $repo Repository des CPU
     * @return Response Redirection vers la forge
     */
    #[Route('/forge/add/cpu/{id}', name: 'forge_add_cpu', methods: ['POST'])]
    public function addCpu(int $id, Request $request, CpuRepository $repo): Response
    {
        $submittedToken = $request->request->get('_token');
        if (!$this->isCsrfTokenValid('add_cpu_' . $id, $submittedToken)) {
            throw $this->createAccessDeniedException('Jeton CSRF invalide.');
        }
        if (!$repo->find($id)) {
            throw $this->createNotFoundException('CPU introuvable');
        }

        $this->buildSession->setComponent('cpuId', $id);

        return $this->redirectToRoute('app_forge');
    }

    /**
     * Affiche les cartes mères compatibles avec le CPU sélectionné
     *
     * Requiert un CPU déjà sélectionné. Filtre les cartes mères
     * par compatibilité de socket avec le processeur choisi.
     *
     * @param CpuRepository $cpuRepo Repository des CPU
     * @param MotherboardRepository $mbRepo Repository des cartes mères
     * @return Response Liste des cartes mères compatibles
     */
    #[Route('/forge/select/motherboard', name: 'forge_select_motherboard', methods: ['GET'])]
    public function selectMotherboard(CpuRepository $cpuRepo, MotherboardRepository $mbRepo): Response
    {
        if (!$this->buildSession->hasComponent('cpuId')) {
            $this->addFlash('warning', 'Choisis d\'abord un processeur.');
            return $this->redirectToRoute('app_forge');
        }

        $cpu = $cpuRepo->find($this->buildSession->getComponent('cpuId'));
        $socket = $cpu->getSocket();
        $motherboards = $mbRepo->findCompatibleWithCpu($socket);

        return $this->render('motherboard/index.html.twig', [
            'motherboards' => $motherboards,
            'build' => $this->buildSession->getBuild(),
        ]);
    }

    /**
     * Ajoute une carte mère au build en session
     *
     * @param int $id ID de la carte mère à ajouter
     * @param Request $request Requête HTTP contenant le token CSRF
     * @param MotherboardRepository $repo Repository des cartes mères
     * @return Response Redirection vers la forge
     */
    #[Route('/forge/add/motherboard/{id}', name: 'forge_add_motherboard', methods: ['POST'])]
    public function addMotherboard(int $id, Request $request, MotherboardRepository $repo): Response
    {
        $submittedToken = $request->request->get('_token');
        if (!$this->isCsrfTokenValid('add_motherboard_' . $id, $submittedToken)) {
            throw $this->createAccessDeniedException('Jeton CSRF invalide.');
        }
        if (!$repo->find($id)) {
            throw $this->createNotFoundException('Motherboard introuvable');
        }

        $this->buildSession->setComponent('mbId', $id);
        return $this->redirectToRoute('app_forge');
    }

    /**
     * Affiche les cartes graphiques compatibles avec la carte mère
     *
     * Requiert une carte mère sélectionnée. Filtre les GPU
     * par compatibilité du module PCIe.
     *
     * @param GpuRepository $gpuRepo Repository des GPU
     * @param MotherboardRepository $mbRepo Repository des cartes mères
     * @return Response Liste des GPU compatibles
     */
    #[Route('/forge/select/gpu', name: 'forge_select_gpu', methods: ['GET'])]
    public function selectGpu(GpuRepository $gpuRepo, MotherboardRepository $mbRepo): Response
    {
        if (!$this->buildSession->hasComponent('mbId')) {
            $this->addFlash('warning', 'Choisis d\'abord une carte mère.');
            return $this->redirectToRoute('app_forge');
        }

        $mb = $mbRepo->find($this->buildSession->getComponent('mbId'));
        $gpus = $gpuRepo->findCompatibleWithMotherboard($mb->getPcieModule());

        return $this->render('gpu/index.html.twig', [
            'gpus' => $gpus,
            'build' => $this->buildSession->getBuild(),
        ]);
    }

    /**
     * Affiche les barrettes RAM compatibles avec la carte mère
     *
     * Requiert un GPU sélectionné. Filtre la RAM par type de mémoire
     * et capacité maximale supportée par la carte mère.
     *
     * @param MotherboardRepository $mbRepo Repository des cartes mères
     * @param RamRepository $ramRepo Repository des RAM
     * @return Response Liste des RAM compatibles
     */
    #[Route('/forge/select/ram', name: 'forge_select_ram', methods: ['GET'])]
    public function selectRam(MotherboardRepository $mbRepo, RamRepository $ramRepo): Response
    {
        if (!$this->buildSession->hasComponent('gpuId')) {
            $this->addFlash('warning', 'Choisis d\'abord une carte graphique.');
            return $this->redirectToRoute('app_forge');
        }

        $mb = $mbRepo->find($this->buildSession->getComponent('mbId'));
        $rams = $ramRepo->findCompatibleWithMotherboard($mb->getMemoryType(), $mb->getMemoryMax());

        return $this->render('ram/index.html.twig', [
            'rams' => $rams,
            'build' => $this->buildSession->getBuild(),
        ]);
    }

    /**
     * Ajoute de la RAM au build en session
     *
     * @param int $id ID de la RAM à ajouter
     * @param Request $request Requête HTTP contenant le token CSRF
     * @param RamRepository $repo Repository des RAM
     * @return Response Redirection vers la forge
     */
    #[Route('/forge/add/ram/{id}', name: 'forge_add_ram', methods: ['POST'])]
    public function addRam(int $id, Request $request, RamRepository $repo): Response
    {
        $submittedToken = $request->request->get('_token');
        if (!$this->isCsrfTokenValid('add_ram_' . $id, $submittedToken)) {
            throw $this->createAccessDeniedException('Jeton CSRF invalide.');
        }
        if (!$repo->find($id)) {
            throw $this->createNotFoundException('ram introuvable');
        }
        $this->buildSession->setComponent('ramId', $id);
        return $this->redirectToRoute('app_forge');
    }

    /**
     * Ajoute un GPU au build en session
     *
     * @param int $id ID du GPU à ajouter
     * @param Request $request Requête HTTP contenant le token CSRF
     * @param GpuRepository $repo Repository des GPU
     * @return Response Redirection vers la forge
     */
    #[Route('/forge/add/gpu/{id}', name: 'forge_add_gpu', methods: ['POST'])]
    public function addGpu(int $id, Request $request, GpuRepository $repo): Response
    {
        $submittedToken = $request->request->get('_token');
        if (!$this->isCsrfTokenValid('add_gpu_' . $id, $submittedToken)) {
            throw $this->createAccessDeniedException('Jeton CSRF invalide.');
        }
        if (!$repo->find($id)) {
            throw $this->createNotFoundException('Gpu introuvable');
        }

        $this->buildSession->setComponent('gpuId', $id);
        return $this->redirectToRoute('app_forge');
    }

    /**
     * Affiche les stockages compatibles avec la carte mère
     *
     * Requiert de la RAM sélectionnée. Filtre les stockages par
     * disponibilité des slots M.2 et ports SATA de la carte mère.
     *
     * @param MotherboardRepository $mbRepo Repository des cartes mères
     * @param StorageRepository $storageRepo Repository des stockages
     * @return Response Liste des stockages compatibles
     */
    #[Route('/forge/select/storage', name: 'forge_select_storage', methods: ['GET'])]
    public function selectStorage(MotherboardRepository $mbRepo, StorageRepository $storageRepo): Response
    {
        if (!$this->buildSession->hasComponent('ramId')) {
            $this->addFlash('warning', 'Choisis d\'abord de la mémoire.');
            return $this->redirectToRoute('app_forge');
        }

        $mb = $mbRepo->find($this->buildSession->getComponent('mbId'));
        $storages = $storageRepo->findCompatibleWithMotherboard($mb->getSlotM2(), $mb->getSataPort());

        return $this->render('storage/index.html.twig', [
            'storages' => $storages,
            'build' => $this->buildSession->getBuild(),
        ]);
    }

    /**
     * Ajoute un stockage au build en session
     *
     * @param int $id ID du stockage à ajouter
     * @param Request $request Requête HTTP contenant le token CSRF
     * @param StorageRepository $repo Repository des stockages
     * @return Response Redirection vers la forge
     */
    #[Route('/forge/add/storage/{id}', name: 'forge_add_storage', methods: ['POST'])]
    public function addStorage(int $id, Request $request, StorageRepository $repo): Response
    {
        $submittedToken = $request->request->get('_token');
        if (!$this->isCsrfTokenValid('add_storage_' . $id, $submittedToken)) {
            throw $this->createAccessDeniedException('Jeton CSRF invalide.');
        }
        if (!$repo->find($id)) {
            throw $this->createNotFoundException('Storage introuvable');
        }

        $this->buildSession->setComponent('storageId', $id);
        return $this->redirectToRoute('app_forge');
    }

    /**
     * Affiche les boîtiers compatibles avec les composants sélectionnés
     *
     * Requiert une alimentation sélectionnée. Filtre les boîtiers par :
     * - Form factor de la carte mère
     * - Longueur du GPU
     * - Hauteur du cooler (si sélectionné) ou radiateur AIO
     *
     * @param GpuRepository $gpuRepo Repository des GPU
     * @param CoolerRepository $coolerRepo Repository des coolers
     * @param MotherboardRepository $mbRepo Repository des cartes mères
     * @param BoitierRepository $caseRepo Repository des boîtiers
     * @return Response Liste des boîtiers compatibles
     */
    #[Route('/forge/select/boitier', name: 'forge_select_boitier', methods: ['GET'])]
    public function selectCase(GpuRepository $gpuRepo, CoolerRepository $coolerRepo, MotherboardRepository $mbRepo, BoitierRepository $caseRepo): Response
    {
        if (!$this->buildSession->hasComponent('psuId')) {
            $this->addFlash('warning', 'Choisis d\'abord une alimentation.');
            return $this->redirectToRoute('app_forge');
        }

        $mb = $mbRepo->find($this->buildSession->getComponent('mbId'));
        $gpu = $gpuRepo->find($this->buildSession->getComponent('gpuId'));
        $mbFormFactor = $mb->getFormFactor();
        $gpuLength = $gpu->getLength();

        if (!$this->buildSession->hasComponent('coolerId')) {
            $boitiers = $caseRepo->findCompatibleWithoutCooler($mbFormFactor, $gpuLength);
        } else {
            $cooler = $coolerRepo->find($this->buildSession->getComponent('coolerId'));

            if ($cooler->isAio()) {
                $boitiers = $caseRepo->findCompatibleWithAioCooler($mbFormFactor, $gpuLength, $cooler->getNbFan());
            } else {
                $boitiers = $caseRepo->findCompatibleWithAirCooler($mbFormFactor, $gpuLength, $cooler->getHeigth());
            }
        }

        return $this->render('boitier/index.html.twig', [
            'boitiers' => $boitiers,
            'build' => $this->buildSession->getBuild(),
        ]);
    }

    /**
     * Ajoute un boîtier au build en session
     *
     * @param int $id ID du boîtier à ajouter
     * @param Request $request Requête HTTP contenant le token CSRF
     * @param BoitierRepository $repo Repository des boîtiers
     * @return Response Redirection vers la forge
     */
    #[Route('/forge/add/boitier/{id}', name: 'forge_add_boitier', methods: ['POST'])]
    public function addCase(int $id, Request $request, BoitierRepository $repo): Response
    {
        $submittedToken = $request->request->get('_token');
        if (!$this->isCsrfTokenValid('add_boitier_' . $id, $submittedToken)) {
            throw $this->createAccessDeniedException('Jeton CSRF invalide.');
        }
        if (!$repo->find($id)) {
            throw $this->createNotFoundException('Boitier introuvable');
        }

        $this->buildSession->setComponent('boitierId', $id);
        return $this->redirectToRoute('app_forge');
    }

    /**
     * Affiche les coolers compatibles avec le CPU sélectionné (optionnel)
     *
     * Requiert du stockage sélectionné. Filtre les coolers par
     * compatibilité de socket et TDP suffisant pour le processeur.
     *
     * @param CoolerRepository $coolerRepo Repository des coolers
     * @param CpuRepository $cpuRepo Repository des CPU
     * @return Response Liste des coolers compatibles
     */
    #[Route('/forge/select/cooler', name: 'forge_select_cooler', methods: ['GET'])]
    public function selectCpuCooler(CoolerRepository $coolerRepo, CpuRepository $cpuRepo): Response
    {
        if (!$this->buildSession->hasComponent('storageId')) {
            $this->addFlash('warning', 'Choisis d\'abord du stockage.');
            return $this->redirectToRoute('app_forge');
        }

        $cpu = $cpuRepo->find($this->buildSession->getComponent('cpuId'));
        $coolers = $coolerRepo->findCompatibleWithCpu($cpu->getSocket(), $cpu->getTdp());

        return $this->render('cooler/index.html.twig', [
            'coolers' => $coolers,
            'build' => $this->buildSession->getBuild(),
        ]);
    }

    /**
     * Ajoute un cooler au build en session (optionnel)
     *
     * @param int $id ID du cooler à ajouter
     * @param Request $request Requête HTTP contenant le token CSRF
     * @param CoolerRepository $repo Repository des coolers
     * @return Response Redirection vers la forge
     */
    #[Route('/forge/add/cooler/{id}', name: 'forge_add_cooler', methods: ['POST'])]
    public function addCpuCooler(int $id, Request $request, CoolerRepository $repo): Response
    {
        $submittedToken = $request->request->get('_token');
        if (!$this->isCsrfTokenValid('add_cooler_' . $id, $submittedToken)) {
            throw $this->createAccessDeniedException('Jeton CSRF invalide.');
        }
        if (!$repo->find($id)) {
            throw $this->createNotFoundException('Cooler introuvable');
        }

        $this->buildSession->setComponent('coolerId', $id);
        return $this->redirectToRoute('app_forge');
    }

    /**
     * Affiche les alimentations suffisamment puissantes
     *
     * Requiert du stockage sélectionné. Calcule la puissance minimale requise
     * (TDP CPU + TDP GPU + 150W marge) et filtre les alimentations.
     *
     * @param GpuRepository $gpuRepo Repository des GPU
     * @param CpuRepository $cpuRepo Repository des CPU
     * @param PsuRepository $psuRepo Repository des alimentations
     * @return Response Liste des alimentations compatibles
     */
    #[Route('/forge/select/psu', name: 'forge_select_psu', methods: ['GET'])]
    public function selectPsu(GpuRepository $gpuRepo, CpuRepository $cpuRepo, PsuRepository $psuRepo): Response
    {
        if (!$this->buildSession->hasComponent('storageId')) {
            $this->addFlash('warning', 'Choisis d\'abord du stockage.');
            return $this->redirectToRoute('app_forge');
        }

        $cpu = $cpuRepo->find($this->buildSession->getComponent('cpuId'));
        $gpu = $gpuRepo->find($this->buildSession->getComponent('gpuId'));
        $tdpTotal = $cpu->getTdp() + $gpu->getTdp() + 150;

        $psus = $psuRepo->findByMinimumWattage($tdpTotal);

        return $this->render('psu/index.html.twig', [
            'psus' => $psus,
            'build' => $this->buildSession->getBuild(),
        ]);
    }

    /**
     * Ajoute une alimentation au build en session
     *
     * @param int $id ID de l'alimentation à ajouter
     * @param Request $request Requête HTTP contenant le token CSRF
     * @param PsuRepository $repo Repository des alimentations
     * @return Response Redirection vers la forge
     */
    #[Route('/forge/add/psu/{id}', name: 'forge_add_psu', methods: ['POST'])]
    public function addPsu(int $id, Request $request, PsuRepository $repo): Response
    {
        $submittedToken = $request->request->get('_token');
        if (!$this->isCsrfTokenValid('add_psu_' . $id, $submittedToken)) {
            throw $this->createAccessDeniedException('Jeton CSRF invalide.');
        }
        if (!$repo->find($id)) {
            throw $this->createNotFoundException('PSU introuvable');
        }

        $this->buildSession->setComponent('psuId', $id);
        return $this->redirectToRoute('app_forge');
    }

    /**
     * Affiche les ventilateurs compatibles avec le boîtier (optionnel)
     *
     * Requiert un boîtier sélectionné. Filtre les ventilateurs par
     * nombre d'emplacements et largeur supportée par le boîtier.
     *
     * @param BoitierRepository $boitierRepo Repository des boîtiers
     * @param FanRepository $fanRepo Repository des ventilateurs
     * @return Response Liste des ventilateurs compatibles
     */
    #[Route('/forge/select/fan', name: 'forge_select_fan', methods: ['GET'])]
    public function selectFan(BoitierRepository $boitierRepo, FanRepository $fanRepo): Response
    {
        if (!$this->buildSession->hasComponent('boitierId')) {
            $this->addFlash('warning', 'Choisis d\'abord un boitier.');
            return $this->redirectToRoute('app_forge');
        }

        $boitier = $boitierRepo->find($this->buildSession->getComponent('boitierId'));
        $fans = $fanRepo->findCompatibleWithCase($boitier->getFanSlot(), $boitier->getFanSlotWidth());

        return $this->render('fan/index.html.twig', [
            'fans' => $fans,
            'build' => $this->buildSession->getBuild(),
        ]);
    }

    /**
     * Ajoute un ventilateur au build en session (optionnel)
     *
     * @param int $id ID du ventilateur à ajouter
     * @param Request $request Requête HTTP contenant le token CSRF
     * @param FanRepository $repo Repository des ventilateurs
     * @return Response Redirection vers la forge
     */
    #[Route('/forge/add/fan/{id}', name: 'forge_add_fan', methods: ['POST'])]
    public function addFan(int $id, Request $request, FanRepository $repo): Response
    {
        $submittedToken = $request->request->get('_token');
        if (!$this->isCsrfTokenValid('add_fan_' . $id, $submittedToken)) {
            throw $this->createAccessDeniedException('Jeton CSRF invalide.');
        }
        if (!$repo->find($id)) {
            throw $this->createNotFoundException('Ventilateur introuvable');
        }

        $this->buildSession->setComponent('fanId', $id);
        return $this->redirectToRoute('app_forge');
    }

    /**
     * Supprime un composant spécifique du build en session
     *
     * Supprime le composant demandé ainsi que tous les composants
     * qui en dépendent (cascade). Liste des composants autorisés :
     * cpuId, mbId, gpuId, ramId, storageId, coolerId, psuId, boitierId, fanId
     *
     * @param string $part Identifiant du composant à supprimer
     * @param Request $request Requête HTTP contenant le token CSRF
     * @return Response Redirection vers la forge
     */
    #[Route('/forge/remove/{part}', name: 'forge_remove_part', methods: ['POST'])]
    public function removePart(string $part, Request $request): Response
    {
        $token = $request->request->get('_token');
        if (!$this->isCsrfTokenValid('forge_remove_part_' . $part, $token)) {
            throw $this->createAccessDeniedException('Token CSRF invalide');
        }

        $allowedParts = [
            'cpuId',
            'mbId',
            'gpuId',
            'ramId',
            'storageId',
            'coolerId',
            'psuId',
            'boitierId',
            'fanId',
        ];
        if (!in_array($part, $allowedParts, true)) {
            throw $this->createNotFoundException('Composant inconnu');
        }

        $this->buildSession->removePart($part);
        return $this->redirectToRoute('app_forge');
    }

    /**
     * Réinitialise complètement le build en session
     *
     * Supprime tous les composants sélectionnés et repart de zéro.
     *
     * @param Request $request Requête HTTP contenant le token CSRF
     * @return Response Redirection vers la forge
     */
    #[Route('/forge/reset', name: 'forge_reset', methods: ['POST'])]
    public function reset(Request $request): Response
    {
        $submittedToken = $request->request->get('_token');
        if (!$this->isCsrfTokenValid('forge_reset', $submittedToken)) {
            throw $this->createAccessDeniedException('Jeton CSRF invalide.');
        }
        $this->buildSession->resetBuild();
        return $this->redirectToRoute('app_forge');
    }
    /**
     * Sauvegarde le build complet en base de données
     *
     * Vérifie que le build est complet (composants obligatoires présents),
     * crée l'entité Build avec tous les composants sélectionnés,
     * calcule le prix total et associe le build à l'utilisateur connecté.
     * Réinitialise la session après sauvegarde.
     *
     * @param Request $request Requête HTTP contenant le token CSRF
     * @param EntityManagerInterface $em Entity manager pour la persistance
     * @return Response Redirection vers la page du build créé
     */
    #[Route('/forge/save', name: 'forge_save', methods: ['POST'])]
    public function save( Request $request, CpuRepository $cpuRepo, MotherboardRepository $mbRepo, GpuRepository $gpuRepo, RamRepository $ramRepo, StorageRepository $storageRepo, CoolerRepository $coolerRepo, PsuRepository $psuRepo, BoitierRepository $boitierRepo, FanRepository $fanRepo, EntityManagerInterface $em ): Response
    {
        $submittedToken = $request->request->get('_token');
        if (!$this->isCsrfTokenValid('forge_save', $submittedToken)) {
            throw $this->createAccessDeniedException('Jeton CSRF invalide.');
        }
        $build = $this->buildSession->getBuild();

        $user = $this->getUser();

        if (!$user instanceof \App\Entity\User) {
            throw $this->createAccessDeniedException('Vous devez être connecter.');
        }

        if (!$this->buildSession->isBuildComplete()) {
            $this->addFlash('error', 'Build non valide');
            return $this->redirectToRoute('app_forge');
        }

        $cpu = $cpuRepo->find($build['cpuId']);
        $mb = $mbRepo->find($build['mbId']);
        $gpu = $gpuRepo->find($build['gpuId']);
        $ram = $ramRepo->find($build['ramId']);
        $storage = $storageRepo->find($build['storageId']);
        $psu = $psuRepo->find($build['psuId']);
        $boitier = $boitierRepo->find($build['boitierId']);

        if (!empty($build['coolerId'])) {
            $cooler = $coolerRepo->find($build['coolerId']);
        }
        if (!empty($build['fanId'])) {
            $fan = $fanRepo->find($build['fanId']);
        }

        $buildCreate = new Build();

        $buildCreate->setCpu($cpu);
        $buildCreate->setMotherboard($mb);
        $buildCreate->addGpu($gpu);
        $buildCreate->setPsu($psu);
        $buildCreate->setBoitier($boitier);
        $buildCreate->addRam($ram);
        $buildCreate->addStorage($storage);

        if (!empty($fan)) {
            $buildCreate->addFan($fan);
        }
        if (!empty($cooler)) {
            $buildCreate->setCooler($cooler);
        }

        $buildCreate->setTotalPrice();
        $buildCreate->setUser($user);

        $em->persist($buildCreate);
        $em->flush();
        $id = $buildCreate->getId();
        $this->buildSession->resetBuild();
        return $this->redirectToRoute('app_build_show', [
            'id' => $id
        ]);
    }
}
