<?php
declare(strict_types=1);

namespace App\Service\Sportmonks\Content\Team;

use App\Entity\Spm\SpmTeam;
use App\Service\Sportmonks\Content\Team\Data\SpmTeamData;


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
