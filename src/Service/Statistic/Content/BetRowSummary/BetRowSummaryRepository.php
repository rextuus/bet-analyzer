<?php

namespace App\Service\Statistic\Content\BetRowSummary;

use App\Entity\Spm\BetRowSummary;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<BetRowSummary>
 *
 * @method BetRowSummary|null find($id, $lockMode = null, $lockVersion = null)
 * @method BetRowSummary|null findOneBy(array $criteria, array $orderBy = null)
 * @method BetRowSummary[]    findAll()
 * @method BetRowSummary[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BetRowSummaryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BetRowSummary::class);
    }

    public function save(BetRowSummary $betRowSummary, bool $flush = true): void
    {
        $this->_em->persist($betRowSummary);
        if($flush){
            $this->_em->flush();
        }
    }
//    /**
//     * @return BetRowSummary[] Returns an array of BetRowSummary objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('b')
//            ->andWhere('b.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('b.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?BetRowSummary
//    {
//        return $this->createQueryBuilder('b')
//            ->andWhere('b.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
