<?php

namespace App\Service\Sportmonks\Content\Score;

use App\Entity\Spm\SpmFixture;
use App\Entity\Spm\SpmScore;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<SpmScore>
 *
 * @method SpmScore|null find($id, $lockMode = null, $lockVersion = null)
 * @method SpmScore|null findOneBy(array $criteria, array $orderBy = null)
 * @method SpmScore[]    findAll()
 * @method SpmScore[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SpmScoreRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SpmScore::class);
    }

    public function save(SpmScore $spmScore, bool $flush = true): void
    {
        $this->_em->persist($spmScore);
        if ($flush){
            $this->_em->flush();
        }
    }

//    /**
//     * @return SpmScore[] Returns an array of SpmScore objects
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

//    public function findOneBySomeField($value): ?SpmScore
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
    public function findScoresForFixture(int $fixtureApi)
    {
        $qb = $this->createQueryBuilder('s');
//        $qb->select('f, s');
        $qb->innerJoin(SpmFixture::class, 'f', 'WITH', 's.fixtureApiId = f.apiId');
        $qb->where('f.apiId = :fixtureApiId')
            ->setParameter('fixtureApiId', $fixtureApi);
        return $qb->getQuery()->getResult();
    }
}
