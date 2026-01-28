<?php

namespace App\Entity;

use App\Repository\PsuRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PsuRepository::class)]
class Psu
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
    private ?int $wattage = null;

    #[ORM\Column]
    private ?int $pcieConnector = null;

    #[ORM\Column]
    private ?int $epsConnector = null;

    #[ORM\Column]
    private ?int $sataConnector = null;

    #[ORM\Column(length: 30)]
    private ?string $modularite = null;

    /**
     * @var Collection<int, Build>
     */
    #[ORM\OneToMany(targetEntity: Build::class, mappedBy: 'Psu')]
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

    public function getWattage(): ?int
    {
        return $this->wattage;
    }

    public function setWattage(int $wattage): static
    {
        $this->wattage = $wattage;

        return $this;
    }

    public function getPcieConnector(): ?int
    {
        return $this->pcieConnector;
    }

    public function setPcieConnector(int $pcieConnector): static
    {
        $this->pcieConnector = $pcieConnector;

        return $this;
    }

    public function getEpsConnector(): ?int
    {
        return $this->epsConnector;
    }

    public function setEpsConnector(int $epsConnector): static
    {
        $this->epsConnector = $epsConnector;

        return $this;
    }

    public function getSataConnector(): ?int
    {
        return $this->sataConnector;
    }

    public function setSataConnector(int $sataConnector): static
    {
        $this->sataConnector = $sataConnector;

        return $this;
    }

    public function getModularite(): ?string
    {
        return $this->modularite;
    }

    public function setModularite(string $modularite): static
    {
        $this->modularite = $modularite;

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
            $build->setPsu($this);
        }

        return $this;
    }

    public function removeBuild(Build $build): static
    {
        if ($this->builds->removeElement($build)) {
            // set the owning side to null (unless already changed)
            if ($build->getPsu() === $this) {
                $build->setPsu(null);
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
