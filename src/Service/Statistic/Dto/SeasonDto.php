<?php
declare(strict_types=1);

namespace App\Service\Statistic\Dto;

/**
 * @author Wolfgang Hinzmann <wolfgang.hinzmann@doccheck.com>
 * @license 2023 DocCheck Community GmbH
 */
class SeasonDto
{
    private string $season;
    private float $highest;
    private string $highestFilter;
    private array $missingHomeFilters;
    private array $missingAwayFilters;
    private array $missingDrawFilters;

    public function getSeason(): string
    {
        return $this->season;
    }

    public function setSeason(string $season): SeasonDto
    {
        $this->season = $season;
        return $this;
    }

    public function getHighest(): float
    {
        return $this->highest;
    }

    public function setHighest(float $highest): SeasonDto
    {
        $this->highest = $highest;
        return $this;
    }

    public function getHighestFilter(): string
    {
        return $this->highestFilter;
    }

    public function setHighestFilter(string $highestFilter): SeasonDto
    {
        $this->highestFilter = $highestFilter;
        return $this;
    }

    public function getMissingHomeFilters(): array
    {
        return $this->missingHomeFilters;
    }

    public function setMissingHomeFilters(array $missingHomeFilters): SeasonDto
    {
        $this->missingHomeFilters = $missingHomeFilters;
        return $this;
    }

    public function getMissingAwayFilters(): array
    {
        return $this->missingAwayFilters;
    }

    public function setMissingAwayFilters(array $missingAwayFilters): SeasonDto
    {
        $this->missingAwayFilters = $missingAwayFilters;
        return $this;
    }

    public function getMissingDrawFilters(): array
    {
        return $this->missingDrawFilters;
    }

    public function setMissingDrawFilters(array $missingDrawFilters): SeasonDto
    {
        $this->missingDrawFilters = $missingDrawFilters;
        return $this;
    }
}
