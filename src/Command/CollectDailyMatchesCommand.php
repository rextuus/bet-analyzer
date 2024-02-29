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
    name: 'CollectDailyMatchesCommand',
    description: 'Add a short description for your command',
)]
class CollectDailyMatchesCommand extends Command
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
        $time = (new \DateTime())->format('d/m/y H:i');
        $this->betSimulationService->sendMessageToTelegramFeed($time);

        return Command::SUCCESS;
    }
}
