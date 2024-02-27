<?php

namespace App\Command;

use App\Entity\BetRowOddFilter;
use App\Entity\SpmFixture;
use App\Service\Sportmonks\Content\Fixture\SpmFixtureService;
use App\Service\Sportmonks\Content\Odd\SpmOddService;
use App\Service\Sportmonks\Content\Season\SpmSeasonService;
use App\Service\Sportmonks\Content\Season\Statistic\SeasonStatisticService;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'Test2',
    description: 'Add a short description for your command',
)]
class Test2Command extends Command
{


    public function __construct(
        private SpmSeasonService $seasonService,
        private SpmFixtureService $fixtureService,
        private SeasonStatisticService $seasonStatisticService,
        private SpmOddService $oddService,
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

        $statistics = $this->seasonStatisticService->findBy(['manuallyConfirmed' => true]);
        $fixtures = [];
        foreach ($statistics as $statistic) {

            $f = $this->fixtureService->findBySeasonOrderedByTime(['seasonApiId' => $statistic->getSeasonApiId()]);
            $fixtures = array_merge($fixtures, $f);
        }

        usort(
            $fixtures,
            function (SpmFixture $a, SpmFixture $b){
                return $a->getStartingAtTimestamp() >$b->getStartingAtTimestamp();
            }
        );

        dump($fixtures[0]->getStartingAtTimestamp());
        dd($fixtures[array_key_last($fixtures)]->getStartingAtTimestamp());
//        $filter = new BetRowOddFilter();
//
//        $this->oddService->findByFixtureAndVariant();


        $io->success('You have a new command! Now make it your own! Pass --help to see your options.');

        return Command::SUCCESS;
    }
}
