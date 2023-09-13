<?php

namespace App\Service\Sportmonks\Content\Round;

use App\Entity\SpmRound;
use App\Entity\SpmStanding;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<SpmRound>
 *
 * @method SpmRound|null find($id, $lockMode = null, $lockVersion = null)
 * @method SpmRound|null findOneBy(array $criteria, array $orderBy = null)
 * @method SpmRound[]    findAll()
 * @method SpmRound[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SpmRoundRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SpmRound::class);
    }

    public function save(SpmRound $spmRound): void
    {
        $this->_em->persist($spmRound);
        $this->_em->flush();
    }
//    /**
//     * @return SpmRound[] Returns an array of SpmRound objects
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

//    public function findOneBySomeField($value): ?SpmRound
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
    public function findRoundWithoutStandings()
    {
        $qb = $this->createQueryBuilder('r');
        $qb->select('r');
        $qb->leftJoin(SpmStanding::class, 's', 'WITH', 's.roundApiId = r.id');
        $qb->where('s.id IS NULL');
        $qb->andWhere('r.id = 44');
        return $qb->getQuery()->getResult();
    }
}
