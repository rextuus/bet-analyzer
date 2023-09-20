<?php

namespace App\Command;

use App\Service\Sportmonks\Api\SportsmonkApiGateway;
use App\Service\Sportmonks\Api\SportsmonkService;
use App\Service\Sportmonks\Content\League\SpmLeagueService;
use App\Service\Sportmonks\Content\Season\Data\SpmSeasonData;
use App\Service\Sportmonks\Content\Season\SpmSeasonService;
use App\Service\Sportmonks\Content\Standing\SpmStandingService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'seasons:init:description',
    description: 'Add a short description for your command',
)]
class SeasonsInitDescriptionCommand extends Command
{


    public function __construct(
        private readonly SpmSeasonService $seasonService,
        private readonly SpmLeagueService $leagueService,
    )
    {
        parent::__construct();
    }

    protected function configure(): void
    {
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $seasons = $this->seasonService->findBy(['displayName' => null]);
        foreach ($seasons as $season) {
            $data = (new SpmSeasonData())->initFromEntity($season);

            $league = $this->leagueService->findById($season->getLeagueApiId());
            if (!$league){
                dump($season);
                continue;
            }
            $leagueName = sprintf(
                '(%d) %s - %s %s',
                $season->getLeagueApiId(),
                $league->getName(),
                $league->getShort(),
                $season->getName(),
            );

            $data->setDisplayName($leagueName);
            $this->seasonService->update($season, $data);
        }

        return Command::SUCCESS;
    }
}
