<?php

namespace App\Command\Spm;

use App\Service\Sportmonks\Content\League\SpmLeagueService;
use App\Service\Sportmonks\Content\Season\Data\SpmSeasonData;
use App\Service\Sportmonks\Content\Season\SpmSeasonService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'variants:init:description',
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
