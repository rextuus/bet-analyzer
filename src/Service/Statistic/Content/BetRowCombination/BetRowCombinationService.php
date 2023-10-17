<?php
declare(strict_types=1);

namespace App\Service\Statistic\Content\BetRowCombination;

use App\Entity\BetRowCombination;
use App\Service\Statistic\Content\BetRowCombination\Data\BetRowCombinationData;
use App\Service\Statistic\Content\BetRowCombination\Data\BetRowCombinationFactory;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @author Wolfgang Hinzmann <wolfgang.hinzmann@doccheck.com>
 * @license 2023 DocCheck Community GmbH
 */
class BetRowCombinationService
{
    public function __construct(private readonly BetRowCombinationRepository $repository, private readonly BetRowCombinationFactory $factory, private readonly EntityManagerInterface $entityManager)
    {
    }

    public function createByData(BetRowCombinationData $data, $flush = true): BetRowCombination
    {
        $betRowCombination = $this->factory->createByData($data);
        $this->repository->save($betRowCombination, $flush);
        return $betRowCombination;
    }

    public function update(BetRowCombination $betRowCombination, BetRowCombinationData $data): BetRowCombination
    {
        $betRowCombination = $this->factory->mapData($data, $betRowCombination);
        $this->repository->save($betRowCombination);
        return $betRowCombination;
    }

    /**
     * @return BetRowCombination[]
     */
    public function findBy(array $conditions): array
    {
        return $this->repository->findBy($conditions);
    }

    public function getActiveCombination(): BetRowCombination
    {
        $activeOnes = $this->repository->findBy(['active' => true]);
        if (count($activeOnes) === 0){
            throw new \Exception('No active combination could be foound');
        }

        if (count($activeOnes) > 1){
            throw new \Exception('There are more than one active combinations');
        }

        return $activeOnes[0];
    }

    /**
     * @param $betRowCombinations BetRowCombinationData[]
     * @return int
     */
    public function createMultipleByData(array $betRowCombinations): int
    {
        $stored = 0;
        foreach ($betRowCombinations as $betRowCombination) {
            if (!$this->repository->findBy(['ident' => $betRowCombination->getIdent()])) {
                $this->createByData($betRowCombination, false);
                $stored++;
            }
        }
        $this->entityManager->flush();

        return $stored;
    }
}
