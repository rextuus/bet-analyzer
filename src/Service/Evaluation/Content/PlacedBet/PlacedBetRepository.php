<?php

namespace App\Service\Evaluation\Content\PlacedBet;

use App\Entity\PlacedBet;
use App\Entity\SimpleBetRow;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PlacedBet>
 *
 * @method PlacedBet|null find($id, $lockMode = null, $lockVersion = null)
 * @method PlacedBet|null findOneBy(array $criteria, array $orderBy = null)
 * @method PlacedBet[]    findAll()
 * @method PlacedBet[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PlacedBetRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PlacedBet::class);
    }

    public function save(PlacedBet $placedBet, bool $flush = true): void
    {
        $this->_em->persist($placedBet);
        if($flush){
            $this->_em->flush();
        }
    }

//    /**
//     * @return PlacedBet[] Returns an array of PlacedBet objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('p.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?PlacedBet
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
