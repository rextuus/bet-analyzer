<?php

namespace App\Command;

use App\Service\Sportmonks\Api\SportsmonkApiGateway;
use App\Service\Sportmonks\Api\SportsmonkService;
use App\Service\Sportmonks\Content\Fixture\SpmFixtureService;
use App\Service\Sportmonks\Content\League\SpmLeagueService;
use App\Service\Sportmonks\Content\Season\Data\SpmSeasonData;
use App\Service\Sportmonks\Content\Season\Event\CalculateFixtureAmountForSeasonMessage;
use App\Service\Sportmonks\Content\Season\SpmSeasonService;
use App\Service\Sportmonks\Content\Standing\SpmStandingService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsCommand(
    name: 'seasons:fixture:expectation',
    description: 'Add a short description for your command',
)]
class SeasonsFixtureExpectationCommand extends Command
{


    public function __construct(
        private readonly MessageBusInterface $bus,
        private readonly SpmSeasonService $seasonService,
        private readonly SportsmonkService $sportsmonkService,
        private readonly SpmFixtureService $fixtureService,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addOption('local', 'l', InputOption::VALUE_NONE, 'Dev mode');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $seasons = $this->seasonService->findBy([]);

        if ($input->getOption('local')){
            $i = 0;
            while ($i <= count($seasons) -1){
                $this->sportsmonkService->calculateExpectedFixtureAmountForSeason($seasons[$i]);
                $i++;
            }
            return Command::SUCCESS;
        }


        foreach ($seasons as $season){
            $message = new CalculateFixtureAmountForSeasonMessage($season);
            $this->bus->dispatch($message);
        }

        return Command::SUCCESS;
    }
}
