<?php

namespace App\Command;

use App\Service\Evaluation\OutcomeTestCalculation\TestBet;
use App\Service\Tipico\Content\Simulator\Data\SimulatorFilterData;
use App\Service\Tipico\Content\Simulator\SimulatorService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'WeekdayStatistic',
    description: 'Add a short description for your command',
)]
class WeekdayStatisticCommand extends Command
{

    public function __construct(private SimulatorService $simulatorService)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $day = 7;

        $filter = new SimulatorFilterData();
//        $filter->setWeekDay($day);
        $filter->setMaxResults(10000);
        $simulators = $this->simulatorService->findByFilter($filter);

        $simulatorsByCash = [];
        foreach ($simulators as $simulator) {
            $cash = 0.0;
            foreach ($simulator->getTipicoPlacements() as $placement) {
                if ($placement->getCreated()->format('N') === (string)$day) {
                    $cash = $cash - $placement->getInput();
                    if ($placement->isWon()) {
                        $cash = $cash + $placement->getValue();
                    }
                }
            }
            $simulatorsByCash[$simulator->getId()] = $cash;
        }

        arsort($simulatorsByCash);

        dd($simulatorsByCash);

        return Command::SUCCESS;
    }

    private function evaluateBet(float &$cashBox, float $currentPlacement, TestBet $bet): void
    {
        $cashBox = $cashBox - $currentPlacement;
        if ($bet->isWon()) {
            $cashBox = $cashBox + ($currentPlacement * $bet->getValue());
        }
    }

    /**
     * @param TestBet[] $bets
     */
    private function calculateBetCombination(array $bets): TestBet
    {
        $combinationIsWon = true;
        foreach ($bets as $bet) {
            if (!$bet->isWon()) {
                $combinationIsWon = false;
            }
        }

        $combinationValue = 0.0;
        if ($combinationIsWon) {
            $combinationValue = 1.0;
            foreach ($bets as $bet) {
                $combinationValue = $combinationValue * $bet->getValue();
            }
        }

        $combinedBet = new TestBet();
        $combinedBet->setIsWon($combinationIsWon);
        $combinedBet->setValue($combinationValue);
        return $combinedBet;
    }
}
