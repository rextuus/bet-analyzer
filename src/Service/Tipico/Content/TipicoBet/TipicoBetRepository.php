<?php

namespace App\Service\Tipico\Content\TipicoBet;

use App\Entity\TipicoBet;
use App\Entity\TipicoOverUnderOdd;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
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
        if($flush){
            $this->_em->flush();
        }
    }

//    /**
//     * @return TipicoBet[] Returns an array of TipicoBet objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('t.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?TipicoBet
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }

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
    public function findInRange(float $min, float $max, string $targetOddColumn, array $alreadyUsed, int $limit = 100): array
    {
        $alreadyUsed = array_merge($alreadyUsed, [-1]);

        $qb = $this->createQueryBuilder('t');
        $this->addSearchQueryParameters($qb, $targetOddColumn, $min, $max, $alreadyUsed);
        $qb->setMaxResults($limit);

        return $qb->getQuery()->getResult();
    }

    /**
     * @return TipicoBet[]
     */
    public function getFittingFixturesWithOverUnderOdds(float $min, float $max, string $targetOddColumn, array $alreadyUsed, int $limit = 100): array
    {
        $alreadyUsed = array_merge($alreadyUsed, [-1]);

        $qb = $this->createQueryBuilder('t');
        $qb->innerJoin(TipicoOverUnderOdd::class, 'o', 'WITH', 't.id = o.bet');
        $this->addSearchQueryParameters($qb, $targetOddColumn, $min, $max, $alreadyUsed);
        $qb->setMaxResults($limit);

        return $qb->getQuery()->getResult();
    }

    public function getFittingFixturesCount(float $min, float $max, string $targetOddColumn, array $alreadyUsed): bool
    {
        $alreadyUsed = array_merge($alreadyUsed, [-1]);

        $qb = $this->createQueryBuilder('t');
        $qb->select('count(t.id)');
        $this->addSearchQueryParameters($qb, $targetOddColumn, $min, $max, $alreadyUsed);

        return (bool) $qb->getQuery()->getSingleScalarResult();
    }

    public function addSearchQueryParameters(QueryBuilder $qb, string $targetOddColumn, float $min, float $max, array $alreadyUsed): void
    {
        $qb->where($qb->expr()->gte('t.' . $targetOddColumn, ':min'));
        $qb->setParameter('min', $min);
        $qb->andWhere($qb->expr()->lte('t.' . $targetOddColumn, ':max'));
        $qb->setParameter('max', $max);
        $qb->andWhere($qb->expr()->eq('t.finished', ':finished'));
        $qb->setParameter('finished', true);

        $qb->andWhere($qb->expr()->notIn('t.id', ':ids'));
        $qb->setParameter('ids', $alreadyUsed);

        $qb->orderBy('t.startAtTimeStamp', 'ASC');
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
        $qb->setParameter('startAfter', $currentDate->getTimestamp()*1000);

        $qb->setMaxResults($limit);

        return $qb->getQuery()->getResult();
    }
}
