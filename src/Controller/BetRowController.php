<?php

namespace App\Controller;

use App\Entity\SpmSeason;
use App\Service\Evaluation\Content\BetRow\SimpleBetRow\SimpleBetRowService;
use App\Service\Statistic\StatisticService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/statistic')]
class BetRowController extends AbstractController
{
    public function __construct(private SimpleBetRowService $betRowService, private StatisticService $statisticService)
    {
    }

    #[Route('/{apiId}', name: 'app_bet_row_list')]
    public function index(SpmSeason $season): Response
    {
        $betRows = $this->betRowService->findBy(['seasonApiId' => 19744]);
        $this->statisticService->getSeasonDto($season);

        return $this->render('bet_row/index.html.twig', [
            'betRows' => $betRows,
        ]);
    }
}
