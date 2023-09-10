<?php

namespace App\Service\Sportmonks\Content\Odd;

use App\Entity\SpmOdd;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<SpmOdd>
 *
 * @method SpmOdd|null find($id, $lockMode = null, $lockVersion = null)
 * @method SpmOdd|null findOneBy(array $criteria, array $orderBy = null)
 * @method SpmOdd[]    findAll()
 * @method SpmOdd[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SpmOddRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SpmOdd::class);
    }

    public function save(SpmOdd $spmOdd, bool $flush = true): void
    {
        $this->_em->persist($spmOdd);
        if ($flush){
            $this->_em->flush();
        }
    }

//    /**
//     * @return SpmOdd[] Returns an array of SpmOdd objects
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

//    public function findOneBySomeField($value): ?SpmOdd
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
