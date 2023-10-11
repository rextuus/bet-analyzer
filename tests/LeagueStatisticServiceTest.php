<?php
declare(strict_types=1);

use App\Entity\BetRowSummary;
use App\Service\Statistic\LeagueStatisticService;
use App\Service\Statistic\SeasonBetRowMap;
use PHPUnit\Framework\TestCase;

/**
 * @author Wolfgang Hinzmann <wolfgang.hinzmann@doccheck.com>
 * @license 2023 DocCheck Community GmbH
 */
class LeagueStatisticServiceTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->service = new LeagueStatisticService();
    }

    public function testCalculateDistribution(): void
    {
        $mapKey1 = 1;
        $mapKey2 = 2;
        $mapKey3 = 3;
        $mapKey4 = 4;
        $keyOverAll1 = "keyOverAll1";
        $keyOverAll2 = "keyOverAll2";
        $keyOnly1And3 = "keyOnly1And3";
        $keyOnly2And4 = "keyOnly2And4";
        $map = [
            $mapKey1 => $this->getSeasonArray([$keyOverAll1, $keyOnly1And3, "a", $keyOverAll2, "b"]),
            $mapKey2 => $this->getSeasonArray([$keyOverAll1, $keyOnly2And4, $keyOverAll2]),
            $mapKey3 => $this->getSeasonArray([$keyOnly1And3, $keyOverAll2, "c", $keyOverAll1]),
            $mapKey4 => $this->getSeasonArray([$keyOverAll2, "e", $keyOnly2And4, "f", $keyOverAll1]),
        ];

        $map2 = new SeasonBetRowMap($map);


        $result = $this->service->calculateDistribution($map2);
        dd($result);
        $this->assertEquals([$keyOverAll1, $keyOverAll2], array_keys($result));

    }

    private function getSeasonArray(array $keys): array
    {
        $array = [];
        foreach ($keys as $key) {
            $array[$key] = (new BetRowSummary());
        }

        return $array;
    }
}
