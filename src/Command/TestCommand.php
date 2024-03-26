<?php

namespace App\Command;

use App\Service\Betano\Api\BetanoApiGateway;
use App\Service\Tipico\Content\Simulator\SimulatorService;
use App\Service\Tipico\TipicoBetSimulationService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsCommand(
    name: 'test',
    description: 'Add a short description for your command',
)]
class TestCommand extends Command
{


    public function __construct(
        private readonly TipicoBetSimulationService $betSimulationService,
        private readonly SimulatorService $simulatorService,
        private readonly MessageBusInterface $messageBus,
        private readonly BetanoApiGateway $apiGateway
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
        $response = $this->apiGateway->getNextDailyMatchEvents();
        dd($response);

//        $sim = $this->simulatorService->findBy(['id' => 80])[0];
//        $this->betSimulationService->simulate($sim);

//        $this->messageBus->dispatch(new InitSimulatorProcessingMessage(SimulatorProcessBulk::OVER_UNDER_SIMULATORS));
        return Command::SUCCESS;
    }
}
