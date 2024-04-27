<?php
declare(strict_types=1);

namespace App\Form;

use App\Entity\Spm\BetRowCombination;


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
