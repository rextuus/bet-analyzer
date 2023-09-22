<?php

namespace App\Service\Evaluation\Message;

use App\Entity\SpmSeason;
use App\Form\InitSimpleBetRowsForSeasonData;
use App\Form\InitSimpleBetRowVariant;
use App\Service\Evaluation\BetRowCalculator;
use App\Service\Evaluation\OddAccumulationVariant;
use App\Service\Sportmonks\Content\Season\SpmSeasonService;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Messenger\MessageBusInterface;

final class TriggerBetRowsForSeasonMessageHandler implements MessageHandlerInterface
{


    public function __construct(
        private BetRowCalculator $betRowCalculator,
        private SpmSeasonService $seasonService,
        private readonly MessageBusInterface $bus,
    )
    {
    }

    public function __invoke(TriggerBetRowsForSeasonMessage $message)
    {
        $season = $this->seasonService->findBy(['apiId' => $message->getSeasonApiId()])[0];

        $slidingData = new InitSimpleBetRowsForSeasonData();
        $slidingData->setSeason($season);
        $slidingData->setMin(1.0);
        $slidingData->setMax(5.0);
        $slidingData->setSteps(0.1);
        $slidingData->setWager(1.0);
        $slidingData->setInitialCashBox(100.0);
        $slidingData->setIncludeTax(true);
        $slidingData->setOddAccumulationVariant(OddAccumulationVariant::MEDIAN);
        $slidingData->setInitSimpleBetRowVariant(InitSimpleBetRowVariant::STEP_WINDOW);

        $decreasingData = new InitSimpleBetRowsForSeasonData();
        $decreasingData->setSeason($season);
        $decreasingData->setMin(1.0);
        $decreasingData->setMax(5.0);
        $decreasingData->setSteps(0.1);
        $decreasingData->setWager(1.0);
        $decreasingData->setInitialCashBox(100.0);
        $decreasingData->setIncludeTax(true);
        $decreasingData->setOddAccumulationVariant(OddAccumulationVariant::MEDIAN);
        $decreasingData->setInitSimpleBetRowVariant(InitSimpleBetRowVariant::DECREASING_WINDOW);

        $this->betRowCalculator->initClassicBetRowSetForSeason($slidingData);
        $this->betRowCalculator->initClassicBetRowSetForSeason($decreasingData);

        // dispatch next one
        $seasonsWithoutBetRows = $this->seasonService->findApprovedSeasonsBetRows();
        /** @var SpmSeason $nextSeasonToDecorate */
        $nextSeasonToDecorate = $seasonsWithoutBetRows[0][0];
        if ($message->isUseEnd()){
            $nextSeasonToDecorate = $seasonsWithoutBetRows[array_key_last($seasonsWithoutBetRows)][0];
        }

        $message = new TriggerBetRowsForSeasonMessage($nextSeasonToDecorate->getApiId(), !$message->isUseEnd());
        $this->bus->dispatch($message);
    }
}
