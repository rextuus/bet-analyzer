<?php
declare(strict_types=1);

namespace App\Service\Evaluation\Content\PlacedBet\Data;

use App\Service\Evaluation\Content\BetRow\BetRowInterface;
use App\Service\Evaluation\Content\PlacedBet\BetRowVariant;

/**
 * @author Wolfgang Hinzmann <wolfgang.hinzmann@doccheck.com>
 * @license 2023 DocCheck Community GmbH
 */
class PlacedBetData
{
    private int $fixtureApiId;
    private array $oddApiIds;
    private BetRowInterface $betRow;
    private float $wager;
    private float $odd;
    private BetRowVariant $variant;
    private bool $won;
    private float $output;
    private int $matchDay;

    public function getFixtureApiId(): int
    {
        return $this->fixtureApiId;
    }

    public function setFixtureApiId(int $fixtureApiId): PlacedBetData
    {
        $this->fixtureApiId = $fixtureApiId;
        return $this;
    }

    public function getOddApiIds(): array
    {
        return $this->oddApiIds;
    }

    public function setOddApiIds(array $oddApiIds): PlacedBetData
    {
        $this->oddApiIds = $oddApiIds;
        return $this;
    }

    public function getBetRow(): BetRowInterface
    {
        return $this->betRow;
    }

    public function setBetRow(BetRowInterface $betRow): PlacedBetData
    {
        $this->betRow = $betRow;
        return $this;
    }

    public function getWager(): float
    {
        return $this->wager;
    }

    public function setWager(float $wager): PlacedBetData
    {
        $this->wager = $wager;
        return $this;
    }

    public function getOdd(): float
    {
        return $this->odd;
    }

    public function setOdd(float $odd): PlacedBetData
    {
        $this->odd = $odd;
        return $this;
    }

    public function getVariant(): BetRowVariant
    {
        return $this->variant;
    }

    public function setVariant(BetRowVariant $variant): PlacedBetData
    {
        $this->variant = $variant;
        return $this;
    }

    public function isWon(): bool
    {
        return $this->won;
    }

    public function setWon(bool $won): PlacedBetData
    {
        $this->won = $won;
        return $this;
    }

    public function getOutput(): float
    {
        return $this->output;
    }

    public function setOutput(float $output): PlacedBetData
    {
        $this->output = $output;
        return $this;
    }

    public function getMatchDay(): int
    {
        return $this->matchDay;
    }

    public function setMatchDay(int $matchDay): PlacedBetData
    {
        $this->matchDay = $matchDay;
        return $this;
    }
}
