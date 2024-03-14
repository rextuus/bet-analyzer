<?php

namespace App\Command;

use App\Service\Tipico\Api\Response\TipicoDailyMatchesResponse;
use App\Service\Tipico\Api\Response\TipicoMatchResultResponse;
use App\Service\Tipico\Api\TipicoApiGateway;
use App\Service\Tipico\Content\Simulator\SimulatorService;
use App\Service\Tipico\SimulationProcessors\SimulationStrategyProcessorProvider;
use App\Service\Tipico\TelegramMessageService;
use App\Service\Tipico\TipicoBetSimulationService;
use GuzzleHttp\Client;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'TestTelegram',
    description: 'Add a short description for your command',
)]
class TestTelegramCommand extends Command
{
    public function __construct(
        private TelegramMessageService $telegramMessageService,
    )
    {
        parent::__construct();
    }

    protected function configure(): void
    {
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->telegramMessageService->sendMessageToTelegramFeed("Test");

        return Command::SUCCESS;
    }
}
