<?php
declare(strict_types=1);

namespace App\Service\Tipico;

use DateTime;
use Symfony\Component\Notifier\Bridge\Telegram\TelegramOptions;
use Symfony\Component\Notifier\ChatterInterface;
use Symfony\Component\Notifier\Message\ChatMessage;

class TelegramMessageService
{
    private const CHAT_ID = '704862488';

    public function __construct(private readonly ChatterInterface $chatter, private readonly bool $telegramEnabled)
    {
    }

    public function sendMessageToTelegramFeed(string $message): void
    {
        if(!$this->telegramEnabled){
            return;
        }

        if ($this->isSilentTime()){
            return;
        }

        $chatMessage = new ChatMessage($message);

        $telegramOptions = (new TelegramOptions())
            ->chatId(self::CHAT_ID)
            ->parseMode('MarkdownV2')
            ->disableWebPagePreview(true);

        $chatMessage->options($telegramOptions);

        $this->chatter->send($chatMessage);
    }

    private function isSilentTime(): bool
    {
        $current_time = new DateTime();
        $current_hour = $current_time->format('G'); // 24-hour format without leading zeros
        $current_minute = $current_time->format('i'); // Minutes without leading zeros

        $weekday_start_time = new DateTime('00:30');
        $weekday_end_time = new DateTime('09:00');

        $weekend_start_time = new DateTime('00:30');
        $weekend_end_time = new DateTime('11:00');

        $is_weekday = in_array($current_time->format('N'), [1, 2, 3, 4, 5]); // Monday to Friday (1 to 5)
        $is_weekend = !$is_weekday;

        if (($is_weekday && $current_time >= $weekday_start_time && $current_time <= $weekday_end_time) ||
            ($is_weekend && $current_time >= $weekend_start_time && $current_time <= $weekend_end_time)) {
            return true;
        } else {
            return false;

        }
    }
}
