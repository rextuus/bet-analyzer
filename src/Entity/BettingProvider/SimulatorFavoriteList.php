<?php

namespace App\Entity\BettingProvider;

use App\Service\Tipico\Content\SimulatorFavoriteList\SimulatorFavoriteListRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SimulatorFavoriteListRepository::class)]
class SimulatorFavoriteList
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $identifier = null;

    #[ORM\ManyToMany(targetEntity: Simulator::class, inversedBy: 'simulatorFavoriteLists')]
    private Collection $simulators;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $created = null;

    #[ORM\Column]
    private ?float $totalCashBox = null;

    #[ORM\Column]
    private ?int $bets = null;

    public function __construct()
    {
        $this->simulators = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    /**
     * @return Collection<int, Simulator>
     */
    public function getSimulators(): Collection
    {
        return $this->simulators;
    }

    public function addSimulator(Simulator $simulator): static
    {
        if (!$this->simulators->contains($simulator)) {
            $this->simulators->add($simulator);
        }

        return $this;
    }

    public function removeSimulator(Simulator $simulator): static
    {
        $this->simulators->removeElement($simulator);

        return $this;
    }

    public function getCreated(): ?\DateTimeInterface
    {
        return $this->created;
    }

    public function setCreated(\DateTimeInterface $created): static
    {
        $this->created = $created;

        return $this;
    }

    public function getTotalCashBox(): ?float
    {
        return $this->totalCashBox;
    }

    public function setTotalCashBox(float $totalCashBox): static
    {
        $this->totalCashBox = $totalCashBox;

        return $this;
    }

    public function getBets(): ?int
    {
        return $this->bets;
    }

    public function setBets(int $bets): static
    {
        $this->bets = $bets;

        return $this;
    }
}
