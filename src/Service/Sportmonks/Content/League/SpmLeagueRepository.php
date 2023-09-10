<?php

namespace App\Service\Sportmonks\Content\League;

use App\Entity\SpmLeague;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<SpmLeague>
 *
 * @method SpmLeague|null find($id, $lockMode = null, $lockVersion = null)
 * @method SpmLeague|null findOneBy(array $criteria, array $orderBy = null)
 * @method SpmLeague[]    findAll()
 * @method SpmLeague[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SpmLeagueRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SpmLeague::class);
    }

    public function save(SpmLeague $spmLeague): void
    {
        $this->_em->persist($spmLeague);
        $this->_em->flush();
    }
//    /**
//     * @return SpmLeagueData[] Returns an array of SpmLeagueData objects
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

//    public function findOneBySomeField($value): ?SpmLeagueData
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
