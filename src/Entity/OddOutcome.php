<?php

namespace App\Entity;

use App\Service\Evaluation\BetOn;
use App\Service\Statistic\Content\OddOutcome\OddOutcomeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OddOutcomeRepository::class)]
class OddOutcome
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?float $min = null;

    #[ORM\Column]
    private ?float $max = null;

    #[ORM\Column]
    private ?int $fixtureAmount = null;

    #[ORM\Column(type: "string", enumType: BetOn::class)]
    private BetOn $betOn;

    #[ORM\Column]
    private ?int $correctOutcomes = null;

    #[ORM\ManyToMany(targetEntity: SpmFixture::class, inversedBy: 'oddOutcomes')]
    private Collection $fixtures;

    public function __construct()
    {
        $this->fixtures = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMin(): ?float
    {
        return $this->min;
    }

    public function setMin(float $min): static
    {
        $this->min = $min;

        return $this;
    }

    public function getMax(): ?float
    {
        return $this->max;
    }

    public function setMax(float $max): static
    {
        $this->max = $max;

        return $this;
    }

    public function getFixtureAmount(): ?int
    {
        return $this->fixtureAmount;
    }

    public function setFixtureAmount(int $fixtureAmount): static
    {
        $this->fixtureAmount = $fixtureAmount;

        return $this;
    }

    public function getBetOn(): BetOn
    {
        return $this->betOn;
    }

    public function setBetOn(BetOn $betOn): OddOutcome
    {
        $this->betOn = $betOn;
        return $this;
    }

    public function getCorrectOutcomes(): ?int
    {
        return $this->correctOutcomes;
    }

    public function setCorrectOutcomes(int $correctOutcomes): static
    {
        $this->correctOutcomes = $correctOutcomes;

        return $this;
    }

    /**
     * @return Collection<int, SpmFixture>
     */
    public function getFixtures(): Collection
    {
        return $this->fixtures;
    }

    public function addFixture(SpmFixture $fixture): static
    {
        if (!$this->fixtures->contains($fixture)) {
            $this->fixtures->add($fixture);
        }

        return $this;
    }

    public function removeFixture(SpmFixture $fixture): static
    {
        $this->fixtures->removeElement($fixture);

        return $this;
    }
}
