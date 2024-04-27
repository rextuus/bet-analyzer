<?php

namespace App\Command;

use App\Service\BettingProvider\Betano\Api\BetanoApiGateway;
use App\Service\BettingProvider\Betano\Content\BetanoBet\BetanoBetService;
use App\Service\BettingProvider\BettingProviderBackupFile\BackupProcessorTargetEntityServiceProvider;
use App\Service\BettingProvider\Bwin\Api\Response\DailyMatchEventResponse;
use App\Service\Tipico\Content\Simulator\SimulatorService;
use App\Service\Tipico\TipicoBetSimulationService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
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
        private readonly BetanoApiGateway $apiGateway,
        private readonly BetanoBetService $betanoBetService,
        private readonly BackupProcessorTargetEntityServiceProvider $serviceProvider,
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
//        dd($this->serviceProvider->getServiceByBettingProvider(BettingProvider::BWIN));
//        $response = $this->betanoBetService->storeBetanoBetsFromBackupFile('public/backups/betano/04_24_2024/2024-04-24_12-41-40.json');
//        $response = $this->apiGateway->getNextDailyMatchEvents();
        $response = null;
        $filesystem = new Filesystem();
        if ($filesystem->exists('bwin_test_response.json')) {
            $jsonData = file_get_contents('bwin_test_response.json');

            $response = json_decode($jsonData, true, 512, JSON_THROW_ON_ERROR);

            $response = new DailyMatchEventResponse($response);
            $response->parseResponse();
        }
//        dd($response->getDataObjects());

//        $sim = $this->simulatorService->findBy(['id' => 80])[0];
//        $this->betSimulationService->simulate($sim);

//        $this->messageBus->dispatch(new InitSimulatorProcessingMessage(SimulatorProcessBulk::OVER_UNDER_SIMULATORS));
        return Command::SUCCESS;
    }
}
