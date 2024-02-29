<?php
declare(strict_types=1);

namespace App\Service\Tipico\Api\Response;

use App\Service\Evaluation\BetOn;
use App\Service\Tipico\Content\TipicoBet\Data\TipicoBetData;

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
    private const KEY_ODD_RESULTS = 'results';
    private const KEY_ODD_CAPTION = 'caption';
    private const KEY_QUOTE_FLOAT_VALUE = 'quoteFloatValue';

    private const NON_STARTED_SCORE_DEFAULT = -1;


    /**
     * @var array<TipicoBetData> $matches
     */
    private array $matches = [];

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
            $data = $this->getEmptyDataInstance();
            $basicInfoAdded = $this->addBasicInfo($events, (int)$matchId, $data);
            $oddInfoAdded = $this->addOddInfo($odds, (int)$matchId, $data);

            if ($basicInfoAdded && $oddInfoAdded) {
                $this->matches[] = $data;
            }
        }
    }

    private function addOddInfo(array $odds, int $matchId, TipicoBetData $data): bool
    {
        if (array_key_exists((string)$matchId, $odds)) {
            $odd = $odds[$matchId];
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
}
