<?php

namespace App\Command\Spm;

use App\Service\Sportmonks\Api\SportsmonkService;
use App\Service\Sportmonks\Content\League\SpmLeagueService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'leagues:init',
    description: 'Add a short description for your command',
)]
class LeagueCommand extends Command
{


    public function __construct(
        private readonly SportsmonkService $sportsmonkService,
        private readonly SpmLeagueService $leagueService,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->sportsmonkService->storeLeagues();

        return Command::SUCCESS;
    }
}
