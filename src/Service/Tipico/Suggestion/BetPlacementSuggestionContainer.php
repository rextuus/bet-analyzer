<?php

declare(strict_types=1);

namespace App\Service\Tipico\Suggestion;

use App\Entity\BettingProvider\TipicoBet;

/**
 * @author Wolfgang Hinzmann <wolfgang.hinzmann@doccheck.com>
 * @license 2024 DocCheck Community GmbH
 */
class BetPlacementSuggestionContainer
{
    /**
     * @var array<int, BetPlacementSuggestion>
     */
    private array $betPlacementSuggestions;

    public function __construct()
    {
        $this->betPlacementSuggestions = [];
    }


    public function getBetPlacementSuggestions(): array
    {
        return $this->betPlacementSuggestions;
    }

    public function setBetPlacementSuggestions(array $betPlacementSuggestions): BetPlacementSuggestionContainer
    {
        $this->betPlacementSuggestions = $betPlacementSuggestions;
        return $this;
    }

    public function containsBet(TipicoBet $tipicoBet): bool
    {
        return array_key_exists($tipicoBet->getId(), $this->getBetPlacementSuggestions());
    }

    public function getSuggestionByBet(TipicoBet $tipicoBet): BetPlacementSuggestion
    {
        if (!array_key_exists($tipicoBet->getId(), $this->betPlacementSuggestions)) {
            $suggestion = new BetPlacementSuggestion();
            $suggestion->setSuggestedBet($tipicoBet);

            $this->betPlacementSuggestions[$tipicoBet->getId()] = $suggestion;

            return $suggestion;
        }

        return $this->betPlacementSuggestions[$tipicoBet->getId()];
    }
}
