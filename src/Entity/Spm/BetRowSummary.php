<?php

namespace App\Entity\Spm;

use App\Service\Statistic\Content\BetRowSummary\BetRowSummaryRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BetRowSummaryRepository::class)]
class BetRowSummary
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?SimpleBetRow $betRow = null;

    #[ORM\Column]
    private ?float $cashBox = null;

    #[ORM\Column]
    private ?float $highest = null;

    #[ORM\Column]
    private ?float $lowest = null;

    #[ORM\Column]
    private ?int $madeBets = null;

    #[ORM\Column]
    private ?float $dailyReproductionChance = null;

    #[ORM\Column]
    private ?float $positiveDays = 0.0;

    #[ORM\Column(type: Types::JSON)]
    private array $daysMadeBets = [];

    #[ORM\Column(type: Types::JSON)]
    private array $daysOutcomes = [];

    #[ORM\Column(type: Types::JSON)]
    private array $seriesStatistics = [];

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

    public function getHighest(): ?float
    {
        return $this->highest;
    }

    public function setHighest(float $highest): static
    {
        $this->highest = $highest;

        return $this;
    }

    public function getLowest(): ?float
    {
        return $this->lowest;
    }

    public function setLowest(float $lowest): static
    {
        $this->lowest = $lowest;

        return $this;
    }

    public function getMadeBets(): ?int
    {
        return $this->madeBets;
    }

    public function setMadeBets(int $madeBets): static
    {
        $this->madeBets = $madeBets;

        return $this;
    }

    public function getDailyReproductionChance(): ?float
    {
        return $this->dailyReproductionChance;
    }

    public function setDailyReproductionChance(float $dailyReproductionChance): static
    {
        $this->dailyReproductionChance = $dailyReproductionChance;

        return $this;
    }

    public function getDaysMadeBets(): array
    {
        return $this->daysMadeBets;
    }

    public function setDaysMadeBets(array $daysMadeBets): static
    {
        $this->daysMadeBets = $daysMadeBets;

        return $this;
    }

    public function getDaysOutcomes(): array
    {
        return $this->daysOutcomes;
    }

    public function setDaysOutcomes(array $daysOutcomes): static
    {
        $this->daysOutcomes = $daysOutcomes;

        return $this;
    }

    public function getSeriesStatistics(): array
    {
        return $this->seriesStatistics;
    }

    public function setSeriesStatistics(array $seriesStatistics): static
    {
        $this->seriesStatistics = $seriesStatistics;

        return $this;
    }

    public function getBetRow(): ?SimpleBetRow
    {
        return $this->betRow;
    }

    public function setBetRow(SimpleBetRow $betRow): static
    {
        $this->betRow = $betRow;

        return $this;
    }

    public function getPositiveDays(): ?float
    {
        return $this->positiveDays;
    }

    public function setPositiveDays(float $positiveDays): static
    {
        $this->positiveDays = $positiveDays;

        return $this;
    }

    public function getDisplayName(): string
    {
        return sprintf('%s', (string) $this->getBetRow()->getBetRowFilters()->toArray()[0]);
    }

}
