<?php

namespace App\Service\Tipico\Content\SimulationStrategy;

use App\Entity\BettingProvider\SimulationStrategy;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<SimulationStrategy>
 *
 * @method SimulationStrategy|null find($id, $lockMode = null, $lockVersion = null)
 * @method SimulationStrategy|null findOneBy(array $criteria, array $orderBy = null)
 * @method SimulationStrategy[]    findAll()
 * @method SimulationStrategy[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SimulationStrategyRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SimulationStrategy::class);
    }

    //    /**
    //     * @return SimulationStrategy[] Returns an array of SimulationStrategy objects
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

    //    public function findOneBySomeField($value): ?SimulationStrategy
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
    public function save(SimulationStrategy $simulationStrategy, bool $flush = true): void
    {
        $this->_em->persist($simulationStrategy);
        if($flush){
            $this->_em->flush();
        }
    }
}
