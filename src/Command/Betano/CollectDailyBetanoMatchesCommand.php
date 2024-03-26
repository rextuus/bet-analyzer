<?php

namespace App\Command\Betano;

use App\Service\Betano\Content\BetanoSettings\BetanoSettingsService;
use App\Service\Betano\Message\CollectBetanoFixturesMessage;
use App\Service\Tipico\TelegramMessageService;
use DateTime;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsCommand(
    name: 'CollectDailyBetanoMatches',
    description: 'Add a short description for your command',
)]
class CollectDailyBetanoMatchesCommand extends Command
{
    public function __construct(
        private BetanoSettingsService $betanoSettingsService,
        private MessageBusInterface $messageBus,
        private TelegramMessageService $telegramMessageService
    )
    {
        parent::__construct();
    }

    protected function configure(): void
    {

    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $betanoSettings = $this->betanoSettingsService->getDefaultSettings();

        if(!$betanoSettings->isCollectionEnabled()){
            return Command::SUCCESS;
        }

        $currentTime = (new DateTime())->getTimestamp() * 1000;

        $time = (new DateTime())->format('d/m/y H:i');
        if($currentTime < $betanoSettings->getExpectedExecutionTime()){
            $message = sprintf('Betano collector still running at %s', $time);
            $this->telegramMessageService->sendMessageToTelegramFeed($message);
            return Command::SUCCESS;
        }

        $message = sprintf('Restart betano collector at %s', $time);
        $this->telegramMessageService->sendMessageToTelegramFeed($message);

        $message = new CollectBetanoFixturesMessage();
        $this->messageBus->dispatch($message);

        return Command::SUCCESS;
    }
}
