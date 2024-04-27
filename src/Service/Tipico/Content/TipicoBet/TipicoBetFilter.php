<?php
declare(strict_types=1);

namespace App\Service\Tipico\Content\TipicoBet;


class TipicoBetFilter
{
    public const TABLE_ALIAS_TIPICO_BET = 'f';
    public const TABLE_ALIAS_TIPICO_ODD_BOTH_SCORE = 'bts';
    public const TABLE_ALIAS_TIPICO_ODD_OVER_UNDER = 'ou';
    public const TABLE_ALIAS_TIPICO_HEAD_TO_HEAD = 'hth';

    private bool $includeBothTeamsScore = false;
    private bool $includeOverUnder = false;
    private bool $includeHeadToHead = false;

    private float $min;
    private float $max;

    private string $searchTableAlias = self::TABLE_ALIAS_TIPICO_BET;
    private string $searchOddColumn = 'homeOdd';

    private array $alreadyUsedFixtureIds = [-1];

    private int $limit = 200;
    private string $order = 'ASC';
    private bool $countRequest = false;
    private float $targetValue;

    private bool $hasTimeFrame = false;
    public function isIncludeBothTeamsScore(): bool
    {
        return $this->includeBothTeamsScore;
    }

    public function setIncludeBothTeamsScore(bool $includeBothTeamsScore): TipicoBetFilter
    {
        $this->includeBothTeamsScore = $includeBothTeamsScore;
        return $this;
    }

    public function isIncludeOverUnder(): bool
    {
        return $this->includeOverUnder;
    }

    public function setIncludeOverUnder(bool $includeOverUnder): TipicoBetFilter
    {
        $this->includeOverUnder = $includeOverUnder;
        return $this;
    }

    public function isIncludeHeadToHead(): bool
    {
        return $this->includeHeadToHead;
    }

    public function setIncludeHeadToHead(bool $includeHeadToHead): TipicoBetFilter
    {
        $this->includeHeadToHead = $includeHeadToHead;
        return $this;
    }

    public function getMin(): float
    {
        return $this->min;
    }

    public function setMin(float $min): TipicoBetFilter
    {
        $this->min = $min;
        return $this;
    }

    public function getMax(): float
    {
        return $this->max;
    }

    public function setMax(float $max): TipicoBetFilter
    {
        $this->max = $max;
        return $this;
    }

    public function getSearchTableAlias(): string
    {
        return $this->searchTableAlias;
    }

    public function setSearchTableAlias(string $searchTableAlias): TipicoBetFilter
    {
        $this->searchTableAlias = $searchTableAlias;
        return $this;
    }

    public function getSearchOddColumn(): string
    {
        return $this->searchOddColumn;
    }

    public function setSearchOddColumn(string $searchOddColumn): TipicoBetFilter
    {
        $this->searchOddColumn = $searchOddColumn;
        return $this;
    }

    public function getAlreadyUsedFixtureIds(): array
    {
        return $this->alreadyUsedFixtureIds;
    }

    public function setAlreadyUsedFixtureIds(array $alreadyUsedFixtureIds): TipicoBetFilter
    {
        $this->alreadyUsedFixtureIds = $alreadyUsedFixtureIds;
        return $this;
    }

    public function getLimit(): int
    {
        return $this->limit;
    }

    public function setLimit(int $limit): TipicoBetFilter
    {
        $this->limit = $limit;
        return $this;
    }

    public function getOrder(): string
    {
        return $this->order;
    }

    public function setOrder(string $order): TipicoBetFilter
    {
        $this->order = $order;
        return $this;
    }

    public function isCountRequest(): bool
    {
        return $this->countRequest;
    }

    public function setCountRequest(bool $countRequest): TipicoBetFilter
    {
        $this->countRequest = $countRequest;
        return $this;
    }

    public function getTargetValue(): float
    {
        return $this->targetValue;
    }

    public function setTargetValue(float $targetValue): TipicoBetFilter
    {
        $this->targetValue = $targetValue;
        return $this;
    }

    public function isHasTimeFrame(): bool
    {
        return $this->hasTimeFrame;
    }

    public function setHasTimeFrame(bool $hasTimeFrame): TipicoBetFilter
    {
        $this->hasTimeFrame = $hasTimeFrame;
        return $this;
    }
}
