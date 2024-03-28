<?php

namespace App\Service\Tipico\Content\Simulator;

use App\Entity\SimulationStrategy;
use App\Entity\Simulator;
use App\Entity\TipicoPlacement;
use App\Service\Tipico\Content\Simulator\Data\SimulatorFilterData;
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

        if ($filter->getMinBets()) {
            $qb->andHaving($qb->expr()->gte('COUNT(p.id)', ':minBets'));
            $qb->setParameter('minBets', $filter->getMinBets());
        }
        if ($filter->getMaxBets()) {
            $qb->andHaving($qb->expr()->lte('COUNT(p.id)', ':maxBets'));
            $qb->setParameter('maxBets', $filter->getMaxBets());
        }

        $qb->groupBy('s');
        $qb->orderBy('s.cashBox', 'DESC');
        $qb->setMaxResults(20);

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
     * @return array<array<string, int>
     */
    public function findByStrategies(array $strategyIdents): array
    {
        $qb = $this->createQueryBuilder('s');
        $qb->select('s.id');
        $qb->leftJoin(SimulationStrategy::class, 'ss', 'WITH', 's.strategy = ss.id');
        $qb->andWhere($qb->expr()->in('ss.identifier', ':strategy'));
        $qb->setParameter('strategy', $strategyIdents);

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

        dd($qb->getQuery()->getResult());
        return $qb->getQuery()->getResult();
    }
}
