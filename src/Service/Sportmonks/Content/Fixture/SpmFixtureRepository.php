<?php

namespace App\Service\Sportmonks\Content\Fixture;

use App\Entity\SpmFixture;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<SpmFixture>
 *
 * @method SpmFixture|null find($id, $lockMode = null, $lockVersion = null)
 * @method SpmFixture|null findOneBy(array $criteria, array $orderBy = null)
 * @method SpmFixture[]    findAll()
 * @method SpmFixture[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SpmFixtureRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SpmFixture::class);
    }

    public function save(SpmFixture $spmFixture): void
    {
        $this->_em->persist($spmFixture);
        $this->_em->flush();
    }
//    /**
//     * @return SpmFixture[] Returns an array of SpmFixture objects
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

//    public function findOneBySomeField($value): ?SpmFixture
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
