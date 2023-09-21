<?php

namespace App\Service\Sportmonks\Content\Odd;

use App\Entity\BetRowOddFilter;
use App\Entity\SpmFixture;
use App\Entity\SpmOdd;
use App\Service\Evaluation\BetOn;
use App\Service\Evaluation\OddVariant;
use App\Service\Sportmonks\Content\Odd\Data\OddFilter;
use App\Service\Sportmonks\Content\Odd\Data\OddSearchFilter;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<SpmOdd>
 *
 * @method SpmOdd|null find($id, $lockMode = null, $lockVersion = null)
 * @method SpmOdd|null findOneBy(array $criteria, array $orderBy = null)
 * @method SpmOdd[]    findAll()
 * @method SpmOdd[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SpmOddRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SpmOdd::class);
    }

    public function save(SpmOdd $spmOdd, bool $flush = true): void
    {
        $this->_em->persist($spmOdd);
        if ($flush){
            $this->_em->flush();
        }
    }

//    /**
//     * @return SpmOdd[] Returns an array of SpmOdd objects
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

//    public function findOneBySomeField($value): ?SpmOdd
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
    public function findByFixtureAndVariant(SpmFixture $fixture, BetRowOddFilter $filter): array
    {
        $betOns = match ($filter->getBetOn()) {
            BetOn::HOME => [BetOn::HOME->value, 'Home'],
            BetOn::DRAW => [BetOn::DRAW->value, 'Draw'],
            BetOn::AWAY => [BetOn::AWAY->value, 'Away'],
        };

        $qb =  $this->createQueryBuilder('o');
        $qb->select('o');
        $qb->where('o.fixtureApiId = :fixture')
            ->setParameter('fixture', $fixture->getApiId());
        $qb->andWhere($qb->expr()->in('o.marketDescription', ':variants'))
            ->setParameter('variants', $filter->getOddVariant()->value);
        $qb->andWhere($qb->expr()->in('o.name', ':names'))
            ->setParameter('names', $betOns);
        $qb->andWhere($qb->expr()->gte('o.value', ':min'))
            ->setParameter('min', $filter->getMin());
        $qb->andWhere($qb->expr()->lte('o.value', ':max'))
            ->setParameter('max', $filter->getMax());

        return $qb->getQuery()->getResult();
    }
}
