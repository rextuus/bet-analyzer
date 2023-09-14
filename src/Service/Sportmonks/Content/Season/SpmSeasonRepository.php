<?php

namespace App\Service\Sportmonks\Content\Season;

use App\Entity\SpmLeague;
use App\Entity\SpmRound;
use App\Entity\SpmSeason;
use App\Entity\SpmStanding;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<SpmSeason>
 *
 * @method SpmSeason|null find($id, $lockMode = null, $lockVersion = null)
 * @method SpmSeason|null findOneBy(array $criteria, array $orderBy = null)
 * @method SpmSeason[]    findAll()
 * @method SpmSeason[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SpmSeasonRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SpmSeason::class);
    }

    public function save(SpmSeason $spmSeason): void
    {
        $this->_em->persist($spmSeason);
        $this->_em->flush();
    }
//    /**
//     * @return SpmSeason[] Returns an array of SpmSeason objects
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

//    public function findOneBySomeField($value): ?SpmSeason
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
    public function getSeasonFixtureAmountBasedOnStanding()
    {
        $qb = $this->createQueryBuilder('s');
        $qb->select('s as season, count(st) as teams');
        $qb->leftJoin(SpmStanding::class, 'st', 'WITH', 'st.seasonApiId = s.apiId');
//        $qb->innerJoin(SpmRound::class, 'r', 'WITH', 'st.roundApiId = r.apiId');
//        $qb->innerJoin(SpmLeague::class, 'l', 'WITH', 'l.apiId = r.leagueApiId');
//        $qb->where($qb->expr()->eq('r.name', ':round'));
//        $qb->setParameter('round', '1');
        $qb->groupBy('s');

        return $qb->getQuery()->getResult();
    }

    public function findRoundWithoutStandings()
    {
        $qb = $this->createQueryBuilder('s');
        $qb->select('s');
        $qb->leftJoin(SpmStanding::class, 'st', 'WITH', 'st.seasonApiId = s.apiId');
        $qb->where('st.id IS NULL');
        $qb->andWhere('s.standingsAvailable = true');
//        $qb->andWhere('r.id = 316');
        $qb->setMaxResults(1);
        return $qb->getQuery()->getResult();
    }
}
