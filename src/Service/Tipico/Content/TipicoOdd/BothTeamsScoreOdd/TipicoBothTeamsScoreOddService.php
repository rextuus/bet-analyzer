<?php
declare(strict_types=1);

namespace App\Service\Tipico\Content\TipicoOdd\BothTeamsScoreOdd;

use App\Entity\TipicoBothTeamsScoreOdd;
use App\Service\Tipico\Content\TipicoOdd\BothTeamsScoreOdd\Data\TipicoBothTeamsScoreOddData;

/**
 * @author Wolfgang Hinzmann <wolfgang.hinzmann@doccheck.com>
 * @license 2024 DocCheck Community GmbH
 */
class TipicoBothTeamsScoreOddService
{
    public function __construct(private readonly TipicoBothTeamsScoreOddRepository $repository, private readonly TipicoBothTeamsScoreOddFactory $factory)
    {
    }

    public function createByData(TipicoBothTeamsScoreOddData $data): TipicoBothTeamsScoreOdd
    {
        $tipicoBothTeamsScoreOdd = $this->factory->createByData($data);
        $this->repository->save($tipicoBothTeamsScoreOdd);
        return $tipicoBothTeamsScoreOdd;
    }

    public function update(TipicoBothTeamsScoreOdd $tipicoBothTeamsScoreOdd, TipicoBothTeamsScoreOddData $data): TipicoBothTeamsScoreOdd
    {
        $tipicoBothTeamsScoreOdd = $this->factory->mapData($data, $tipicoBothTeamsScoreOdd);
        $this->repository->save($tipicoBothTeamsScoreOdd);
        return $tipicoBothTeamsScoreOdd;
    }

    /**
     * @return TipicoBothTeamsScoreOdd[]
     */
    public function findBy(array $conditions): array
    {
        return $this->repository->findBy($conditions);
    }

    public function findByTipicoId(int $getTipicoBetId)
    {
        return $this->repository->findBy(['bet' => $getTipicoBetId]);
    }
}
