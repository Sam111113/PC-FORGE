<?php

namespace App\Entity;

use App\Repository\CpuRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CpuRepository::class)]
class Cpu
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $marque = null;

    #[ORM\Column(length: 100)]
    private ?string $modele = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $prix = null;

    #[ORM\Column(length: 30)]
    private ?string $socket = null;

    /**
     * @var Collection<int, Build>
     */
    #[ORM\OneToMany(targetEntity: Build::class, mappedBy: 'Cpu')]
    private Collection $builds;

    #[ORM\Column]
    private ?int $tdp = null;

    #[ORM\Column(length: 30)]
    private ?string $Ascin = null;

    public function __construct()
    {
        $this->builds = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMarque(): ?string
    {
        return $this->marque;
    }

    public function setMarque(string $marque): static
    {
        $this->marque = $marque;

        return $this;
    }

    public function getModele(): ?string
    {
        return $this->modele;
    }

    public function setModele(string $modele): static
    {
        $this->modele = $modele;

        return $this;
    }

    public function getPrix(): ?string
    {
        return $this->prix;
    }

    public function setPrix(string $prix): static
    {
        $this->prix = $prix;

        return $this;
    }

    public function getSocket(): ?string
    {
        return $this->socket;
    }

    public function setSocket(string $socket): static
    {
        $this->socket = $socket;

        return $this;
    }

    /**
     * @return Collection<int, Build>
     */
    public function getBuilds(): Collection
    {
        return $this->builds;
    }

    public function addBuild(Build $build): static
    {
        if (!$this->builds->contains($build)) {
            $this->builds->add($build);
            $build->setCpu($this);
        }

        return $this;
    }

    public function removeBuild(Build $build): static
    {
        if ($this->builds->removeElement($build)) {
            if ($build->getCpu() === $this) {
                $build->setCpu(null);
            }
        }

        return $this;
    }

    public function __toString(): string
    {
        return $this->marque . ' ' . $this->modele . ' - ' . $this->prix . '€';
    }

    public function getTdp(): ?int
    {
        return $this->tdp;
    }

    public function setTdp(int $tdp): static
    {
        $this->tdp = $tdp;

        return $this;
    }

    public function getAscin(): ?string
    {
        return $this->Ascin;
    }

    public function setAscin(string $Ascin): static
    {
        $this->Ascin = $Ascin;

        return $this;
    }
}
