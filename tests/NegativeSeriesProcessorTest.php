<?php
declare(strict_types=1);

use App\Service\Tipico\Content\Placement\Data\TipicoPlacementData;
use App\Service\Tipico\Simulation\AdditionalProcessors\NegativeSeriesProcessor;
use PHPUnit\Framework\TestCase;

/**
 * @author Wolfgang Hinzmann <wolfgang.hinzmann@doccheck.com>
 * @license 2023 DocCheck Community GmbH
 */
class NegativeSeriesProcessorTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->service = new NegativeSeriesProcessor();
    }

    public function testNegativeSeriesStartingWithCurrentSeriesIsZero(): void
    {
        $placements = [
            $this->getDataDummy(1, true),
            $this->getDataDummy(2, true),
            $this->getDataDummy(3, false),
            $this->getDataDummy(4, false),
            $this->getDataDummy(5, false),
            $this->getDataDummy(6, true),
            $this->getDataDummy(7, true),
        ];
        $negativeSeriesBreak = 2;
        $currentNegativeSeriesCount = 0;

        $result = $this->service->processNegativeSeriesBreakParameter(
            $placements,
            $negativeSeriesBreak,
            $currentNegativeSeriesCount
        );

        $placements = $result->getPlacementData();

        $this->assertCount(5, $placements);

        $expectedValues = [1,2,3,4,7];
        foreach ($placements as $index => $placement) {
            $this->assertEquals((float)$expectedValues[$index], $placement->getValue());
        }
        $this->assertEquals(0, $result->getCurrentNegativeSeries());
    }

    public function testNegativeSeriesStartingWithCurrentSeriesIsNotZero(): void
    {
        $placements = [
            $this->getDataDummy(1, false),
            $this->getDataDummy(2, true),
            $this->getDataDummy(3, false),
            $this->getDataDummy(4, false),
            $this->getDataDummy(5, false),
            $this->getDataDummy(6, true),
            $this->getDataDummy(7, true),
        ];
        $negativeSeriesBreak = 2;
        $currentNegativeSeriesCount = 1;

        $result = $this->service->processNegativeSeriesBreakParameter(
            $placements,
            $negativeSeriesBreak,
            $currentNegativeSeriesCount
        );

        $placements = $result->getPlacementData();

        $this->assertCount(4, $placements);

        $expectedValues = [1,3,4,7];
        foreach ($placements as $index => $placement) {
            $this->assertEquals((float)$expectedValues[$index], $placement->getValue());
        }
    }

    public function testNegativeSeriesWithMultiplePlacements(): void
    {
        $placements = [
            $this->getDataDummy(1, true),
            $this->getDataDummy(2, false),
            $this->getDataDummy(3, true),
            $this->getDataDummy(4, true),
            $this->getDataDummy(5, false),
            $this->getDataDummy(6, false),
            $this->getDataDummy(7, false),
            $this->getDataDummy(8, true),
            $this->getDataDummy(9, true),
            $this->getDataDummy(10, false),
            $this->getDataDummy(11, false),
            $this->getDataDummy(12, true),
            $this->getDataDummy(13, true),
            $this->getDataDummy(14, true),
            $this->getDataDummy(15, false),
            $this->getDataDummy(16, true),
            $this->getDataDummy(17, false),
            $this->getDataDummy(18, false),
            $this->getDataDummy(19, true),
            $this->getDataDummy(20, false),
        ];
        $expectedValues = [1,2,3,4,5,6,9,10,11,13,14,15,16,17,18,20];

        $negativeSeriesBreak = 2;
        $currentNegativeSeriesCount = 0;

        $result = $this->service->processNegativeSeriesBreakParameter(
            $placements,
            $negativeSeriesBreak,
            $currentNegativeSeriesCount
        );

        $placements = $result->getPlacementData();

        $this->assertCount(count($expectedValues), $placements);

        foreach ($placements as $index => $placement) {
            $this->assertEquals((float)$expectedValues[$index], $placement->getValue());
        }
        $this->assertEquals(1, $result->getCurrentNegativeSeries());
    }

    public function testNegativeSeriesOnlyLooses(): void
    {
        $placements = [
            $this->getDataDummy(1, false),
            $this->getDataDummy(2, false),
            $this->getDataDummy(3, false),
            $this->getDataDummy(4, false),
            $this->getDataDummy(5, false),
            $this->getDataDummy(6, false),
            $this->getDataDummy(7, false),
        ];
        $negativeSeriesBreak = 2;
        $currentNegativeSeriesCount = 0;

        $result = $this->service->processNegativeSeriesBreakParameter(
            $placements,
            $negativeSeriesBreak,
            $currentNegativeSeriesCount
        );

        $placements = $result->getPlacementData();

        $this->assertCount(2, $placements);

        $expectedValues = [1,2];
        foreach ($placements as $index => $placement) {
            $this->assertEquals((float)$expectedValues[$index], $placement->getValue());
        }
        $this->assertEquals(7, $result->getCurrentNegativeSeries());
    }

    public function testNegativeSeriesOnlyWins(): void
    {
        $placements = [
            $this->getDataDummy(1, true),
            $this->getDataDummy(2, true),
            $this->getDataDummy(3, true),
            $this->getDataDummy(4, true),
            $this->getDataDummy(5, true),
            $this->getDataDummy(6, true),
            $this->getDataDummy(7, true),
        ];
        $negativeSeriesBreak = 2;
        $currentNegativeSeriesCount = 0;

        $result = $this->service->processNegativeSeriesBreakParameter(
            $placements,
            $negativeSeriesBreak,
            $currentNegativeSeriesCount
        );

        $placements = $result->getPlacementData();

        $this->assertCount(7, $placements);

        $expectedValues = [1,2,3,4,5,6,7];
        foreach ($placements as $index => $placement) {
            $this->assertEquals((float)$expectedValues[$index], $placement->getValue());
        }
        $this->assertEquals(0, $result->getCurrentNegativeSeries());
    }

    private function getDataDummy(int $value, bool $won): TipicoPlacementData
    {
        $data = new TipicoPlacementData();
        $data->setValue($value);
        $data->setWon($won);

        return $data;
    }
}
