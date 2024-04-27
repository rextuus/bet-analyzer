<?php

namespace App\Service\Statistic\Content\SeasonSummary;

use App\Entity\Spm\SeasonSummary;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<SeasonSummary>
 *
 * @method SeasonSummary|null find($id, $lockMode = null, $lockVersion = null)
 * @method SeasonSummary|null findOneBy(array $criteria, array $orderBy = null)
 * @method SeasonSummary[]    findAll()
 * @method SeasonSummary[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SeasonSummaryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SeasonSummary::class);
    }

    public function save(SeasonSummary $seasonSummary, bool $flush = true): void
    {
        $this->_em->persist($seasonSummary);
        if($flush){
            $this->_em->flush();
        }
    }

//    /**
//     * @return SeasonSummary[] Returns an array of SeasonSummary objects
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

//    public function findOneBySomeField($value): ?SeasonSummary
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
