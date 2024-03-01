<?php
declare(strict_types=1);

namespace App\Service\Tipico;

use Symfony\Component\Notifier\Bridge\Telegram\TelegramOptions;
use Symfony\Component\Notifier\ChatterInterface;
use Symfony\Component\Notifier\Message\ChatMessage;

/**
 * @author Wolfgang Hinzmann <wolfgang.hinzmann@doccheck.com>
 * @license 2024 DocCheck Community GmbH
 */
class TelegramMessageService
{
    private const CHAT_ID = '704862488';

    public function __construct(private readonly ChatterInterface $chatter)
    {
    }

    public function sendMessageToTelegramFeed(string $message): void
    {
        $chatMessage = new ChatMessage($message);

        $telegramOptions = (new TelegramOptions())
            ->chatId(self::CHAT_ID)
            ->parseMode('MarkdownV2')
            ->disableWebPagePreview(true);

        $chatMessage->options($telegramOptions);

        $this->chatter->send($chatMessage);
    }
}
