<?php

namespace App\Service\Tipico\Content\SimulatorFavoriteList;

use App\Entity\Simulator;
use App\Entity\SimulatorFavoriteList;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<SimulatorFavoriteList>
 *
 * @method SimulatorFavoriteList|null find($id, $lockMode = null, $lockVersion = null)
 * @method SimulatorFavoriteList|null findOneBy(array $criteria, array $orderBy = null)
 * @method SimulatorFavoriteList[]    findAll()
 * @method SimulatorFavoriteList[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SimulatorFavoriteListRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SimulatorFavoriteList::class);
    }

    public function save(SimulatorFavoriteList $simulatorFavoriteList, bool $flush = true): void
    {
        $this->_em->persist($simulatorFavoriteList);
        if($flush){
            $this->_em->flush();
        }
    }

    //    /**
    //     * @return SimulatorFavoriteList[] Returns an array of SimulatorFavoriteList objects
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

    //    public function findOneBySomeField($value): ?SimulatorFavoriteList
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }

    /**
     * @return SimulatorFavoriteList[]
     */
    public function findListsNotContainingSimulator(Simulator $simulator):array
    {
        $qb = $this->createQueryBuilder('l');
        $qb->leftJoin('l.simulators', 's');
        $qb->where($qb->expr()->isNull('s.id'));
        $qb->orWhere($qb->expr()->neq('s.id', ':simulator'));
        $qb->setParameter('simulator', $simulator->getId());

        return $qb->getQuery()->getResult();
    }

    public function findAllWithPlacements()
    {
        $qb = $this->createQueryBuilder('l');

        $qb->leftJoin('l.simulators', 's');
        $qb->leftJoin('s.tipicoPlacements', 'p');
        $qb->groupBy('l');

        dd($qb->getQuery()->getResult());
        return $qb->getQuery()->getResult();
    }
}
