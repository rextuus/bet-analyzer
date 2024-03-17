<?php

namespace App\Service\Tipico\Content\TipicoOdd;

use App\Entity\TipicoOverUnderOdd;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<TipicoOverUnderOdd>
 *
 * @method TipicoOverUnderOdd|null find($id, $lockMode = null, $lockVersion = null)
 * @method TipicoOverUnderOdd|null findOneBy(array $criteria, array $orderBy = null)
 * @method TipicoOverUnderOdd[]    findAll()
 * @method TipicoOverUnderOdd[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TipicoOverUnderOddRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TipicoOverUnderOdd::class);
    }

    public function save(TipicoOverUnderOdd $tipicoOverUnderOdd, bool $flush = true): void
    {
        $this->_em->persist($tipicoOverUnderOdd);
        if($flush){
            $this->_em->flush();
        }
    }

    //    /**
    //     * @return TipicoOverUnderOdd[] Returns an array of TipicoOverUnderOdd objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('t')
    //            ->andWhere('t.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('t.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?TipicoOverUnderOdd
    //    {
    //        return $this->createQueryBuilder('t')
    //            ->andWhere('t.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
