<?php

namespace App\Entity;

use App\Repository\GpuRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: GpuRepository::class)]
class Gpu
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
    private ?string $pcieModule = null;

    #[ORM\Column]
    private ?int $length = null;

    #[ORM\Column]
    private ?int $tdp = null;

    /**
     * @var Collection<int, Build>
     */
    #[ORM\ManyToMany(targetEntity: Build::class, mappedBy: 'Gpu')]
    private Collection $builds;

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

    public function getPcieModule(): ?string
    {
        return $this->pcieModule;
    }

    public function setPcieModule(string $pcieModule): static
    {
        $this->pcieModule = $pcieModule;

        return $this;
    }

    public function getLength(): ?int
    {
        return $this->length;
    }

    public function setLength(int $length): static
    {
        $this->length = $length;

        return $this;
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
            $build->addGpu($this);
        }

        return $this;
    }

    public function removeBuild(Build $build): static
    {
        if ($this->builds->removeElement($build)) {
            $build->removeGpu($this);
        }

        return $this;
    }
    public function __toString(): string
    {
        return $this->marque . ' ' . $this->modele . ' - ' . $this->prix . '€';
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
