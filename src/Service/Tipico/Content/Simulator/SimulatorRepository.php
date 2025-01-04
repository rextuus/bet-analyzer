<?php

namespace App\Service\Tipico\Content\Simulator;

use App\Entity\BettingProvider\SimulationStrategy;
use App\Entity\BettingProvider\Simulator;
use App\Entity\BettingProvider\TipicoPlacement;
use App\Service\Tipico\Content\Simulator\Data\SimulatorFilterData;
use App\Service\Tipico\Simulation\AdditionalProcessors\Weekday;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Simulator>
 *
 * @method Simulator|null find($id, $lockMode = null, $lockVersion = null)
 * @method Simulator|null findOneBy(array $criteria, array $orderBy = null)
 * @method Simulator[]    findAll()
 * @method Simulator[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SimulatorRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Simulator::class);
    }

    public function save(Simulator $simulator, bool $flush = true): void
    {
        $this->_em->persist($simulator);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * @return Simulator[]
     */
    public function findByFilter(SimulatorFilterData $filter): array
    {
        $qb = $this->createQueryBuilder('s');

        $qb->leftJoin(TipicoPlacement::class, 'p', 'WITH', 's.id = p.simulator');
        if ($filter->isExcludeNegative()){
            $qb->andWhere($qb->expr()->gt('s.cashBox', 100.0));
        }
        if ($filter->getVariant()){
            $qb->leftJoin(SimulationStrategy::class, 'ss', 'WITH', 's.strategy = ss.id');

            $qb->andWhere($qb->expr()->eq('ss.identifier', ':strategy'));
            $qb->setParameter('strategy', $filter->getVariant());
        }

        if ($filter->getMinCashBox()) {
            $qb->andWhere($qb->expr()->gte('s.cashBox', ':min'));
            $qb->setParameter('min', $filter->getMinCashBox());
        }

        if ($filter->getMaxCashBox()) {
            $qb->andWhere($qb->expr()->lte('s.cashBox', ':max'));
            $qb->setParameter('max', $filter->getMaxCashBox());
        }

        if ($filter->isExcludeNsb()) {
            $qb->andWhere($qb->expr()->notLike('s.identifier', ':exclude'));
            $qb->setParameter('exclude', '%nsb%');
        }

        // TODO this is working?? And where its used?
        if ($filter->getWeekday()) {
            $qb->andWhere('WEEKDAY(p.created) = :weekday');
            $qb->setParameter('weekday', $filter->getWeekday());
        }

        if ($filter->getMinBets()) {
            $qb->andHaving($qb->expr()->gte('COUNT(p.id)', ':minBets'));
            $qb->setParameter('minBets', $filter->getMinBets());
        }
        if ($filter->getMaxBets()) {
            $qb->andHaving($qb->expr()->lte('COUNT(p.id)', ':maxBets'));
            $qb->setParameter('maxBets', $filter->getMaxBets());
        }

        $qb->groupBy('s');
        $qb->setMaxResults($filter->getMaxResults());
        $qb->setFirstResult($filter->getOffset());

        // TODO this is working?? And where its used?
        if ($filter->getWeekDay()) {
            $orderExpr = "SUM(CASE WHEN p.won = true THEN (p.value - p.input) ELSE 0 END) AS HIDDEN orderValue";

            $qb->addSelect($orderExpr);
            $qb->orderBy('orderValue', 'DESC');
        } else {
            $qb->orderBy('s.cashBox', 'DESC');
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * @return int[]
     */
    public function findAllById(): array
    {
        $qb = $this->createQueryBuilder('s');
        $qb->select('s.id');

        return $qb->getQuery()->getResult();
    }

    /**
     * @param string[] $strategyIdents
     * @param array<string, string> $filterOptions
     * @return array<array<string, int>
     */
    public function findByStrategies(array $strategyIdents, array $additional, $filterOptions): array
    {
        $qb = $this->createQueryBuilder('s');
        $qb->select('s.id');
        $qb->leftJoin(SimulationStrategy::class, 'ss', 'WITH', 's.strategy = ss.id');
        $qb->andWhere($qb->expr()->in('ss.identifier', ':strategy'));
        $qb->setParameter('strategy', $strategyIdents);

        if (count($additional) > 0){
            $qb->andWhere($qb->expr()->in('ss.additionalProcessingIdent', ':additional'));
            $qb->setParameter('additional', $additional);
        }

        if (count($filterOptions) > 0) {
            if (array_key_exists(SimulatorService::FILTER_OPTION_IDENTIFIER, $filterOptions)) {
                $qb->andWhere($qb->expr()->like('s.identifier', ':filterOption'));
                $qb->setParameter(
                    'filterOption',
                    '%' . $filterOptions[SimulatorService::FILTER_OPTION_IDENTIFIER] . '%'
                );
            }
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * @return Simulator[]
     */
    public function findWithPlacements(): array
    {
        $qb = $this->createQueryBuilder('s');
        $qb->join('s.tipicoPlacements', 'p')
        ->distinct();

        return $qb->getQuery()->getResult();
    }

    public function findBySimulatorsWithPlacements(array $simulatorIds, DateTime $from, DateTime $until): array
    {
        $qb = $this->createQueryBuilder('s');
        $qb->select('s, p');
        $qb->join('s.tipicoPlacements', 'p');
        $qb->where($qb->expr()->in('s.id', ':ids'));
        $qb->setParameter('ids', $simulatorIds);

        $qb->andWhere($qb->expr()->gte('p.created', ':from'));
        $qb->setParameter('from', $from);

        $qb->andWhere($qb->expr()->lte('p.created', ':until'));
        $qb->setParameter('until', $until);

        return $qb->getQuery()->getResult();
    }

    /**
     * @return array<Simulator>
     */
    public function findTopSimulatorsByWeekDay(
        Weekday $weekday,
        int $usedSimulators = 100,
        float $cashBoxMin = 60.0
    ): array {
        $totalValue = '';
        match ($weekday) {
            Weekday::Monday => $totalValue = 'mondayTotal',
            Weekday::Tuesday => $totalValue = 'tuesdayTotal',
            Weekday::Wednesday => $totalValue = 'wednesdayTotal',
            Weekday::Thursday => $totalValue = 'thursdayTotal',
            Weekday::Friday => $totalValue = 'fridayTotal',
            Weekday::Saturday => $totalValue = 'saturdayTotal',
            Weekday::Sunday => $totalValue = 'sundayTotal',
        };

        $qb = $this->createQueryBuilder('s');
        $qb->join('s.simulatorDetailStatistic', 'd');

        $qb->where($qb->expr()->gt('d.' . $totalValue, ':minTotal'));
        $qb->setParameter('minTotal', 0.0);

        $qb->andWhere($qb->expr()->gte('s.cashBox', ':minCashBox'));
        $qb->setParameter('minCashBox', $cashBoxMin);

        $qb->orderBy('d.' . $totalValue, 'DESC');

        $qb->setMaxResults($usedSimulators);

        return $qb->getQuery()->getResult();
    }
}
