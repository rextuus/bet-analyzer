<?php

namespace App\Command\Tipico;

use App\Service\Tipico\Api\TipicoApiGateway;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpKernel\KernelInterface;

#[AsCommand(
    name: 'StoreTipicoBackupFileCommand',
    description: 'Add a short description for your command',
)]
class StoreTipicoBackupFileCommand extends Command
{
    public function __construct(private KernelInterface $kernel, private TipicoApiGateway $tipicoApiGateway)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $backupDir = 'backups';
        $backupDir = $this->kernel->getProjectDir() . '/public/backups/tipico';

        $backupFile = $backupDir . '/' . date('Y-m-d') . '.json';

        // Download JSON content
        $rawResponse = $this->tipicoApiGateway->getDailyMatchEventsRaw();


        $filesystem = new Filesystem();

        if (!$filesystem->exists($backupDir)){
            $filesystem->mkdir($backupDir);
        }

        $filesystem->touch($backupFile);
        $filesystem->appendToFile($backupFile, $rawResponse);
        return Command::SUCCESS;
    }
}
