<?php

namespace App\Entity;

use App\Repository\BuildRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BuildRepository::class)]
class Build
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\ManyToOne(inversedBy: 'builds')]
    private ?Motherboard $Motherboard = null;

    #[ORM\ManyToOne(inversedBy: 'builds')]
    private ?Cpu $Cpu = null;

    /**
     * @var Collection<int, Gpu>
     */
    #[ORM\ManyToMany(targetEntity: Gpu::class, inversedBy: 'builds')]
    private Collection $Gpu;

    /**
     * @var Collection<int, Ram>
     */
    #[ORM\ManyToMany(targetEntity: Ram::class, inversedBy: 'builds')]
    private Collection $Ram;

    #[ORM\ManyToOne(inversedBy: 'builds')]
    private ?Boitier $Boitier = null;

    #[ORM\ManyToOne(inversedBy: 'builds')]
    private ?Psu $Psu = null;

    #[ORM\ManyToOne(inversedBy: 'builds')]
    private ?Cooler $Cooler = null;

    /**
     * @var Collection<int, Storage>
     */
    #[ORM\ManyToMany(targetEntity: Storage::class, inversedBy: 'builds')]
    private Collection $Storage;

    /**
     * @var Collection<int, Fan>
     */
    #[ORM\ManyToMany(targetEntity: Fan::class, inversedBy: 'builds')]
    private Collection $Fan;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    private ?Image $Image = null;

    #[ORM\ManyToOne(inversedBy: 'builds')]
    private ?User $User = null;

    #[ORM\Column]
    private ?bool $isPreBuild = false;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(nullable: true)]
    private ?bool $isMonthBuild = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, nullable: true)]
    private ?string $totalPrice = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $name = null;

    public function __construct()
    {
        $this->Gpu = new ArrayCollection();
        $this->Ram = new ArrayCollection();
        $this->Storage = new ArrayCollection();
        $this->Fan = new ArrayCollection();
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getMotherboard(): ?Motherboard
    {
        return $this->Motherboard;
    }

    public function setMotherboard(?Motherboard $Motherboard): static
    {
        $this->Motherboard = $Motherboard;

        return $this;
    }

    public function getCpu(): ?Cpu
    {
        return $this->Cpu;
    }

    public function setCpu(?Cpu $Cpu): static
    {
        $this->Cpu = $Cpu;

        return $this;
    }

    /**
     * @return Collection<int, Gpu>
     */
    public function getGpu(): Collection
    {
        return $this->Gpu;
    }

    public function addGpu(Gpu $gpu): static
    {
        if (!$this->Gpu->contains($gpu)) {
            $this->Gpu->add($gpu);
        }

        return $this;
    }

    public function removeGpu(Gpu $gpu): static
    {
        $this->Gpu->removeElement($gpu);

        return $this;
    }

    /**
     * @return Collection<int, Ram>
     */
    public function getRam(): Collection
    {
        return $this->Ram;
    }

    public function addRam(Ram $ram): static
    {
        if (!$this->Ram->contains($ram)) {
            $this->Ram->add($ram);
        }

        return $this;
    }

    public function removeRam(Ram $ram): static
    {
        $this->Ram->removeElement($ram);

        return $this;
    }

    public function getBoitier(): ?Boitier
    {
        return $this->Boitier;
    }

    public function setBoitier(?Boitier $Boitier): static
    {
        $this->Boitier = $Boitier;

        return $this;
    }

    public function getPsu(): ?Psu
    {
        return $this->Psu;
    }

    public function setPsu(?Psu $Psu): static
    {
        $this->Psu = $Psu;

        return $this;
    }

    public function getCooler(): ?Cooler
    {
        return $this->Cooler;
    }

    public function setCooler(?Cooler $Cooler): static
    {
        $this->Cooler = $Cooler;

        return $this;
    }

    /**
     * @return Collection<int, Storage>
     */
    public function getStorage(): Collection
    {
        return $this->Storage;
    }

    public function addStorage(Storage $storage): static
    {
        if (!$this->Storage->contains($storage)) {
            $this->Storage->add($storage);
        }

        return $this;
    }

    public function removeStorage(Storage $storage): static
    {
        $this->Storage->removeElement($storage);

        return $this;
    }

    /**
     * @return Collection<int, Fan>
     */
    public function getFan(): Collection
    {
        return $this->Fan;
    }

    public function addFan(Fan $fan): static
    {
        if (!$this->Fan->contains($fan)) {
            $this->Fan->add($fan);
        }

        return $this;
    }

    public function removeFan(Fan $fan): static
    {
        $this->Fan->removeElement($fan);

        return $this;
    }

    public function getImage(): ?Image
    {
        return $this->Image;
    }

    public function setImage(?Image $Image): static
    {
        $this->Image = $Image;
        if ($Image) {
            $Image->setContext('build');
        }

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->User;
    }

    public function setUser(?User $User): static
    {
        $this->User = $User;

        return $this;
    }

    public function isPreBuild(): ?bool
    {
        return $this->isPreBuild;
    }

    public function setIsPreBuild(bool $isPreBuild): static
    {
        $this->isPreBuild = $isPreBuild;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(): static
    {
        $this->createdAt = new \DateTimeImmutable();

        return $this;
    }

    public function isMonthBuild(): ?bool
    {
        return $this->isMonthBuild;
    }

    public function setIsMonthBuild(?bool $isMonthBuild): static
    {
        $this->isMonthBuild = $isMonthBuild;

        return $this;
    }

    public function getTotalPrice(): ?string
    {
        return $this->totalPrice;
    }

    /**
     * Calcule le prix total du build en additionnant tous les composants
     * @return string Le prix total calculé au format décimal
     */
    public function calculateTotalPrice(): string
    {
        $totalPrice = '0';

        // Composants uniques (ManyToOne)
        if ($this->getCpu()) {
            $totalPrice = bcadd($totalPrice, $this->getCpu()->getPrix() ?? '0', 2);
        }
        if ($this->getMotherboard()) {
            $totalPrice = bcadd($totalPrice, $this->getMotherboard()->getPrix() ?? '0', 2);
        }
        if ($this->getPsu()) {
            $totalPrice = bcadd($totalPrice, $this->getPsu()->getPrix() ?? '0', 2);
        }
        if ($this->getCooler()) {
            $totalPrice = bcadd($totalPrice, $this->getCooler()->getPrix() ?? '0', 2);
        }
        if ($this->getBoitier()) {
            $totalPrice = bcadd($totalPrice, $this->getBoitier()->getPrix() ?? '0', 2);
        }

        // Collections (ManyToMany) - GPU
        if (!$this->getGpu()->isEmpty()) {
            foreach ($this->getGpu() as $gpu) {
                $totalPrice = bcadd($totalPrice, $gpu->getPrix() ?? '0', 2);
            }
        }

        // Collections (ManyToMany) - RAM
        if (!$this->getRam()->isEmpty()) {
            foreach ($this->getRam() as $ram) {
                $totalPrice = bcadd($totalPrice, $ram->getPrix() ?? '0', 2);
            }
        }

        // Collections (ManyToMany) - Storage
        if (!$this->getStorage()->isEmpty()) {
            foreach ($this->getStorage() as $storage) {
                $totalPrice = bcadd($totalPrice, $storage->getPrix() ?? '0', 2);
            }
        }

        // Collections (ManyToMany) - Fan
        if (!$this->getFan()->isEmpty()) {
            foreach ($this->getFan() as $fan) {
                $totalPrice = bcadd($totalPrice, $fan->getPrix() ?? '0', 2);
            }
        }

        return $totalPrice;
    }

    /**
     * Définit le prix total du build
     * Si aucun paramètre n'est fourni, calcule automatiquement le prix
     * @param string|null $totalPrice Le prix à définir, ou null pour calcul auto
     * @return static
     */
    public function setTotalPrice(?string $totalPrice = null): static
    {
        if ($totalPrice === null) {
            $this->totalPrice = $this->calculateTotalPrice();
        } else {
            $this->totalPrice = $totalPrice;
        }

        return $this;
    }

    /**
     * Hook Doctrine qui met à jour automatiquement le prix total avant la sauvegarde
     */
    #[ORM\PrePersist]
    #[ORM\PreUpdate]
    public function updateTotalPrice(): void
    {
        $this->totalPrice = $this->calculateTotalPrice();
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): static
    {
        $this->name = $name;

        return $this;
    }
}
