<?php

namespace App\Service\Statistic\Content\OddOutcome;

use App\Entity\Spm\OddOutcome;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<OddOutcome>
 *
 * @method OddOutcome|null find($id, $lockMode = null, $lockVersion = null)
 * @method OddOutcome|null findOneBy(array $criteria, array $orderBy = null)
 * @method OddOutcome[]    findAll()
 * @method OddOutcome[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OddOutcomeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, OddOutcome::class);
    }

    public function save(OddOutcome $oddOutcome, bool $flush = true): void
    {
        $this->_em->persist($oddOutcome);
        if($flush){
            $this->_em->flush();
        }
    }

//    /**
//     * @return OddOutcome[] Returns an array of OddOutcome objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('o')
//            ->andWhere('o.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('o.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?OddOutcome
//    {
//        return $this->createQueryBuilder('o')
//            ->andWhere('o.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
