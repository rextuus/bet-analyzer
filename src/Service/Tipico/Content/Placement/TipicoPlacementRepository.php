<?php

namespace App\Service\Tipico\Content\Placement;

use App\Entity\TipicoPlacement;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<TipicoPlacement>
 *
 * @method TipicoPlacement|null find($id, $lockMode = null, $lockVersion = null)
 * @method TipicoPlacement|null findOneBy(array $criteria, array $orderBy = null)
 * @method TipicoPlacement[]    findAll()
 * @method TipicoPlacement[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TipicoPlacementRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TipicoPlacement::class);
    }

    //    /**
    //     * @return TipicoPlacement[] Returns an array of TipicoPlacement objects
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

    //    public function findOneBySomeField($value): ?TipicoPlacement
    //    {
    //        return $this->createQueryBuilder('t')
    //            ->andWhere('t.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }

    public function save(TipicoPlacement $tipicoPlacement, bool $flush = true): void
    {
        $this->_em->persist($tipicoPlacement);
        if($flush){
            $this->_em->flush();
        }
    }
}
