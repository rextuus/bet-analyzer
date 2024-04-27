<?php

namespace App\Entity\BettingProvider;

use App\Service\Tipico\Content\Placement\TipicoPlacementRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TipicoPlacementRepository::class)]
class TipicoPlacement
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToMany(targetEntity: TipicoBet::class, inversedBy: 'tipicoPlacements')]
    private Collection $fixtures;

    #[ORM\Column]
    private ?float $value = null;

    #[ORM\ManyToOne(inversedBy: 'tipicoPlacements')]
    private ?Simulator $simulator = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $created = null;

    #[ORM\Column]
    private ?bool $won = null;

    #[ORM\Column]
    private ?float $input = null;

    public function __construct()
    {
        $this->fixtures = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getValue(): ?float
    {
        return $this->value;
    }

    public function setValue(float $value): static
    {
        $this->value = $value;

        return $this;
    }

    public function getSimulator(): ?Simulator
    {
        return $this->simulator;
    }

    public function setSimulator(?Simulator $simulator): static
    {
        $this->simulator = $simulator;

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

    public function isWon(): ?bool
    {
        return $this->won;
    }

    public function setWon(bool $won): static
    {
        $this->won = $won;

        return $this;
    }

    public function getInput(): ?float
    {
        return $this->input;
    }

    public function setInput(float $input): static
    {
        $this->input = $input;

        return $this;
    }
}
