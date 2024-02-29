<?php
declare(strict_types=1);

namespace App\Service\Tipico\Message;

use App\Service\Tipico\TipicoBetSimulationService;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

/**
 * @author Wolfgang Hinzmann <wolfgang.hinzmann@doccheck.com>
 * @license 2024 DocCheck Community GmbH
 */
#[AsMessageHandler]
class CollectDailyMatchesMessageHandler
{
    public function __construct(private TipicoBetSimulationService $betSimulationService)
    {
    }

    public function __invoke(CollectDailyMatchesMessage $message)
    {
        $this->betSimulationService->sendMessageToTelegramFeed('Cool');
    }
}
