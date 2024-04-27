<?php
declare(strict_types=1);

namespace App\Service\Statistic\Content\OddOutcome;

use App\Entity\Spm\BetRowOddFilter;
use App\Entity\Spm\OddOutcome;
use App\Entity\Spm\SpmFixture;
use App\Entity\Spm\SpmScore;
use App\Service\Evaluation\BetOn;
use App\Service\Evaluation\Message\UpdateOddOutcomeMessage;
use App\Service\Evaluation\NoScoresFoundForFixtureException;
use App\Service\Evaluation\OddVariant;
use App\Service\Sportmonks\Content\Fixture\SpmFixtureService;
use App\Service\Sportmonks\Content\Odd\SpmOddService;
use App\Service\Sportmonks\Content\Score\SpmScoreService;
use App\Service\Statistic\Content\OddOutcome\Data\OddOutcomeData;


class OutcomeCalculator
{
    public function __construct(
        private SpmOddService $oddService,
        private SpmFixtureService $fixtureService,
        private SpmScoreService $scoreService,
        private OddOutcomeService $oddOutcomeService
    )
    {
    }

    public function calculateAll(UpdateOddOutcomeMessage $updateOddOutcomeMessage): void
    {
        foreach ($updateOddOutcomeMessage->getFixtureIds() as $fixtureId){
            $this->calculate($fixtureId);
        }
    }

    public function calculate(int $fixtureApi): void
    {
        $filter = new BetRowOddFilter;
        $filter->setMin(0.0);
        $filter->setMax(30.0);
        $filter->setOddVariant(OddVariant::CLASSIC_3_WAY);
        $filter->setBetOn(BetOn::HOME);

        $fixture = $this->fixtureService->findByApiId($fixtureApi);

        if (!$fixture){
            return;
        }

        // check outcome
        $scores = $this->scoreService->findBy(['fixtureApiId' => $fixtureApi]);
        if (!count($scores)) {
            throw new NoScoresFoundForFixtureException('Fixture id: ' . $fixtureApi);
        }

        $home = 0;
        $away = 0;
        foreach ($scores as $score) {
            if ($score->getDescription() === SpmScore::SECOND_HALF && $score->getParticipant() === SpmScore::PARTICIPANT_HOME) {
                $home = $score->getGoals();
            }
            if ($score->getDescription() === SpmScore::SECOND_HALF && $score->getParticipant() === SpmScore::PARTICIPANT_AWAY) {
                $away = $score->getGoals();
            }
        }
        $result = BetOn::DRAW;

        if ($home > $away) {
            $result = BetOn::HOME;
        }
        if ($home < $away) {
            $result = BetOn::AWAY;
        }

        // odds home
        $odds = $this->oddService->findByFixtureAndVariant($fixture, $filter);

        $homeOdd = 0.0;
        foreach ($odds as $odd){
            $homeOdd = $homeOdd + $odd->getValue();
        }
        $homeOdd = $homeOdd / count($odds);

        // odds home
        $filter->setBetOn(BetOn::AWAY);
        $odds = $this->oddService->findByFixtureAndVariant($fixture, $filter);

        $awayOdd = 0.0;
        foreach ($odds as $odd){
            $awayOdd = $awayOdd + $odd->getValue();
        }
        $awayOdd = $awayOdd / count($odds);

        // odds draw
        $filter->setBetOn(BetOn::DRAW);
        $odds = $this->oddService->findByFixtureAndVariant($fixture, $filter);

        $drawOdd = 0.0;
        foreach ($odds as $odd){
            $drawOdd = $drawOdd + $odd->getValue();
        }
        $drawOdd = $drawOdd / count($odds);
        $homeOdd = round($homeOdd, 1);
        $drawOdd = round($drawOdd, 1);
        $awayOdd = round($awayOdd, 1);

        $this->addToOutcome($homeOdd, $homeOdd+0.1, BetOn::HOME, $result, $fixture);
        $this->addToOutcome($drawOdd, $drawOdd+0.1, BetOn::DRAW, $result, $fixture);
        $this->addToOutcome($awayOdd, $awayOdd+0.1, BetOn::AWAY, $result, $fixture);
    }

    private function addToOutcome(float $min, float $max, BetOn $betOn, BetOn $result, SpmFixture $fixture): OddOutcome
    {
        $outCome = $this->oddOutcomeService->findByRangeAndVariant($min, $max, $betOn);
        if ($outCome) {
            $data = (new OddOutcomeData())->initFromEntity($outCome);

            $data->setFixtureAmount($data->getFixtureAmount() + 1);
            if ($betOn === $result) {
                $data->setCorrectOutcomes($data->getCorrectOutcomes() + 1);
            }

            $data->setFixtures([$fixture]);

            $this->oddOutcomeService->update($outCome, $data);

            return $outCome;
        }

        $data = new OddOutcomeData();
        $data->setMin($min);
        $data->setMax($max);
        $data->setBetOn($betOn);

        $data->setFixtureAmount(1);

        $data->setCorrectOutcomes(0);
        if ($betOn === $result) {
            $data->setCorrectOutcomes(1);
        }

        $data->setFixtures([$fixture]);

        $outCome = $this->oddOutcomeService->createByData($data);

        return $outCome;
    }
}
