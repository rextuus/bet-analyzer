<?php
declare(strict_types=1);

namespace App\Service\BettingProvider\Betano\Api\Response;

use App\Service\BettingProvider\Betano\Content\BetanoBet\Data\BetanoBetData;
use App\Service\BettingProvider\BettingProviderBackupFile\BettingProviderApiResponseInterface;

/**
 * @author Wolfgang Hinzmann <wolfgang.hinzmann@doccheck.com>
 * @license 2024 DocCheck Community GmbH
 */
class DailyMatchEventResponse implements BettingProviderApiResponseInterface
{
    private const KEY_DATA = 'data';
    private const KEY_BLOCKS = 'blocks';
    private const KEY_EVENTS = 'events';
    private const KEY_PARTICIPANTS = 'participants';
    private const KEY_PARTICIPANTS_NAME = 'name';

    private const KEY_URL = 'url';
    private const KEY_BET_RADAR_ID = 'betRadarId';
    private const KEY_MARKETS = 'markets';
    private const KEY_MARKETS_NAME = 'name';
    private const KEY_MARKETS_NAME_THREE_WAY = 'Endergebnis';
    private const KEY_MARKETS_SELECTIONS = 'selections';
    private const KEY_MARKETS_SELECTIONS_NAME = 'name';
    private const KEY_MARKETS_SELECTIONS_PRICE = 'price';
    private const KEY_MARKETS_SELECTIONS_HOME = '1';
    private const KEY_MARKETS_SELECTIONS_DRAW = 'X';
    private const KEY_MARKETS_SELECTIONS_AWAY = '2';

    private const KEY_START_TIME = 'startTime';
    private const KEY_ID = 'id';

    /**
     * @var array<string, mixed>
     */
    private array $rawData;

    /**
     * @var BetanoBetData[]
     */
    private array $betanoBetDataObjects;

    /**
     * @param array<string, mixed> $rawData
     */
    public function __construct(array $rawData)
    {
        $this->rawData = $rawData;
    }

    public function parseResponse(): void
    {
        $events = $this->rawData[self::KEY_DATA][self::KEY_BLOCKS][0][self::KEY_EVENTS];
        $betanoBetData = [];

        foreach ($events as $event) {
            $data = new BetanoBetData();
            $data->setBetanoId((int) $event[self::KEY_ID]);
            $data->setStartAtTimeStamp((int) $event[self::KEY_START_TIME]);
            $data->setUrl($event[self::KEY_URL]);
            $data->setSportRadarId($event[self::KEY_BET_RADAR_ID]);

            $participants = $event[self::KEY_PARTICIPANTS];
            $data->setHomeTeamName($participants[0][self::KEY_PARTICIPANTS_NAME]);
            $data->setAwayTeamName($participants[1][self::KEY_PARTICIPANTS_NAME]);

            $markets = $event[self::KEY_MARKETS];
            foreach ($markets as $market) {
                if ($market[self::KEY_MARKETS_NAME] === self::KEY_MARKETS_NAME_THREE_WAY) {
                    $selections = $market[self::KEY_MARKETS_SELECTIONS];

                    foreach ($selections as $selection) {
                        if ($selection[self::KEY_MARKETS_SELECTIONS_NAME] === self::KEY_MARKETS_SELECTIONS_HOME) {
                            $data->setOddHome(
                                (float)$selection[self::KEY_MARKETS_SELECTIONS_PRICE]
                            );
                        }
                        if ($selection[self::KEY_MARKETS_SELECTIONS_NAME] === self::KEY_MARKETS_SELECTIONS_DRAW) {
                            $data->setOddDraw(
                                (float)$selection[self::KEY_MARKETS_SELECTIONS_PRICE]
                            );
                        }
                        if ($selection[self::KEY_MARKETS_SELECTIONS_NAME] === self::KEY_MARKETS_SELECTIONS_AWAY) {
                            $data->setOddAway(
                                (float)$selection[self::KEY_MARKETS_SELECTIONS_PRICE]
                            );
                        }
                    }
                }
            }

            $betanoBetData[] = $data;
        }

        $this->betanoBetDataObjects = $betanoBetData;
    }

    public function getDataObjects(): array
    {
        return $this->betanoBetDataObjects;
    }
}
