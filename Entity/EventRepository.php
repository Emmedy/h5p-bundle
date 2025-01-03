<?php


namespace Emmedy\H5PBundle\Entity;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class EventRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Event::class);
    }

    public function findRecentlyUsedLibraries($userId): array
    {
        $qb = $this->createQueryBuilder('e')
            ->select('e.libraryName, MAX(e.createdAt) as max_created_at')
            ->where('e.type = \'content\' and e.subType = \'created\' and e.user = :user')
            ->groupBy('e.libraryName')
            ->orderBy('max_created_at', 'DESC')
            ->setParameter('user', $userId);
        return $qb->getQuery()->getArrayResult();
    }
}
