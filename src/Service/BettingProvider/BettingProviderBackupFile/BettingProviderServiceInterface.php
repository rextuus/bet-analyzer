<?php

namespace App\Service\BettingProvider\BettingProviderBackupFile;

use App\Service\BettingProvider\BettingProvider;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('betting.provider')]
interface BettingProviderServiceInterface
{
    public function storeBetsFromBackupFile(int $backupId): void;

    public function createResponseToParse(array $content): BettingProviderApiResponseInterface;

    public function getProviderIdent(): string;

    public function getBettingProvider(): BettingProvider;

}