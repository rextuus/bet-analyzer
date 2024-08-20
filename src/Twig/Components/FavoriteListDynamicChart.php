<?php

namespace App\Twig\Components;

use App\Entity\BettingProvider\SimulatorFavoriteList;
use App\Service\Tipico\Content\SimulatorFavoriteList\Data\FavoriteListPeriodStatisticData;
use App\Service\Tipico\Content\SimulatorFavoriteList\FavoriteListStatisticService;
use DateTime;
use Symfony\UX\Chartjs\Model\Chart;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\UX\TwigComponent\Attribute\ExposeInTemplate;

#[AsLiveComponent]
class FavoriteListDynamicChart
{
    use DefaultActionTrait;

    #[LiveProp(writable: true)]
    public float $input = 1.00;

    #[LiveProp(writable: true)]
    public int $fromYear = -28;

    #[LiveProp(writable: true)]
    public int $toYear = 0;

    #[LiveProp(writable: true)]
    public SimulatorFavoriteList $favoriteList;

    private ?FavoriteListPeriodStatisticData $favoriteListPeriodStatisticData = null;

    public function __construct(private FavoriteListStatisticService $favoriteListStatisticService)
    {
    }

    #[ExposeInTemplate]
    public function getChart(): Chart
    {
        $this->initializeFavoriteListPeriodStatisticData();

        return $this->favoriteListPeriodStatisticData->getDailyChart();
    }

    #[ExposeInTemplate]
    public function getBalanceChart(): Chart
    {
        $this->initializeFavoriteListPeriodStatisticData();

        return $this->favoriteListPeriodStatisticData->getTotalChart();
    }

    #[ExposeInTemplate]
    public function getTotalBalance(): float
    {
        $this->initializeFavoriteListPeriodStatisticData();

        return $this->favoriteListPeriodStatisticData->getTotalBalance();
    }

    #[ExposeInTemplate]
    public function getTotalBets(): float
    {
        $this->initializeFavoriteListPeriodStatisticData();

        return $this->favoriteListPeriodStatisticData->getTotalBets();
    }

    #[ExposeInTemplate]
    public function getExtremeValues(): array
    {
        $this->initializeFavoriteListPeriodStatisticData();

        return [
            'min' => $this->favoriteListPeriodStatisticData->getDailyMin(),
            'minDate' => $this->favoriteListPeriodStatisticData->getDailyMinDate(),
            'max' => $this->favoriteListPeriodStatisticData->getDailyMax(),
            'maxDate' => $this->favoriteListPeriodStatisticData->getDailyMaxDate(),
        ];
    }

    private function initializeFavoriteListPeriodStatisticData(): void
    {
        if ($this->favoriteListPeriodStatisticData === null) {
            $fromDate = new DateTime($this->fromYear . ' days');
            $toDate = new DateTime($this->toYear . ' days');

            $this->favoriteListPeriodStatisticData = $this->favoriteListStatisticService->getFavoriteListStatisticForTimePeriod(
                $this->favoriteList,
                $fromDate,
                $toDate,
                $this->input
            );
        }
    }
}
