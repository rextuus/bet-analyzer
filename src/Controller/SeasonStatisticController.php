<?php

namespace App\Controller;

use App\Entity\SeasonStatistic;
use App\Service\Sportmonks\Content\Fixture\SpmFixtureService;
use App\Service\Sportmonks\Content\Season\Statistic\Data\SeasonStatisticData;
use App\Service\Sportmonks\Content\Season\Statistic\SeasonStatisticService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/season/statistic')]
class SeasonStatisticController extends AbstractController
{
    #[Route('/', name: 'app_season_statistic_list')]
    public function list(SeasonStatisticService $seasonStatisticService): Response
    {
        $dtos = $seasonStatisticService->getViewDtos();

        return $this->render('season_statistic/index.html.twig', [
            'dtos' => $dtos,
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
