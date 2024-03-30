<?php

namespace App\Command\Tipico;

use App\Service\Tipico\TipicoBetSimulationService;
use DateTime;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'CollectDailyMatchesFromBackupCommand',
    description: 'Add a short description for your command',
)]
class CollectDailyMatchesFromBackupCommand extends Command
{
    public function __construct(private TipicoBetSimulationService $betSimulationService)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('file', InputArgument::REQUIRED, 'Argument description');

    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $file = $input->getArgument('file');


        $time = (new \DateTime())->format('d/m/y H:i');
        $message = sprintf('Start harvesting matches at %s', $time);
        $this->betSimulationService->sendMessageToTelegramFeed($message);

        $stored = $this->betSimulationService->storeDailyMatchesFromBackup($file);

        $message = sprintf('Added %d new matches for %s', $stored, (new DateTime())->format('d.m.Y'));
        $this->betSimulationService->sendMessageToTelegramFeed($message);

        return Command::SUCCESS;
    }
}
