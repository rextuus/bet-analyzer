<?php

declare(strict_types=1);

namespace App\Service\BettingProvider\BettingProviderBackupFile\Message;

use App\Service\BettingProvider\BettingProviderBackupFile\BackupProcessorTargetEntityServiceProvider;
use Exception;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;


#[AsMessageHandler]
class StoreBetsForProviderMessageHandler
{
    public function __construct(private readonly BackupProcessorTargetEntityServiceProvider $serviceProvider)
    {
    }

    public function __invoke(StoreBetsForProviderMessage $message): void
    {
        $service = $this->serviceProvider->getServiceByBettingProvider($message->getBettingProvider());
        if ($service === null) {
            throw new Exception('No service found for betting provider');
        }
        $service->storeBetsFromBackupFile($message->getBackupId());
    }
}
