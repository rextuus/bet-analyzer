<?php

namespace App\Command;

use App\Service\Evaluation\OutcomeTestCalculation\TestBet;
use App\Service\Tipico\Content\Simulator\Data\SimulatorFilterData;
use App\Service\Tipico\Content\Simulator\SimulatorService;
use App\Service\Tipico\Content\SimulatorFavoriteList\Data\SimulatorFavoriteListData;
use App\Service\Tipico\Content\SimulatorFavoriteList\SimulatorFavoriteListService;
use App\Service\Tipico\Simulation\AdditionalProcessors\Weekday;
use App\Service\Tipico\SimulatorDuplicationService;
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

    public function __construct(
        private SimulatorService $simulatorService,
        private SimulatorDuplicationService $duplicationService,
        private SimulatorFavoriteListService $favoriteListService,
    )
    {
        parent::__construct();
    }

    protected function configure(): void
    {
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
//        $day = Weekday::Friday;

        $filter = new SimulatorFilterData();
        $filter->setMaxResults(1000000);
        $simulators = $this->simulatorService->findByFilter($filter);

        foreach (Weekday::cases() as $day) {
            $output->writeln($day->name);
            $simulatorsByCash = [];
            foreach ($simulators as $simulator) {
                $cash = 0.0;
                foreach ($simulator->getTipicoPlacements() as $placement) {
                    if ($placement->getCreated()->format('N') === (string)$day->value) {
                        $cash = $cash - $placement->getInput();
                        if ($placement->isWon()) {
                            $cash = $cash + $placement->getValue();
                        }
                    }
                }
                $simulatorsByCash[$simulator->getId()] = $cash;
            }

            arsort($simulatorsByCash);

            $counter = 0;
            $newSimulators = [];
            foreach ($simulatorsByCash as $id => $cash) {
                if ($counter < 20) {
                    $simulator = $this->simulatorService->find($id);
                    $output->writeln(
                        sprintf(
                            'Copy Simulator %s with win of %.2f',
                            $simulator->getIdentifier(),
                            $simulatorsByCash[$id]
                        )
                    );
                    $newSimulators[] = $this->duplicationService->duplicateSimulatorAndLimitToWeekday($simulator, $day);
                }
                $counter++;
            }

            $favoriteListData = new SimulatorFavoriteListData();
            $favoriteListData->setIdentifier('Top_simulators_' . $day->name);
            $favoriteListData->setCreated(new \DateTime());
            $favoriteListData->setSimulators($newSimulators);


            $favoriteList = $this->favoriteListService->createByData($favoriteListData);
        }

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
