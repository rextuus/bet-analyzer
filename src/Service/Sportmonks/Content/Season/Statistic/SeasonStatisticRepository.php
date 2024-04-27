<?php

namespace App\Service\Sportmonks\Content\Season\Statistic;

use App\Entity\Spm\SeasonStatistic;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<SeasonStatistic>
 *
 * @method SeasonStatistic|null find($id, $lockMode = null, $lockVersion = null)
 * @method SeasonStatistic|null findOneBy(array $criteria, array $orderBy = null)
 * @method SeasonStatistic[]    findAll()
 * @method SeasonStatistic[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SeasonStatisticRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SeasonStatistic::class);
    }

    public function save(SeasonStatistic $seasonStatistic, bool $flush = true): void
    {
        $this->_em->persist($seasonStatistic);
        if($flush){
            $this->_em->flush();
        }
    }

//    /**
//     * @return SeasonStatistic[] Returns an array of SeasonStatistic objects
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

//    public function findOneBySomeField($value): ?SeasonStatistic
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
