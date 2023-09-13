<?php

declare(strict_types=1);

namespace App\Service\Sportmonks\Content\Odd\Data;

/**
 * @author  Wolfgang Hinzmann <wolfgang.hinzmann@doccheck.com>
 * @license 2023 DocCheck Community GmbH
 */
class SpmOddData
{
    private int $apiId;
    private int $fixtureApiId;
    private int $bookmakerApiId;
    private float $value;
    private string $marketDescription;
    private string $name;
    private string $label;

    public function getApiId(): int
    {
        return $this->apiId;
    }

    public function setApiId(int $apiId): SpmOddData
    {
        $this->apiId = $apiId;
        return $this;
    }

    public function getFixtureApiId(): int
    {
        return $this->fixtureApiId;
    }

    public function setFixtureApiId(int $fixtureApiId): SpmOddData
    {
        $this->fixtureApiId = $fixtureApiId;
        return $this;
    }

    public function getBookmakerApiId(): int
    {
        return $this->bookmakerApiId;
    }

    public function setBookmakerApiId(int $bookmakerApiId): SpmOddData
    {
        $this->bookmakerApiId = $bookmakerApiId;
        return $this;
    }

    public function getValue(): float
    {
        return $this->value;
    }

    public function setValue(float $value): SpmOddData
    {
        $this->value = $value;
        return $this;
    }

    public function getMarketDescription(): string
    {
        return $this->marketDescription;
    }

    public function setMarketDescription(string $marketDescription): SpmOddData
    {
        $this->marketDescription = $marketDescription;
        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): SpmOddData
    {
        $this->name = $name;
        return $this;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function setLabel(string $label): SpmOddData
    {
        $this->label = $label;
        return $this;
    }

    public function initFromApiResponse(array $response): SpmOddData
    {
        $needed = ['id', 'fixture_id', 'name', 'label', 'bookmaker_id', 'market_description', 'value'];
        $this->checkForNecessaryKeys($needed, $response);

        $this->setApiId($response['id']);
        $this->setName($response['name'] ?: '-');
        $this->setLabel($response['label']);
        $this->setFixtureApiId($response['fixture_id']);
        $this->setBookmakerApiId($response['bookmaker_id']);
        $this->setValue((float) $response['value']);
        $this->setMarketDescription($response['market_description']);
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
