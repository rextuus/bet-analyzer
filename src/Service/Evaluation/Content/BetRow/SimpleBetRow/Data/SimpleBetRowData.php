<?php
declare(strict_types=1);

namespace App\Service\Evaluation\Content\BetRow\SimpleBetRow\Data;

use App\Entity\BetRowOddFilter;
use App\Service\Evaluation\Content\PlacedBet\BetRowVariant;
use App\Service\Evaluation\OddAccumulationVariant;
use App\Service\Sportmonks\Content\Odd\Data\OddFilter;

/**
 * @author Wolfgang Hinzmann <wolfgang.hinzmann@doccheck.com>
 * @license 2023 DocCheck Community GmbH
 */
class SimpleBetRowData
{
    private int $leagueApiId;
    private int $seasonApiId;
    private BetRowVariant $variant;
    private float $cashBox;
    private float $wager;
    private OddAccumulationVariant $oddAccumulationVariant;
    private bool $includeTaxes;

    /**
     * @var BetRowOddFilter[]
     */
    private array $filters;

    public function getLeagueApiId(): int
    {
        return $this->leagueApiId;
    }

    public function setLeagueApiId(int $leagueApiId): SimpleBetRowData
    {
        $this->leagueApiId = $leagueApiId;
        return $this;
    }

    public function getSeasonApiId(): int
    {
        return $this->seasonApiId;
    }

    public function setSeasonApiId(int $seasonApiId): SimpleBetRowData
    {
        $this->seasonApiId = $seasonApiId;
        return $this;
    }

    public function getVariant(): BetRowVariant
    {
        return $this->variant;
    }

    public function setVariant(BetRowVariant $variant): SimpleBetRowData
    {
        $this->variant = $variant;
        return $this;
    }

    public function getCashBox(): float
    {
        return $this->cashBox;
    }

    public function setCashBox(float $cashBox): SimpleBetRowData
    {
        $this->cashBox = $cashBox;
        return $this;
    }

    public function getWager(): float
    {
        return $this->wager;
    }

    public function setWager(float $wager): SimpleBetRowData
    {
        $this->wager = $wager;
        return $this;
    }

    public function getOddAccumulationVariant(): OddAccumulationVariant
    {
        return $this->oddAccumulationVariant;
    }

    public function setOddAccumulationVariant(OddAccumulationVariant $oddAccumulationVariant): SimpleBetRowData
    {
        $this->oddAccumulationVariant = $oddAccumulationVariant;
        return $this;
    }

    public function isIncludeTaxes(): bool
    {
        return $this->includeTaxes;
    }

    public function setIncludeTaxes(bool $includeTaxes): SimpleBetRowData
    {
        $this->includeTaxes = $includeTaxes;
        return $this;
    }

    public function getFilters(): array
    {
        return $this->filters;
    }

    public function setFilters(array $filters): SimpleBetRowData
    {
        $this->filters = $filters;
        return $this;
    }
}
