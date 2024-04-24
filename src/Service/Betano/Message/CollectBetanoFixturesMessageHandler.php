<?php
declare(strict_types=1);

namespace App\Service\Betano\Message;

use App\Service\Betano\Content\BetanoBet\BetanoBetService;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

/**
 * @author Wolfgang Hinzmann <wolfgang.hinzmann@doccheck.com>
 * @license 2024 DocCheck Community GmbH
 */
#[AsMessageHandler]
class CollectBetanoFixturesMessageHandler
{
    public function __construct(
        private readonly BetanoBetService $betanoBetService,
    )
    {
    }

    public function __invoke(CollectBetanoFixturesMessage $message): void
    {
        $this->betanoBetService->storeBetanoBetsFromBackupFile($message->getBackupId());
    }
}
