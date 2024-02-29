<?php
declare(strict_types=1);

namespace App\Service\Evaluation\OutcomeTestCalculation;

/**
 * @author Wolfgang Hinzmann <wolfgang.hinzmann@doccheck.com>
 * @license 2024 DocCheck Community GmbH
 */
class TestCalculator
{
    public function getByValueRangeAndWinPercentage(float $min, float $max, int $percentage): TestBet
    {
        $bet = new TestBet();
        $bet->setIsWon($this->isWon($percentage));
        $bet->setValue($this->getValue($min, $max));

        return $bet;
    }

    private function isWon(int $percentage): bool
    {
        $random_number = mt_rand(1, 100); // Generate a random number between 1 and 100
        return $random_number <= $percentage; // Check if the random number is less than or equal to the percentage
    }

    private function getValue(float $min, float $max)
    {
        $random_int = mt_rand(1, 2); // Generate a random integer between 1 and 2
        if ($random_int === 1) {
            return $min;
        } else {
            return $max;
        }
    }
}
