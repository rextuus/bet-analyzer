<?php

namespace App\Service\BettingProvider\Bwin\Content\BwinBet;

use App\Entity\BettingProvider\BwinBet;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<BwinBet>
 *
 * @method BwinBet|null find($id, $lockMode = null, $lockVersion = null)
 * @method BwinBet|null findOneBy(array $criteria, array $orderBy = null)
 * @method BwinBet[]    findAll()
 * @method BwinBet[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BwinBetRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BwinBet::class);
    }

    public function save(BwinBet $bwinBet, bool $flush = true): void
    {
        $this->_em->persist($bwinBet);
        if ($flush) {
            $this->_em->flush();
        }
    }
    //    /**
    //     * @return BwinBet[] Returns an array of BwinBet objects
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

    //    public function findOneBySomeField($value): ?BwinBet
    //    {
    //        return $this->createQueryBuilder('b')
    //            ->andWhere('b.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
