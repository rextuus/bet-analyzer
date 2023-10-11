<?php

namespace App\Command;

use App\Service\Evaluation\Message\CalculateSummariesMessage;
use App\Service\Sportmonks\Content\Season\SpmSeasonService;
use App\Service\Statistic\StatisticService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsCommand(
    name: 'season:calculate:summaries',
    description: 'Add a short description for your command',
)]
class SeasonCalculateSummariesCommand extends Command
{


    public function __construct(private SpmSeasonService $seasonService, private StatisticService $statisticService, private readonly MessageBusInterface $bus)
    {
        parent::__construct();
    }

    protected function configure(): void
    {

    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $nextOnes = $this->seasonService->findApprovedSeasonsBetRows();
        foreach ($nextOnes as $nextOne){
            $message = new CalculateSummariesMessage($nextOne[0]->getApiId());
            $this->bus->dispatch($message);
        }

        return Command::SUCCESS;
    }
}
