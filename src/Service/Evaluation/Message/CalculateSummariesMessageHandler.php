<?php

namespace App\Service\Evaluation\Message;

use App\Service\Sportmonks\Content\Season\SpmSeasonService;
use App\Service\Statistic\StatisticService;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\MessageBusInterface;

/**
 * @deprecated SPM can be removed. Data are not worthy and won't be used anymore
 */
#[AsMessageHandler]

class CalculateSummariesMessageHandler
{
    public function __construct(
        private StatisticService $statisticService,
        private SpmSeasonService $seasonService,
        private readonly MessageBusInterface $bus,
    )
    {
    }

    public function __invoke(CalculateSummariesMessage $message)
    {
        $season = $this->seasonService->findBy(['apiId' => $message->getSeasonApiId()])[0];
        $this->statisticService->getSeasonDto($season);
    }
}
