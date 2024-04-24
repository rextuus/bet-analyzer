<?php

namespace App\Service\Betano\Content\BetanoBackup;

use App\Entity\BetanoBackup;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<BetanoBackup>
 *
 * @method BetanoBackup|null find($id, $lockMode = null, $lockVersion = null)
 * @method BetanoBackup|null findOneBy(array $criteria, array $orderBy = null)
 * @method BetanoBackup[]    findAll()
 * @method BetanoBackup[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BetanoBackupRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BetanoBackup::class);
    }

    public function save(BetanoBackup $betanoBackup, bool $flush = true): void
    {
        $this->_em->persist($betanoBackup);
        if ($flush) {
            $this->_em->flush();
        }
    }
    //    /**
    //     * @return BetanoBackup[] Returns an array of BetanoBackup objects
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

    //    public function findOneBySomeField($value): ?BetanoBackup
    //    {
    //        return $this->createQueryBuilder('b')
    //            ->andWhere('b.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
