<?php

namespace App\Service\Tipico\Content\TipicoBet;

use App\Entity\TipicoBet;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<TipicoBet>
 *
 * @method TipicoBet|null find($id, $lockMode = null, $lockVersion = null)
 * @method TipicoBet|null findOneBy(array $criteria, array $orderBy = null)
 * @method TipicoBet[]    findAll()
 * @method TipicoBet[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TipicoBetRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TipicoBet::class);
    }

    public function save(TipicoBet $tipicoBet, bool $flush = true): void
    {
        $this->_em->persist($tipicoBet);
        if($flush){
            $this->_em->flush();
        }
    }

//    /**
//     * @return TipicoBet[] Returns an array of TipicoBet objects
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

//    public function findOneBySomeField($value): ?TipicoBet
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }

    /**
     * @return TipicoBet[]
     */
    public function findAllUndecoratedMatches(): array
    {
        $timeStamp = (new DateTime('+2h'))->getTimestamp();
        $timeStamp = $timeStamp * 1000;
dump($timeStamp);
        $qb = $this->createQueryBuilder('t');
        $qb->where($qb->expr()->lt('t.startAtTimeStamp', ':timeStamp'));
        $qb->setParameter('timeStamp', $timeStamp);
        $qb->andWhere($qb->expr()->eq('t.finished', ':finished'));
        $qb->setParameter('finished', false);

        return $qb->getQuery()->getResult();
    }
}
