<?php

declare(strict_types=1);

namespace App\Service\BettingProvider\BettingProviderBackupFile;

use App\Service\BettingProvider\BettingProvider;
use Symfony\Component\DependencyInjection\Attribute\TaggedIterator;


class BackupProcessorTargetEntityServiceProvider
{
    public function __construct(
        /**
         * @var iterable<int, BettingProviderServiceInterface> $processors
         */
        #[TaggedIterator('betting.provider')]
        private readonly iterable $services,
    ) {
    }

    public function getServiceByBettingProvider(BettingProvider $bettingProvider): ?BettingProviderServiceInterface
    {
        foreach ($this->services as $service) {
            if ($service->getBettingProvider() === $bettingProvider) {
                return $service;
            }
        }

        return null;
    }

}
