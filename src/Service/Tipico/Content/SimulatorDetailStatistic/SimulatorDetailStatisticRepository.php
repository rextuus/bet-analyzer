<?php

namespace App\Service\Tipico\Content\SimulatorDetailStatistic;

use App\Entity\BettingProvider\Simulator;
use App\Entity\BettingProvider\SimulatorDetailStatistic;
use App\Service\Tipico\Simulation\AdditionalProcessors\Weekday;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<SimulatorDetailStatistic>
 *
 * @method SimulatorDetailStatistic|null find($id, $lockMode = null, $lockVersion = null)
 * @method SimulatorDetailStatistic|null findOneBy(array $criteria, array $orderBy = null)
 * @method SimulatorDetailStatistic[]    findAll()
 * @method SimulatorDetailStatistic[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SimulatorDetailStatisticRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SimulatorDetailStatistic::class);
    }


    public function save(SimulatorDetailStatistic $simulatorDetailStatistic, bool $flush = true): void
    {
        $this->_em->persist($simulatorDetailStatistic);
        if ($flush) {
            $this->_em->flush();
        }
    }

    //    /**
    //     * @return SimulatorDetailStatistic[] Returns an array of SimulatorDetailStatistic objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('s.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?SimulatorDetailStatistic
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }

    /**
     * @return array<SimulatorDetailStatistic>
     */
    public function findByWeekdayOrderedDesc(Weekday $weekday, int $limit): array
    {
        $qb = $this->createQueryBuilder('s');
        $qb->innerJoin(Simulator::class, 'sim', 'WITH', 's.simulator = sim.id');

        $qb->andWhere($qb->expr()->notLike('sim.identifier', ':exclude'));
        $qb->setParameter('exclude', '%nsb%');

        match ($weekday) {
            Weekday::Monday => $this->addSortCondition($qb, 'mondayTotal'),
            Weekday::Tuesday => $this->addSortCondition($qb, 'tuesdayTotal'),
            Weekday::Wednesday => $this->addSortCondition($qb, 'wednesdayTotal'),
            Weekday::Thursday => $this->addSortCondition($qb, 'thursdayTotal'),
            Weekday::Friday => $this->addSortCondition($qb, 'fridayTotal'),
            Weekday::Saturday => $this->addSortCondition($qb, 'saturdayTotal'),
            Weekday::Sunday => $this->addSortCondition($qb, 'sundayTotal'),
        };

        $qb->setMaxResults($limit);

        return $qb->getQuery()->getResult();
    }

    private function addSortCondition(QueryBuilder $qb, string $sortProperty): void
    {
        $qb->orderBy('s.' . $sortProperty, 'DESC');
    }
}
