<?php
declare(strict_types=1);

namespace App\Service\Tipico\Api\Response;

use App\Service\Evaluation\BetOn;


class TipicoMatchResultResponse
{
    private const KEY_BASE = 'event';
    private const KEY_POINT_SCORE = 'fulltimeScore';
    private const KEY_ENDED = 'ended';
    private const KEY_COUNT_1 = 'count1';
    private const KEY_COUNT_2 = 'count2';

    /**
     * @var array<string, mixed> $decodedResponseBody
     */
    private array $decodedResponseBody;

    private int $homeGoals = 0;
    private int $awayGoals = 0;
    private BetOn $result = BetOn::DRAW;
    private bool $gameIsFinished = false;

    /**
     * @param array<string, mixed> $decodedResponseBody
     */
    public function __construct(array $decodedResponseBody)
    {
        $this->decodedResponseBody = $decodedResponseBody;
    }

    public function parseResponse(): void
    {
        $event = $this->decodedResponseBody[self::KEY_BASE];

        if (!array_key_exists(self::KEY_POINT_SCORE, $event)) {
            return;
        }

        if (!$event[self::KEY_ENDED]) {
            return;
        }

        $score = $this->decodedResponseBody[self::KEY_BASE][self::KEY_POINT_SCORE];

        $home = $score[self::KEY_COUNT_1];
        $away = $score[self::KEY_COUNT_2];

        if ($home > $away) {
            $this->result = BetOn::HOME;
        }

        if ($away > $home) {
            $this->result = BetOn::AWAY;
        }

        $this->homeGoals = (int)$home;
        $this->awayGoals = (int)$away;
        $this->gameIsFinished = true;
    }

    public function getHomeGoals(): int
    {
        return $this->homeGoals;
    }

    public function getAwayGoals(): int
    {
        return $this->awayGoals;
    }

    public function getResult(): BetOn
    {
        return $this->result;
    }

    public function isGameIsFinished(): bool
    {
        return $this->gameIsFinished;
    }
}
