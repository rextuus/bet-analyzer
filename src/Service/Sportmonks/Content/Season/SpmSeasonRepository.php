<?php

namespace App\Service\Sportmonks\Content\Season;

use App\Entity\SpmSeason;
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
}
