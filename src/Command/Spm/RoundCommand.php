<?php

namespace App\Command\Spm;

use App\Service\Sportmonks\Api\SportsmonkService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'rounds:init',
    description: 'Add a short description for your command',
)]
class RoundCommand extends Command
{


    public function __construct(
        private readonly SportsmonkService $sportsmonkService,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('page', InputArgument::OPTIONAL, 'Start page');
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
