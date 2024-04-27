<?php
declare(strict_types=1);

namespace App\Service\Sportmonks\Content\Team;

use App\Entity\Spm\SpmTeam;
use App\Service\Sportmonks\Content\Team\Data\SpmTeamData;
use Doctrine\ORM\EntityManagerInterface;


class SpmTeamService
{
    public function __construct(private readonly SpmTeamRepository $repository, private readonly SpmTeamFactory $factory, private readonly EntityManagerInterface $entityManager)
    {
    }

    public function createByData(SpmTeamData $data, $flush = true): SpmTeam
    {
        $spmTeam = $this->factory->createByData($data);
        $this->repository->save($spmTeam, $flush);
        return $spmTeam;
    }

    public function update(SpmTeam $spmTeam, SpmTeamData $data): SpmTeam
    {
        $spmTeam = $this->factory->mapData($data, $spmTeam);
        $this->repository->save($spmTeam);
        return $spmTeam;
    }

    /**
     * @return SpmTeam[]
     */
    public function findBy(array $conditions): array
    {
        return $this->repository->findBy($conditions);
    }

    /**
     * @param $spmTeams SpmTeamData[]
     * @return int
     */
    public function createMultipleByData(array $spmTeams): int
    {
        $stored = 0;
        foreach ($spmTeams as $spmTeam) {
            if (!$this->repository->findBy(['apiId' => $spmTeam->getApiId()])) {
                $this->createByData($spmTeam, false);
                $stored++;
            }
        }
        $this->entityManager->flush();

        return $stored;
    }
}
