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
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

//vérification que l'utilisateur sois connecté
#[IsGranted('IS_AUTHENTICATED_FULLY')]
final class ForgeController extends AbstractController
{
    //création du key stocker en session
    private const SESSION_KEY = 'userBuild';

    private function initBuild(SessionInterface $session): array
    {
        //création de mon tableau build pour stocker les composants selectionnés avec des valeurs null par default
        $state = $session->get(self::SESSION_KEY);
        if (!$state) {
            $state = [
                'cpuId' => null,
                'mbId' => null,
                'gpuId' => null,
                'ramId' => null,
                'storageId' => null,
                'coolerId' => null,
                'psuId' => null,
                'boitierId' => null,
                'fanId' => null,
            ];
            $session->set(self::SESSION_KEY, $state);
        }
        return $state;
    }
    //function pour save le build
    private function saveBuild(SessionInterface $session, array $state): void
    {
        $session->set(self::SESSION_KEY, $state);
    }
    //function pour recup le build
    private function getBuild(SessionInterface $session): array
    {
        return $this->initBuild($session);
    }
    //function pour ajouter les id au build
    private function setSingle(array $state, string $key, ?int $id): array
    {
        $state[$key] = $id;
        return $state;
    }
    //landing page de la forge qui envoie un tableau a la vue avec les compo du build selectionner, qui est mis a jour a chaque refresh de la page
    #[Route('/forge', name: 'app_forge')]
    public function index(
        Request $request,
        CpuRepository $cpuRepo,
        MotherboardRepository $mbRepo,
        GpuRepository $gpuRepo,
        RamRepository $ramRepo,
        StorageRepository $storageRepo,
        CoolerRepository $coolerRepo,
        PsuRepository $psuRepo,
        BoitierRepository $boitierRepo,
        FanRepository $fanRepo,
    ): Response {
        //je recupère le build stocker en session qui contient les id (si composant selectioner)
        $session = $request->getSession();
        $build = $this->getBuild($session);
        //je fait un tableau avec des valeur null et pour chaque id stocker dans le build state je fait une requête via le repo
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

        if (!empty($build['cpuId'])) {
            $selected['cpu'] = $cpuRepo->find($build['cpuId']);
        }

        if (!empty($build['mbId'])) {
            $selected['motherboard'] = $mbRepo->find($build['mbId']);
        }

        if (!empty($build['gpuId'])) {
            $selected['gpu'] = $gpuRepo->find($build['gpuId']);
        }

        if (!empty($build['ramId'])) {
            $selected['ram'] = $ramRepo->find($build['ramId']);
        }

        if (!empty($build['storageId'])) {
            $selected['storage'] = $storageRepo->find($build['storageId']);
        }

        if (!empty($build['coolerId'])) {
            $selected['cooler'] = $coolerRepo->find($build['coolerId']);
        }

        if (!empty($build['psuId'])) {
            $selected['psu'] = $psuRepo->find($build['psuId']);
        }

        if (!empty($build['boitierId'])) {
            $selected['boitier'] = $boitierRepo->find($build['boitierId']);
        }

        if (!empty($build['fanId'])) {
            $selected['fan'] = $fanRepo->find($build['fanId']);
        }

        return $this->render('forge/index.html.twig', [
            'selected' => $selected,
        ]);
    }
    //si aucun cpuId dans la session envoie vers cpu index pour selectionner un cpu et j'envoie aussi le state pour ajouter des condition a la vue si un build est commencer
    #[Route('/forge/select/cpu', name: 'forge_select_cpu')]
    public function selectCpu(CpuRepository $cpuRepo, SessionInterface $session): Response
    {
        $build = $this->getBuild($session);
        if (!empty($build['cpuId'])) {
            $this->addFlash('warning', 'Tu as déjà sélectionné un processeur.');
            return $this->redirectToRoute('app_forge');
        }
        $cpus = $cpuRepo->findBy([], ['prix' => 'DESC']);
        return $this->render('cpu/index.html.twig', [
            'cpus' => $cpus,
            'build' => $build,
        ]);
    }

    //verif via token csrf, puis verfi que l'id existe en bdd et ensuite il est ajouter au build state puis rediriger vers la page du builder
    #[Route('/forge/add/cpu/{id}', name: 'forge_add_cpu', methods: ['POST'])]
    public function addCpu(int $id, Request $request, SessionInterface $session, CpuRepository $repo): Response
    {
        $submittedToken = $request->request->get('_token');
        if (!$this->isCsrfTokenValid('add_cpu_' . $id, $submittedToken)) {
            throw $this->createAccessDeniedException('Jeton CSRF invalide.');
        }
        if (!$repo->find($id)) {
            throw $this->createNotFoundException('CPU introuvable');
        }

        $build = $this->getBuild($session);
        $build = $this->setSingle($build, 'cpuId', $id);
        $this->saveBuild($session, $build);

        return $this->redirectToRoute('app_forge');
    }

    //verfi que le cpu est choisis avant la motherboard si oui je recupere via le build state l'id du cpu je recupere ces attribus via le repo,
//je trie les motherboard qui ont un socket compatible, l'utilisateur et rediriger vers une vue ou il peux selectionner une des motherboard
    #[Route('/forge/select/motherboard', name: 'forge_select_motherboard', methods: ['GET'])]
    public function selectMotherboard(SessionInterface $session, CpuRepository $cpuRepo, MotherboardRepository $mbRepo): Response
    {
        $build = $this->getBuild($session);
        $cpuId = $build['cpuId'];
        if (!$build['cpuId']) {
            $this->addFlash('warning', 'Choisis d’abord un processeur.');
            return $this->redirectToRoute('app_forge');
        }
        $cpu = $cpuRepo->find($cpuId);
        $socket = $cpu->getSocket();
        $motherboards = $mbRepo->findBy(['socket' => $socket], ['id' => 'DESC']);

        return $this->render('motherboard/index.html.twig', [
            'motherboards' => $motherboards,
            'build' => $build,
        ]);
    }

    //Motherboard Addition
    #[Route('/forge/add/motherboard/{id}', name: 'forge_add_motherboard', methods: ['POST'])]
    public function addMotherboard(int $id, Request $request, SessionInterface $session, MotherboardRepository $repo): Response
    {
        $submittedToken = $request->request->get('_token');
        if (!$this->isCsrfTokenValid('add_motherboard_' . $id, $submittedToken)) {
            throw $this->createAccessDeniedException('Jeton CSRF invalide.');
        }
        if (!$repo->find($id)) {
            throw $this->createNotFoundException('Motherboard introuvable');
        }

        $build = $this->getBuild($session);
        $build = $this->setSingle($build, 'mbId', $id);
        $this->saveBuild($session, $build);
        return $this->redirectToRoute('app_forge');
    }

    //RAM Selection
    #[Route('/forge/select/ram', name: 'forge_select_ram', methods: ['GET'])]
    public function selectRam(MotherboardRepository $mbRepo, RamRepository $ramRepo, SessionInterface $session): Response
    {
        $build = $this->getBuild($session);
        $gpuId = $build['gpuId'];
        if (!$gpuId) {
            $this->addFlash('warning', 'Choisis d’abord une carte graphique.');
            return $this->redirectToRoute('app_forge');
        }
        $mb = $mbRepo->find($build['mbId']);
        $memoryType = $mb->getMemoryType();
        $memoryMax = $mb->getMemoryMax();
        $rams = $ramRepo->createQueryBuilder('ram')
            ->andWhere('ram.type LIKE :memoryType')
            ->andWhere('ram.total <= :memoryMax')
            ->setParameter('memoryType', $memoryType)
            ->setParameter('memoryMax', $memoryMax)
            ->orderBy('ram.prix', 'DESC')
            ->getQuery()
            ->getResult();

        return $this->render('ram/index.html.twig', [
            'rams' => $rams,
            'build' => $build,
        ]);
    }

    //RAM Addition
    #[Route('/forge/add/ram/{id}', name: 'forge_add_ram', methods: ['POST'])]
    public function addRam(int $id, Request $request, SessionInterface $session, RamRepository $repo): Response
    {
        $submittedToken = $request->request->get('_token');
        if (!$this->isCsrfTokenValid('add_ram_' . $id, $submittedToken)) {
            throw $this->createAccessDeniedException('Jeton CSRF invalide.');
        }
        if (!$repo->find($id)) {
            throw $this->createNotFoundException('ram introuvable');
        }
        $build = $this->getBuild($session);
        $build = $this->setSingle($build, 'ramId', $id);
        $this->saveBuild($session, $build);
        return $this->redirectToRoute('app_forge');
    }

    //GPU Selection
    #[Route('/forge/select/gpu', name: 'forge_select_gpu', methods: ['GET'])]
    public function selectGpu(SessionInterface $session, GpuRepository $gpuRepo, MotherboardRepository $mbRepo): Response
    {
        $build = $this->getBuild($session);
        $mbId = $build['mbId'];
        if (!$mbId) {
            $this->addFlash('warning', 'Choisis d’abord une carte mère.');
            return $this->redirectToRoute('app_forge');
        }
        $mb = $mbRepo->find($build['mbId']);
        $mbPcie = $mb->getPcieModule();
        $gpus = $gpuRepo->findBy(['pcieModule' => $mbPcie], ['id' => 'DESC']);

        return $this->render('gpu/index.html.twig', [
            'gpus' => $gpus,
            'build' => $build,
        ]);
    }

    //GPU Addition
    #[Route('/forge/add/gpu/{id}', name: 'forge_add_gpu', methods: ['POST'])]
    public function addGpu(int $id, Request $request, SessionInterface $session, GpuRepository $repo): Response
    {
        $submittedToken = $request->request->get('_token');
        if (!$this->isCsrfTokenValid('add_gpu_' . $id, $submittedToken)) {
            throw $this->createAccessDeniedException('Jeton CSRF invalide.');
        }
        if (!$repo->find($id)) {
            throw $this->createNotFoundException('Gpu introuvable');
        }

        $build = $this->getBuild($session);
        $build = $this->setSingle($build, 'gpuId', $id);
        $this->saveBuild($session, $build);

        return $this->redirectToRoute('app_forge');
    }

    //STORAGE Selection
    #[Route('/forge/select/storage', name: 'forge_select_storage', methods: ['GET'])]
    public function selectStorage(SessionInterface $session, MotherboardRepository $mbRepo, StorageRepository $storageRepo): Response
    {
        $build = $this->getBuild($session);
        $ramId = $build['ramId'];
        $mb = $mbRepo->find($build['mbId']);
        if (!$ramId) {
            $this->addFlash('warning', 'Choisis d’abord de la mémoire.');
            return $this->redirectToRoute('app_forge');
        }

        $mbSata = $mb->getSataPort();
        $mbM2 = $mb->getSlotM2();
        if ($mbM2 > 0) {
            $storages = $storageRepo->createQueryBuilder('storage')
                ->andWhere('storage.interface LIKE :pcie')
                ->setParameter('pcie', '%PCie%')
                ->orderBy('storage.prix', 'DESC')
                ->getQuery()
                ->getResult();
        } elseif ($mbSata > 0) {
            $storages = $storageRepo->createQueryBuilder('storage')
                ->andWhere('storage.interface LIKE :sata')
                ->setParameter('sata', '%SATA%')
                ->orderBy('storage.prix', 'DESC')
                ->getQuery()
                ->getResult();
        } else {
            $storages = $storageRepo->findAll();
        }
        return $this->render('storage/index.html.twig', [
            'storages' => $storages,
            'build' => $build,
        ]);
    }

    //STORAGE Addition
    #[Route('/forge/add/storage/{id}', name: 'forge_add_storage', methods: ['POST'])]
    public function addStorage(int $id, Request $request, SessionInterface $session, StorageRepository $repo): Response
    {
        $submittedToken = $request->request->get('_token');
        if (!$this->isCsrfTokenValid('add_storage_' . $id, $submittedToken)) {
            throw $this->createAccessDeniedException('Jeton CSRF invalide.');
        }
        if (!$repo->find($id)) {
            throw $this->createNotFoundException('Storage introuvable');
        }

        $build = $this->getBuild($session);
        $build = $this->setSingle($build, 'storageId', $id);
        $this->saveBuild($session, $build);
        return $this->redirectToRoute('app_forge');
    }

    //CASE Selection
    #[Route('/forge/select/boitier', name: 'forge_select_boitier', methods: ['GET'])]
    public function selectCase(SessionInterface $session, GpuRepository $gpuRepo, CoolerRepository $coolerRepo, MotherboardRepository $mbRepo, BoitierRepository $caseRepo): Response
    {
        $build = $this->getBuild($session);
        $psuId = $build['psuId'];
        $coolerId = $build['coolerId'];
        $mb = $mbRepo->find($build['mbId']);
        $gpu = $gpuRepo->find($build['gpuId']);

        if (!$psuId) {
            $this->addFlash('warning', 'Choisis d’abord une alimentation.');
            return $this->redirectToRoute('app_forge');
        }

        $mbFormFactor = $mb->getFormFactor();
        $gpuLength = $gpu->getLength();

        if (!$coolerId) {
            $boitiers = $caseRepo->createQueryBuilder('c')
                ->andWhere('c.gpuMaxL >= :length')
                ->andWhere('c.mbFormFactor LIKE :formFactor')
                ->setParameter('length', $gpuLength)
                ->setParameter('formFactor', '%' . $mbFormFactor . '%')
                ->orderBy('c.prix', 'DESC')
                ->getQuery()
                ->getResult();
        } else {
            $cooler = $coolerRepo->find($coolerId);
            $coolerIsAio = $cooler->isAio();

            if ($coolerIsAio === false) {

                $coolerHeight = $cooler->getHeigth();
                $boitiers = $caseRepo->createQueryBuilder('c')
                    ->andWhere('c.gpuMaxL >= :length')
                    ->andWhere('c.mbFormFactor LIKE :formFactor')
                    ->andWhere('c.coolerMaxHeight>= :coolerHeight')
                    ->setParameter('length', $gpuLength)
                    ->setParameter('formFactor', '%' . $mbFormFactor . '%')
                    ->setParameter('coolerHeight', $coolerHeight)
                    ->orderBy('c.prix', 'DESC')
                    ->getQuery()
                    ->getResult();
            } else {
                $coolerFan = $cooler->getNbFan();
                $boitiers = $caseRepo->createQueryBuilder('c')
                    ->andWhere('c.gpuMaxL >= :length')
                    ->andWhere('c.mbFormFactor LIKE :formFactor')
                    ->andWhere('c.fanSlot >= :fan')
                    ->setParameter('fan', $coolerFan)
                    ->setParameter('length', $gpuLength)
                    ->setParameter('formFactor', '%' . $mbFormFactor . '%')
                    ->orderBy('c.prix', 'DESC')
                    ->getQuery()
                    ->getResult();
            }
        }
        return $this->render('boitier/index.html.twig', [
            'boitiers' => $boitiers,
            'build' => $build,
        ]);
    }

    //CASE Addition
    #[Route('/forge/add/boitier/{id}', name: 'forge_add_boitier', methods: ['POST'])]
    public function addCase(int $id, Request $request, SessionInterface $session, BoitierRepository $repo): Response
    {
        $submittedToken = $request->request->get('_token');
        if (!$this->isCsrfTokenValid('add_boitier_' . $id, $submittedToken)) {
            throw $this->createAccessDeniedException('Jeton CSRF invalide.');
        }
        if (!$repo->find($id)) {
            throw $this->createNotFoundException('Storage introuvable');
        }

        $build = $this->getBuild($session);
        $build = $this->setSingle($build, 'boitierId', $id);
        $this->saveBuild($session, $build);
        return $this->redirectToRoute('app_forge');
    }

    //COOLER Selection
    #[Route('/forge/select/cooler', name: 'forge_select_cooler', methods: ['GET'])]
    public function selectCpuCooler(
        SessionInterface $session,
        CoolerRepository $coolerRepo,
        CpuRepository $cpuRepo
    ): Response {
        $build = $this->getBuild($session);
        $storageId = $build['storageId'];
        if (!$storageId) {
            $this->addFlash('warning', 'Choisis d’abord du stockage.');
            return $this->redirectToRoute('app_forge');
        }

        $cpu = $cpuRepo->find($build['cpuId']);
        $tdp = $cpu->getTdp();
        $socket = $cpu->getSocket();

        $coolers = $coolerRepo->createQueryBuilder('cooler')
            ->andWhere('cooler.socket LIKE :socket')
            ->andWhere('cooler.tdp >= :tdp')
            ->setParameter('socket', '%' . $socket . '%')
            ->setParameter('tdp', $tdp)
            ->orderBy('cooler.prix', 'DESC')
            ->getQuery()
            ->getResult();


        return $this->render('cooler/index.html.twig', [
            'coolers' => $coolers,
            'build' => $build,
        ]);
    }

    //COOLER Addition
    #[Route('/forge/add/cooler/{id}', name: 'forge_add_cooler', methods: ['POST'])]
    public function addCpuCooler(int $id, Request $request, SessionInterface $session, CoolerRepository $repo): Response
    {
        $submittedToken = $request->request->get('_token');
        if (!$this->isCsrfTokenValid('add_cooler_' . $id, $submittedToken)) {
            throw $this->createAccessDeniedException('Jeton CSRF invalide.');
        }
        if (!$repo->find($id)) {
            throw $this->createNotFoundException('Storage introuvable');
        }

        $build = $this->getBuild($session);
        $build = $this->setSingle($build, 'coolerId', $id);
        $this->saveBuild($session, $build);
        return $this->redirectToRoute('app_forge');
    }

    //PSU Selection
    #[Route('/forge/select/psu', name: 'forge_select_psu', methods: ['GET'])]
    public function selectPsu(
        SessionInterface $session,
        GpuRepository $gpuRepo,
        CpuRepository $cpuRepo,
        PsuRepository $psuRepo
    ): Response {
        $build = $this->getBuild($session);
        $storageId = $build['storageId'];
        $cpuId = $build['cpuId'];
        $gpuId = $build['gpuId'];
        if (!$storageId) {
            $this->addFlash('warning', 'Choisis d’abord du stockage.');
            return $this->redirectToRoute('app_forge');
        }

        $cpu = $cpuRepo->find($cpuId);
        $cpuTdp = $cpu->getTdp();

        $gpu = $gpuRepo->find($gpuId);
        $gpuTdp = $gpu->getTdp();

        $tdpTotal = $cpuTdp + $gpuTdp + 150;


        $psus = $psuRepo->createQueryBuilder('psu')
            ->andWhere('psu.wattage >= :tdp')
            ->setParameter('tdp', $tdpTotal)
            ->orderBy('psu.prix', 'DESC')
            ->getQuery()
            ->getResult();

        return $this->render('psu/index.html.twig', [
            'psus' => $psus,
            'build' => $build,
        ]);
    }

    //PSU Addition
    #[Route('/forge/add/psu/{id}', name: 'forge_add_psu', methods: ['POST'])]
    public function addPsu(int $id, Request $request, SessionInterface $session, PsuRepository $repo): Response
    {
        $submittedToken = $request->request->get('_token');
        if (!$this->isCsrfTokenValid('add_psu_' . $id, $submittedToken)) {
            throw $this->createAccessDeniedException('Jeton CSRF invalide.');
        }
        if (!$repo->find($id)) {
            throw $this->createNotFoundException('Storage introuvable');
        }

        $build = $this->getBuild($session);
        $build = $this->setSingle($build, 'psuId', $id);
        $this->saveBuild($session, $build);
        return $this->redirectToRoute('app_forge');
    }

    //FAN Selection
    #[Route('/forge/select/fan', name: 'forge_select_fan', methods: ['GET'])]
    public function selectFan(
        SessionInterface $session,
        BoitierRepository $boitierRepo,
        FanRepository $fanRepo
    ): Response {
        $build = $this->getBuild($session);
        $boitierId = $build['boitierId'];
        if (!$boitierId) {
            $this->addFlash('warning', 'Choisis d’abord un boitier.');
            return $this->redirectToRoute('app_forge');
        }

        $boitier = $boitierRepo->find($boitierId);
        $maxFanSlot = $boitier->getFanSlot();
        $maxFanWidth = $boitier->getFanSlotWidth();

        $fans = $fanRepo->createQueryBuilder('fan')
            ->andWhere('fan.width <= :maxWidth')
            ->andWhere('fan.quantity <= :slot')
            ->setParameter('slot', $maxFanSlot)
            ->setParameter('maxWidth', $maxFanWidth)
            ->orderBy('fan.prix', 'DESC')
            ->getQuery()
            ->getResult();
        return $this->render('fan/index.html.twig', [
            'fans' => $fans,
            'build' => $build,
        ]);
    }

    //FAN Addition
    #[Route('/forge/add/fan/{id}', name: 'forge_add_fan', methods: ['POST'])]
    public function addFan(int $id, Request $request, SessionInterface $session, FanRepository $repo): Response
    {
        $submittedToken = $request->request->get('_token');
        if (!$this->isCsrfTokenValid('add_fan_' . $id, $submittedToken)) {
            throw $this->createAccessDeniedException('Jeton CSRF invalide.');
        }
        if (!$repo->find($id)) {
            throw $this->createNotFoundException('Ventilateur introuvable');
        }

        $build = $this->getBuild($session);
        $build = $this->setSingle($build, 'fanId', $id);
        $this->saveBuild($session, $build);
        return $this->redirectToRoute('app_forge');
    }

    //RESET
    #[Route('/forge/remove/{part}', name: 'forge_remove_part', methods: ['POST'])]
    public function removePart(string $part, SessionInterface $session, Request $request): Response
    {
        $token = $request->request->get('_token');
        if (!$this->isCsrfTokenValid('forge_remove_part_' . $part, $token)) {
            throw $this->createAccessDeniedException('Token CSRF invalide');
        }

        $build = $this->getBuild($session);
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
        }else {
        if ($part === 'cpuId') {

            $build['cpuId'] = null;
            $build['mbId'] = null;
            $build['gpuId'] = null;
            $build['ramId'] = null;
            $build['storageId'] = null;
            $build['coolerId'] = null;
            $build['psuId'] = null;
            $build['boitierId'] = null;
            $build['fanId'] = null;

        } elseif ($part === 'mbId') {

            $build['mbId'] = null;
            $build['gpuId'] = null;
            $build['ramId'] = null;
            $build['storageId'] = null;
            $build['coolerId'] = null;
            $build['psuId'] = null;
            $build['boitierId'] = null;
            $build['fanId'] = null;

        } elseif ($part === 'gpuId') {

            $build['gpuId'] = null;
            $build['ramId'] = null;
            $build['storageId'] = null;
            $build['coolerId'] = null;
            $build['psuId'] = null;
            $build['boitierId'] = null;
            $build['fanId'] = null;

        } elseif ($part === 'ramId') {

            $build['ramId'] = null;
            $build['storageId'] = null;
            $build['coolerId'] = null;
            $build['psuId'] = null;
            $build['boitierId'] = null;
            $build['fanId'] = null;

        } elseif ($part === 'storageId') {

            $build['storageId'] = null;
            $build['coolerId'] = null;
            $build['psuId'] = null;
            $build['boitierId'] = null;
            $build['fanId'] = null;

        } elseif ($part === 'coolerId') {

            $build['coolerId'] = null;
            $build['psuId'] = null;
            $build['boitierId'] = null;
            $build['fanId'] = null;

        } elseif ($part === 'psuId') {

            $build['psuId'] = null;
            $build['boitierId'] = null;
            $build['fanId'] = null;

        } elseif ($part === 'boitierId') {

            $build['boitierId'] = null;
            $build['fanId'] = null;

        } elseif ($part === 'fanId') {

            $build['fanId'] = null;

        }}

        $this->saveBuild($session, $build);
        return $this->redirectToRoute('app_forge');
    }
    #[Route('/forge/reset', name: 'forge_reset', methods: ['POST'])]
    public function reset(Request $request, SessionInterface $session): Response
    {
        $submittedToken = $request->request->get('_token');
        if (!$this->isCsrfTokenValid('forge_reset', $submittedToken)) {
            throw $this->createAccessDeniedException('Jeton CSRF invalide.');
        }
        $session->remove(self::SESSION_KEY);
        return $this->redirectToRoute('app_forge');
    }
    //SAVE
    #[Route('/forge/save', name: 'forge_save', methods: ['POST'])]
    public function save(
        SessionInterface $session,
        Request $request,
        CpuRepository $cpuRepo,
        MotherboardRepository $mbRepo,
        GpuRepository $gpuRepo,
        RamRepository $ramRepo,
        StorageRepository $storageRepo,
        CoolerRepository $coolerRepo,
        PsuRepository $psuRepo,
        BoitierRepository $boitierRepo,
        FanRepository $fanRepo,
        EntityManagerInterface $em,
    ): Response {
        $submittedToken = $request->request->get('_token');
        if (!$this->isCsrfTokenValid('forge_save', $submittedToken)) {
            throw $this->createAccessDeniedException('Jeton CSRF invalide.');
        }
        $build = $this->getBuild($session);

        $user = $this->getUser();

        if (!$user instanceof \App\Entity\User) {
            throw $this->createAccessDeniedException('Vous devez être connecter.');
        }

        // CPU
        $cpu = $cpuRepo->find($build['cpuId']);

        // Motherboard
        $mb = $mbRepo->find($build['mbId']);

        // GPU
        $gpu = $gpuRepo->find($build['gpuId']);

        // RAM
        $ram = $ramRepo->find($build['ramId']);

        // Storage
        $storage = $storageRepo->find($build['storageId']);

        // Cooler
        if (!empty($build['coolerId'])) {
            $cooler = $coolerRepo->find($build['coolerId']);
        }

        // PSU
        $psu = $psuRepo->find($build['psuId']);

        // Boitier
        $boitier = $boitierRepo->find($build['boitierId']);

        // Fan
        if (!empty($build['fanId'])) {
            $fan = $fanRepo->find($build['fanId']);
        }

        if (
            empty($cpu) ||
            empty($mb) ||
            empty($gpu) ||
            empty($storage) ||
            empty($ram) ||
            empty($boitier) ||
            empty($psu)
        ) {
            $this->addFlash('error', 'Build non valide');
            return $this->redirectToRoute('app_forge');
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

        $buildCreate->setUser($user);
        $buildCreate->setTotalPrice();
        $em->persist($buildCreate);
        $em->flush();
        $id = $buildCreate->getId();
        $session->remove(self::SESSION_KEY);
        return $this->redirectToRoute('app_build_show', [
            'id' => $id
        ]);
    }
}