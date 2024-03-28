<?php

namespace App\Service\Tipico\Content\Placement;

use App\Entity\Simulator;
use App\Entity\SimulatorFavoriteList;
use App\Entity\SpmSeason;
use App\Entity\TipicoPlacement;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<TipicoPlacement>
 *
 * @method TipicoPlacement|null find($id, $lockMode = null, $lockVersion = null)
 * @method TipicoPlacement|null findOneBy(array $criteria, array $orderBy = null)
 * @method TipicoPlacement[]    findAll()
 * @method TipicoPlacement[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TipicoPlacementRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TipicoPlacement::class);
    }

    public function save(TipicoPlacement $tipicoPlacement, bool $flush = true): void
    {
        $this->_em->persist($tipicoPlacement);
        if($flush){
            $this->_em->flush();
        }
    }

    public function getPlacementChangeComparedToDayBefore(Simulator $simulator, DateTime $from, DateTime $until): array
    {
        $qb = $this->createQueryBuilder('p');
        $qb->select('sum(p.value * p.input * p.won) - count(p.id) as changeVolume, count(p.id) as madeBets');
        $qb->andWhere($qb->expr()->eq('p.simulator', ':simulator'));
        $qb->setParameter('simulator', $simulator->getId());
        $qb->andWhere($qb->expr()->gt('p.created', ':from'));
        $qb->setParameter('from', $from);
        $qb->andWhere($qb->expr()->lt('p.created', ':until'));
        $qb->setParameter('until', $until);

        return $qb->getQuery()->getResult()[0];
    }

    public function getTopSimulatorsOfLastDays(DateTime $from, DateTime $until, int $limit = 5, string $direction = 'DESC'): array
    {
        $qb = $this->createQueryBuilder('p');
        $qb->select('s.id, s.identifier, sum(p.value * p.input * p.won) - count(p.id) as changeVolume, count(p.id) as madeBets');

        $qb->innerJoin(Simulator::class, 's', 'WITH', 'p.simulator = s.id');

        $qb->andWhere($qb->expr()->gt('p.created', ':from'));
        $qb->setParameter('from', $from);
        $qb->andWhere($qb->expr()->lt('p.created', ':until'));
        $qb->setParameter('until', $until);

        $qb->groupBy('p.simulator');
        $qb->orderBy('changeVolume', $direction);
        $qb->setMaxResults($limit);

        return $qb->getQuery()->getResult();
    }

    public function findBySimulatorsAndDateTime(SimulatorFavoriteList $simulatorFavoriteList, DateTime $from, DateTime $until)
    {
        $ids = [];
        foreach ($simulatorFavoriteList->getSimulators() as $simulator){
            $ids[] = $simulator->getId();
        }

        $qb = $this->createQueryBuilder('p');
        $qb->select('s.id, s.identifier, sum(p.value * p.input * p.won) - count(p.id) as changeVolume, count(p.id) as madeBets');

        $qb->innerJoin(Simulator::class, 's', 'WITH', 'p.simulator = s.id');

        $qb->andWhere($qb->expr()->in('s.id', ':ids'));
        $qb->setParameter('ids', $ids);

        $qb->andWhere($qb->expr()->gt('p.created', ':from'));
        $qb->setParameter('from', $from);
        $qb->andWhere($qb->expr()->lt('p.created', ':until'));
        $qb->setParameter('until', $until);

        $qb->groupBy('p.simulator');
        $qb->orderBy('changeVolume', 'ASC');

        return ($qb->getQuery()->getResult());
    }
}
