<?php

namespace App\Service\Sportmonks\Content\Fixture;

use App\Entity\Spm\InvalidFixture;
use App\Entity\Spm\SpmFixture;
use App\Entity\Spm\SpmOdd;
use App\Entity\Spm\SpmSeason;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<SpmFixture>
 *
 * @method SpmFixture|null find($id, $lockMode = null, $lockVersion = null)
 * @method SpmFixture|null findOneBy(array $criteria, array $orderBy = null)
 * @method SpmFixture[]    findAll()
 * @method SpmFixture[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SpmFixtureRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SpmFixture::class);
    }

    public function save(SpmFixture $spmFixture): void
    {
        $this->_em->persist($spmFixture);
        $this->_em->flush();
    }
//    /**
//     * @return SpmFixture[] Returns an array of SpmFixture objects
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

//    public function findOneBySomeField($value): ?SpmFixture
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
    public function findNextUndecoratedFixture()
    {
        $qb = $this->createQueryBuilder('f');
        $qb->select('f');
        $qb->leftJoin(InvalidFixture::class, 'i', 'WITH', 'i.fixtureApiId = f.apiId');
        $qb->where('i.id IS NULL');
        $qb->andWhere('f.oddDecorated = :oddDecorated')
        ->setParameter('oddDecorated', false);

        return $qb->getQuery()->getResult();
    }

    public function getFixtureWithOddDecorationBySeason(\App\Entity\Spm\SeasonStatistic $seasonStatistic): int
    {
        $qb = $this->createQueryBuilder('f');
        $qb->select('f');
        $qb->innerJoin(SpmSeason::class, 's', 'WITH', 'f.seasonApiId = s.apiId');
        $qb->innerJoin(SpmOdd::class, 'o', 'WITH', 'o.fixtureApiId = f.apiId');
        $qb->where('s.apiId = :seasonApiId')
            ->setParameter('seasonApiId', $seasonStatistic->getSeasonApiId());

        $result = $qb->getQuery()->getResult();
        if (is_array($result)){
            return count($result);
        }
        return 0;
    }

    public function findFixturesAndOddsBySeason(SpmSeason $season)
    {
        $qb = $this->createQueryBuilder('f');
        $qb->select('f, o');
//        $qb->innerJoin(SpmSeason::class, 's', 'WITH', 'f.seasonApiId = s.apiId');
        $qb->innerJoin(SpmOdd::class, 'o', 'WITH', 'o.fixtureApiId = f.apiId');
        $qb->where('f.seasonApiId = :seasonApiId')
            ->setParameter('seasonApiId', $season->getApiId());
        $qb->groupBy('f');

        $result = $qb->getQuery()->getResult();
        return $result;
    }
}
