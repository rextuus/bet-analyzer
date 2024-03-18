<?php

namespace App\Service\Tipico\Content\Simulator;

use App\Entity\SimulationStrategy;
use App\Entity\Simulator;
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

    //    /**
    //     * @return Simulator[] Returns an array of Simulator objects
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

    //    public function findOneBySomeField($value): ?Simulator
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }

    public function save(Simulator $simulator, bool $flush = true): void
    {
        $this->_em->persist($simulator);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * @param array<string, mixed> $data
     * @return Simulator[]
     */
    public function findByFilter(array $data): array
    {
        $qb = $this->createQueryBuilder('s');
        if (array_key_exists('excludeNegative', $data) && $data['excludeNegative']){
            $qb->andWhere($qb->expr()->gt('s.cashBox', 100.0));
        }
        if (array_key_exists('variant', $data)){
            $qb->leftJoin(SimulationStrategy::class, 'ss', 'WITH', 's.strategy = ss.id');

            $qb->andWhere($qb->expr()->eq('ss.identifier', ':strategy'));
            $qb->setParameter('strategy', $data['variant']);
        }

        $qb->orderBy('s.cashBox', 'DESC');
        $qb->setMaxResults(10);

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
}
