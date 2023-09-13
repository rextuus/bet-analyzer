<?php

namespace App\Service\Sportmonks\Content\Standing;

use App\Entity\SpmStanding;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<SpmStanding>
 *
 * @method SpmStanding|null find($id, $lockMode = null, $lockVersion = null)
 * @method SpmStanding|null findOneBy(array $criteria, array $orderBy = null)
 * @method SpmStanding[]    findAll()
 * @method SpmStanding[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SpmStandingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SpmStanding::class);
    }

    public function save(SpmStanding $spmStanding, bool $flush = true): void
    {
        $this->_em->persist($spmStanding);
        if ($flush) {
            $this->_em->flush();
        }
    }
//    /**
//     * @return SpmStanding[] Returns an array of SpmStanding objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('s.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?SpmStanding
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
