<?php

namespace App\Entity;

use App\Repository\BoitierRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BoitierRepository::class)]
class Boitier
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

    #[ORM\Column]
    private ?int $length = null;

    #[ORM\Column]
    private ?int $heigth = null;

    #[ORM\Column]
    private ?int $width = null;

    #[ORM\Column]
    private ?int $gpuMaxL = null;

    #[ORM\Column]
    private ?int $fanSlot = null;

    #[ORM\Column]
    private ?int $psuMaxL = null;

    #[ORM\Column(length: 30)]
    private ?string $aioSupport = null;

    #[ORM\Column(length: 30)]
    private ?string $mbFormFactor = null;

    #[ORM\Column]
    private ?int $FanSlotWidth = null;

    /**
     * @var Collection<int, Build>
     */
    #[ORM\OneToMany(targetEntity: Build::class, mappedBy: 'Boitier')]
    private Collection $builds;

    #[ORM\Column]
    private ?int $coolerMaxHeight = null;

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

    public function getLength(): ?int
    {
        return $this->length;
    }

    public function setLength(int $length): static
    {
        $this->length = $length;

        return $this;
    }

    public function getHeigth(): ?int
    {
        return $this->heigth;
    }

    public function setHeigth(int $heigth): static
    {
        $this->heigth = $heigth;

        return $this;
    }

    public function getWidth(): ?int
    {
        return $this->width;
    }

    public function setWidth(int $width): static
    {
        $this->width = $width;

        return $this;
    }

    public function getGpuMaxL(): ?int
    {
        return $this->gpuMaxL;
    }

    public function setGpuMaxL(int $gpuMaxL): static
    {
        $this->gpuMaxL = $gpuMaxL;

        return $this;
    }

    public function getFanSlot(): ?int
    {
        return $this->fanSlot;
    }

    public function setFanSlot(int $fanSlot): static
    {
        $this->fanSlot = $fanSlot;

        return $this;
    }

    public function getPsuMaxL(): ?int
    {
        return $this->psuMaxL;
    }

    public function setPsuMaxL(int $psuMaxL): static
    {
        $this->psuMaxL = $psuMaxL;

        return $this;
    }

    public function getAioSupport(): ?string
    {
        return $this->aioSupport;
    }

    public function setAioSupport(string $aioSupport): static
    {
        $this->aioSupport = $aioSupport;

        return $this;
    }

    public function getMbFormFactor(): ?string
    {
        return $this->mbFormFactor;
    }

    public function setMbFormFactor(string $mbFormFactor): static
    {
        $this->mbFormFactor = $mbFormFactor;

        return $this;
    }

    public function getFanSlotWidth(): ?int
    {
        return $this->FanSlotWidth;
    }

    public function setFanSlotWidth(int $FanSlotWidth): static
    {
        $this->FanSlotWidth = $FanSlotWidth;

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
            $build->setBoitier($this);
        }

        return $this;
    }

    public function removeBuild(Build $build): static
    {
        if ($this->builds->removeElement($build)) {
            // set the owning side to null (unless already changed)
            if ($build->getBoitier() === $this) {
                $build->setBoitier(null);
            }
        }

        return $this;
    }

    public function getCoolerMaxHeight(): ?int
    {
        return $this->coolerMaxHeight;
    }

    public function setCoolerMaxHeight(int $coolerMaxHeight): static
    {
        $this->coolerMaxHeight = $coolerMaxHeight;

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
