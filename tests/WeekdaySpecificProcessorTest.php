<?php

declare(strict_types=1);

use App\Service\Tipico\Content\Placement\Data\TipicoPlacementData;
use App\Service\Tipico\Simulation\AdditionalProcessors\Weekday;
use App\Service\Tipico\Simulation\AdditionalProcessors\WeekdaySpecificProcessor;
use App\Service\Tipico\Simulation\Data\AdditionalProcessResult;
use App\Service\Tipico\SimulationProcessors\AbstractSimulationProcessor;
use PHPUnit\Framework\TestCase;


class WeekdaySpecificProcessorTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->service = new WeekdaySpecificProcessor();
    }

    public function testNegativeSeriesStartingWithCurrentSeriesIsZero(): void
    {
        $placements = [
            $this->getDataDummy(1, true, 'Monday'),
            $this->getDataDummy(2, true, 'Tuesday'),
            $this->getDataDummy(3, false, 'Wednesday'),
            $this->getDataDummy(4, false, 'Thursday'),
            $this->getDataDummy(5, false, 'Friday'),
            $this->getDataDummy(6, true, 'Saturday'),
            $this->getDataDummy(7, true, 'Sunday'),
            $this->getDataDummy(8, true, 'Friday'),
        ];

        $result = new AdditionalProcessResult();
        $result->setPlacementData($placements);
        $result = $this->service->process(
            $result,
            [
                AbstractSimulationProcessor::PARAMETER_ALLOWED_WEEKDAYS => [Weekday::Friday->value],
            ]
        );

        $placements = $result->getPlacementData();

        $this->assertCount(2, $placements);

        $expectedValues = [5, 8];
        foreach ($placements as $index => $placement) {
            $this->assertEquals((float)$expectedValues[$index], $placement->getValue());
        }
        $this->assertEquals(0, $result->getCurrentNegativeSeries());
    }

    /**
     * @param string $weekday ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday']
     * @return DateTime
     * @throws Exception
     */
    function getRandomDateWithWeekday(string $weekday): DateTime
    {
        $date = new DateTime();

        // Generate a random number between -30 and 30
        $randomDays = mt_rand(-30, 30);

        $date->modify($randomDays . ' days');

        // Find the nearest specified weekday
        if ($date->format('l') !== $weekday) {
            $date->modify('next ' . $weekday);
        }

        return $date;
    }

    private function getDataDummy(int $value, bool $won, string $weekday): TipicoPlacementData
    {
        $data = new TipicoPlacementData();
        $data->setValue($value);
        $data->setWon($won);
        $data->setCreated($this->getRandomDateWithWeekday($weekday));

        return $data;
    }
}
