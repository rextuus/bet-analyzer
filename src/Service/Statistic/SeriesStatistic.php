<?php
declare(strict_types=1);

namespace App\Service\Statistic;


class SeriesStatistic
{
    private SeriesVariant $variant;
    private int $start;
    private int $end;
    private int $days;

    public function __construct(SeriesVariant $variant, int $start, int $end, int $days)
    {
        $this->variant = $variant;
        $this->start = $start;
        $this->end = $end;
        $this->days = $days;
    }

    public function getVariant(): SeriesVariant
    {
        return $this->variant;
    }

    public function setVariant(SeriesVariant $variant): SeriesStatistic
    {
        $this->variant = $variant;
        return $this;
    }

    public function getStart(): int
    {
        return $this->start;
    }

    public function setStart(int $start): SeriesStatistic
    {
        $this->start = $start;
        return $this;
    }

    public function getEnd(): int
    {
        return $this->end;
    }

    public function setEnd(int $end): SeriesStatistic
    {
        $this->end = $end;
        return $this;
    }

    public function getDays(): int
    {
        return $this->days;
    }

    public function setDays(int $days): SeriesStatistic
    {
        $this->days = $days;
        return $this;
    }
}
