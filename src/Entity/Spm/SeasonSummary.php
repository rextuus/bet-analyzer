<?php

namespace App\Entity\Spm;

use App\Service\Statistic\Content\SeasonSummary\SeasonSummaryRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SeasonSummaryRepository::class)]
class SeasonSummary
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?SpmSeason $season = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?SimpleBetRow $highest = null;

    #[ORM\Column(type: Types::JSON)]
    private array $missingHomeFilters = [];

    #[ORM\Column(type: Types::JSON)]
    private array $missingDrawFilters = [];

    #[ORM\Column(type: Types::JSON)]
    private array $missingAwayFilters = [];

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMissingHomeFilters(): array
    {
        return $this->missingHomeFilters;
    }

    public function setMissingHomeFilters(array $missingHomeFilters): static
    {
        $this->missingHomeFilters = $missingHomeFilters;

        return $this;
    }

    public function getMissingDrawFilters(): array
    {
        return $this->missingDrawFilters;
    }

    public function setMissingDrawFilters(array $missingDrawFilters): static
    {
        $this->missingDrawFilters = $missingDrawFilters;

        return $this;
    }

    public function getMissingAwayFilters(): array
    {
        return $this->missingAwayFilters;
    }

    public function setMissingAwayFilters(array $missingAwayFilters): static
    {
        $this->missingAwayFilters = $missingAwayFilters;

        return $this;
    }

    public function getSeason(): ?SpmSeason
    {
        return $this->season;
    }

    public function setSeason(SpmSeason $season): static
    {
        $this->season = $season;

        return $this;
    }

    public function getHighest(): ?SimpleBetRow
    {
        return $this->highest;
    }

    public function setHighest(SimpleBetRow $highest): static
    {
        $this->highest = $highest;

        return $this;
    }


}
