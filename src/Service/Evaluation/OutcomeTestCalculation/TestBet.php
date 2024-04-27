<?php
declare(strict_types=1);

namespace App\Service\Evaluation\OutcomeTestCalculation;


class TestBet
{
    private bool $isWon;
    private float $value;

    public function isWon(): bool
    {
        return $this->isWon;
    }

    public function setIsWon(bool $isWon): TestBet
    {
        $this->isWon = $isWon;
        return $this;
    }

    public function getValue(): float
    {
        return $this->value;
    }

    public function setValue(float $value): TestBet
    {
        $this->value = $value;
        return $this;
    }
}
