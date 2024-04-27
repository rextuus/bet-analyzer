<?php

namespace App\Command\Spm;

use App\Entity\SpmFixture;
use App\Service\Evaluation\Message\InitOddOutcomeMessage;
use App\Service\Sportmonks\Content\Fixture\SpmFixtureService;
use App\Service\Sportmonks\Content\Odd\SpmOddService;
use App\Service\Sportmonks\Content\Season\SpmSeasonService;
use App\Service\Sportmonks\Content\Season\Statistic\SeasonStatisticService;
use App\Service\Statistic\Content\OddOutcome\OutcomeCalculator;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
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
//        $message->setFixtureIds([6781]);
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

            $fixtureIds[] = $fixture->getApiId();
            if ($counter % 50 === 0) {
                $message = new InitOddOutcomeMessage();
                $message->setFixtureIds($fixtureIds);
                $this->messageBus->dispatch($message);

                $fixtureIds = [];
            }
            $counter++;
//            $message = new InitOddOutcomeMessage();
//
//            $message->setFixtureIds([$fixture->getId()]);
//            $this->messageBus->dispatch($message);
        }

        $message = new InitOddOutcomeMessage();
        $message->setFixtureIds($fixtureIds);
        $this->messageBus->dispatch($message);


        return Command::SUCCESS;
    }
}
