<?php

namespace App\Service\Evaluation\Content\BetRow;

use App\Entity\BetRowOddFilter;

interface BetRowInterface
{
    public function addBetRowOddFilter(BetRowOddFilter $betRowOddFilter): BetRowInterface;
    public function getId(): ?int;
}