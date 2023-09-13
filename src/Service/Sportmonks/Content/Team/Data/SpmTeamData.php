<?php
declare(strict_types=1);

namespace App\Service\Sportmonks\Content\Team\Data;

use App\Entity\SpmTeam;

/**
 * @author Wolfgang Hinzmann <wolfgang.hinzmann@doccheck.com>
 * @license 2023 DocCheck Community GmbH
 */
class SpmTeamData
{
    private int $apiId;
    private int $countryApiId;
    private string $name;
    private string $shortCode;
    private string $imgPath;

    public function getApiId(): int
    {
        return $this->apiId;
    }

    public function setApiId(int $apiId): SpmTeamData
    {
        $this->apiId = $apiId;
        return $this;
    }

    public function getCountryApiId(): int
    {
        return $this->countryApiId;
    }

    public function setCountryApiId(int $countryApiId): SpmTeamData
    {
        $this->countryApiId = $countryApiId;
        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): SpmTeamData
    {
        $this->name = $name;
        return $this;
    }

    public function getShortCode(): string
    {
        return $this->shortCode;
    }

    public function setShortCode(string $shortCode): SpmTeamData
    {
        $this->shortCode = $shortCode;
        return $this;
    }

    public function getImgPath(): string
    {
        return $this->imgPath;
    }

    public function setImgPath(string $imgPath): SpmTeamData
    {
        $this->imgPath = $imgPath;
        return $this;
    }

    public function initFromApiResponse(array $response): SpmTeamData
    {
        $needed = ['id', 'country_id', 'name', 'short_code', 'image_path'];
        $this->checkForNecessaryKeys($needed, $response);

        $this->setApiId($response['id']);
        $this->setCountryApiId($response['country_id']);
        $this->setName($response['name']);
        $this->setShortCode($response['short_code'] ?: '');
        $this->setImgPath($response['image_path']);

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

    public function initFromEntity(SpmTeam $team): SpmTeamData
    {
        $this->setApiId($team->getApiId());
        $this->setCountryApiId($team->getCountryApiId());
        $this->setName($team->getName());
        $this->setShortCode($team->getShortCode());
        $this->setImgPath($team->getImgPath());

        return $this;
    }
}
