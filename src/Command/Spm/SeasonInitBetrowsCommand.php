<?php

namespace App\Command\Spm;

use App\Service\Evaluation\Message\TriggerBetRowsForSeasonMessage;
use App\Service\Sportmonks\Content\Season\SpmSeasonService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsCommand(
    name: 'season:init:betrows',
    description: 'Add a short description for your command',
)]
class SeasonInitBetrowsCommand extends Command
{


    public function __construct(
        private readonly SpmSeasonService $seasonService,
        private readonly MessageBusInterface $bus,
    )
    {
        parent::__construct();
    }

    protected function configure(): void
    {
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $seasonsWithoutBetRows = $this->seasonService->findApprovedSeasonsBetRows();
        $nextSeasonToDecorate = $seasonsWithoutBetRows[0][0];

        $message = new TriggerBetRowsForSeasonMessage($nextSeasonToDecorate->getApiId(), false);
        $this->bus->dispatch($message);
        return Command::SUCCESS;
    }
}
