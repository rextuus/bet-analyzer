<?php

namespace App\Command;

use App\Service\Sportmonks\Api\SportsmonkApiGateway;
use App\Service\Sportmonks\Api\SportsmonkService;
use App\Service\Sportmonks\Content\League\SpmLeagueService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'rounds:init',
    description: 'Add a short description for your command',
)]
class RoundCommand extends Command
{


    public function __construct(
        private readonly SportsmonkService $sportsmonkService,
        private readonly SpmLeagueService $leagueService,
        private readonly SportsmonkApiGateway $apiGateway
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('page', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $page = null;
        if ($input->getArgument('page')){
            $page = $input->getArgument('page');
        }
        $this->sportsmonkService->storeRoundsAndFixturesByPage($page);

        return Command::SUCCESS;
    }
}
