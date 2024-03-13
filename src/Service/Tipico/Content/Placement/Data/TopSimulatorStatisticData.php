<?php
declare(strict_types=1);

namespace App\Service\Tipico\Content\Placement\Data;

use DateTime;
use PHPUnit\Util\Exception;

/**
 * @author Wolfgang Hinzmann <wolfgang.hinzmann@doccheck.com>
 * @license 2024 DocCheck Community GmbH
 */
class TopSimulatorStatisticData
{
    private DateTime $from;
    private DateTime $until;

    /**
     * @var int[]
     */
    private array $simulatorIds = [];

    /**
     * @var float[]
     */
    private array $changeVolumes = [];

    /**
     * @var int[]
     */
    private array $madeBets = [];

    /**
     * @var string[]
     */
    private array $simulatorIdents = [];

    /**
     * @var int[]
     */
    private array $ranks = [];

    public function getFrom(): DateTime
    {
        return $this->from;
    }

    public function setFrom(DateTime $from): TopSimulatorStatisticData
    {
        $this->from = $from;
        return $this;
    }

    public function getUntil(): DateTime
    {
        return $this->until;
    }

    public function setUntil(DateTime $until): TopSimulatorStatisticData
    {
        $this->until = $until;
        return $this;
    }

    public function getSimulatorIds(): array
    {
        return $this->simulatorIds;
    }

    public function setSimulatorIds(array $simulatorIds): TopSimulatorStatisticData
    {
        $this->simulatorIds = $simulatorIds;
        return $this;
    }

    public function getChangeVolumes(): array
    {
        return $this->changeVolumes;
    }

    public function setChangeVolumes(array $changeVolumes): TopSimulatorStatisticData
    {
        $this->changeVolumes = $changeVolumes;
        return $this;
    }

    public function getMadeBets(): array
    {
        return $this->madeBets;
    }

    public function setMadeBets(array $madeBets): TopSimulatorStatisticData
    {
        $this->madeBets = $madeBets;
        return $this;
    }

    public function getSimulatorIdents(): array
    {
        return $this->simulatorIdents;
    }

    public function setSimulatorIdents(array $simulatorIdents): TopSimulatorStatisticData
    {
        $this->simulatorIdents = $simulatorIdents;
        return $this;
    }

    public function getRanks(): array
    {
        return $this->ranks;
    }

    public function setRanks(array $ranks): TopSimulatorStatisticData
    {
        $this->ranks = $ranks;
        return $this;
    }

    public function calculateRanks(): TopSimulatorStatisticData
    {
        $changes = $this->getChangeVolumes();
        asort($changes);

        $ranks = [];
        $rank = 0;
        foreach ($changes as $key => $value) {
            $ranks[$key] = $rank;
            $rank++;
        }

        $this->setRanks($ranks);

        return $this;
    }

    public function addValueByArrayKey(string $key, int|float|string|null $value): TopSimulatorStatisticData
    {
        match ($key) {
            'id' => $this->simulatorIds[] = $value,
            'identifier' => $this->simulatorIdents[] = $value,
            'changeVolume' => $this->changeVolumes[] = $value,
            'madeBets' => $this->madeBets[] = $value,
            'default' => throw new Exception('TopSimulatorStatisticData cant handle key: ' . $key),
        };

        return $this;
    }
}
