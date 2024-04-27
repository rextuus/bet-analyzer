<?php
declare(strict_types=1);

namespace App\Service\Statistic;

use App\Entity\Spm\BetRowSummary;


class SeasonBetRowDistribution
{
    private array $variants;

    public function __construct(array $variants = [])
    {
        $this->variants = $variants;
    }

    public function addVariant(string $variant, int $leagueApiId, BetRowSummary $betRow): void
    {
        if (!array_key_exists($variant, $this->variants)){
            $this->variants[$variant] = [];
            $this->variants[$variant][$leagueApiId] = $betRow;
        }else{
            $this->variants[$variant][$leagueApiId] = $betRow;
        }
    }

    /**
     * @return BetRowSummary[][]
     */
    public function getVariantsDescending(): array
    {
        $desc = array_map(
          function ($array){
              return count($array);
          },
            $this->variants
        );

        arsort($desc, SORT_DESC);
        $sorted = [];
        foreach ($desc as $variant => $amount){
            $sorted[$variant] = $this->variants[$variant];
        }
        return $sorted;
    }
}
