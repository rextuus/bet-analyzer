<?php

namespace App\Command;

use App\Service\Evaluation\OutcomeTestCalculation\TestBet;
use App\Service\Evaluation\OutcomeTestCalculation\TestCalculator;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'TestCalculation',
    description: 'Add a short description for your command',
)]
class TestCalculationCommand extends Command
{


    private float $value = 1.2;
    private int $percentage = 84;

    public function __construct(private TestCalculator $calculator)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $percentage = $this->percentage;

        $cashBox = 100;
        $tries = 0;

        $fibo = [1, 2, 3, 5, 8, 13, 21];
        $fiboIndex = 0;
        $highestFiboIndex = 0;
        $highestPlacement = 1.0;

        $loses = 0;
        $wins = 0;

        $minCashBox = 100.0;
        $currentPlacement = 1.0;
        while ($tries < 100) {
            $tries++;

            $bet1 = $this->calculator->getByValueRangeAndWinPercentage(1.3, 1.4, 77);
            $bet2 = $this->calculator->getByValueRangeAndWinPercentage(1.3, 1.4, 77);
            $bet3 = $this->calculator->getByValueRangeAndWinPercentage(1.4, 1.5, 71);
            $bet4 = $this->calculator->getByValueRangeAndWinPercentage(1.5, 1.6, 66);
            $bet5 = $this->calculator->getByValueRangeAndWinPercentage(1.7, 1.8, 60);
            $bet6 = $this->calculator->getByValueRangeAndWinPercentage(1.2, 1.3, 84);
            $bet7 = $this->calculator->getByValueRangeAndWinPercentage(1.3, 1.4, 77);

            $combi12 = $this->calculateBetCombination([$bet1, $bet6]);
            $combi13 = $this->calculateBetCombination([$bet1, $bet7]);
            $combi34 = $this->calculateBetCombination([$bet3, $bet4]);
//            $combi23 = $this->calculateBetCombination([$bet2, $bet3]);

            $this->evaluateBet($cashBox, $currentPlacement, $combi12);
//            $this->evaluateBet($cashBox, $currentPlacement, $combi13);
//            $this->evaluateBet($cashBox, $currentPlacement, $combi34);
//            $this->evaluateBet($cashBox, $currentPlacement, $combi13);

            // normal
//            if (!$combi12->isWon()){
//                $currentPlacement = $currentPlacement + 1.0;
//
//                if ($currentPlacement > $highestPlacement){
//                    $highestPlacement = $currentPlacement;
//                }
//            }else{
//                $currentPlacement = 1.0;
//            }

            // fibo
//            if (!$combi12->isWon()) {
//                $fiboIndex++;
//                if ($fiboIndex === count($fibo)){
//                    $fiboIndex = count(($fibo)) -1;
//                }
//                $currentPlacement = $fibo[$fiboIndex];
//
//                if ($currentPlacement > $highestPlacement) {
//                    $highestPlacement = $currentPlacement;
//                }
//            }else{
//                $currentPlacement = 1.0;
//                $fiboIndex = 0;
//            }
//
            if ($cashBox < $minCashBox) {
                $minCashBox = $cashBox;
            }
//            $this->evaluateBet($cashBox, $currentPlacement, $combi23);
        }

        dump($cashBox);
        dump($minCashBox);
        dump($highestPlacement);
//        dump($loses);
//        dump($wins);

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
