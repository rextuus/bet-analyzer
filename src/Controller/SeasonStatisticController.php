<?php

namespace App\Controller;

use App\Entity\Spm\SeasonStatistic;
use App\Service\Evaluation\Message\UpdateOddOutcomeMessage;
use App\Service\Sportmonks\Content\Fixture\SpmFixtureService;
use App\Service\Sportmonks\Content\Season\Statistic\Data\SeasonStatisticData;
use App\Service\Sportmonks\Content\Season\Statistic\SeasonStatisticService;
use App\Service\Statistic\Content\OddOutcome\OutcomeCalculator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/league/statistic')]
class SeasonStatisticController extends AbstractController
{
    #[Route('/', name: 'app_season_statistic_list')]
    public function list(SeasonStatisticService $seasonStatisticService, OutcomeCalculator $calculator): Response
    {
        $message = new UpdateOddOutcomeMessage();
        $message->setFixtureIds([6781]);
        $calculator->calculateAll($message);

        return $this->render('season_statistic/index.html.twig', [
            'dtos' => [],
        ]);
    }

    #[Route("/check/{seasonApiId}", name: 'app_season_statistic_check')]
    public function check(SeasonStatistic $seasonStatistic, SeasonStatisticService $seasonStatisticService, SpmFixtureService $fixtureService): Response
    {
        $data = (new SeasonStatisticData())->initFromEntity($seasonStatistic);
        $fixtures = $fixtureService->getFixtureWithOddDecorationBySeason($seasonStatistic);
        $data->setActuallyBetDecorated($fixtures);
        $seasonStatisticService->update($seasonStatistic, $data);

        return new Response();
    }

    #[Route("/mark/{seasonApiId}", name: 'app_season_statistic_mark')]
    public function mark(SeasonStatistic $seasonStatistic, SeasonStatisticService $seasonStatisticService): Response
    {
        $data = (new SeasonStatisticData())->initFromEntity($seasonStatistic);
        $data->setManuallyConfirmed(true);
        $data->setIsReliable(true);
        $seasonStatisticService->update($seasonStatistic, $data);

        return new Response();
    }
}
