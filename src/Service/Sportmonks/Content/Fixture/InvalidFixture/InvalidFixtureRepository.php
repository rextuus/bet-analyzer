<?php

namespace App\Service\Sportmonks\Content\Fixture\InvalidFixture;

use App\Entity\Spm\InvalidFixture;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<InvalidFixture>
 *
 * @method InvalidFixture|null find($id, $lockMode = null, $lockVersion = null)
 * @method InvalidFixture|null findOneBy(array $criteria, array $orderBy = null)
 * @method InvalidFixture[]    findAll()
 * @method InvalidFixture[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class InvalidFixtureRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, InvalidFixture::class);
    }

    public function save(InvalidFixture $invalidFixture, bool $flush = true): void
    {
        $this->_em->persist($invalidFixture);
        if($flush){
            $this->_em->flush();
        }
    }
//    /**
//     * @return InvalidFixture[] Returns an array of InvalidFixture objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('i')
//            ->andWhere('i.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('i.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?InvalidFixture
//    {
//        return $this->createQueryBuilder('i')
//            ->andWhere('i.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
