<?php

namespace App\Service\Statistic\Content\BetRowCombination;

use App\Entity\BetRowCombination;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<BetRowCombination>
 *
 * @method BetRowCombination|null find($id, $lockMode = null, $lockVersion = null)
 * @method BetRowCombination|null findOneBy(array $criteria, array $orderBy = null)
 * @method BetRowCombination[]    findAll()
 * @method BetRowCombination[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BetRowCombinationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BetRowCombination::class);
    }

    public function save(BetRowCombination $betRowCombination, bool $flush = true): void
    {
        $this->_em->persist($betRowCombination);
        if($flush){
            $this->_em->flush();
        }
    }

//    /**
//     * @return BetRowCombination[] Returns an array of BetRowCombination objects
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

//    public function findOneBySomeField($value): ?BetRowCombination
//    {
//        return $this->createQueryBuilder('b')
//            ->andWhere('b.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
