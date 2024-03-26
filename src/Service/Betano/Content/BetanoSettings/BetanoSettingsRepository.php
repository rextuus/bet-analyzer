<?php

namespace App\Service\Betano\Content\BetanoSettings;

use App\Entity\BetanoSettings;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<BetanoSettings>
 *
 * @method BetanoSettings|null find($id, $lockMode = null, $lockVersion = null)
 * @method BetanoSettings|null findOneBy(array $criteria, array $orderBy = null)
 * @method BetanoSettings[]    findAll()
 * @method BetanoSettings[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BetanoSettingsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BetanoSettings::class);
    }

    public function save(BetanoSettings $betanoSettings, bool $flush = true): void
    {
        $this->_em->persist($betanoSettings);
        if ($flush) {
            $this->_em->flush();
        }
    }
    //    /**
    //     * @return BetanoSettings[] Returns an array of BetanoSettings objects
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

    //    public function findOneBySomeField($value): ?BetanoSettings
    //    {
    //        return $this->createQueryBuilder('b')
    //            ->andWhere('b.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
