<?php

namespace App\Service\Tipico\Content\TipicoOdd\BothTeamsScoreOdd;

use App\Entity\BettingProvider\TipicoBothTeamsScoreOdd;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<TipicoBothTeamsScoreOdd>
 *
 * @method TipicoBothTeamsScoreOdd|null find($id, $lockMode = null, $lockVersion = null)
 * @method TipicoBothTeamsScoreOdd|null findOneBy(array $criteria, array $orderBy = null)
 * @method TipicoBothTeamsScoreOdd[]    findAll()
 * @method TipicoBothTeamsScoreOdd[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TipicoBothTeamsScoreOddRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TipicoBothTeamsScoreOdd::class);
    }

    public function save(TipicoBothTeamsScoreOdd $tipicoBothTeamsScoreBet, bool $flush = true): void
    {
        $this->_em->persist($tipicoBothTeamsScoreBet);
        if($flush){
            $this->_em->flush();
        }
    }

    //    /**
    //     * @return TipicoBothTeamsScoreBet[] Returns an array of TipicoBothTeamsScoreBet objects
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

    //    public function findOneBySomeField($value): ?TipicoBothTeamsScoreBet
    //    {
    //        return $this->createQueryBuilder('t')
    //            ->andWhere('t.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
