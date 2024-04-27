<?php
declare(strict_types=1);

namespace App\Service\Evaluation\Message;

use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\MessageBusInterface;


#[AsMessageHandler]
class InitOddOutcomeMessageHandler
{
    public function __construct(private MessageBusInterface $messageBus)
    {
    }

    public function __invoke(InitOddOutcomeMessage $message): void
    {
        foreach ($message->getFixtureIds() as $fixtureId) {
            $updateMessage = new UpdateOddOutcomeMessage();
            $updateMessage->setFixtureIds([$fixtureId]);
            $this->messageBus->dispatch($updateMessage);
        }
    }
}
