<?php

namespace App\Command;

use App\Service\Sportmonks\Api\SportsmonkApiGateway;
use App\Service\Sportmonks\Api\SportsmonkService;
use App\Service\Sportmonks\Content\League\SpmLeagueService;
use App\Service\Sportmonks\Content\Round\SpmRoundService;
use App\Service\Sportmonks\Content\Standing\SpmStandingService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'standings:init',
    description: 'Add a short description for your command',
)]
/**
 * @deprecated
 */
class StandingsCommand extends Command
{


    public function __construct(
        private readonly SportsmonkService $sportsmonkService,
        private readonly SpmRoundService $roundService,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('roundApiId', InputArgument::OPTIONAL, 'Start round id');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $roundApiId = null;

        if ($input->getArgument('roundApiId')){
            $roundApiId = $input->getArgument('roundApiId');
            $this->sportsmonkService->storeStandings($roundApiId);
            return Command::SUCCESS;
        }

        $standings = $this->roundService->findRoundWithoutStandings();
        $this->sportsmonkService->storeStandings($standings[0]->getApiId());

        return Command::SUCCESS;
    }
}
