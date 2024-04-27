<?php
declare(strict_types=1);

namespace App\Twig\Data;


class UpcomingFixtureOdd
{
    private float $oddValue;
    private string $cssClass;

    public function getOddValue(): string
    {
        if ($this->oddValue === 0.0){
            return '-';
        }

        return sprintf('%.2f', $this->oddValue);
    }

    public function setOddValue(float $oddValue): UpcomingFixtureOdd
    {
        $this->oddValue = $oddValue;
        return $this;
    }

    public function getCssClass(): string
    {
        return $this->cssClass;
    }

    public function setCssClass(string $cssClass): UpcomingFixtureOdd
    {
        $this->cssClass = $cssClass;
        return $this;
    }
}
