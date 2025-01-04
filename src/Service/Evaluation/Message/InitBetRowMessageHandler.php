<?php

namespace App\Service\Evaluation\Message;

use App\Service\Evaluation\BetRowCalculator;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

/**
 * @deprecated SPM can be removed. Data are not worthy and won't be used anymore
 */
#[AsMessageHandler]
class InitBetRowMessageHandler
{
    public function __construct(
        readonly private BetRowCalculator $betRowCalculator
    )
    {
    }

    public function __invoke(InitBetRowMessage $message): void
    {
        $this->betRowCalculator->prepareInitBetRow(
            $message->getData(),
            $message->getBetOnVariant(),
            $message->getFrom(),
            $message->getTo(),
        );
    }
}
