<?php
declare(strict_types=1);

namespace App\Service\Tipico;

use App\Entity\TipicoBet;
use App\Service\Tipico\Api\TipicoApiGateway;
use App\Service\Tipico\Content\TipicoBet\Data\TipicoBetData;
use App\Service\Tipico\Content\TipicoBet\TipicoBetService;
use DateTime;
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
        private readonly ChatterInterface $chatter
    )
    {
    }

    public function storeDailyMatches(): void
    {
        $response = $this->tipicoApiGateway->getDailyMatchEvents();

        $stored = 0;
        foreach ($response->getMatches() as $tipicoBetData) {
            $this->tipicoBetService->createByData($tipicoBetData);
            $stored++;
        }
        $message = sprintf('Added %d new matches for %s', $stored, (new DateTime())->format('d.m.Y'));

        $this->sendMessageToTelegramFeed($message);
    }

    public function checkMatchOutcome(TipicoBet $match): bool
    {
        $response = $this->tipicoApiGateway->getEventInfo((string) $match->getTipicoId());

        $data = (new TipicoBetData())->initFromEntity($match);

        if($response->isGameIsFinished()){
            $data->setEndScoreHome($response->getHomeGoals());
            $data->setEndScoreAway($response->getAwayGoals());
            $data->setResult($response->getResult());
            $data->setFinished(true);

            $this->tipicoBetService->update($match, $data);
            return true;
        }

        return false;
    }

    public function checkMatches(): void
    {
        $matches = $this->tipicoBetService->findAllUndecoratedMatches();
dd($matches);
        $finished = 0;
        foreach ($matches as $match){
            $this->checkMatchOutcome($match);
            $finished++;
        }
    }

    public function sendMessageToTelegramFeed(string $message): void
    {
        $chatMessage = new ChatMessage($message);

        $telegramOptions = (new TelegramOptions())
            ->chatId('704862488')
            ->parseMode('MarkdownV2')
            ->disableWebPagePreview(true);

        $chatMessage->options($telegramOptions);

        $this->chatter->send($chatMessage);
    }
}
