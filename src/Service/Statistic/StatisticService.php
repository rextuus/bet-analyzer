<?php
declare(strict_types=1);

namespace App\Service\Statistic;

use App\Entity\BetRowOddFilter;
use App\Entity\SimpleBetRow;
use App\Entity\SpmSeason;
use App\Service\Evaluation\BetOn;
use App\Service\Evaluation\Content\BetRow\SimpleBetRow\SimpleBetRowService;
use App\Service\Evaluation\SlideWindowFactory;
use App\Service\Statistic\Dto\BetRowStatistics;
use App\Service\Statistic\Dto\SeasonDto;

/**
 * @author Wolfgang Hinzmann <wolfgang.hinzmann@doccheck.com>
 * @license 2023 DocCheck Community GmbH
 */
class StatisticService
{


    public function __construct(
        private readonly SimpleBetRowService $betRowService,
        private readonly SlideWindowFactory $slideWindowFactory,
    )
    {
    }

    public function getSeasonDto(SpmSeason $season): SeasonDto
    {
        $dto = new SeasonDto();


        $betRowsOfSeason = $this->betRowService->findBy(['seasonApiId' => $season->getApiId()]);
        $this->createBetRowsStatistic($betRowsOfSeason);
        $dto->setSeason($season->getDisplayName());

        return $dto;
    }

    /**
     * @param SimpleBetRow[] $betRows
     * @return BetRowStatistics
     */
    private function createBetRowsStatistic(array $betRows): BetRowStatistics
    {
        $slideWindow = $this->slideWindowFactory->calculateStepsForSlideWindow(1.0, 5.0,0.1);
        $decreaseWindow = $this->slideWindowFactory->calculateStepsForDecreasingWindow(1.0, 5.0,0.1);
        $betRowCreationMap = [
            BetOn::HOME->value => array_merge($this->slideWindowFactory->convertWindowToMap($slideWindow), $this->slideWindowFactory->convertWindowToMap($decreaseWindow)),
            BetOn::DRAW->value => array_merge($this->slideWindowFactory->convertWindowToMap($slideWindow), $this->slideWindowFactory->convertWindowToMap($decreaseWindow)),
            BetOn::AWAY->value => array_merge($this->slideWindowFactory->convertWindowToMap($slideWindow), $this->slideWindowFactory->convertWindowToMap($decreaseWindow))
        ];

        $statistic = new BetRowStatistics();
        foreach ($betRows as $betRow){
            /** @var BetRowOddFilter[] $filters */
            $filters = $betRow->getBetRowFilters()->toArray();
            foreach ($filters as $filter){
                $betRowCreationMap[$filter->getBetOn()->value][$filter->getMin().'-'.$filter->getMax()] = true;
            }
        }

        $missingHomes = array_filter(
            $betRowCreationMap[BetOn::HOME->value],
            function ($rowPresent) {
                return !$rowPresent;
            }
        );
        dd($missingHomes);
    }
}
