<?php

namespace App\Entity;

use App\Service\Tipico\Content\Simulator\SimulatorRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SimulatorRepository::class)]
class Simulator
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?float $cashBox = null;

    #[ORM\ManyToMany(targetEntity: TipicoBet::class, inversedBy: 'simulators')]
    private Collection $fixtures;

    #[ORM\Column(length: 255)]
    private ?string $identifier = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?SimulationStrategy $strategy = null;

    #[ORM\OneToMany(mappedBy: 'simulator', targetEntity: TipicoPlacement::class)]
    private Collection $tipicoPlacements;

    #[ORM\Column]
    private ?float $currentIn = 0.0;

    public function __construct()
    {
        $this->fixtures = new ArrayCollection();
        $this->tipicoPlacements = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCashBox(): ?float
    {
        return $this->cashBox;
    }

    public function setCashBox(float $cashBox): static
    {
        $this->cashBox = $cashBox;

        return $this;
    }

    /**
     * @return Collection<int, TipicoBet>
     */
    public function getFixtures(): Collection
    {
        return $this->fixtures;
    }

    public function addFixture(TipicoBet $fixture): static
    {
        if (!$this->fixtures->contains($fixture)) {
            $this->fixtures->add($fixture);
        }

        return $this;
    }

    public function removeFixture(TipicoBet $fixture): static
    {
        $this->fixtures->removeElement($fixture);

        return $this;
    }

    public function getIdentifier(): ?string
    {
        return $this->identifier;
    }

    public function setIdentifier(string $identifier): static
    {
        $this->identifier = $identifier;

        return $this;
    }

    public function getStrategy(): ?SimulationStrategy
    {
        return $this->strategy;
    }

    public function setStrategy(SimulationStrategy $strategy): static
    {
        $this->strategy = $strategy;

        return $this;
    }

    /**
     * @return Collection<int, TipicoPlacement>
     */
    public function getTipicoPlacements(): Collection
    {
        return $this->tipicoPlacements;
    }

    public function addTipicoPlacement(TipicoPlacement $tipicoPlacement): static
    {
        if (!$this->tipicoPlacements->contains($tipicoPlacement)) {
            $this->tipicoPlacements->add($tipicoPlacement);
            $tipicoPlacement->setSimulator($this);
        }

        return $this;
    }

    public function removeTipicoPlacement(TipicoPlacement $tipicoPlacement): static
    {
        if ($this->tipicoPlacements->removeElement($tipicoPlacement)) {
            // set the owning side to null (unless already changed)
            if ($tipicoPlacement->getSimulator() === $this) {
                $tipicoPlacement->setSimulator(null);
            }
        }

        return $this;
    }

    public function getCurrentIn(): ?float
    {
        return $this->currentIn;
    }

    public function setCurrentIn(float $currentIn): static
    {
        $this->currentIn = $currentIn;

        return $this;
    }
}
