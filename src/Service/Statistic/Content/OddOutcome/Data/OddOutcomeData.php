<?php
declare(strict_types=1);

namespace App\Service\Statistic\Content\OddOutcome\Data;

use App\Entity\Spm\OddOutcome;
use App\Entity\Spm\SpmFixture;
use App\Service\Evaluation\BetOn;


class OddOutcomeData
{
    private float $min;
    private float $max;
    private int $fixtureAmount;
    private BetOn $betOn;
    private int $correctOutcomes;

    /**
     * @var SpmFixture[] $fixtures
     */
    private array $fixtures;

    public function getMin(): float
    {
        return $this->min;
    }

    public function setMin(float $min): OddOutcomeData
    {
        $this->min = $min;
        return $this;
    }

    public function getMax(): float
    {
        return $this->max;
    }

    public function setMax(float $max): OddOutcomeData
    {
        $this->max = $max;
        return $this;
    }

    public function getFixtureAmount(): int
    {
        return $this->fixtureAmount;
    }

    public function setFixtureAmount(int $fixtureAmount): OddOutcomeData
    {
        $this->fixtureAmount = $fixtureAmount;
        return $this;
    }

    public function getBetOn(): BetOn
    {
        return $this->betOn;
    }

    public function setBetOn(BetOn $betOn): OddOutcomeData
    {
        $this->betOn = $betOn;
        return $this;
    }

    public function getCorrectOutcomes(): int
    {
        return $this->correctOutcomes;
    }

    public function setCorrectOutcomes(int $correctOutcomes): OddOutcomeData
    {
        $this->correctOutcomes = $correctOutcomes;
        return $this;
    }

    public function getFixtures(): array
    {
        return $this->fixtures;
    }

    public function setFixtures(array $fixtures): OddOutcomeData
    {
        $this->fixtures = $fixtures;
        return $this;
    }

    public function initFromEntity(OddOutcome $oddOutcome): OddOutcomeData
    {
        $this->setMin($oddOutcome->getMin());
        $this->setBetOn($oddOutcome->getBetOn());
        $this->setMax($oddOutcome->getMax());
        $this->setCorrectOutcomes($oddOutcome->getCorrectOutcomes());
        $this->setFixtureAmount($oddOutcome->getFixtureAmount());

        return $this;
    }
}
