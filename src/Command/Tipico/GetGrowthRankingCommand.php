<?php

namespace App\Command\Tipico;

use App\Service\Tipico\SimulationStatisticService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Routing\RouterInterface;

#[AsCommand(
    name: 'GetGrowthRankingCommand',
    description: 'Add a short description for your command',
)]
class GetGrowthRankingCommand extends Command
{
    public function __construct(
        private readonly SimulationStatisticService $simulationStatisticService,
        private readonly RouterInterface $router,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $result = $this->simulationStatisticService->calculateAverageIncreaseScore();


        foreach ($result as $item) {
            $output->writeln(sprintf('<info>%s</info>', $item['simulator']->getIdentifier()));
            $output->writeln(sprintf('<info>%s</info>', $item['std_dev']));
            $output->writeln(sprintf('<info>%s</info>', $item['positive_growth_proportion']));
            $output->writeln(sprintf('<info>%s</info>', $item['r_squared']));
            $output->writeln(sprintf('<info>%s</info>', $item['composite_score']));
            $link = $this->router->generate('app_tipico_simulation_detail', ['simulator' => $item['simulator']->getId()]
            );
            $link = 'https://bet-analyzer.wh-company.de' . $link;
            dump($link);
        }
        return Command::SUCCESS;
    }
}
