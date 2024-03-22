<?php
declare(strict_types=1);

namespace App\Service\Tipico\Api\Response;

use App\Service\Evaluation\BetOn;
use App\Service\Tipico\Content\TipicoBet\Data\TipicoBetData;
use App\Service\Tipico\Content\TipicoOdd\BothTeamsScoreOdd\Data\TipicoBothTeamsScoreOddData;
use App\Service\Tipico\Content\TipicoOdd\HeadToHeadOdd\Data\TipicoHeadToHeadOddData;
use App\Service\Tipico\Content\TipicoOdd\OverUnderOdd\Data\TipicoOverUnderOddData;

/**
 * @author Wolfgang Hinzmann <wolfgang.hinzmann@doccheck.com>
 * @license 2024 DocCheck Community GmbH
 */
class TipicoDailyMatchesResponse
{
    private const KEY_BASE = 'UPCOMING';
    private const KEY_EVENTS_BY_SPORTS = 'eventsBySport';
    private const KEY_MATCH_ODD_GROUPS = 'matchOddGroups';
    private const KEY_EVENTS = 'events';
    private const KEY_SOCCER = 'soccer';
    private const KEY_TEAM_1 = 'team1';
    private const KEY_TEAM_2 = 'team2';

    private const KEY_TEAM_ID_1 = 'team1Id';
    private const KEY_TEAM_ID_2 = 'team2Id';
    private const KEY_EVENT_START_TIME = 'eventStartTime';

    private const KEY_ODD_STANDARD = 'standard';
    private const KEY_POINTS_MORE_LESS_THAN = 'points-more-less-than';
    private const KEY_BOTH_TEAMS_SCORE = 'score-both';
    private const KEY_HEAD_TO_HEAD = 'head-to-head';
    private const KEY_HEAD_TO_HEAD_CAPTION_HOME = "1";
    private const KEY_HEAD_TO_HEAD_CAPTION_AWAY = "2";
    private const KEY_BOTH_TEAMS_SCORE_CAPTION_YES = 'J';
    private const KEY_BOTH_TEAMS_SCORE_CAPTION_NO = 'N';
    private const KEY_FIXED_PARAM_TEXT = 'fixedParamText';
    private const KEY_ODD_RESULTS = 'results';
    private const KEY_ODD_CAPTION = 'caption';
    private const KEY_QUOTE_FLOAT_VALUE = 'quoteFloatValue';

    private const NON_STARTED_SCORE_DEFAULT = -1;


    /**
     * @var array<TipicoBetData> $matches
     */
    private array $matches = [];

    /**
     * @var array<TipicoOverUnderOddData> $overUnderOdds
     */
    private array $overUnderOdds = [];

    /**
     * @var array<TipicoBothTeamsScoreOddData> $overUnderOdds
     */
    private array $bothTeamsScoreOdds = [];

    /**
     * @var array<TipicoHeadToHeadOddData> $overUnderOdds
     */
    private array $headToHeadOdds = [];

    /**
     * @var array<string, mixed> $decodedResponseBody
     */
    private array $decodedResponseBody;

    /**
     * @param array<string, mixed> $decodedResponseBody
     */
    public function __construct(array $decodedResponseBody)
    {
        $this->decodedResponseBody = $decodedResponseBody;
    }

    public function parseResponse(): void
    {
        $matchIds = $this->decodedResponseBody[self::KEY_BASE][self::KEY_EVENTS_BY_SPORTS][self::KEY_SOCCER];
        $events = $this->decodedResponseBody[self::KEY_BASE][self::KEY_EVENTS];
        $odds = $this->decodedResponseBody[self::KEY_BASE][self::KEY_MATCH_ODD_GROUPS];

        foreach ($matchIds as $matchId) {
            if (array_key_exists((string)$matchId, $odds)) {
                $odd = $odds[$matchId];

                $data = $this->getEmptyDataInstance();
                $basicInfoAdded = $this->addBasicInfo($events, (int)$matchId, $data);
                $standardOdds = $this->parseStandardOdds($odd, $data);
                $overUnderOdds = $this->parseOverUnderOdds($odd, (int)$matchId);
                $parseBothTeamsScoreOdds = $this->parseBothTeamsScoreOdds($odd, (int)$matchId);
                $parseBothTeamsScoreOdds = $this->parseHeadToHeadOdds($odd, (int)$matchId);

                if ($basicInfoAdded && $standardOdds) {
                    $this->matches[] = $data;
                }
            }
        }
    }

    private function parseStandardOdds(array $odd, TipicoBetData $data): bool
    {
        if (array_key_exists(self::KEY_ODD_STANDARD, $odd)) {
            $results = $odd[self::KEY_ODD_STANDARD][0][self::KEY_ODD_RESULTS];

            $setOdds = 0;
            foreach ($results as $result) {
                if ($result[self::KEY_ODD_CAPTION] === "1") {
                    $data->setOddHome($result[self::KEY_QUOTE_FLOAT_VALUE]);
                    $setOdds++;
                }
                if ($result[self::KEY_ODD_CAPTION] === "X") {
                    $data->setOddDraw($result[self::KEY_QUOTE_FLOAT_VALUE]);
                    $setOdds++;
                }
                if ($result[self::KEY_ODD_CAPTION] === "2") {
                    $data->setOddAway($result[self::KEY_QUOTE_FLOAT_VALUE]);
                    $setOdds++;
                }
            }

            return $setOdds === 3;
        }

        return false;
    }

    private function parseOverUnderOdds(array $odd, int $matchId): bool
    {
        if (array_key_exists(self::KEY_POINTS_MORE_LESS_THAN, $odd)) {
            $overUnderOdds = $odd[self::KEY_POINTS_MORE_LESS_THAN];

            foreach ($overUnderOdds as $overUnderOdd) {
                $oddData = new TipicoOverUnderOddData();
                $oddData->setTipicoBetId($matchId);

                $results = $overUnderOdd[self::KEY_ODD_RESULTS];
                $target = (float)$overUnderOdd[self::KEY_FIXED_PARAM_TEXT];
                $oddData->setTarget($target);

                foreach ($results as $result) {
                    $caption = $result[self::KEY_ODD_CAPTION];
                    $value = $result[self::KEY_QUOTE_FLOAT_VALUE];
                    if ($caption === '+') {
                        $oddData->setOver($value);
                    }
                    if ($caption === '-') {
                        $oddData->setUnder($value);
                    }

                }
                $this->overUnderOdds[] = $oddData;
            }
        }

        if (count($this->overUnderOdds) === 5) {
            return true;
        }

        return false;
    }

    private function parseBothTeamsScoreOdds(array $odd, int $matchId): bool
    {
        if (array_key_exists(self::KEY_BOTH_TEAMS_SCORE, $odd)) {
            $bothTeamsScoreOdd = $odd[self::KEY_BOTH_TEAMS_SCORE][0];

            $oddData = new TipicoBothTeamsScoreOddData();
            $oddData->setTipicoBetId($matchId);

            $results = $bothTeamsScoreOdd[self::KEY_ODD_RESULTS];
            foreach ($results as $result) {
                if ($result[self::KEY_ODD_CAPTION] === self::KEY_BOTH_TEAMS_SCORE_CAPTION_YES) {
                    $oddData->setConditionTrueValue($result[self::KEY_QUOTE_FLOAT_VALUE]);
                }
                if ($result[self::KEY_ODD_CAPTION] === self::KEY_BOTH_TEAMS_SCORE_CAPTION_NO) {
                    $oddData->setConditionFalseValue($result[self::KEY_QUOTE_FLOAT_VALUE]);
                }
            }

            $this->bothTeamsScoreOdds[] = $oddData;
        }

        if (count($this->bothTeamsScoreOdds) === 1) {
            return true;
        }

        return false;
    }

    private function parseHeadToHeadOdds(array $odd, int $matchId): bool
    {
        if (array_key_exists(self::KEY_HEAD_TO_HEAD, $odd)) {
            $headToHeadOdd = $odd[self::KEY_HEAD_TO_HEAD][0];

            $oddData = new TipicoHeadToHeadOddData();
            $oddData->setTipicoBetId($matchId);

            $results = $headToHeadOdd[self::KEY_ODD_RESULTS];
            foreach ($results as $result) {
                if ($result[self::KEY_ODD_CAPTION] === self::KEY_HEAD_TO_HEAD_CAPTION_HOME) {
                    $oddData->setHomeTeamValue($result[self::KEY_QUOTE_FLOAT_VALUE]);
                }
                if ($result[self::KEY_ODD_CAPTION] === self::KEY_HEAD_TO_HEAD_CAPTION_AWAY) {
                    $oddData->setAwayTeamValue($result[self::KEY_QUOTE_FLOAT_VALUE]);
                }
            }

            $this->headToHeadOdds[] = $oddData;
        }

        if (count($this->headToHeadOdds) === 1) {
            return true;
        }

        return false;
    }

    private function addBasicInfo(array $events, int $matchId, TipicoBetData $data): bool
    {
        if (array_key_exists((string)$matchId, $events)) {
            $event = $events[$matchId];

            $data->setTipicoId($matchId);
            $data->setHomeTeamName($event[self::KEY_TEAM_1]);
            $data->setAwayTeamName($event[self::KEY_TEAM_2]);
            $data->setTipicoHomeTeamId($event[self::KEY_TEAM_ID_1]);
            $data->setTipicoAwayTeamId($event[self::KEY_TEAM_ID_2]);
            $data->setStartAtTimeStamp($event[self::KEY_EVENT_START_TIME]);

            return true;
        }

        return false;
    }


    private function getEmptyDataInstance(): TipicoBetData
    {
        $data = new TipicoBetData();

        $data->setEndScoreHome(self::NON_STARTED_SCORE_DEFAULT);
        $data->setEndScoreAway(self::NON_STARTED_SCORE_DEFAULT);
        $data->setFinished(false);
        $data->setResult(BetOn::DRAW);

        return $data;
    }

    public function getMatches(): array
    {
        return $this->matches;
    }

    public function getOverUnderOdds(): array
    {
        return $this->overUnderOdds;
    }

    public function getBothTeamsScoreOdds(): array
    {
        return $this->bothTeamsScoreOdds;
    }

    public function getHeadToHeadOdds(): array
    {
        return $this->headToHeadOdds;
    }
}
