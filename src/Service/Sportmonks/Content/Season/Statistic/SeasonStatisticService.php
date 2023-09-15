<?php
declare(strict_types=1);

namespace App\Service\Sportmonks\Content\Season\Statistic;

use App\Entity\SeasonStatistic;
use App\Service\Sportmonks\Content\Season\Statistic\Data\SeasonStatisticData;
use App\Service\Sportmonks\Content\Season\Statistic\Dto\ListingDto;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @author Wolfgang Hinzmann <wolfgang.hinzmann@doccheck.com>
 * @license 2023 DocCheck Community GmbH
 */
class SeasonStatisticService
{
    public function __construct(private readonly SeasonStatisticRepository $repository, private readonly SeasonStatisticFactory $factory, private readonly EntityManagerInterface $entityManager)
    {
    }

    public function createByData(SeasonStatisticData $data, $flush = true): SeasonStatistic
    {
        $seasonStatistic = $this->factory->createByData($data);
        $this->repository->save($seasonStatistic, $flush);
        return $seasonStatistic;
    }

    public function update(SeasonStatistic $seasonStatistic, SeasonStatisticData $data): SeasonStatistic
    {
        $seasonStatistic = $this->factory->mapData($data, $seasonStatistic);
        $this->repository->save($seasonStatistic);
        return $seasonStatistic;
    }

    /**
     * @return SeasonStatistic[]
     */
    public function findBy(array $conditions): array
    {
        return $this->repository->findBy($conditions);
    }

    /**
     * @param $seasonStatistics SeasonStatisticData[]
     * @return int
     */
    public function createMultipleByData(array $seasonStatistics): int
    {
        $stored = 0;
        foreach ($seasonStatistics as $seasonStatistic) {
            if (!$this->repository->findBy(['apiId' => $seasonStatistic->getApiId()])) {
                $this->createByData($seasonStatistic, false);
                $stored++;
            }
        }
        $this->entityManager->flush();

        return $stored;
    }

    public function getViewDtos()
    {
        $statistics = $this->repository->findAll();
        $dtos = [];
        foreach ($statistics as $statistic){
            $dto = $this->createDto($statistic);
            if ($dto){
                $dtos[] = $dto;
            }
        }

        return $dtos;
    }

    private function createDto(SeasonStatistic $statistic) {
        if ($statistic->getDecoratedFixtures() === 0){
            return null;
        }

        $dto = new ListingDto();
        $dto->setLeague($statistic->getLeague());
        $dto->setTeams($statistic->getTeams());
        $dto->setDecoratedFixtures($statistic->getDecoratedFixtures());
        $dto->setMatchDays($statistic->getMatchDays());
        $dto->setExpectedMatchDays($statistic->getExpectedMatchDays());
        $dto->setExpectedMatchDaysAlternative($statistic->getExpectedMatchDaysAlternative());
        $dto->setYear($statistic->getYear().' ('.$statistic->getSeasonApiId().')');
        $dto->setStage($statistic->getStage());
        $statistic->isIsRegularSeason() ? $dto->setInvalidStageClass('') : $dto->setInvalidStageClass('invalid-stage');

        $expectedClass = '';
        $fitsExpectationClass = '';
        if ($statistic->getExpectedMatchDays() !== $statistic->getExpectedMatchDaysAlternative()){
//            $expectedClass = 'invalid-expectations';
//            $fitsExpectationClass = 'invalid-expectations';
        }else{

        }
        if ($statistic->getExpectedMatchDays() !== $statistic->getDecoratedFixtures()){
            $fitsExpectationClass = 'not-enough-fixtures';
        }

        $dto->setInvalidExpectationsClass($expectedClass);
        $dto->setFitsExpectationClass($fitsExpectationClass);

        $dto->setNoStandings($statistic->isNoStandingsAvailable());

        $invalidTeamClass = '';
        if ($statistic->getTeams() % 2 !== 0){
            $invalidTeamClass = 'invalid-teams';
        }
        $dto->setInvalidTeamAmount($invalidTeamClass);

        preg_match('~\(.*\)(.*)\(.*\)~', $statistic->getLeague(), $matches);
        $link = trim($matches[1]).' '.$statistic->getYear();
        $link = str_replace('DNK SL', 'Dänemark', $link);
        $dto->setSearchLinkContent($link);

        $dto->setActuallyBetDecorated($statistic->getActuallyBetDecorated());
        $dto->setManuallyConfirmed($statistic->isManuallyConfirmed());
        $dto->setSeasonId($statistic->getSeasonApiId());

        $statistic->isManuallyConfirmed() ? $dto->setConfirmedManuallyClass('confirmed') : $dto->setConfirmedManuallyClass('');

        return $dto;
    }
}
