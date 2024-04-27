<?php

namespace App\Service\BettingProvider\BettingProviderBackupFile\Content;

use App\Entity\BettingProvider\BettingProviderBackupFile;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<BettingProviderBackupFile>
 *
 * @method BettingProviderBackupFile|null find($id, $lockMode = null, $lockVersion = null)
 * @method BettingProviderBackupFile|null findOneBy(array $criteria, array $orderBy = null)
 * @method BettingProviderBackupFile[]    findAll()
 * @method BettingProviderBackupFile[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BettingProviderBackupFileRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BettingProviderBackupFile::class);
    }

    public function save(BettingProviderBackupFile $bettingProviderBackupFile, bool $flush = true): void
    {
        $this->_em->persist($bettingProviderBackupFile);
        if ($flush) {
            $this->_em->flush();
        }
    }
    //    /**
    //     * @return BettingProviderBackupFile[] Returns an array of BettingProviderBackupFile objects
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

    //    public function findOneBySomeField($value): ?BettingProviderBackupFile
    //    {
    //        return $this->createQueryBuilder('b')
    //            ->andWhere('b.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
