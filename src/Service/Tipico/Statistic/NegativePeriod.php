<?php

declare(strict_types=1);

namespace App\Service\Tipico\Statistic;

use DateTime;

/**
 * @author Wolfgang Hinzmann <wolfgang.hinzmann@doccheck.com>
 * @license 2024 DocCheck Community GmbH
 */
class NegativePeriod
{
    private int $days;

    private DateTime $startDate;
    private DateTime $endDate;
    private DateTime $cashBoxMinimumDate;
    private float $startAmount;
    private float $endAmount;
    private float $cashBoxMinimum;

    public function getDays(): int
    {
        return $this->days;
    }

    public function setDays(int $days): NegativePeriod
    {
        $this->days = $days;
        return $this;
    }

    public function getStartDate(): DateTime
    {
        return $this->startDate;
    }

    public function setStartDate(DateTime $startDate): NegativePeriod
    {
        $this->startDate = $startDate;
        return $this;
    }

    public function getEndDate(): DateTime
    {
        return $this->endDate;
    }

    public function setEndDate(DateTime $endDate): NegativePeriod
    {
        $this->endDate = $endDate;
        return $this;
    }

    public function getStartAmount(): float
    {
        return $this->startAmount;
    }

    public function setStartAmount(float $startAmount): NegativePeriod
    {
        $this->startAmount = $startAmount;
        return $this;
    }

    public function getEndAmount(): float
    {
        return $this->endAmount;
    }

    public function setEndAmount(float $endAmount): NegativePeriod
    {
        $this->endAmount = $endAmount;
        return $this;
    }

    public function getCashBoxMinimumDate(): DateTime
    {
        return $this->cashBoxMinimumDate;
    }

    public function setCashBoxMinimumDate(DateTime $cashBoxMinimumDate): NegativePeriod
    {
        $this->cashBoxMinimumDate = $cashBoxMinimumDate;
        return $this;
    }

    public function getCashBoxMinimum(): float
    {
        return $this->cashBoxMinimum;
    }

    public function setCashBoxMinimum(float $cashBoxMinimum): NegativePeriod
    {
        $this->cashBoxMinimum = $cashBoxMinimum;
        return $this;
    }

    public function getCalculatedMinimumAmount(): float
    {
        return ($this->getStartAmount() - $this->getCashBoxMinimum()) * -1;
    }
}
