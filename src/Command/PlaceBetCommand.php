<?php

namespace App\Command;

use App\Service\Evaluation\BetRowCalculator;
use App\Service\Evaluation\Content\BetRow\SimpleBetRow\SimpleBetRowService;
use App\Service\Evaluation\Content\BetRowOddFilter\BetRowOddFilterService;
use App\Service\Evaluation\Content\PlacedBet\BetRowVariant;
use App\Service\Evaluation\OddAccumulationVariant;
use App\Service\Sportmonks\Content\Fixture\SpmFixtureService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'bet:place',
    description: 'Add a short description for your command',
)]
class PlaceBetCommand extends Command
{


    public function __construct(
        private readonly BetRowCalculator $betRowCalculator,
        private readonly SpmFixtureService $fixtureService,
        private readonly BetRowOddFilterService $betRowOddFilterService,
        private readonly SimpleBetRowService $betRowService,
    )
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('fixtureApiId', InputArgument::REQUIRED, 'fixtureApiId to decorate');
        $this->addArgument('betRowFilterId', InputArgument::REQUIRED, 'Fitler id');
        $this->addArgument('betRowId', InputArgument::REQUIRED, 'betRow id');
        $this->addArgument('betRowVariant', InputArgument::REQUIRED, 'betRow Variant');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $betRowVariant = BetRowVariant::SimpleBetRow;
        $accumulationVariant = OddAccumulationVariant::MEDIAN;
        $wager = 1.0;
        $includeTax = true;

        $fixture = null;
        if ($input->getArgument('fixtureApiId')) {
            $fixture = $this->fixtureService->findByApiId($input->getArgument('fixtureApiId'));
        }

        $filter = null;
        if ($input->getArgument('betRowFilterId')) {
            $filter = $this->betRowOddFilterService->findById($input->getArgument('betRowFilterId'));
        }

        $betRow = null;
        if ($input->getArgument('betRowId')) {
            $betRow = $this->betRowService->findById($input->getArgument('betRowId'));
        }


        $this->betRowCalculator->placeBet(
            $fixture,
            $filter,
            $accumulationVariant,
            $betRow,
            $betRowVariant,
            $wager,
            $includeTax
        );
        return Command::SUCCESS;
    }
}
