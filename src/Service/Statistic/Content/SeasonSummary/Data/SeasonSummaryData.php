<?php
declare(strict_types=1);

namespace App\Service\Statistic\Content\SeasonSummary\Data;

use App\Entity\SimpleBetRow;
use App\Entity\SpmSeason;

/**
 * @author Wolfgang Hinzmann <wolfgang.hinzmann@doccheck.com>
 * @license 2023 DocCheck Community GmbH
 */
class SeasonSummaryData
{
    private SpmSeason $season;
    private ?SimpleBetRow $highest;
    private array $missingHomeFilters;
    private array $missingDrawFilters;
    private array $missingAwayFilters;

    public function getSeason(): SpmSeason
    {
        return $this->season;
    }

    public function setSeason(SpmSeason $season): SeasonSummaryData
    {
        $this->season = $season;
        return $this;
    }

    public function getHighest(): ?SimpleBetRow
    {
        return $this->highest;
    }

    public function setHighest(?SimpleBetRow $highest): SeasonSummaryData
    {
        $this->highest = $highest;
        return $this;
    }

    public function getMissingHomeFilters(): array
    {
        return $this->missingHomeFilters;
    }

    public function setMissingHomeFilters(array $missingHomeFilters): SeasonSummaryData
    {
        $this->missingHomeFilters = $missingHomeFilters;
        return $this;
    }

    public function getMissingDrawFilters(): array
    {
        return $this->missingDrawFilters;
    }

    public function setMissingDrawFilters(array $missingDrawFilters): SeasonSummaryData
    {
        $this->missingDrawFilters = $missingDrawFilters;
        return $this;
    }

    public function getMissingAwayFilters(): array
    {
        return $this->missingAwayFilters;
    }

    public function setMissingAwayFilters(array $missingAwayFilters): SeasonSummaryData
    {
        $this->missingAwayFilters = $missingAwayFilters;
        return $this;
    }
}
