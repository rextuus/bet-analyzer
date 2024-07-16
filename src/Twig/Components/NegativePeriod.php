<?php

namespace App\Twig\Components;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent]
final class NegativePeriod
{
    public \App\Service\Tipico\Statistic\NegativePeriod $negativeSeries;

    public string $variant;

    public function getHeader(): string
    {
        if ($this->variant === 'highest') {
            return '<i class="fa-solid fa-mountain "></i> Höchster Verlust';
        }
        if ($this->variant === 'longest') {
            return '<i class="fa-solid fa-clock"></i> Längste Zeit mit Minus-Kontostand';
        }

        return 'Header';
    }

    public function getDays(): int
    {
        return $this->negativeSeries->getDays();
    }

    public function getStart(): string
    {
        return $this->negativeSeries->getStartDate()->format('Y-m-d');
    }

    public function getEnd(): string
    {
        return $this->negativeSeries->getEndDate()->format('Y-m-d');
    }

    public function getSum(): float
    {
        return round(($this->negativeSeries->getStartAmount() - $this->negativeSeries->getCashBoxMinimum()) * -1, 2);
    }

    public function getMinimumDate(): string
    {
        return $this->negativeSeries->getCashBoxMinimumDate()->format('Y-m-d');
    }
}
