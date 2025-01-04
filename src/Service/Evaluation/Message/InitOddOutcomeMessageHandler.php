<?php
declare(strict_types=1);

namespace App\Service\Evaluation\Message;

use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\MessageBusInterface;

/**
 * @deprecated SPM can be removed. Data are not worthy and won't be used anymore
 */
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
