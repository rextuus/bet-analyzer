<?php
declare(strict_types=1);

namespace App\Form;

use App\Entity\BetRowCombination;

/**
 * @author Wolfgang Hinzmann <wolfgang.hinzmann@doccheck.com>
 * @license 2023 DocCheck Community GmbH
 */
class BetRowCombinationChoiceData
{
    private BetRowCombination $combination;

    public function getCombination(): BetRowCombination
    {
        return $this->combination;
    }

    public function setCombination(BetRowCombination $combination): BetRowCombinationChoiceData
    {
        $this->combination = $combination;
        return $this;
    }
}
