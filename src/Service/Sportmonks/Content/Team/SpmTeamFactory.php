<?php
declare(strict_types=1);

namespace App\Service\Sportmonks\Content\Team;

use App\Entity\SpmTeam;
use App\Service\Sportmonks\Content\Team\Data\SpmTeamData;

/**
 * @author Wolfgang Hinzmann <wolfgang.hinzmann@doccheck.com>
 * @license 2023 DocCheck Community GmbH
 */
class SpmTeamFactory
{
    public function createByData(SpmTeamData $data): SpmTeam
    {
        $spmTeam = $this->createNewInstance();
        $this->mapData($data, $spmTeam);
        return $spmTeam;
    }

    public function mapData(SpmTeamData $data, SpmTeam $spmTeam): SpmTeam
    {
        $spmTeam->setApiId($data->getApiId());
        $spmTeam->setCountryApiId($data->getCountryApiId());
        $spmTeam->setName($data->getName());
        $spmTeam->setShortCode($data->getShortCode());
        $spmTeam->setImgPath($data->getImgPath());

        return $spmTeam;
    }

    private function createNewInstance(): SpmTeam
    {
        return new SpmTeam();
    }
}
