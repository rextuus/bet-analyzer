<?php
declare(strict_types=1);

namespace App\Service\Statistic\Dto;

use App\Service\Evaluation\Content\PlacedBet\BetRowVariant;
use App\Service\Evaluation\OddVariant;
use App\Service\Sportmonks\Content\Odd\Data\OddFilter;

/**
 * @author Wolfgang Hinzmann <wolfgang.hinzmann@doccheck.com>
 * @license 2023 DocCheck Community GmbH
 */
class SeasonVariantDto
{
    private float $from;
    private float $to;
    private OddVariant $oddVariant;

    public function getFrom(): float
    {
        return $this->from;
    }

    public function setFrom(float $from): SeasonVariantDto
    {
        $this->from = $from;
        return $this;
    }

    public function getTo(): float
    {
        return $this->to;
    }

    public function setTo(float $to): SeasonVariantDto
    {
        $this->to = $to;
        return $this;
    }

    public function getOddVariant(): OddVariant
    {
        return $this->oddVariant;
    }

    public function setOddVariant(OddVariant $oddVariant): SeasonVariantDto
    {
        $this->oddVariant = $oddVariant;
        return $this;
    }
}
