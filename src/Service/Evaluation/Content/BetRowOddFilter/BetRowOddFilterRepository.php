<?php

namespace App\Service\Evaluation\Content\BetRowOddFilter;

use App\Entity\BetRowOddFilter;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<BetRowOddFilter>
 *
 * @method BetRowOddFilter|null find($id, $lockMode = null, $lockVersion = null)
 * @method BetRowOddFilter|null findOneBy(array $criteria, array $orderBy = null)
 * @method BetRowOddFilter[]    findAll()
 * @method BetRowOddFilter[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BetRowOddFilterRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BetRowOddFilter::class);
    }

    public function save(BetRowOddFilter $betRowOddFilter, bool $flush = true): void
    {
        $this->_em->persist($betRowOddFilter);
        if($flush){
            $this->_em->flush();
        }
    }



//    /**
//     * @return BetRowOddFilter[] Returns an array of BetRowOddFilter objects
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

//    public function findOneBySomeField($value): ?BetRowOddFilter
//    {
//        return $this->createQueryBuilder('b')
//            ->andWhere('b.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
