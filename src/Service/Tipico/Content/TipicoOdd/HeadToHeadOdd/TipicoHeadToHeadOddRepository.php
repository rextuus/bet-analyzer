<?php

namespace App\Service\Tipico\Content\TipicoOdd\HeadToHeadOdd;

use App\Entity\TipicoHeadToHeadOdd;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<TipicoHeadToHeadOdd>
 *
 * @method TipicoHeadToHeadOdd|null find($id, $lockMode = null, $lockVersion = null)
 * @method TipicoHeadToHeadOdd|null findOneBy(array $criteria, array $orderBy = null)
 * @method TipicoHeadToHeadOdd[]    findAll()
 * @method TipicoHeadToHeadOdd[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TipicoHeadToHeadOddRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TipicoHeadToHeadOdd::class);
    }

    public function save(TipicoHeadToHeadOdd $tipicoHeadToHeadScore, bool $flush = true): void
    {
        $this->_em->persist($tipicoHeadToHeadScore);
        if($flush){
            $this->_em->flush();
        }
    }
    //    /**
    //     * @return TipicoHeadToHeadScore[] Returns an array of TipicoHeadToHeadScore objects
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

    //    public function findOneBySomeField($value): ?TipicoHeadToHeadScore
    //    {
    //        return $this->createQueryBuilder('t')
    //            ->andWhere('t.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
