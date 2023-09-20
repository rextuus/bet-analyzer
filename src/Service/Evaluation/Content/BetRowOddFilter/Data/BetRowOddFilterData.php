<?php
declare(strict_types=1);

namespace App\Service\Evaluation\Content\BetRowOddFilter\Data;

use App\Service\Evaluation\BetOn;
use App\Service\Evaluation\Content\BetRow\BetRowInterface;
use App\Service\Evaluation\OddVariant;

/**
 * @author Wolfgang Hinzmann <wolfgang.hinzmann@doccheck.com>
 * @license 2023 DocCheck Community GmbH
 */
class BetRowOddFilterData
{
    private float $min;
    private float $max;
    private OddVariant $oddVariant;
    private BetOn $betOn;

    public function getMin(): float
    {
        return $this->min;
    }

    public function setMin(float $min): BetRowOddFilterData
    {
        $this->min = $min;
        return $this;
    }

    public function getMax(): float
    {
        return $this->max;
    }

    public function setMax(float $max): BetRowOddFilterData
    {
        $this->max = $max;
        return $this;
    }

    public function getOddVariant(): OddVariant
    {
        return $this->oddVariant;
    }

    public function setOddVariant(OddVariant $oddVariant): BetRowOddFilterData
    {
        $this->oddVariant = $oddVariant;
        return $this;
    }

    public function getBetOn(): BetOn
    {
        return $this->betOn;
    }

    public function setBetOn(BetOn $betOn): BetRowOddFilterData
    {
        $this->betOn = $betOn;
        return $this;
    }
}
