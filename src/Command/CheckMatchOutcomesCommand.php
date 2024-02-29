<?php

namespace App\Command;

use App\Service\Tipico\TipicoBetSimulationService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'CheckMatchOutcomes',
    description: 'Add a short description for your command',
)]
class CheckMatchOutcomesCommand extends Command
{
    public function __construct(private TipicoBetSimulationService $betSimulationService)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->betSimulationService->sendMessageToTelegramFeed('Check for finished matches');
        $finished = $this->betSimulationService->checkMatches();
        $this->betSimulationService->sendMessageToTelegramFeed('Updated '.$finished.' finished matches');

        return Command::SUCCESS;
    }
}
