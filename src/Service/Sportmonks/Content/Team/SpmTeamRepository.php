<?php

namespace App\Service\Sportmonks\Content\Team;

use App\Entity\SpmTeam;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<SpmTeam>
 *
 * @method SpmTeam|null find($id, $lockMode = null, $lockVersion = null)
 * @method SpmTeam|null findOneBy(array $criteria, array $orderBy = null)
 * @method SpmTeam[]    findAll()
 * @method SpmTeam[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SpmTeamRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SpmTeam::class);
    }

    public function save(SpmTeam $spmTeam, bool $flush = true): void
    {
        $this->_em->persist($spmTeam);
        if($flush){
            $this->_em->flush();
        }
    }
//    /**
//     * @return SpmTeam[] Returns an array of SpmTeam objects
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

//    public function findOneBySomeField($value): ?SpmTeam
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
