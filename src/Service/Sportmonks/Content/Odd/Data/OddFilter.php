<?php
declare(strict_types=1);

namespace App\Service\Sportmonks\Content\Odd\Data;

use App\Service\Evaluation\BetOn;
use App\Service\Evaluation\OddVariant;

/**
 * @author Wolfgang Hinzmann <wolfgang.hinzmann@doccheck.com>
 * @license 2023 DocCheck Community GmbH
 */
class OddFilter
{
    private OddVariant $variant;
    private BetOn $betOn;
    private float $minOdd;
    private float $maxOdd;

    public function getVariant(): OddVariant
    {
        return $this->variant;
    }

    public function setVariant(OddVariant $variant): OddFilter
    {
        $this->variant = $variant;
        return $this;
    }

    public function getBetOn(): BetOn
    {
        return $this->betOn;
    }

    public function setBetOn(BetOn $betOn): OddFilter
    {
        $this->betOn = $betOn;
        return $this;
    }

    public function getMinOdd(): float
    {
        return $this->minOdd;
    }

    public function setMinOdd(float $minOdd): OddFilter
    {
        $this->minOdd = $minOdd;
        return $this;
    }

    public function getMaxOdd(): float
    {
        return $this->maxOdd;
    }

    public function setMaxOdd(float $maxOdd): OddFilter
    {
        $this->maxOdd = $maxOdd;
        return $this;
    }
}
