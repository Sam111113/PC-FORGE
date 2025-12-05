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

#[IsGranted('IS_AUTHENTICATED_FULLY')]
final class ForgeController extends AbstractController
{
    private const SESSION_KEY = 'userBuild';

    private function initBuild(SessionInterface $session): array
    {
        $state = $session->get(self::SESSION_KEY);
        if (!$state) {
            $state = [
                'cpuId' => null,
                'mbId' => null,
                'gpuId' => null,
                'ramId' => [],
                'storageId' => [],
                'coolerId' => null,
                'psuId' => null,
                'boitierId' => null,
                'fan' => [],
            ];
            $session->set(self::SESSION_KEY, $state);
        }
        return $state;
    }

    private function saveBuild(SessionInterface $session, array $state): void
    {
        $session->set(self::SESSION_KEY, $state);
    }

    private function getBuild(SessionInterface $session): array
    {
        return $this->initBuild($session);
    }

    private function setSingle(array $state, string $key, ?int $id): array
    {
        $state[$key] = $id;
        return $state;
    }
    private function toggleInList(array $state, string $key, int $id): array
    {
        $list = $state[$key] ?? [];
        $pos = array_search($id, $list, true);

        if ($pos === false) {
            $list[] = $id;
        } else {
            array_splice($list, $pos, 1);
        }
        $state[$key] = $list;
        return $state;
    }
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
        $session = $request->getSession();
        $build = $this->getBuild($session);
        // Prépare un tableau qui contiendra les entités à envoyer à Twig
        $selected = [
            'cpu' => null,
            'motherboard' => null,
            'gpu' => null,
            'rams' => [],   // liste
            'storages' => [],   // liste
            'cooler' => null,
            'psu' => null,
            'boitier' => null,
            'fans' => [],   // liste
        ];

        // CPU
        if (!empty($build['cpuId'])) {
            $selected['cpu'] = $cpuRepo->find($build['cpuId']);
        }

        // Motherboard
        if (!empty($build['mbId'])) {
            $selected['motherboard'] = $mbRepo->find($build['mbId']);
            $mb = $mbRepo->find($build['mbId']);
            if ($mb) {
                $maxRamSlot = $mb->getMemorySlot();
                $maxStorageSlot = $mb->getSataPort() + $mb->getSlotM2();
            }
        }

        // GPU
        if (!empty($build['gpuId'])) {
            $selected['gpu'] = $gpuRepo->find($build['gpuId']);
        }

        // RAM (liste)
        $selected['rams'] = [];
        if (!empty($build['ramId'])) {
            foreach ($build['ramId'] as $ramId) {
                if ($ram = $ramRepo->find($ramId)) {
                    $selected['rams'][] = $ram;
                    $nbRam = $ram->getNbModule();
                    $maxRamSlot -= $nbRam;
                }
            }
        }

        // Storage (liste)
        $selected['storages'] = [];
        if (!empty($build['storageId'])) {
            foreach ($build['storageId'] as $storageId) {
                if ($storage = $storageRepo->find($storageId)) {
                    $selected['storages'][] = $storage;
                    $maxStorageSlot -= 1;
                }
            }
        }

        // Cooler
        if (!empty($build['coolerId'])) {
            $selected['cooler'] = $coolerRepo->find($build['coolerId']);
            $cooler = $coolerRepo->find($build['coolerId']);
            $isAio = $cooler->isAio();
            if ($isAio === true) {
                $nbCoolerFan = $cooler->getNbFan();
            }

        }

        // PSU
        if (!empty($build['psuId'])) {
            $selected['psu'] = $psuRepo->find($build['psuId']);
        }

        // Boitier
        if (!empty($build['boitierId'])) {
            $selected['boitier'] = $boitierRepo->find($build['boitierId']);
            $boitier = $boitierRepo->find($build['boitierId']);
            if ($boitier) {
                $maxFanSlot = $boitier->getFanSlot();
                if (!empty($nbCoolerFan)) {
                    $maxFanSlot -= $nbCoolerFan;
                }
            }
        }

        // Fan (liste)
        $selected['fans'] = [];
        if (!empty($build['fan'])) {
            foreach ($build['fan'] as $fanId) {
                if ($fan = $fanRepo->find($fanId)) {
                    $selected['fans'][] = $fan;
                    $nbOfFan = $fan->getQuantity();
                    $maxFanSlot -= $nbOfFan;
                }
            }
        }

        // Affichage dans la vue
        return $this->render('forge/index.html.twig', [
            'build' => $build,
            'selected' => $selected,
            'maxRamSlot' => $maxRamSlot ?? 0,
            'maxStorageSlot' => $maxStorageSlot ?? 0,
            'maxFanSlot' => $maxFanSlot ?? 0,
        ]);
    }
    #[Route('/forge/select/cpu', name: 'forge_select_cpu')]
    public function selectCpu(CpuRepository $cpuRepo, Request $request): Response
    {
        $session = $request->getSession();
        $build = $session->get('userBuild', []);
        $cpus = $cpuRepo->findBy([], ['prix' => 'DESC']);
        return $this->render('cpu/index.html.twig', [
            'cpus' => $cpus,
            'build' => $build,
        ]);
    }

    //CPU Addition
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

        $state = $this->getBuild($session);
        $state = $this->setSingle($state, 'cpuId', $id);
        $this->saveBuild($session, $state);

        return $this->redirectToRoute('app_forge');
    }

    //Motherboard Selection
    #[Route('/forge/select/motherboard', name: 'forge_select_motherboard', methods: ['GET'])]
    public function selectMotherboard(SessionInterface $session, CpuRepository $cpuRepo, MotherboardRepository $mbRepo): Response
    {
        $build = $session->get('userBuild', []);
        $cpuId = $build['cpuId'];
        if (!$cpuId) {
            $this->addFlash('warning', 'Choisis d’abord un processeur.');
            return $this->redirectToRoute('app_forge');
        }
        $cpu = $cpuRepo->find($cpuId);
        $socket = $cpu->getSocket();
        $cpu = $cpuRepo->find($build['cpuId']);
        $motherboards = $mbRepo->findBy(['socket' => $socket], ['id' => 'ASC']);

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

        $state = $this->getBuild($session);
        $state = $this->setSingle($state, 'mbId', $id);
        $this->saveBuild($session, $state);
        return $this->redirectToRoute('app_forge');
    }

    //RAM Selection
    #[Route('/forge/select/ram', name: 'forge_select_ram', methods: ['GET'])]
    public function selectRam(MotherboardRepository $mbRepo, RamRepository $ramRepo, GpuRepository $gpuRepo, SessionInterface $session): Response
    {
        $build = $session->get('userBuild', []);
        $gpuId = $build['gpuId'];
        if (!$gpuId) {
            $this->addFlash('warning', 'Choisis d’abord une carte graphique.');
            return $this->redirectToRoute('app_forge');
        }
        $mb = $mbRepo->find($build['mbId']);
        $memoryType = $mb->getMemoryType();
        $maxRamSlot = $mb->getMemorySlot();
        $rams = $ramRepo->findBy(['type' => $memoryType], ['id' => 'ASC']);

        return $this->render('ram/index.html.twig', [
            'rams' => $rams,
            'build' => $build,
            'maxRamSlot' => $maxRamSlot,
        ]);
    }

    //RAM Addition
    #[Route('/forge/add/ram/{id}', name: 'forge_add_ram', methods: ['POST'])]
    public function addRam(int $id, Request $request, SessionInterface $session, RamRepository $repo): Response
    {
        $submittedToken = $request->request->get('_token');
        $ramQuantity = $request->request->get('quantity');
        if (!$this->isCsrfTokenValid('add_ram_' . $id, $submittedToken)) {
            throw $this->createAccessDeniedException('Jeton CSRF invalide.');
        }
        if (!$repo->find($id)) {
            throw $this->createNotFoundException('ram introuvable');
        }
        for ($i = 0; $i < $ramQuantity; $i++) {
        $state = $this->getBuild($session);
        $state = $this->toggleInList($state, 'ramId', $id);
        }
        $this->saveBuild($session, $state);
        return $this->redirectToRoute('app_forge');
    }

    //GPU Selection
    #[Route('/forge/select/gpu', name: 'forge_select_gpu', methods: ['GET'])]
    public function selectGpu(SessionInterface $session, GpuRepository $gpuRepo, MotherboardRepository $mbRepo): Response
    {
        $build = $session->get('userBuild', []);
        $mbId = $build['mbId'];
        if (!$mbId) {
            $this->addFlash('warning', 'Choisis d’abord une carte mère.');
            return $this->redirectToRoute('app_forge');
        }
        $mb = $mbRepo->find($build['mbId']);
        $mbPcie = $mb->getPcieModule();
        $gpus = $gpuRepo->findBy(['pcieModule' => $mbPcie], ['id' => 'ASC']);

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

        $state = $this->getBuild($session);
        $state = $this->setSingle($state, 'gpuId', $id);
        $this->saveBuild($session, $state);

        return $this->redirectToRoute('app_forge');
    }

    //STORAGE Selection
    #[Route('/forge/select/storage', name: 'forge_select_storage', methods: ['GET'])]
    public function selectStorage(SessionInterface $session, MotherboardRepository $mbRepo, StorageRepository $storageRepo): Response
    {
        $build = $session->get('userBuild', []);
        $ramId = $build['ramId'];
        $mb = $mbRepo->find($build['mbId']);
        $storageIds = ['storageId'];
        if (!$ramId) {
            $this->addFlash('warning', 'Choisis d’abord de la mémoire.');
            return $this->redirectToRoute('app_forge');
        }

        $mbSataMax = (int) $mb->getSataPort();
        $mbM2Max = (int) $mb->getSlotM2();
        $mbMaxStorageSlot = $mbM2Max + $mbSataMax;

        // Comptage des stockages déjà sélectionnés
        $usedSata = 0;
        $usedM2 = 0;
        if (!empty($storageIds)) {
            $already = $storageRepo->findBy(['id' => $storageIds]);
            foreach ($already as $s) {
                $iface = (string) $s->getInterface(); // ex: "SATA III" ou "PCIe 4.0"
                $type = (string) $s->getType();      // ex: "SSD NVMe" / "SSD SATA" / "HDD"

                $isSata = stripos($iface, 'SATA') !== false
                    || stripos($type, 'SATA') !== false
                    || stripos($type, 'HDD') !== false;

                $isM2 = stripos($iface, 'PCIe') !== false   // NVMe via PCIe en M.2
                    || stripos($iface, 'M.2') !== false
                    || stripos($type, 'NVMe') !== false;

                if ($isSata) {
                    $usedSata++;
                } elseif ($isM2) {
                    $usedM2++;
                }
            }
        }


        $sataLeft = max(0, $mbSataMax - $usedSata);
        $m2Left = max(0, $mbM2Max - $usedM2);


        $sataOptions = $sataLeft > 0
            ? $storageRepo->createQueryBuilder('st')
                ->where('st.interface LIKE :sata OR st.type LIKE :ssdSata OR st.type = :hdd')
                ->setParameter('sata', 'SATA%')
                ->setParameter('ssdSata', '%SATA%')
                ->setParameter('hdd', 'HDD')
                ->orderBy('st.id', 'ASC')
                ->getQuery()->getResult()
            : [];

        $m2Options = $m2Left > 0
            ? $storageRepo->createQueryBuilder('st')
                ->where('(st.interface LIKE :pcie OR st.interface LIKE :m2 OR st.type LIKE :nvme)')
                ->setParameter('pcie', 'PCIe%')
                ->setParameter('m2', 'M.2%')
                ->setParameter('nvme', '%NVMe%')
                ->orderBy('st.id', 'ASC')
                ->getQuery()->getResult()
            : [];
        $storages = array_merge($sataOptions, $m2Options);

        return $this->render('storage/index.html.twig', [
            'storages' => $storages,
            'mbMaxStorageSlot' => $mbMaxStorageSlot,
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

        $state = $this->getBuild($session);
        $state = $this->toggleInList($state, 'storageId', $id);
        $this->saveBuild($session, $state);
        return $this->redirectToRoute('app_forge');
    }

    //CASE Selection
    #[Route('/forge/select/boitier', name: 'forge_select_boitier', methods: ['GET'])]
    public function selectCase(SessionInterface $session, GpuRepository $gpuRepo, CoolerRepository $coolerRepo, MotherboardRepository $mbRepo, BoitierRepository $caseRepo): Response
    {
        $build = $session->get('userBuild', []);
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
                ->orderBy('c.prix', 'ASC')
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
                    ->orderBy('c.prix', 'ASC')
                    ->getQuery()
                    ->getResult();
            } else {
                $boitiers = $caseRepo->createQueryBuilder('c')
                    ->andWhere('c.gpuMaxL >= :length')
                    ->andWhere('c.mbFormFactor LIKE :formFactor')
                    ->setParameter('length', $gpuLength)
                    ->setParameter('formFactor', '%' . $mbFormFactor . '%')
                    ->orderBy('c.prix', 'ASC')
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

        $state = $this->getBuild($session);
        $state = $this->setSingle($state, 'boitierId', $id);
        $this->saveBuild($session, $state);
        return $this->redirectToRoute('app_forge');
    }

    //COOLER Selection
    #[Route('/forge/select/cooler', name: 'forge_select_cooler', methods: ['GET'])]
    public function selectCpuCooler(
        Request $request,
        CoolerRepository $coolerRepo,
        CpuRepository $cpuRepo
    ): Response {
        $session = $request->getSession();
        $build = $session->get('userBuild', []);
        $storageId = $build['cpuId'];
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
            ->orderBy('cooler.prix', 'ASC')
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

        $state = $this->getBuild($session);
        $state = $this->setSingle($state, 'coolerId', $id);
        $this->saveBuild($session, $state);
        return $this->redirectToRoute('app_forge');
    }

    //PSU Selection
    #[Route('/forge/select/psu', name: 'forge_select_psu', methods: ['GET'])]
    public function selectPsu(
        Request $request,
        GpuRepository $gpuRepo,
        CpuRepository $cpuRepo,
        PsuRepository $psuRepo
    ): Response {
        $session = $request->getSession();
        $build = $session->get('userBuild', []);
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
            ->orderBy('psu.prix', 'ASC')
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

        $state = $this->getBuild($session);
        $state = $this->setSingle($state, 'psuId', $id);
        $this->saveBuild($session, $state);
        return $this->redirectToRoute('app_forge');
    }

    //FAN Selection
    #[Route('/forge/select/fan', name: 'forge_select_fan', methods: ['GET'])]
    public function selectFan(
        Request $request,
        BoitierRepository $boitierRepo,
        FanRepository $fanRepo
    ): Response {

        $session = $request->getSession();
        $build = $session->get('userBuild', []);
        $boitierId = $build['boitierId'];
        $fanId = $build['fan'];
        if (!$boitierId) {
            $this->addFlash('warning', 'Choisis d’abord un boitier.');
            return $this->redirectToRoute('app_forge');
        }
        $nbFanSelected = 0;
        if ($fanId) {
            foreach ($fanId as $key => $id) {
                $fan = $fanRepo->find($id);
                $nbFanSelected += $fan->getQuantity();
            }
        }
        $boitier = $boitierRepo->find($boitierId);
        $maxBoitierFan = $boitier->getFanSlot();
        $maxFanSlot = $boitier->getFanSlot() - $nbFanSelected;
        $maxFanWidth = $boitier->getFanSlotWidth();

        $fans = $fanRepo->createQueryBuilder('fan')
            ->andWhere('fan.width <= :maxWidth')
            ->andWhere('fan.quantity <= :slot')
            ->setParameter('slot', $maxFanSlot)
            ->setParameter('maxWidth', $maxFanWidth)
            ->orderBy('fan.prix', 'ASC')
            ->getQuery()
            ->getResult();
        return $this->render('fan/index.html.twig', [
            'fans' => $fans,
            'build' => $build,
            'maxBoitierFan' => $maxBoitierFan,
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

        $state = $this->getBuild($session);
        $state = $this->toggleInList($state, 'fan', $id);
        $this->saveBuild($session, $state);
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

        $state = $this->getBuild($session);

        $singleKeys = ['cpuId', 'mbId', 'gpuId', 'coolerId', 'psuId', 'boitierId'];
        $listKeys = ['ramId', 'storageId', 'fan'];

        if (in_array($part, $singleKeys, true)) {
            $state[$part] = null;
        } elseif (in_array($part, $listKeys, true)) {
            $state[$part] = [];
        } else {
            throw $this->createNotFoundException('Part inconnue');
        }

        $this->saveBuild($session, $state);
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
        $session = $request->getSession();
        $build = $this->getBuild($session);

        $user = $this->getUser();

        if (!$user instanceof \App\Entity\User) {
            throw $this->createAccessDeniedException('You must be logged in to save a build.');
        }

        // CPU
            $cpu = $cpuRepo->find($build['cpuId']);

        // Motherboard
            $mb = $mbRepo->find($build['mbId']);

        // GPU
            $gpu = $gpuRepo->find($build['gpuId']);

        // RAM (liste)
        $rams = [];
            foreach ($build['ramId'] as $ramId) {
                if ($ram = $ramRepo->find($ramId)) {
                    $rams[] = $ram;
                }
            }

        // Storage (liste)
        $storages = [];
            foreach ($build['storageId'] as $storageId) {
                if ($storage = $storageRepo->find($storageId)) {
                    $storages[] = $storage;
                }
            }

        // Cooler
        if (!empty($build['cooler'])) {
            $cooler = $coolerRepo->find($build['coolerId']);
        }

        // PSU
            $psu = $psuRepo->find($build['psuId']);

        // Boitier
            $boitier = $boitierRepo->find($build['boitierId']);

        // Fan (liste)
        $fans = [];
        if (!empty($build['fan'])) {
            foreach ($build['fan'] as $fanId) {
                if ($fan = $fanRepo->find($fanId)) {
                    $fans[] = $fan;
                }
            }
        }

        if (
            empty($cpu) ||
            empty($mb) ||
            empty($gpu) ||
            empty($storages) ||
            empty($rams) ||
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

        foreach ($rams as $ram) {
            $buildCreate->addRam($ram);
        }
        foreach ($storages as $storage) {
            $buildCreate->addStorage($storage);
        }
        if (!empty($fans)) {
            foreach ($fans as $fan) {
                $buildCreate->addFan($fan);
            }
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