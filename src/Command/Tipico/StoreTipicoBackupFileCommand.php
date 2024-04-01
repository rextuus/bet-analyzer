<?php

namespace App\Command\Tipico;

use App\Service\Tipico\Api\TipicoApiGateway;
use App\Service\Tipico\TipicoBetSimulationService;
use DateTime;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'StoreTipicoBackupFileCommand',
    description: 'Add a short description for your command',
)]
class StoreTipicoBackupFileCommand extends Command
{
    public function __construct(private TipicoApiGateway $tipicoApiGateway)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $backupDir = 'backups';
        $backupFile = $backupDir . '/' . date('Y-m-d') . '.json';

        // Download JSON content
        $rawResponse = $this->tipicoApiGateway->getDailyMatchEventsRaw();

        // Save JSON content to file
        if (!file_exists($backupDir)) {
            mkdir($backupDir, 0777, true);
        }
        file_put_contents($backupFile, $rawResponse);

        return Command::SUCCESS;
    }
}
