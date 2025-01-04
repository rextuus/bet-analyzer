<?php

namespace App\Service\Tipico\Content\TipicoBet;

use App\Entity\BettingProvider\TipicoBet;
use App\Entity\BettingProvider\TipicoBothTeamsScoreOdd;
use App\Entity\BettingProvider\TipicoHeadToHeadOdd;
use App\Entity\BettingProvider\TipicoOverUnderOdd;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<TipicoBet>
 *
 * @method TipicoBet|null find($id, $lockMode = null, $lockVersion = null)
 * @method TipicoBet|null findOneBy(array $criteria, array $orderBy = null)
 * @method TipicoBet[]    findAll()
 * @method TipicoBet[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TipicoBetRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TipicoBet::class);
    }

    public function save(TipicoBet $tipicoBet, bool $flush = true): void
    {
        $this->_em->persist($tipicoBet);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * @return TipicoBet[]
     */
    public function findAllUndecoratedMatches(): array
    {
        $timeStamp = (new DateTime('+2h'))->getTimestamp();
        $timeStamp = $timeStamp * 1000;

        $qb = $this->createQueryBuilder('t');
        $qb->where($qb->expr()->lt('t.startAtTimeStamp', ':timeStamp'));
        $qb->setParameter('timeStamp', $timeStamp);
        $qb->andWhere($qb->expr()->eq('t.finished', ':finished'));
        $qb->setParameter('finished', false);

        return $qb->getQuery()->getResult();
    }

    /**
     * @return TipicoBet[]
     */
    public function findUpcomingEventsByRange(float $min, float $max, string $targetOddColumn, int $limit = 100): array
    {
        $alreadyUsed = [-1];
        $currentDate = new DateTime();
        $currentDate->setTime(0, 0, 0);

        $qb = $this->createQueryBuilder('b');
        $qb->where($qb->expr()->gte('b.' . $targetOddColumn, ':min'));
        $qb->setParameter('min', $min);
        $qb->andWhere($qb->expr()->lte('b.' . $targetOddColumn, ':max'));
        $qb->setParameter('max', $max);

        $qb->andWhere($qb->expr()->gt('b.startAtTimeStamp', ':startAfter'));
        $qb->setParameter('startAfter', $currentDate->getTimestamp() * 1000);

        $qb->setMaxResults($limit);

        return $qb->getQuery()->getResult();
    }

    // simulation process
    public function getFixtureByFilter(TipicoBetFilter $filter): array|int
    {
        $qb = $this->createQueryBuilder(TipicoBetFilter::TABLE_ALIAS_TIPICO_BET);

        if ($filter->isIncludeBothTeamsScore()) {
            $condition = sprintf(
                '%s.id = %s.bet',
                TipicoBetFilter::TABLE_ALIAS_TIPICO_BET,
                TipicoBetFilter::TABLE_ALIAS_TIPICO_ODD_BOTH_SCORE
            );
            $qb->innerJoin(
                TipicoBothTeamsScoreOdd::class,
                TipicoBetFilter::TABLE_ALIAS_TIPICO_ODD_BOTH_SCORE,
                'WITH',
                $condition
            );
        }
        if ($filter->isIncludeOverUnder()) {
            $condition = sprintf(
                '%s.id = %s.bet and %s.targetValue = %s',
                TipicoBetFilter::TABLE_ALIAS_TIPICO_BET,
                TipicoBetFilter::TABLE_ALIAS_TIPICO_ODD_OVER_UNDER,
                TipicoBetFilter::TABLE_ALIAS_TIPICO_ODD_OVER_UNDER,
                $filter->getTargetValue(),
            );
            $qb->innerJoin(TipicoOverUnderOdd::class,
                TipicoBetFilter::TABLE_ALIAS_TIPICO_ODD_OVER_UNDER,
                'WITH',
                $condition
            );
        }
        if ($filter->isIncludeHeadToHead()) {
            $condition = sprintf(
                '%s.id = %s.bet',
                TipicoBetFilter::TABLE_ALIAS_TIPICO_BET,
                TipicoBetFilter::TABLE_ALIAS_TIPICO_HEAD_TO_HEAD
            );
            $qb->innerJoin(
                TipicoHeadToHeadOdd::class,
                TipicoBetFilter::TABLE_ALIAS_TIPICO_HEAD_TO_HEAD,
                'WITH',
                $condition
            );
        }

        $searchExpression = sprintf('%s.%s', $filter->getSearchTableAlias(), $filter->getSearchOddColumn());

        $qb->where($qb->expr()->gte($searchExpression, ':min'));
        $qb->setParameter('min', $filter->getMin());
        $qb->andWhere($qb->expr()->lte($searchExpression, ':max'));
        $qb->setParameter('max', $filter->getMax());

        $qb->setMaxResults($filter->getLimit());
        $qb->orderBy(TipicoBetFilter::TABLE_ALIAS_TIPICO_BET . '.startAtTimeStamp', $filter->getOrder());

        if ($filter->isHasTimeFrame()) {
            $currentDate = new DateTime();
            $currentDate->setTime(0, 0, 0);

            $qb->andWhere($qb->expr()->gt(TipicoBetFilter::TABLE_ALIAS_TIPICO_BET . '.startAtTimeStamp', ':startAfter'));
            $qb->setParameter('startAfter', $currentDate->getTimestamp() * 1000);
        } else {
            $qb->andWhere($qb->expr()->notIn(TipicoBetFilter::TABLE_ALIAS_TIPICO_BET . '.id', ':ids'));
            $qb->setParameter('ids', $filter->getAlreadyUsedFixtureIds());
            $qb->andWhere($qb->expr()->eq(TipicoBetFilter::TABLE_ALIAS_TIPICO_BET . '.finished', ':finished'));
            $qb->setParameter('finished', true);
        }

        if ($filter->isCountRequest()) {
            $qb->select('count(' . TipicoBetFilter::TABLE_ALIAS_TIPICO_BET . '.id)');
            return (int)$qb->getQuery()->getSingleScalarResult();
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * @return TipicoBet[]
     */
    public function findInRange(?int $from, ?int $until): array
    {
        $qb = $this->createQueryBuilder('f');
        if ($from){
            $qb->andWhere($qb->expr()->gte('f.startAtTimeStamp', ':from'));
            $qb->setParameter('from', $from);
        }
        if ($until){
            $qb->andWhere($qb->expr()->lte('f.startAtTimeStamp', ':until'));
            $qb->setParameter('until', $until);
        }

        $qb->setMaxResults(800);

        return $qb->getQuery()->getResult();
    }
}
