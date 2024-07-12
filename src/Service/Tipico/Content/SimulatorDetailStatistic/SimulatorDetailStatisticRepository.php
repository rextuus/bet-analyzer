<?php

namespace App\Service\Tipico\Content\SimulatorDetailStatistic;

use App\Entity\BettingProvider\SimulatorDetailStatistic;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
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
}
