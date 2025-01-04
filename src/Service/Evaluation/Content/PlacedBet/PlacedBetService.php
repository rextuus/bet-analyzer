<?php
declare(strict_types=1);

namespace App\Service\Evaluation\Content\PlacedBet;

use App\Entity\Spm\PlacedBet;
use App\Service\Evaluation\Content\PlacedBet\Data\PlacedBetData;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @deprecated SPM can be removed. Data are not worthy and won't be used anymore
 */
class PlacedBetService
{
    public function __construct(private readonly PlacedBetRepository $repository, private readonly PlacedBetFactory $factory, private readonly EntityManagerInterface $entityManager)
    {
    }

    public function createByData(PlacedBetData $data, $flush = true): PlacedBet
    {
        $placedBet = $this->factory->createByData($data);
        $this->repository->save($placedBet, $flush);
        return $placedBet;
    }

    public function update(PlacedBet $placedBet, PlacedBetData $data): PlacedBet
    {
        $placedBet = $this->factory->mapData($data, $placedBet);
        $this->repository->save($placedBet);
        return $placedBet;
    }

    /**
     * @return PlacedBet[]
     */
    public function findBy(array $conditions): array
    {
        return $this->repository->findBy($conditions);
    }

    /**
     * @param $placedBets PlacedBetData[]
     * @return int
     */
    public function createMultipleByData(array $placedBets): int
    {
        $stored = 0;
        foreach ($placedBets as $placedBet) {
            if (!$this->repository->findBy(['apiId' => $placedBet->getFixtureApiId()])) {
                $this->createByData($placedBet, false);
                $stored++;
            }
        }
        $this->entityManager->flush();

        return $stored;
    }
}
