<?php

namespace App\Command;

use App\Service\BettingProvider\Betano\Content\BetanoBackup\BetanoBackupService;
use App\Service\BettingProvider\BettingProvider;
use App\Service\BettingProvider\BettingProviderBackupFile\Content\BettingProviderBackupFileService;
use App\Service\BettingProvider\BettingProviderBackupFile\Content\Data\BettingProviderBackupFileData;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'moveBetanoBackupToNewStructure',
    description: 'Add a short description for your command',
)]
class MoveBetanoBackupToNewStructureCommand extends Command
{
    public function __construct(
        private readonly BetanoBackupService $betanoBackupService,
        private readonly BettingProviderBackupFileService $bettingProviderBackupFileService,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $betanoSpecificBackups = $this->betanoBackupService->findBy([]);
        foreach ($betanoSpecificBackups as $betanoSpecificBackup) {
            $data = new BettingProviderBackupFileData();

            $data->setProvider(BettingProvider::BETANO);
            $data->setFilePath($betanoSpecificBackup->getFilePath());
            $data->setContainingBets($betanoSpecificBackup->getContainedBets());
            $data->setAlreadyFittedBets($betanoSpecificBackup->getAlreadyStoredBets());
            $data->setNonFittedBets($betanoSpecificBackup->getNonFittedBets());
            $data->setFittedBets($betanoSpecificBackup->getFittedBets());
            $data->setCreated($betanoSpecificBackup->getCreated());
            $data->setIsConsumed($betanoSpecificBackup->isIsConsumed());

            $this->bettingProviderBackupFileService->createByData($data);
        }

        $output->writeln(sprintf('Switched %s backup file entities', count($betanoSpecificBackups)));
        return Command::SUCCESS;
    }
}
