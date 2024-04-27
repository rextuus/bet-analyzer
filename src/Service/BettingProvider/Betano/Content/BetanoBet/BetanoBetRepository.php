<?php

namespace App\Service\BettingProvider\Betano\Content\BetanoBet;

use App\Entity\BettingProvider\BetanoBet;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<BetanoBet>
 *
 * @method BetanoBet|null find($id, $lockMode = null, $lockVersion = null)
 * @method BetanoBet|null findOneBy(array $criteria, array $orderBy = null)
 * @method BetanoBet[]    findAll()
 * @method BetanoBet[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BetanoBetRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BetanoBet::class);
    }

    public function save(BetanoBet $betanoBet, bool $flush = true): void
    {
        $this->_em->persist($betanoBet);
        if($flush){
            $this->_em->flush();
        }
    }
    //    /**
    //     * @return BetanoBet[] Returns an array of BetanoBet objects
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

    //    public function findOneBySomeField($value): ?BetanoBet
    //    {
    //        return $this->createQueryBuilder('b')
    //            ->andWhere('b.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
