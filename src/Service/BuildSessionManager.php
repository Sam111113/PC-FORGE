<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Service de gestion de la session du builder PC
 * Centralise toute la logique de stockage et récupération
 * des composants sélectionnés pendant la construction du PC
 */
class BuildSessionManager
{
    private const SESSION_KEY = 'userBuild';
    /**
     * Permet d'instancier RequestStack dans n'importe quelle méthode de cette classe
     */
    public function __construct(
        private readonly RequestStack $requestStack
    ) {
    }

    /**
     * Récupère la session depuis la requête courante
     */
    private function getSession()
    {
        return $this->requestStack->getSession();
    }

    /**
     * Initialise un nouveau build vide en session
     * ou récupère le build existant s'il y en a un
     */
    public function initBuild(): array
    {
        $session = $this->getSession();
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

    /**
     * Récupère le build actuel depuis la session
     */
    public function getBuild(): array
    {
        return $this->initBuild();
    }

    /**
     * Sauvegarde le build en session
     */
    public function saveBuild(array $state): void
    {
        $this->getSession()->set(self::SESSION_KEY, $state);
    }

    /**
     * Définit l'ID d'un composant spécifique dans le build
     */
    public function setComponent(string $key, ?int $id): void
    {
        $state = $this->getBuild();
        $state[$key] = $id;
        $this->saveBuild($state);
    }

    /**
     * Récupère l'ID d'un composant spécifique dans le build
     */
    public function getComponent(string $key): ?int
    {
        $build = $this->getBuild();
        return $build[$key] ?? null;
    }

    /**
     * Vérifie si un composant a été sélectionné dans le build
     */
    public function hasComponent(string $key): bool
    {
        return !empty($this->getComponent($key));
    }

    /**
     * Supprime un composant et tous ceux qui en dépendent
     * Respecte l'ordre de dépendance : CPU → MB → GPU → RAM → Storage → Cooler → PSU → Boitier → Fan
     */
    public function removePart(string $part): void
    {
        $build = $this->getBuild();

        // Ordre de dépendance des composants
        $dependencyMap = [
            'cpuId' => ['cpuId', 'mbId', 'gpuId', 'ramId', 'storageId', 'coolerId', 'psuId', 'boitierId', 'fanId'],
            'mbId' => ['mbId', 'gpuId', 'ramId', 'storageId', 'coolerId', 'psuId', 'boitierId', 'fanId'],
            'gpuId' => ['gpuId', 'ramId', 'storageId', 'coolerId', 'psuId', 'boitierId', 'fanId'],
            'ramId' => ['ramId', 'storageId', 'coolerId', 'psuId', 'boitierId', 'fanId'],
            'storageId' => ['storageId', 'coolerId', 'psuId', 'boitierId', 'fanId'],
            'coolerId' => ['coolerId', 'psuId', 'boitierId', 'fanId'],
            'psuId' => ['psuId', 'boitierId', 'fanId'],
            'boitierId' => ['boitierId', 'fanId'],
            'fanId' => ['fanId'],
        ];

        if (isset($dependencyMap[$part])) {
            foreach ($dependencyMap[$part] as $key) {
                $build[$key] = null;
            }
            $this->saveBuild($build);
        }
    }

    /**
     * Réinitialise complètement le build (supprime de la session)
     */
    public function resetBuild(): void
    {
        $this->getSession()->remove(self::SESSION_KEY);
    }

    /**
     * Vérifie si le build est complet (tous les composants obligatoires sont présents)
     */
    public function isBuildComplete(): bool
    {
        $build = $this->getBuild();

        // Composants obligatoires (cooler et fan sont optionnels)
        $required = ['cpuId', 'mbId', 'gpuId', 'ramId', 'storageId', 'psuId', 'boitierId'];

        foreach ($required as $key) {
            if (empty($build[$key])) {
                return false;
            }
        }

        return true;
    }

}
