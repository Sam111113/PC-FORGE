<?php

namespace App\Entity;

use App\Repository\MotherboardRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MotherboardRepository::class)]
class Motherboard
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

    #[ORM\Column(length: 20)]
    private ?string $socket = null;

    #[ORM\Column]
    private ?int $pcieSlot = null;

    #[ORM\Column(length: 20)]
    private ?string $pcieModule = null;

    #[ORM\Column]
    private ?int $slotM2 = null;

    #[ORM\Column]
    private ?int $sataPort = null;

    #[ORM\Column]
    private ?int $memoryMax = null;

    #[ORM\Column(length: 30)]
    private ?string $memoryType = null;

    #[ORM\Column]
    private ?int $memorySlot = null;

    #[ORM\Column(length: 20)]
    private ?string $formFactor = null;

    /**
     * @var Collection<int, Build>
     */
    #[ORM\OneToMany(targetEntity: Build::class, mappedBy: 'Motherboard')]
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

    public function getSocket(): ?string
    {
        return $this->socket;
    }

    public function setSocket(string $socket): static
    {
        $this->socket = $socket;

        return $this;
    }

    public function getPcieSlot(): ?int
    {
        return $this->pcieSlot;
    }

    public function setPcieSlot(int $pcieSlot): static
    {
        $this->pcieSlot = $pcieSlot;

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

    public function getSlotM2(): ?int
    {
        return $this->slotM2;
    }

    public function setSlotM2(int $slotM2): static
    {
        $this->slotM2 = $slotM2;

        return $this;
    }

    public function getSataPort(): ?int
    {
        return $this->sataPort;
    }

    public function setSataPort(int $sataPort): static
    {
        $this->sataPort = $sataPort;

        return $this;
    }

    public function getMemoryMax(): ?int
    {
        return $this->memoryMax;
    }

    public function setMemoryMax(int $memoryMax): static
    {
        $this->memoryMax = $memoryMax;

        return $this;
    }

    public function getMemoryType(): ?string
    {
        return $this->memoryType;
    }

    public function setMemoryType(string $memoryType): static
    {
        $this->memoryType = $memoryType;

        return $this;
    }

    public function getMemorySlot(): ?int
    {
        return $this->memorySlot;
    }

    public function setMemorySlot(int $memorySlot): static
    {
        $this->memorySlot = $memorySlot;

        return $this;
    }

    public function getFormFactor(): ?string
    {
        return $this->formFactor;
    }

    public function setFormFactor(string $formFactor): static
    {
        $this->formFactor = $formFactor;

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
            $build->setMotherboard($this);
        }

        return $this;
    }

    public function removeBuild(Build $build): static
    {
        if ($this->builds->removeElement($build)) {
            // set the owning side to null (unless already changed)
            if ($build->getMotherboard() === $this) {
                $build->setMotherboard(null);
            }
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
