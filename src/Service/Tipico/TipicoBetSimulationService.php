<?php
declare(strict_types=1);

namespace App\Service\Tipico;

use App\Entity\Simulator;
use App\Entity\TipicoBet;
use App\Service\Tipico\Api\TipicoApiGateway;
use App\Service\Tipico\Content\TipicoBet\Data\TipicoBetData;
use App\Service\Tipico\Content\TipicoBet\TipicoBetService;
use App\Service\Tipico\Content\TipicoOdd\BothTeamsScoreOdd\TipicoBothTeamsScoreOddService;
use App\Service\Tipico\Content\TipicoOdd\HeadToHeadOdd\TipicoHeadToHeadOddService;
use App\Service\Tipico\Content\TipicoOdd\OverUnderOdd\TipicoOverUnderOddService;
use App\Service\Tipico\SimulationProcessors\SimulationStrategyProcessorProvider;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;


/**
 * @author Wolfgang Hinzmann <wolfgang.hinzmann@doccheck.com>
 * @license 2024 DocCheck Community GmbH
 */
class TipicoBetSimulationService
{

    public function __construct(
        private readonly TipicoApiGateway $tipicoApiGateway,
        private readonly TipicoBetService $tipicoBetService,
        private readonly TipicoOverUnderOddService $tipicoOverUnderOddService,
        private readonly TipicoBothTeamsScoreOddService $tipicoBothTeamsScoreOddService,
        private readonly TipicoHeadToHeadOddService $tipicoHeadToHeadOddService,
        private readonly SimulationStrategyProcessorProvider $processorProvider,
        private readonly TelegramMessageService $telegramMessageService,
        private readonly UrlGeneratorInterface $urlGenerator,
        private readonly float $cashBoxLimit,
    )
    {
    }

    public function storeDailyMatches(): int
    {
        $response = $this->tipicoApiGateway->getDailyMatchEvents();

        // standard bets
        $newCreatedBets = [];
        foreach ($response->getMatches() as $tipicoBetData) {
            if ($this->tipicoBetService->findByTipicoId($tipicoBetData->getTipicoId())) {
                continue;
            }

            $newCreatedBets[$tipicoBetData->getTipicoId()] = $this->tipicoBetService->createByData($tipicoBetData);
        }

        // over under odds
        foreach ($response->getOverUnderOdds() as $oddDataSet){
            if (!array_key_exists($oddDataSet->getTipicoBetId(), $newCreatedBets)){
                continue;
            }
            if ($this->tipicoOverUnderOddService->findByTipicoId($oddDataSet->getTipicoBetId())) {
                continue;
            }

            $bet = $newCreatedBets[$oddDataSet->getTipicoBetId()];
            $oddDataSet->setBet($bet);
            $this->tipicoOverUnderOddService->createByData($oddDataSet);
        }

        // both teams score odds
        foreach ($response->getBothTeamsScoreOdds() as $oddDataSet){
            if (!array_key_exists($oddDataSet->getTipicoBetId(), $newCreatedBets)){
                continue;
            }
            if ($this->tipicoBothTeamsScoreOddService->findByTipicoId($oddDataSet->getTipicoBetId())) {
                continue;
            }
            $bet = $newCreatedBets[$oddDataSet->getTipicoBetId()];
            $oddDataSet->setBet($bet);
            $this->tipicoBothTeamsScoreOddService->createByData($oddDataSet);
        }

        // head to head odds
        foreach ($response->getHeadToHeadOdds() as $oddDataSet){
            if (!array_key_exists($oddDataSet->getTipicoBetId(), $newCreatedBets)){
                continue;
            }
            if ($this->tipicoHeadToHeadOddService->findByTipicoId($oddDataSet->getTipicoBetId())) {
                continue;
            }
            $bet = $newCreatedBets[$oddDataSet->getTipicoBetId()];
            $oddDataSet->setBet($bet);
            $this->tipicoHeadToHeadOddService->createByData($oddDataSet);
        }

        return count($newCreatedBets);
    }

    public function checkMatchOutcome(TipicoBet $match): bool
    {
        $response = $this->tipicoApiGateway->getEventInfo((string)$match->getTipicoId());
        if(!$response){
            return false;
        }

        $data = (new TipicoBetData())->initFromEntity($match);

        if ($response->isGameIsFinished()) {
            $data->setEndScoreHome($response->getHomeGoals());
            $data->setEndScoreAway($response->getAwayGoals());
            $data->setResult($response->getResult());
            $data->setFinished(true);

            $this->tipicoBetService->update($match, $data);
            return true;
        }

        return false;
    }

    public function checkMatches(): int
    {
        $matches = $this->tipicoBetService->findAllUndecoratedMatches();

        $finished = 0;
        foreach ($matches as $match) {
            $this->checkMatchOutcome($match);
            $finished++;
        }

        return $finished;
    }

    public function simulate(Simulator $simulator): void
    {
        if ($simulator->getCashBox() <= $this->cashBoxLimit){
            return;
        }

        $processor = $this->processorProvider->getProcessorByIdent($simulator->getStrategy()->getIdentifier());
        if (!$processor) {
            return;
        }

        $result = $processor->calculate($simulator);

        if (
            count($result->getPlacements()) > 0 &&
            ($result->getCashBoxChange() > 2.0 || $result->getCashBoxChange() < -2.0)
        ){
            $message = sprintf(
                '"%s" simulator placed %d bets and made a sales volume of %.2f. Current cash box: %.2f: https://bet-analyzer.wh-company.de%s',
                $simulator->getIdentifier(),
                count($result->getPlacements()),
                $result->getCashBoxChange(),
                $simulator->getCashBox(),
                $this->urlGenerator->generate('app_tipico_simulation_detail', ['simulator' => $simulator->getId()]),
            );

            $this->telegramMessageService->sendMessageToTelegramFeed($message);
        }
    }

    public function sendMessageToTelegramFeed(string $message): void
    {
        $this->telegramMessageService->sendMessageToTelegramFeed($message);
    }

    public function isHighCalculationAmount(Simulator $simulator): bool
    {
        $processor = $this->processorProvider->getProcessorByIdent($simulator->getStrategy()->getIdentifier());
        if (!$processor) {
            return false;
        }

        return $processor->isHighCalculationAmount($simulator);
    }
}
