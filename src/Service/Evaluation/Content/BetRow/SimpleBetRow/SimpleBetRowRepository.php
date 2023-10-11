<?php

namespace App\Service\Evaluation\Content\BetRow\SimpleBetRow;

use App\Entity\BetRowOddFilter;
use App\Entity\BetRowSummary;
use App\Entity\SimpleBetRow;
use App\Entity\SpmLeague;
use App\Entity\SpmSeason;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<SimpleBetRow>
 *
 * @method SimpleBetRow|null find($id, $lockMode = null, $lockVersion = null)
 * @method SimpleBetRow|null findOneBy(array $criteria, array $orderBy = null)
 * @method SimpleBetRow[]    findAll()
 * @method SimpleBetRow[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SimpleBetRowRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SimpleBetRow::class);
    }

    public function save(SimpleBetRow $simpleBetRow, bool $flush = true): void
    {
        $this->_em->persist($simpleBetRow);
        if($flush){
            $this->_em->flush();
        }
    }

//    /**
//     * @return SimpleBetRow[] Returns an array of SimpleBetRow objects
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

//    public function findOneBySomeField($value): ?SimpleBetRow
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
    public function findBySeasonAndFilter(SpmSeason $season, BetRowOddFilter $filter): int
    {
        $qb = $this->createQueryBuilder('br');
        $qb->select('count (br) as amount');
//        $qb->leftJoin(BetRowOddFilter::class, 'f', 'WITH', 'br.betRowFilters = f.id');
        $qb->join('br.betRowFilters', 'f');
        $qb->where($qb->expr()->eq('br.seasonApiId', ':season'));
        $qb->setParameter('season', $season->getApiId());
        $qb->andWhere($qb->expr()->eq('f.id', ':filter'));
        $qb->setParameter('filter', $filter->getId());

        $result = 0;
        try {
            $result = $qb->getQuery()->getSingleScalarResult();
        } catch (NoResultException|NonUniqueResultException $e) {
        }
        return $result;
    }

    public function findBySeasonIncludingSummaries(SpmSeason $season)
    {
        $qb = $this->createQueryBuilder('br');
        $qb->select('br, brs');
        $qb->join(BetRowSummary::class, 'brs', 'WITH', 'br.id = brs.betRow');
//        $qb->join('br.betRowSummary', 'brs');
        $qb->where($qb->expr()->isNotNull('brs.id', ':season'));
//        $qb->setParameter('season', $season->getApiId());
        $qb->andWhere($qb->expr()->gte('brs.cashBox', ':cashBoxMin'));
        $qb->setParameter('cashBoxMin', 100.01);
        $qb->andWhere($qb->expr()->eq('br.seasonApiId', ':season'));
        $qb->setParameter('season', $season->getApiId());
        return $qb->getQuery()->getResult();
    }

    public function findRowsExistingInAllSeasonsOfLeagueInDeltaRange(SpmLeague $league, float $min, float $max)
    {
        $qb = $this->createQueryBuilder('br');
        $qb->select('s, brs');
        $qb->innerJoin(SpmSeason::class, 's', 'WITH', 's.apiId = br.seasonApiId');
        $qb->innerJoin(BetRowSummary::class, 'brs', 'WITH', 'br.id = brs.betRow');
        $qb->leftJoin('br.betRowFilters', 'f');

        $qb->where($qb->expr()->eq('s.leagueApiId', ':leagueApiId'));
        $qb->setParameter('leagueApiId', $league->getApiId());

        $qb->groupBy('s, brs');
        $qb->having($qb->expr()->gte('brs.cashBox', ':min'));
        $qb->setParameter('min', $min);

        return $qb->getQuery()->getResult();
    }
}
