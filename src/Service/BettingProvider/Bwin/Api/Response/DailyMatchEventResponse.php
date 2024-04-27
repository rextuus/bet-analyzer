<?php

declare(strict_types=1);

namespace App\Service\BettingProvider\Bwin\Api\Response;

use App\Service\BettingProvider\BettingProviderBackupFile\BettingProviderApiResponseInterface;
use App\Service\BettingProvider\Bwin\Content\BwinBet\Data\BwinBetData;
use DateTime;
use Exception;

/**
 * @author Wolfgang Hinzmann <wolfgang.hinzmann@doccheck.com>
 * @license 2024 DocCheck Community GmbH
 */
class DailyMatchEventResponse implements BettingProviderApiResponseInterface
{
    private const KEY_FIXTURES = 'fixtures';
    private const KEY_ID = 'id';
    private const KEY_OPTION_MARKETS = 'optionMarkets';
    private const KEY_PARTICIPANTS = 'participants';
    private const KEY_PARTICIPANTS_NAME = 'name';
    private const KEY_PARTICIPANTS_NAME_VALUE = 'value';
    private const KEY_ADDONS = 'addons';
    private const KEY_ADDONS_BET_RADAR = 'betRadar';
    private const KEY_START_DATE = 'startDate';
    private const KEY_MARKETS_NAME_THREE_WAY = 'Spielresultat';
    private const KEY_MARKETS_NAME = 'name';
    private const KEY_MARKETS_NAME_VALUE = 'value';
    private const KEY_MARKETS_OPTIONS = 'options';
    private const KEY_MARKETS_OPTIONS_PRICE = 'price';
    private const KEY_MARKETS_OPTIONS_PRICE_ODDS = 'odds';

    /**
     * @var array<string, mixed>
     */
    private array $rawData;

    /**
     * @var BwinBetData[]
     */
    private array $bwinBetDataObjects;

    /**
     * @param array<string, mixed> $rawData
     */
    public function __construct(array $rawData)
    {
        $this->rawData = $rawData;
    }

    public function parseResponse(): void
    {
        $events = $this->rawData[self::KEY_FIXTURES];
        $bwinBetData = [];

        foreach ($events as $event) {
            $data = new BwinBetData();
            $data->setBwinId($event[self::KEY_ID]);
            $startTime = -1;
            try {
                $startTime = (new DateTime($event[self::KEY_START_DATE]))->getTimestamp() * 1000;
            } catch (Exception $e) {
            }
            $data->setStartAtTimeStamp($startTime);
            $data->setUrl('-');

            $betRadarId = -1;
            if (array_key_exists(self::KEY_ADDONS_BET_RADAR, $event[self::KEY_ADDONS])) {
                $betRadarId = $event[self::KEY_ADDONS][self::KEY_ADDONS_BET_RADAR];
            }
            $data->setSportRadarId($betRadarId);

            $participants = $event[self::KEY_PARTICIPANTS];
            $data->setHomeTeamName($participants[0][self::KEY_PARTICIPANTS_NAME][self::KEY_PARTICIPANTS_NAME_VALUE]);
            $data->setAwayTeamName($participants[1][self::KEY_PARTICIPANTS_NAME][self::KEY_PARTICIPANTS_NAME_VALUE]);

            $markets = $event[self::KEY_OPTION_MARKETS];
            foreach ($markets as $market) {
                if ($market[self::KEY_MARKETS_NAME][self::KEY_MARKETS_NAME_VALUE] === self::KEY_MARKETS_NAME_THREE_WAY) {
                    $options = $market[self::KEY_MARKETS_OPTIONS];
                    $data->setOddHome(
                        (float)$options[0][self::KEY_MARKETS_OPTIONS_PRICE][self::KEY_MARKETS_OPTIONS_PRICE_ODDS]
                    );
                    $data->setOddDraw(
                        (float)$options[1][self::KEY_MARKETS_OPTIONS_PRICE][self::KEY_MARKETS_OPTIONS_PRICE_ODDS]
                    );
                    $data->setOddAway(
                        (float)$options[2][self::KEY_MARKETS_OPTIONS_PRICE][self::KEY_MARKETS_OPTIONS_PRICE_ODDS]
                    );
                }
            }

            $bwinBetData[] = $data;
        }

        $this->bwinBetDataObjects = $bwinBetData;
    }

    public function getDataObjects(): array
    {
        return $this->bwinBetDataObjects;
    }
}
