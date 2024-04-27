<?php
declare(strict_types=1);

namespace App\Form;

use App\Entity\Spm\SpmLeague;


class LeagueStatisticFilterData
{
    private SpmLeague $league;

    private float $min;


    public function getLeague(): SpmLeague
    {
        return $this->league;
    }

    public function setLeague(SpmLeague $league): LeagueStatisticFilterData
    {
        $this->league = $league;
        return $this;
    }

    public function getMin(): float
    {
        return $this->min;
    }

    public function setMin(float $min): LeagueStatisticFilterData
    {
        $this->min = $min;
        return $this;
    }
}
