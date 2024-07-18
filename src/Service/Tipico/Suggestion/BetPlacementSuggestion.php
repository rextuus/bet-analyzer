<?php

declare(strict_types=1);

namespace App\Service\Tipico\Suggestion;

use App\Entity\BettingProvider\TipicoBet;
use App\Service\Tipico\Simulation\AdditionalProcessors\Weekday;

/**
 * @author Wolfgang Hinzmann <wolfgang.hinzmann@doccheck.com>
 * @license 2024 DocCheck Community GmbH
 */
class BetPlacementSuggestion
{
    private TipicoBet $suggestedBet;

    /**
     * @var array<PlacementSuggestion>
     */
    private array $placementSuggestions;

    private Weekday $weekday;

    public function getSuggestedBet(): TipicoBet
    {
        return $this->suggestedBet;
    }

    public function setSuggestedBet(TipicoBet $suggestedBet): BetPlacementSuggestion
    {
        $this->suggestedBet = $suggestedBet;
        return $this;
    }

    public function getPlacementSuggestions(): array
    {
        return $this->placementSuggestions;
    }

    public function setPlacementSuggestions(array $placementSuggestions): BetPlacementSuggestion
    {
        $this->placementSuggestions = $placementSuggestions;
        return $this;
    }

    public function addPlacementSuggestions(PlacementSuggestion $placementSuggestion): BetPlacementSuggestion
    {
        $this->placementSuggestions[] = $placementSuggestion;
        return $this;
    }

    public function getWeekday(): Weekday
    {
        return $this->weekday;
    }

    public function setWeekday(Weekday $weekday): BetPlacementSuggestion
    {
        $this->weekday = $weekday;
        return $this;
    }
}
