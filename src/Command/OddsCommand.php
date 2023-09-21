<?php

namespace App\Command;

use App\Service\Sportmonks\Api\SportsmonkService;
use App\Service\Sportmonks\Content\Fixture\SpmFixtureService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'odds:init',
    description: 'Add a short description for your command',
)]
class OddsCommand extends Command
{


    public function __construct(
        private readonly SportsmonkService $sportsmonkService,
        private readonly SpmFixtureService $fixtureService,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('fixtureApiId', InputArgument::OPTIONAL, 'Start page');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $fixtureId = null;
        if ($input->getArgument('fixtureApiId')){
            $fixtureId = $input->getArgument('fixtureApiId');
        }

        if (!is_null($fixtureId)){
            $this->sportsmonkService->storeOddForFixture($fixtureId);
            return Command::SUCCESS;
        }

        $undecorated = $this->fixtureService->findBy(['oddDecorated' => false]);
        $this->sportsmonkService->storeOddForFixture($undecorated[0]->getApiId());
        return Command::SUCCESS;
    }
}
