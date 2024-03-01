<?php
declare(strict_types=1);

namespace App\Service\Tipico;

use App\Entity\Simulator;
use App\Entity\TipicoBet;
use App\Service\Tipico\Api\TipicoApiGateway;
use App\Service\Tipico\Content\Simulator\SimulatorService;
use App\Service\Tipico\Content\TipicoBet\Data\TipicoBetData;
use App\Service\Tipico\Content\TipicoBet\TipicoBetService;
use App\Service\Tipico\SimulationProcessors\SimulationStrategyProcessorProvider;
use DateTime;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Notifier\Bridge\Telegram\TelegramOptions;
use Symfony\Component\Notifier\ChatterInterface;
use Symfony\Component\Notifier\Message\ChatMessage;


/**
 * @author Wolfgang Hinzmann <wolfgang.hinzmann@doccheck.com>
 * @license 2024 DocCheck Community GmbH
 */
class TipicoBetSimulationService
{

    public function __construct(
        private readonly TipicoApiGateway $tipicoApiGateway,
        private readonly TipicoBetService $tipicoBetService,
        private readonly SimulationStrategyProcessorProvider $processorProvider,
        private readonly TelegramMessageService $telegramMessageService,
    )
    {
    }

    public function storeDailyMatches(): int
    {
        $response = $this->tipicoApiGateway->getDailyMatchEvents();

        $stored = 0;
        foreach ($response->getMatches() as $tipicoBetData) {
            if ($this->tipicoBetService->findByTipicoId($tipicoBetData->getTipicoId())) {
                continue;
            }

            $this->tipicoBetService->createByData($tipicoBetData);
            $stored++;
        }

        return $stored;
    }

    public function checkMatchOutcome(TipicoBet $match): bool
    {
        $response = $this->tipicoApiGateway->getEventInfo((string)$match->getTipicoId());

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
        $processor = $this->processorProvider->getProcessorByIdent($simulator->getStrategy()->getIdentifier());
        if (!$processor) {
            return;
        }

        $processor->calculate($simulator);
    }

    public function sendMessageToTelegramFeed(string $message): void
    {
        $this->telegramMessageService->sendMessageToTelegramFeed($message);
    }
}
