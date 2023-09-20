<?php
declare(strict_types=1);

namespace App\Service\Evaluation;

use App\Entity\BetRowOddFilter;
use App\Entity\SpmSeason;
use App\Service\Evaluation\Content\BetRow\BetRowInterface;
use App\Service\Evaluation\Content\PlacedBet\BetRowVariant;
use App\Service\Sportmonks\Content\Odd\Data\OddFilter;

/**
 * @author Wolfgang Hinzmann <wolfgang.hinzmann@doccheck.com>
 * @license 2023 DocCheck Community GmbH
 */
class ClassicBetRowCalculatorInitData
{
    private BetRowInterface $betRow;
    private BetRowVariant $variant;
    private SpmSeason $season;
    /**
     * @var BetRowOddFilter[]
     */
    private array $oddFilter;
    private bool $includeTax;

    /**
     * @var OddVariant[]
     */
    private array $oddVariants;

    private OddAccumulationVariant $accumulationVariant;

    private float $wager;

    public function getBetRow(): BetRowInterface
    {
        return $this->betRow;
    }

    public function setBetRow(BetRowInterface $betRow): ClassicBetRowCalculatorInitData
    {
        $this->betRow = $betRow;
        return $this;
    }

    public function getVariant(): BetRowVariant
    {
        return $this->variant;
    }

    public function setVariant(BetRowVariant $variant): ClassicBetRowCalculatorInitData
    {
        $this->variant = $variant;
        return $this;
    }

    public function getSeason(): SpmSeason
    {
        return $this->season;
    }

    public function setSeason(SpmSeason $season): ClassicBetRowCalculatorInitData
    {
        $this->season = $season;
        return $this;
    }

    public function getOddFilter(): array
    {
        return $this->oddFilter;
    }

    public function setOddFilter(array $oddFilter): ClassicBetRowCalculatorInitData
    {
        $this->oddFilter = $oddFilter;
        return $this;
    }

    public function isIncludeTax(): bool
    {
        return $this->includeTax;
    }

    public function setIncludeTax(bool $includeTax): ClassicBetRowCalculatorInitData
    {
        $this->includeTax = $includeTax;
        return $this;
    }

    public function getOddVariants(): array
    {
        return $this->oddVariants;
    }

    public function setOddVariants(array $oddVariants): ClassicBetRowCalculatorInitData
    {
        $this->oddVariants = $oddVariants;
        return $this;
    }

    public function getAccumulationVariant(): OddAccumulationVariant
    {
        return $this->accumulationVariant;
    }

    public function setAccumulationVariant(OddAccumulationVariant $accumulationVariant): ClassicBetRowCalculatorInitData
    {
        $this->accumulationVariant = $accumulationVariant;
        return $this;
    }

    public function getWager(): float
    {
        return $this->wager;
    }

    public function setWager(float $wager): ClassicBetRowCalculatorInitData
    {
        $this->wager = $wager;
        return $this;
    }
}
