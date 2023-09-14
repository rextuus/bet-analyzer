<?php
declare(strict_types=1);

namespace App\Service\Sportmonks\Content\Fixture\InvalidFixture;

use App\Entity\InvalidFixture;
use App\Service\Sportmonks\Content\Fixture\InvalidFixture\Data\InvalidFixtureData;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @author Wolfgang Hinzmann <wolfgang.hinzmann@doccheck.com>
 * @license 2023 DocCheck Community GmbH
 */
class InvalidFixtureService
{
    public function __construct(private readonly InvalidFixtureRepository $repository, private readonly InvalidFixtureFactory $factory, private readonly EntityManagerInterface $entityManager)
    {
    }

    public function createByData(InvalidFixtureData $data, $flush = true): InvalidFixture
    {
        $invalidFixture = $this->factory->createByData($data);
        $this->repository->save($invalidFixture, $flush);
        return $invalidFixture;
    }

    public function update(InvalidFixture $invalidFixture, InvalidFixtureData $data): InvalidFixture
    {
        $invalidFixture = $this->factory->mapData($data, $invalidFixture);
        $this->repository->save($invalidFixture);
        return $invalidFixture;
    }

    /**
     * @return InvalidFixture[]
     */
    public function findBy(array $conditions): array
    {
        return $this->repository->findBy($conditions);
    }

    /**
     * @param $invalidFixtures InvalidFixtureData[]
     * @return int
     */
    public function createMultipleByData(array $invalidFixtures): int
    {
        $stored = 0;
        foreach ($invalidFixtures as $invalidFixture) {
            if (!$this->repository->findBy(['fixtureApiId' => $invalidFixture->getFixtureApiId()])) {
                $this->createByData($invalidFixture, false);
                $stored++;
            }
        }
        $this->entityManager->flush();

        return $stored;
    }
}
