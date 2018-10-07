<?php

namespace App\Repository;

use App\Entity\Video\DailymotionVideo;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method DailymotionVideo|null find($id, $lockMode = null, $lockVersion = null)
 * @method DailymotionVideo|null findOneBy(array $criteria, array $orderBy = null)
 * @method DailymotionVideo[]    findAll()
 * @method DailymotionVideo[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DailymotionVideoRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, DailymotionVideo::class);
    }

//    /**
//     * @return AbstractVideo[] Returns an array of AbstractVideo objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('v')
            ->andWhere('v.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('v.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?AbstractVideo
    {
        return $this->createQueryBuilder('v')
            ->andWhere('v.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
