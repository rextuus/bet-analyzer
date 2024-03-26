<?php
declare(strict_types=1);

namespace App\Service\Betano\Message;

use App\Service\Betano\Api\BetanoApiGateway;
use App\Service\Betano\Content\BetanoBet\BetanoBetService;
use App\Service\Betano\Content\BetanoSettings\BetanoSettingsService;
use DateTime;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\DelayStamp;

/**
 * @author Wolfgang Hinzmann <wolfgang.hinzmann@doccheck.com>
 * @license 2024 DocCheck Community GmbH
 */
#[AsMessageHandler]
class CollectBetanoFixturesMessageHandler
{
    public function __construct(
        private readonly BetanoSettingsService $betanoSettingsService,
        private readonly BetanoBetService $betanoBetService,
        private readonly BetanoApiGateway $betanoApiGateway,
        private readonly MessageBusInterface $messageBus,
    )
    {
    }

    public function __invoke(CollectBetanoFixturesMessage $message): void
    {
        $apiResponse = $this->betanoApiGateway->getNextDailyMatchEvents();
        $dataObjects = $apiResponse->getBetanoBetDataObjects();
        foreach ($dataObjects as $betanoBetData) {
            if (count($this->betanoBetService->findBy(['betanoId' => $betanoBetData->getBetanoId()]))) {
                continue;
            }
            $this->betanoBetService->createByData($betanoBetData);
        }

        $lastFixture = $dataObjects[array_key_last($dataObjects)];
        $lastFixtureStart = $lastFixture->getStartAtTimeStamp();

        $currentTime = (new DateTime())->getTimestamp()*1000;
        $nextExecutionIn = $lastFixtureStart - $currentTime;

        $this->betanoSettingsService->setDefaultSettingsNextExecutionTime($lastFixture->getStartAtTimeStamp());

        // calculate next execution time
        $this->messageBus->dispatch(new Envelope(new CollectBetanoFixturesMessage(), [
            new DelayStamp($nextExecutionIn),
        ]));
    }
}
