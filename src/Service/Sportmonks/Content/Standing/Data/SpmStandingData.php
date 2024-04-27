<?php
declare(strict_types=1);

namespace App\Service\Sportmonks\Content\Standing\Data;

use App\Entity\Spm\SpmStanding;


class SpmStandingData
{
    private int $apiId;
    private int $participantApiId;
    private int $leagueApiId;
    private int $seasonApiId;
    private int $roundApiId;
    private int $stageApiId;
    private int $position;
    private int $points;
    private string $result;

    public function getApiId(): int
    {
        return $this->apiId;
    }

    public function setApiId(int $apiId): SpmStandingData
    {
        $this->apiId = $apiId;
        return $this;
    }

    public function getParticipantApiId(): int
    {
        return $this->participantApiId;
    }

    public function setParticipantApiId(int $participantApiId): SpmStandingData
    {
        $this->participantApiId = $participantApiId;
        return $this;
    }

    public function getLeagueApiId(): int
    {
        return $this->leagueApiId;
    }

    public function setLeagueApiId(int $leagueApiId): SpmStandingData
    {
        $this->leagueApiId = $leagueApiId;
        return $this;
    }

    public function getSeasonApiId(): int
    {
        return $this->seasonApiId;
    }

    public function setSeasonApiId(int $seasonApiId): SpmStandingData
    {
        $this->seasonApiId = $seasonApiId;
        return $this;
    }

    public function getRoundApiId(): int
    {
        return $this->roundApiId;
    }

    public function setRoundApiId(int $roundApiId): SpmStandingData
    {
        $this->roundApiId = $roundApiId;
        return $this;
    }

    public function getStageApiId(): int
    {
        return $this->stageApiId;
    }

    public function setStageApiId(int $stageApiId): SpmStandingData
    {
        $this->stageApiId = $stageApiId;
        return $this;
    }

    public function getPosition(): int
    {
        return $this->position;
    }

    public function setPosition(int $position): SpmStandingData
    {
        $this->position = $position;
        return $this;
    }

    public function getPoints(): int
    {
        return $this->points;
    }

    public function setPoints(int $points): SpmStandingData
    {
        $this->points = $points;
        return $this;
    }

    public function getResult(): string
    {
        return $this->result;
    }

    public function setResult(string $result): SpmStandingData
    {
        $this->result = $result;
        return $this;
    }

    public function initFromApiResponse(array $response): SpmStandingData
    {
        $needed = ['id', 'participant_id', 'league_id', 'season_id', 'stage_id', 'round_id', 'position', 'points', 'result'];
        $this->checkForNecessaryKeys($needed, $response);

        $this->setApiId($response['id']);
        $this->setParticipantApiId($response['participant_id']);
        $this->setLeagueApiId($response['league_id']);
        $this->setSeasonApiId($response['season_id']);
        $this->setStageApiId($response['stage_id']);
        $this->setRoundApiId($response['round_id'] ?: 0);
        $this->setPosition($response['position']);
        $this->setPoints($response['points']);
        $this->setResult($response['result'] ?: '');

        return $this;
    }

    private function checkForNecessaryKeys(array $keys, array $response): void
    {
        foreach ($keys as $key) {
            if (!array_key_exists($key, $response)) {
                throw new \Exception(sprintf('Expected api response key %s for class %s', $key, get_class($this)));
            }
        }
    }

    public function initFromEntity(SpmStanding $standing): SpmStandingData
    {
        $this->setApiId($standing->getApiId());
        $this->setParticipantApiId($standing->getParticipantApiId());
        $this->setLeagueApiId($standing->getLeagueApiId());
        $this->setSeasonApiId($standing->getSeasonApiId());
        $this->setStageApiId($standing->getStageApiId());
        $this->setRoundApiId($standing->getRoundApiId());
        $this->setPosition($standing->getPosition());
        $this->setPoints($standing->getPoints());
        $this->setResult($standing->getResult());

        return $this;
    }
}
