<?php

namespace App\Command;

use App\Service\BettingProvider\Betano\Content\BetanoBackup\BetanoBackupService;
use App\Service\BettingProvider\BettingProviderBackupFile\Content\BettingProviderBackupFileService;
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
        private readonly BettingProviderBackupFileService $bettingProviderBackupFileService,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {

        $output->writeln(sprintf('Switched %s backup file entities', count($betanoSpecificBackups)));
        return Command::SUCCESS;
    }
}
