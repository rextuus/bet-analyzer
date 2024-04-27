<?php
declare(strict_types=1);

namespace App\Service\Sportmonks\Content\Score\Data;


class SpmScoreData
{
    private int $apiId;
    private int $fixtureApiId;
    private int $participantApiId;
    private int $goals;
    private string $description;
    private string $participant;

    public function getApiId(): int
    {
        return $this->apiId;
    }

    public function setApiId(int $apiId): SpmScoreData
    {
        $this->apiId = $apiId;
        return $this;
    }

    public function getFixtureApiId(): int
    {
        return $this->fixtureApiId;
    }

    public function setFixtureApiId(int $fixtureApiId): SpmScoreData
    {
        $this->fixtureApiId = $fixtureApiId;
        return $this;
    }

    public function getParticipantApiId(): int
    {
        return $this->participantApiId;
    }

    public function setParticipantApiId(int $participantApiId): SpmScoreData
    {
        $this->participantApiId = $participantApiId;
        return $this;
    }

    public function getGoals(): int
    {
        return $this->goals;
    }

    public function setGoals(int $goals): SpmScoreData
    {
        $this->goals = $goals;
        return $this;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): SpmScoreData
    {
        $this->description = $description;
        return $this;
    }

    public function getParticipant(): string
    {
        return $this->participant;
    }

    public function setParticipant(string $participant): SpmScoreData
    {
        $this->participant = $participant;
        return $this;
    }

    public function initFromApiResponse(array $response): SpmScoreData
    {
        $needed = ['id', 'fixture_id', 'participant_id', 'score', 'description'];
        $this->checkForNecessaryKeys($needed, $response);

        $this->setApiId($response['id']);
        $this->setFixtureApiId($response['fixture_id']);
        $this->setParticipantApiId($response['participant_id']);
        $this->setGoals($response['score']['goals']);
        $this->setParticipant($response['score']['participant']);
        $this->setDescription($response['description']);
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
}
