<?php

namespace App\Repository;

use App\Entity\Video\YoutubeVideo;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method YoutubeVideo|null find($id, $lockMode = null, $lockVersion = null)
 * @method YoutubeVideo|null findOneBy(array $criteria, array $orderBy = null)
 * @method YoutubeVideo[]    findAll()
 * @method YoutubeVideo[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class YoutubeVideoRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, YoutubeVideo::class);
    }

    public function findAllWithPagination(int $start = 0, $nbMaxParPage = 50)
    {
        return $this->createQueryBuilder('v')
            ->getQuery()
            ->setFirstResult($start)
            ->setMaxResults($nbMaxParPage)
            ->getResult();
    }
}
