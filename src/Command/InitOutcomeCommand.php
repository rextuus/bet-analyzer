<?php

namespace App\Command;

use App\Entity\BetRowOddFilter;
use App\Entity\SpmFixture;
use App\Service\Evaluation\Message\UpdateOddOutcomeMessage;
use App\Service\Sportmonks\Content\Fixture\SpmFixtureService;
use App\Service\Sportmonks\Content\Odd\SpmOddService;
use App\Service\Sportmonks\Content\Season\SpmSeasonService;
use App\Service\Sportmonks\Content\Season\Statistic\SeasonStatisticService;
use App\Service\Statistic\Content\OddOutcome\OutcomeCalculator;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsCommand(
    name: 'InitOutcome',
    description: 'Add a short description for your command',
)]
class InitOutcomeCommand extends Command
{


    public function __construct(
        private SpmSeasonService $seasonService,
        private SpmFixtureService $fixtureService,
        private SeasonStatisticService $seasonStatisticService,
        private SpmOddService $oddService,
        private OutcomeCalculator $outcomeCalculator,
        private MessageBusInterface $messageBus
    )
    {
        parent::__construct();
    }

    protected function configure(): void
    {

    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

//        $message = new UpdateOddOutcomeMessage();
//        $message->setFixtureIds([7626]);
//        dd($this->outcomeCalculator->calculateAll($message));

        $statistics = $this->seasonStatisticService->findBy(['manuallyConfirmed' => true]);
        $fixtures = [];
        foreach ($statistics as $statistic) {

            $f = $this->fixtureService->findBySeasonOrderedByTime(['seasonApiId' => $statistic->getSeasonApiId()]);
            $fixtures = array_merge($fixtures, $f);
        }

        usort(
            $fixtures,
            function (SpmFixture $a, SpmFixture $b) {
                return $a->getStartingAtTimestamp() > $b->getStartingAtTimestamp();
            }
        );

        $counter = 0;
        $fixtureIds = [];
        foreach ($fixtures as $fixture) {

            $fixtureIds[] = $fixture->getId();
            if ($counter % 50 === 0) {
                $message = new UpdateOddOutcomeMessage();
                $message->setFixtureIds($fixtureIds);
                $this->messageBus->dispatch($message);

                $fixtureIds = [];
            }
            $counter++;
        }

        $message = new UpdateOddOutcomeMessage();
        $message->setFixtureIds($fixtureIds);
        $this->messageBus->dispatch($message);


        return Command::SUCCESS;
    }
}
